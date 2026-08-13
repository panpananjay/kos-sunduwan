<?php

namespace App\Http\Controllers;

use App\Models\Penghuni;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image; 
use Carbon\Carbon;
use Midtrans\Config;
use Midtrans\Snap;

class TagihanController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Tagihan::with('penghuni.kamar')->latest();

        if ($user->role == 'admin') {
            // Notifikasi admin sekarang hitung yang bener-bener belum bayar saja
            $currentNotifCount = Tagihan::where('status', 'belum_bayar')->count();
            session(['last_seen_admin_notif' => $currentNotifCount]);
        } else {
            $penghuni = Penghuni::where('user_id', $user->id)->first();
            if ($penghuni) {
                $query->where('penghuni_id', $penghuni->id);
            }
        }

        // Fitur Pencarian
        if ($request->filled('cari')) {
            $cari = strtoupper(trim($request->cari));
            $query->whereHas('penghuni', function($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%");
            });
        }

        // Filter
        if ($request->filled('bulan')) $query->where('bulan', $request->bulan);
        if ($request->filled('tahun')) $query->where('tahun', $request->tahun);
        if ($request->filled('status')) $query->where('status', $request->status);

        $tagihans = $query->get();
        return view('tagihan.index', compact('tagihans'));
    }

    // --- INTEGRASI MIDTRANS ---

    public function bayar($id)
    {
        $tagihan = Tagihan::with('penghuni')->findOrFail($id);

        $params = [
            'transaction_details' => [
                'order_id' => 'INV-' . $tagihan->id . '-' . time(),
                'gross_amount' => (int) $tagihan->jumlah_tagihan,
            ],
            'customer_details' => [
                'first_name' => $tagihan->penghuni->nama,
                'phone' => $tagihan->penghuni->no_hp,
            ],
            'item_details' => [
                [
                    'id' => $tagihan->id,
                    'price' => (int) $tagihan->jumlah_tagihan,
                    'quantity' => 1,
                    'name' => "Sewa Kos {$tagihan->bulan} {$tagihan->tahun}",
                ]
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $orderIdParts = explode('-', $request->order_id);
                $tagihanId = $orderIdParts[1];

                $tagihan = Tagihan::find($tagihanId);
                if ($tagihan && $tagihan->status != 'lunas') {
                    $this->prosesPelunasanOtomatis($tagihan);
                }
            }
        }
        return response()->json(['status' => 'success']);
    }

    private function prosesPelunasanOtomatis($tagihan)
    {
        $tagihan->update(['status' => 'lunas', 'catatan' => null]);

        // 1. Logika Poin Kedisiplinan
        $selisihHari = Carbon::now()->diffInDays($tagihan->created_at);
        $poin = ($selisihHari <= 7) ? 50 : -50;
        $tagihan->penghuni->increment('poin', $poin);

        // 2. Generate Invoice Image
        $this->generateInvoiceImage($tagihan);

        // 3. Kirim WhatsApp via Fonnte
        $nominal = number_format($tagihan->jumlah_tagihan, 0, ',', '.');
        $totalPoin = $tagihan->penghuni->fresh()->poin;
        $labelPoin = ($selisihHari <= 7) ? "TEPAT WAKTU! Anda mendapatkan +50 Poin Kedisiplinan." : "Terlambat, Anda dikurangi 50 Poin Kedisiplinan.";
        $linkInvoice = route('tagihan.unduh', $tagihan->id);

        $pesan = "*--- PEMBAYARAN KOS LUNAS ---*\n" .
                "Halo *{$tagihan->penghuni->nama}* 👋\n" .
                "Terima kasih, pembayaran kos periode *{$tagihan->bulan} {$tagihan->tahun}* sebesar *Rp {$nominal}* telah SUKSES diverifikasi otomatis oleh sistem.\n" .
                "🏆 *POIN:*\n" .
                "{$labelPoin}\n" .
                "Total indeks kedisiplinan Anda saat ini: *{$totalPoin} Poin*.\n" .
                "📄 *E-INVOICE RESMI:*\n" .
                "Silakan unduh bukti pembayaran sah Anda di sini:\n" .
                "{$linkInvoice}\n" .
                "Salam hangat, Kos Putri Sunduwan ✨";

        $this->sendWhatsApp($tagihan->penghuni->no_hp, $pesan);
    }

    private function generateInvoiceImage($tagihan)
    {
        $templatePath = public_path('images/template_invoice.jpg');
        if (!file_exists($templatePath)) return;

        $img = Image::read($templatePath)->scale(width: 800);
        $fontBold = public_path('fonts/Montserrat-Bold.ttf');
        
        $img->text(strtoupper($tagihan->penghuni->nama), 70, 315, function($font) use ($fontBold) {
            $font->file($fontBold); $font->size(28); $font->color('#1e293b');
        });
        
        // Simpan ke storage
        $namaFile = 'Invoice_' . $tagihan->id . '.png';
        Storage::disk('public')->put('invoices/' . $namaFile, (string) $img->encodeByExtension('png'));
    }

    /**
     * Download invoice yang sudah di-generate (dipanggil dari tombol "Invoice" di blade)
     */
    public function unduh($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $namaFile = 'Invoice_' . $tagihan->id . '.png';
        $path = 'invoices/' . $namaFile;

        if (!Storage::disk('public')->exists($path)) {
            return redirect()->route('tagihan.index')->with('error', 'File invoice belum tersedia untuk tagihan ini.');
        }

        return Storage::disk('public')->download($path, $namaFile);
    }

    // --- MANAJEMEN TAGIHAN ADMIN ---

    public function generate(Request $request)
    {
        Carbon::setLocale('id');
        $bulan = $request->input('bulan', Carbon::now()->translatedFormat('F'));
        $tahun = $request->input('tahun', Carbon::now()->year);

        $penghunis = Penghuni::whereNotNull('kamar_id')->with('kamar')->get();
        $jumlahTerkirim = 0;

        foreach ($penghunis as $penghuni) {
            $tagihan = Tagihan::where('penghuni_id', $penghuni->id)
                                ->where('bulan', $bulan)
                                ->where('tahun', $tahun)
                                ->first();

            // Cek jika sudah lunas
            if ($tagihan && $tagihan->status == 'lunas') continue;

            if (!$tagihan) {
                $tagihan = Tagihan::create([
                    'penghuni_id' => $penghuni->id,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'jumlah_tagihan' => $penghuni->kamar->harga ?? 0,
                    'status' => 'belum_bayar',
                ]);
            }

            $nominal = number_format($tagihan->jumlah_tagihan, 0, ',', '.');
            $pesan = "*--- NOTIFIKASI TAGIHAN KOS ---*\n" .
                    "Halo *{$penghuni->nama}* 👋\n" .
                    "Informasi tagihan periode *{$bulan} {$tahun}* sudah terbit.\n" .
                    "💰 Total: *Rp {$nominal}*\n" .
                    "📌 Status: *BELUM BAYAR*\n" .
                    "Silakan selesaikan pembayaran otomatis secara aman melalui aplikasi ya! ✨";
            
            $this->sendWhatsApp($penghuni->no_hp, $pesan);
            $jumlahTerkirim++;
        }

        return redirect()->route('tagihan.index')->with('success', "Berhasil generate {$jumlahTerkirim} tagihan.");
    }

    public function verifikasi($id)
    {
        // Tetap ada buat jaga-jaga kalau admin mau melunasi manual tanpa lewat Midtrans
        $tagihan = Tagihan::findOrFail($id);

        if ($tagihan->status == 'lunas') {
            return redirect()->route('tagihan.index')->with('success', 'Tagihan ini sudah lunas sebelumnya.');
        }

        $this->prosesPelunasanOtomatis($tagihan);
        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dilunasi manual! ✅');
    }

    /**
     * Tandai tagihan sebagai ditolak/belum bayar (route sudah ada sebelumnya tapi method-nya hilang)
     */
    public function tolak($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->update(['status' => 'belum_bayar', 'catatan' => 'Pembayaran ditolak/dibatalkan admin']);

        return redirect()->route('tagihan.index')->with('success', 'Tagihan ditandai belum bayar.');
    }

    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        Storage::disk('public')->delete('invoices/Invoice_' . $tagihan->id . '.png');
        $tagihan->delete();
        return redirect()->route('tagihan.index')->with('success', 'Data tagihan berhasil dihapus!');
    }

    private function sendWhatsApp($target, $message)
    {
        try {
            return Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])
                ->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62',
                ]);
        } catch (\Exception $e) {
            return false;
        }
    }
}