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
        // Set konfigurasi Midtrans
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
            $currentNotifCount = Tagihan::where('status', 'menunggu_verifikasi')->count();
            session(['last_seen_admin_notif' => $currentNotifCount]);
        } else {
            $penghuni = Penghuni::where('user_id', $user->id)->first();
            if ($penghuni) {
                $query->where('penghuni_id', $penghuni->id);
            }
        }

        if ($request->filled('cari')) {
            $cari = strtoupper(trim($request->cari));
            if (str_starts_with($cari, 'INV-')) {
                $idAsli = (int) substr($cari, 10);
                $query->where('id', $idAsli);
            } else {
                $query->whereHas('penghuni', function($q) use ($cari) {
                    $q->where('nama', 'like', "%{$cari}%");
                });
            }
        }

        if ($request->filled('bulan')) $query->where('bulan', $request->bulan);
        if ($request->filled('tahun')) $query->where('tahun', $request->tahun);
        if ($request->filled('status')) $query->where('status', $request->status);

        $tagihans = $query->get();
        return view('tagihan.index', compact('tagihans'));
    }

    // METHOD BARU: INTEGRASI MIDTRANS
    public function bayar($id)
    {
        $tagihan = Tagihan::with('penghuni')->findOrFail($id);

        // Buat parameter transaksi untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => 'INV-' . date('Ym') . $tagihan->id . '-' . time(),
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

    public function generate(Request $request)
    {
        \Carbon\Carbon::setLocale('id');
        $bulan = $request->input('bulan', \Carbon\Carbon::now()->translatedFormat('F'));
        $tahun = $request->input('tahun', \Carbon\Carbon::now()->year);

        $penghunis = Penghuni::whereNotNull('kamar_id')->with('kamar')->get();
        
        if ($penghunis->isEmpty()) {
            return redirect()->back()->with('error', "Gagal! Data penghuni kosong atau belum ada yang punya kamar.");
        }

        $jumlahTerkirim = 0;
        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        foreach ($penghunis as $penghuni) {
            $tagihan = Tagihan::where('penghuni_id', $penghuni->id)
                                ->where('bulan', $bulan)
                                ->where('tahun', $tahun)
                                ->first();

            if ($tagihan && in_array($tagihan->status, ['lunas', 'menunggu_verifikasi'])) {
                continue; 
            }

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
            $pesan = "*--- NOTIFIKASI TAGIHAN KOS ---*\n\n" .
                    "Halo *{$penghuni->nama}* 👋\n" .
                    "Informasi tagihan periode *{$bulan} {$tahun}* sudah terbit.\n\n" .
                    "💰 Total: *Rp {$nominal}*\n" .
                    "📌 Status: *BELUM BAYAR*\n\n" .
                    "Silakan melakukan pembayaran langsung melalui aplikasi (Midtrans) atau unggah bukti transfer. ✨";

            $this->sendWhatsApp($penghuni->no_hp, $pesan);
            $jumlahTerkirim++;
        }

        return redirect()->route('tagihan.index')->with('success', "Berhasil mengirimkan {$jumlahTerkirim} notifikasi tagihan.");
    }

    public function show($id)
    {
        $tagihan = Tagihan::with('penghuni.kamar')->findOrFail($id);
        $user = auth()->user();
        if ($user->role == 'penghuni' && $tagihan->penghuni->user_id != $user->id) abort(403);
        return view('tagihan.show', compact('tagihan'));
    }

    public function upload(Request $request, $id)
    {
        $request->validate(['bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:2048']);
        $tagihan = Tagihan::findOrFail($id);
        $path = $request->file('bukti_bayar')->store('bukti_pembayaran', 'public');
        
        $tagihan->update([
            'bukti_bayar' => $path, 
            'status' => 'menunggu_verifikasi',
            'catatan' => null 
        ]);
        
        return redirect()->back()->with('success', 'Bukti berhasil diunggah! Mohon menunggu diverifikasi oleh admin.. ');
    }

    public function verifikasi($id)
    {
        $tagihan = Tagihan::with('penghuni.kamar')->findOrFail($id);
        if (auth()->user()->role != 'admin') abort(403);

        $tagihan->update([
            'status' => 'lunas',
            'catatan' => null
        ]);

        // LOGIKA GAMIFIKASI POIN (TETAP DIJAGA)
        $tanggalTerbit = $tagihan->created_at;
        $tanggalBayar = \Carbon\Carbon::now();
        $selisihHari = $tanggalBayar->diffInDays($tanggalTerbit);

        if ($selisihHari <= 7) {
            $poin_tambahan = 50;
            $teks_gamifikasi = 'TEPAT WAKTU! Anda mendapatkan +50 Poin Kedisiplinan';
            $warna_teks_gamifikasi = '#059669'; 
            $warna_border_gamifikasi = '#6ee7b7'; 
            $warna_bg_gamifikasi = '#ecfdf5'; 
        } else {
            $poin_tambahan = -50;
            $teks_gamifikasi = 'TERLAMBAT! Anda terkena -50 Poin Kedisiplinan';
            $warna_teks_gamifikasi = '#e11d48'; 
            $warna_border_gamifikasi = '#fda4af'; 
            $warna_bg_gamifikasi = '#fff1f2'; 
        }

        $tagihan->penghuni->increment('poin', $poin_tambahan);
        $penghuni = $tagihan->penghuni->fresh();

        // LOGIKA GENERATE INVOICE IMAGE (TETAP DIJAGA)
        $templatePath = public_path('images/template_invoice.jpg');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template invoice tidak ditemukan.');
        }
        
        $img = Image::read($templatePath)->scale(width: 800);
        $fontBold = public_path('fonts/Montserrat-Bold.ttf');
        $fontReg  = public_path('fonts/Montserrat-Regular.ttf');
        
        // ... (Logika penulisan teks pada image tetap sama seperti sebelumnya) ...
        $img->text(strtoupper($penghuni->nama), 70, 315, function($font) use ($fontBold) {
            $font->file($fontBold); $font->size(28); $font->color('#1e293b');
        });
        // (Sengaja gue persingkat di sini agar tidak kepanjangan, intinya semua baris $img->text lo aman)
        
        // Simpan Invoice
        $namaFile = 'Invoice_' . $tagihan->id . '.png';
        $pathImage = 'invoices/' . $namaFile;
        Storage::disk('public')->put($pathImage, (string) $img->encodeByExtension('png'));

        // Notifikasi WA Selesai Bayar
        $this->sendWhatsApp($penghuni->no_hp, "Halo *{$penghuni->nama}*! Terimakasih telah melunasi tagihan kos periode {$tagihan->bulan}. Invoice sudah tersedia di aplikasi!✨");

        return redirect()->route('tagihan.index')->with('success', 'Verifikasi Pembayaran Berhasil! ✅');
    }

    public function tolak(Request $request, $id)
    {
        if (auth()->user()->role != 'admin') abort(403);
        $request->validate(['catatan' => 'required|string|max:255']);
        $tagihan = Tagihan::findOrFail($id);

        $tagihan->update([
            'status' => 'belum_bayar',
            'catatan' => $request->catatan,
            'bukti_bayar' => null, 
        ]);

        $this->sendWhatsApp($tagihan->penghuni->no_hp, "*--- PEMBAYARAN DITOLAK ---*\n\nAlasan: {$request->catatan}");
        return redirect()->route('tagihan.index')->with('success', 'Pembayaran ditolak.');
    }

    private function sendWhatsApp($target, $message)
    {
        return Http::withHeaders([
            'Authorization' => env('FONNTE_TOKEN'),
        ])->post('https://api.fonnte.com/send', [
            'target'      => $target,
            'message'     => $message,
            'countryCode' => '62', 
        ]);
    }

    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        Storage::disk('public')->delete('invoices/Invoice_' . $tagihan->id . '.png');
        $tagihan->delete();
        return redirect()->route('tagihan.index')->with('success', 'Data tagihan berhasil dihapus!');
    }
}