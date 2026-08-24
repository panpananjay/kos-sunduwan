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
    /**
     * Daftarkan konfigurasi dasar SDK Midtrans secara global
     */
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = false; // Set true jika sudah live production
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

    public function generate(Request $request)
    {
        Carbon::setLocale('id');
        // Ambil input bulan & tahun dari request, atau default ke bulan/tahun sekarang
        $bulan = $request->input('bulan', Carbon::now()->translatedFormat('F'));
        $tahun = $request->input('tahun', Carbon::now()->year);

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
            // 1. Cari apakah tagihan untuk penghuni di bulan & tahun ini sudah ada
            $tagihan = Tagihan::where('penghuni_id', $penghuni->id)
                                ->where('bulan', $bulan)
                                ->where('tahun', $tahun)
                                ->first();

            // Logika reset poin tahunan
            if ($penghuni->created_at) {
                $bulan_masuk = $daftarBulan[$penghuni->created_at->month]; 
                $tahun_masuk = $penghuni->created_at->year;
                if ($bulan_masuk == $bulan && $tahun > $tahun_masuk && !$tagihan) {
                    $penghuni->update(['poin' => 0]);
                }
            }

            // 2. Jika tagihan sudah ada dan statusnya sudah lunas / menunggu verifikasi, skip
            if ($tagihan && in_array($tagihan->status, ['lunas', 'menunggu_verifikasi'])) {
                continue; 
            }

            // 3. Jika tagihan belum ada sama sekali, buat baru di database
            if (!$tagihan) {
                $tagihan = Tagihan::create([
                    'penghuni_id'    => $penghuni->id,
                    'bulan'          => $bulan,
                    'tahun'          => $tahun,
                    'jumlah_tagihan' => $penghuni->kamar->harga ?? 0,
                    'status'         => 'belum_bayar',
                ]);
            }

            // 4. Kirim notifikasi WhatsApp
            $nominal = number_format($tagihan->jumlah_tagihan, 0, ',', '.');
            $pesan = "*--- NOTIFIKASI TAGIHAN KOS ---*\n\n" .
                    "Halo *{$penghuni->nama}* 👋\n" .
                    "Informasi tagihan periode *{$bulan} {$tahun}* sudah terbit.\n\n" .
                    "💰 Total: *Rp {$nominal}*\n" .
                    "📌 Status: *BELUM BAYAR*\n\n" .
                    "Silakan selesaikan pembayaran otomatis secara aman melalui aplikasi ya! ✨";

            if (!empty($penghuni->no_hp)) {
                $this->sendWhatsApp($penghuni->no_hp, $pesan);
                $jumlahTerkirim++;
            }
        }

        return redirect()->route('tagihan.index')->with('success', "Berhasil memproses dan mengirimkan {$jumlahTerkirim} notifikasi tagihan.");
    }

    public function show($id)
    {
        $tagihan = Tagihan::with('penghuni.kamar')->findOrFail($id);
        $user = auth()->user();
        if ($user->role == 'penghuni' && $tagihan->penghuni->user_id != $user->id) abort(403);
        return view('tagihan.show', compact('tagihan'));
    }

    /**
     * Mengambil Snap Token dari Midtrans untuk Pembayaran Otomatis
     */
    public function bayar($id)
    {
        $tagihan = Tagihan::with('penghuni.user')->findOrFail($id);

        $params = [
            'transaction_details' => [
                'order_id' => 'INV-' . $tagihan->id . '-' . time(), 
                'gross_amount' => (int) $tagihan->jumlah_tagihan,
            ],
            'customer_details' => [
                'first_name' => $tagihan->penghuni->nama ?? 'Penghuni',
                'email' => $tagihan->penghuni->user->email ?? 'penghuni@sunduwan.com',
                'phone' => $tagihan->penghuni->no_hp ?? '',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Webhook/callback penampung notifikasi otomatis dari server Midtrans
     */
    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed === $request->signature_key) {
            $orderParts = explode('-', $request->order_id);
            $tagihanId = isset($orderParts[1]) ? (int)$orderParts[1] : null;

            if ($tagihanId) {
                $tagihan = Tagihan::with('penghuni.kamar')->find($tagihanId);

                if ($tagihan && $tagihan->status != 'lunas') {
                    $transactionStatus = $request->transaction_status;
                    $paymentType = $request->payment_type;

                    if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                        $this->prosesPelunasanOtomatis($tagihan, $paymentType);
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Memproses database, mengkalkulasi poin, membuat berkas gambar invoice, 
     * dan memicu pengiriman pesan sukses WhatsApp Gateway.
     */
    private function prosesPelunasanOtomatis($tagihan, $paymentType = null)
    {
        // Penentuan Catatan Pembayaran
        $catatan = match($paymentType) {
            'cash_manual' => 'DIBAYAR VIA CASH MANUAL (ADMIN)',
            'manual'      => 'DIBAYAR VIA VERIFIKASI MANUAL (ADMIN)',
            default       => $paymentType ? 'Dibayar via ' . strtoupper(str_replace('_', ' ', $paymentType)) : null
        };

        $tagihan->update([
            'status' => 'lunas',
            'catatan' => $catatan
        ]);

        // LOGIKA GAMIFIKASI POIN (DEADLINE 7 HARI PENUH HINGGA PUKUL 23:59:59)
        $deadline = Carbon::parse($tagihan->created_at)->addDays(7)->endOfDay();
        $tanggalBayar = Carbon::now();

        if ($tanggalBayar->lte($deadline)) {
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

        // Pengaman relasi jika data penghuni kosong
        if ($tagihan->penghuni) {
            $tagihan->penghuni->increment('poin', $poin_tambahan);
            $penghuni = $tagihan->penghuni->fresh();
        } else {
            $penghuni = new \stdClass();
            $penghuni->nama = 'Pelanggan';
            $penghuni->no_hp = '';
            $penghuni->poin = 0;
        }

        // GENERATE FISIK INVOICE (.PNG)
        $templatePath = public_path('images/template_invoice.jpg');
        if (!file_exists($templatePath)) {
            return;
        }
        
        $img = Image::read($templatePath)->scale(width: 800);

        $fontBold = public_path('fonts/Montserrat-Bold.ttf');
        $fontReg  = public_path('fonts/Montserrat-Regular.ttf');
        
        $colorDark   = '#1e293b'; 
        $colorGray   = '#64748b'; 
        $colorAccent = '#c026d3'; 
        $colorGreen  = '#10b981'; 

        $img->text('DITAGIHKAN KEPADA:', 70, 280, function($font) use ($fontReg, $colorGray) {
            $font->file($fontReg); $font->size(14); $font->color($colorGray);
        });
        $img->text(strtoupper($penghuni->nama), 70, 315, function($font) use ($fontBold, $colorDark) {
            $font->file($fontBold); $font->size(28); $font->color($colorDark);
        });
        $img->text('Kamar No. ' . ($tagihan->penghuni->kamar?->nomor_kamar ?? '-'), 70, 350, function($font) use ($fontReg, $colorDark) {
            $font->file($fontReg); $font->size(18); $font->color($colorDark);
        });

        $img->text('NOMOR INVOICE', 730, 280, function($font) use ($fontReg, $colorGray) {
            $font->file($fontReg); $font->size(14); $font->color($colorGray); $font->align('right');
        });
        $img->text('INV-' . date('Ym') . $tagihan->id, 730, 310, function($font) use ($fontBold, $colorDark) {
            $font->file($fontBold); $font->size(20); $font->color($colorDark); $font->align('right');
        });

        $img->text('TANGGAL BAYAR', 730, 360, function($font) use ($fontReg, $colorGray) {
            $font->file($fontReg); $font->size(14); $font->color($colorGray); $font->align('right');
        });
        $img->text(now()->locale('id')->isoFormat('DD MMMM YYYY'), 730, 390, function($font) use ($fontBold, $colorDark) {
            $font->file($fontBold); $font->size(20); $font->color($colorDark); $font->align('right');
        });

        $img->text('STATUS', 730, 440, function($font) use ($fontReg, $colorGray) {
            $font->file($fontReg); $font->size(14); $font->color($colorGray); $font->align('right');
        });
        $img->text('LUNAS', 730, 475, function($font) use ($fontBold, $colorGreen) {
            $font->file($fontBold); $font->size(26); $font->color($colorGreen); $font->align('right');
        });

        $img->drawRectangle(70, 540, function($draw) { 
            $draw->size(660, 45); 
            $draw->background('#f8fafc'); 
        });
        
        $img->text('DESKRIPSI', 90, 568, function($font) use ($fontBold, $colorGray) { 
            $font->file($fontBold); $font->size(14); $font->color($colorGray); 
        });
        $img->text('PERIODE', 400, 568, function($font) use ($fontBold, $colorGray) { 
            $font->file($fontBold); $font->size(14); $font->color($colorGray); $font->align('center'); 
        });
        $img->text('JUMLAH', 710, 568, function($font) use ($fontBold, $colorGray) { 
            $font->file($fontBold); $font->size(14); $font->color($colorGray); $font->align('right'); 
        });

        $img->text('Sewa Kos Putri Sunduwan', 90, 630, function($font) use ($fontBold, $colorDark) { 
            $font->file($fontBold); $font->size(18); $font->color($colorDark); 
        });
        $img->text($tagihan->bulan . ' ' . $tagihan->tahun, 400, 630, function($font) use ($fontReg, $colorDark) { 
            $font->file($fontReg); $font->size(18); $font->color($colorDark); $font->align('center'); 
        });
        
        $hargaFormat = number_format($tagihan->jumlah_tagihan, 0, ',', '.');
        $img->text('Rp ' . $hargaFormat, 710, 630, function($font) use ($fontReg, $colorDark) { 
            $font->file($fontReg); $font->size(18); $font->color($colorDark); $font->align('right'); 
        });

        $img->drawRectangle(70, 680, function($draw) { 
            $draw->size(660, 2); 
            $draw->background('#f1f5f9'); 
        });

        $img->text('TOTAL PELUNASAN', 710, 740, function($font) use ($fontBold, $colorGray) { 
            $font->file($fontBold); $font->size(16); $font->color($colorGray); $font->align('right'); 
        });
        $img->text('Rp ' . $hargaFormat, 710, 790, function($font) use ($fontBold, $colorAccent) { 
            $font->file($fontBold); $font->size(45); $font->color($colorAccent); $font->align('right'); 
        });

        $img->drawRectangle(70, 870, function($draw) use ($warna_bg_gamifikasi, $warna_border_gamifikasi) { 
            $draw->size(660, 90); 
            $draw->background($warna_bg_gamifikasi); 
            $draw->border($warna_border_gamifikasi, 2); 
        });
        
        $img->text($teks_gamifikasi, 400, 905, function($font) use ($fontBold, $warna_teks_gamifikasi) { 
            $font->file($fontBold); $font->size(18); $font->color($warna_teks_gamifikasi); $font->align('center'); 
        });
        $img->text('Indeks Kedisiplinan Anda: ' . $penghuni->poin . ' Poin', 400, 935, function($font) use ($fontReg, $colorDark) { 
            $font->file($fontReg); $font->size(15); $font->color($colorDark); $font->align('center'); 
        });

        $img->text('Terima kasih atas pembayaran Anda. Dokumen ini sah dan diterbitkan', 400, 1020, function($font) use ($fontReg, $colorGray) { 
            $font->file($fontReg); $font->size(13); $font->color($colorGray); $font->align('center'); 
        });
        $img->text('secara otomatis oleh Sistem Manajemen Kos Putri Sunduwan.', 400, 1040, function($font) use ($fontReg, $colorGray) { 
            $font->file($fontReg); $font->size(13); $font->color($colorGray); $font->align('center'); 
        });

        $namaFile = 'Invoice_' . $tagihan->id . '.png';
        $pathImage = 'invoices/' . $namaFile;
        Storage::disk('public')->put($pathImage, (string) $img->encodeByExtension('png'));

        $linkInvoice = url('/tagihan/' . $tagihan->id . '/unduh');

        $pesanSukses = "*--- PEMBAYARAN KOS LUNAS ---*\n\n" .
                       "Halo *{$penghuni->nama}* 👋\n" .
                       "Terima kasih, pembayaran kos periode *{$tagihan->bulan} {$tagihan->tahun}* sebesar *Rp {$hargaFormat}* telah SUKSES diverifikasi.\n\n" .
                       "🏆 *GAMIFIKASI POIN:*\n" .
                       "{$teks_gamifikasi}.\n" .
                       "Total indeks kedisiplinan Anda saat ini: *{$penghuni->poin} Poin*.\n\n" .
                       "📄 *E-INVOICE RESMI:*\n" .
                       "Silakan unduh bukti pembayaran sah Anda di sini:\n" .
                       $linkInvoice . "\n\n" .
                       "Salam hangat, Kos Putri Sunduwan ✨";

        if (!empty($penghuni->no_hp)) {
            $this->sendWhatsApp($penghuni->no_hp, $pesanSukses);
        }
    }

    public function verifikasi($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        if ($tagihan->status != 'lunas') {
            $this->prosesPelunasanOtomatis($tagihan, 'manual');
            return redirect()->route('tagihan.index')->with('success', 'Selamat! Pembayaran berhasil diverifikasi manual dan invoice telah diterbitkan.');
        }
        return redirect()->route('tagihan.index');
    }

    public function lunasiManual($id)
    {
        $tagihan = Tagihan::with('penghuni.kamar')->findOrFail($id);

        if ($tagihan->status != 'lunas') {
            $this->prosesPelunasanOtomatis($tagihan, 'cash_manual');
            return redirect()->back()->with('success', 'Tagihan ' . ($tagihan->penghuni->nama ?? '') . ' berhasil dilunasi!');
        }

        return redirect()->back()->with('error', 'Tagihan ini sudah berstatus lunas.');
    }

    private function sendWhatsApp($target, $message)
    {
        $target = preg_replace('/[^0-9]/', '', $target);
        if (strpos($target, '0') === 0) {
            $target = '62' . substr($target, 1);
        }

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => env('FONNTE_TOKEN'),
                ])
                ->timeout(5)
                ->post('https://api.fonnte.com/send', [
                    'target'      => $target,
                    'message'     => $message,
                    'countryCode' => '62', 
                ]);

            return $response->successful();

        } catch (\Exception $e) {
            return false;
        }
    }

    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        Storage::disk('public')->delete('invoices/Invoice_' . $tagihan->id . '.png');
        $tagihan->delete();
        return redirect()->route('tagihan.index')->with('success', 'Data tagihan berhasil dihapus!');
    }

    public function unduhInvoice($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $filePath = 'invoices/Invoice_' . $tagihan->id . '.png';

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download(
                $filePath, 
                'Invoice_' . $tagihan->bulan . '_' . $tagihan->tahun . '.png'
            );
        }

        return redirect()->back()->with('error', 'Berkas fisik invoice gambar belum dibuat atau tidak ditemukan di folder server.');
    }
}