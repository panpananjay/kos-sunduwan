<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kamar;
use App\Models\Tagihan;
use App\Models\Penghuni;
use App\Models\Pengaduan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $daftarBulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        Carbon::setLocale('id');

        $bulanIni = $request->input(
            'bulan',
            Carbon::now()->translatedFormat('F')
        );

        $tahunIni = $request->input('tahun', date('Y'));

        // ==========================================
        // LOGIKA UNTUK ADMIN
        // ==========================================

        if ($user->role == 'admin') {

            $totalKamar = Kamar::count();

            $kamarTerisi = Kamar::where('status', 'terisi')->count();

            $kamarKosong = Kamar::where('status', 'kosong')->count();

           $queryTagihan = Tagihan::where('tahun', $tahunIni)
                ->where('status', '!=', 'dibatalkan');

            // Jika filter bukan "Semua", filter berdasarkan bulan
            if ($bulanIni !== 'Semua') {
                $queryTagihan->where('bulan', $bulanIni);
            }

            $totalTagihanBulanIni = (clone $queryTagihan)
                ->sum('jumlah_tagihan');

            $uangMasuk = (clone $queryTagihan)
                ->where('status', 'lunas')
                ->sum('jumlah_tagihan');

            $piutang = (clone $queryTagihan)
                ->where('status', 'belum_bayar')
                ->sum('jumlah_tagihan');

            // Total estimasi pendapatan dari kamar yang sedang terisi
            $estimasiPendapatanPerBulan = Kamar::where(
                'status',
                'terisi'
            )->sum('harga');

            // ==========================================
            // FORECASTING 3 BULAN
            // ==========================================

            $forecasting = [];

            for ($i = 1; $i <= 3; $i++) {

                $date = Carbon::now()->addMonths($i);

                $forecasting[] = [
                    'bulan' => $date->translatedFormat('F'),
                    'estimasi' => $estimasiPendapatanPerBulan
                ];
            }

            return view('dashboard', compact(
                'bulanIni',
                'totalKamar',
                'kamarTerisi',
                'kamarKosong',
                'totalTagihanBulanIni',
                'uangMasuk',
                'piutang',
                'estimasiPendapatanPerBulan',
                'forecasting'
            ));
        }

        // ==========================================
        // LOGIKA UNTUK PENGHUNI KOS
        // ==========================================

        else {

            /*
            |--------------------------------------------------------------------------
            | Ambil data penghuni berdasarkan user_id
            |--------------------------------------------------------------------------
            |
            | Nama penghuni yang digunakan di dashboard berasal dari:
            |
            | $penghuniKu->nama
            |
            | yaitu:
            |
            | penghunis.nama
            |
            | BUKAN:
            |
            | users.name
            |
            */

            $penghuniKu = Penghuni::with('kamar')
                ->where('user_id', $user->id)
                ->first();

            $tagihanBulanIni = null;

            $pengaduanTerakhir = null;

            $totalLunas = 0;

            $level = 'Penghuni Baru 🌱';

            $tanggalMasuk = null;

            if ($penghuniKu) {

                // ==========================================
                // CARI TAGIHAN BULAN INI
                // ==========================================

                $tagihanBulanIni = Tagihan::where(
                    'penghuni_id',
                    $penghuniKu->id
                )
                    ->where('bulan', $bulanIni)
                    ->where('tahun', $tahunIni)
                    ->first();

                // ==========================================
                // CARI PENGADUAN TERAKHIR
                // ==========================================

                $pengaduanTerakhir = Pengaduan::where(
                    'user_id',
                    $user->id
                )
                    ->where('status', '!=', 'Selesai')
                    ->latest()
                    ->first();

                // ==========================================
                // HITUNG TOTAL PEMBAYARAN LUNAS
                // ==========================================

                $totalLunas = Tagihan::where(
                    'penghuni_id',
                    $penghuniKu->id
                )
                    ->where('status', 'lunas')
                    ->count();

                // ==========================================
                // CARI TANGGAL MASUK
                // Dari tagihan LUNAS pertama
                // ==========================================

                $daftarBulanUrut = [
                    'Januari' => 1,
                    'Februari' => 2,
                    'Maret' => 3,
                    'April' => 4,
                    'Mei' => 5,
                    'Juni' => 6,
                    'Juli' => 7,
                    'Agustus' => 8,
                    'September' => 9,
                    'Oktober' => 10,
                    'November' => 11,
                    'Desember' => 12,
                ];

                $tagihanPertamaLunas = Tagihan::where(
                    'penghuni_id',
                    $penghuniKu->id
                )
                    ->where('status', 'lunas')
                    ->get()
                    ->sortBy(function ($tagihan) use ($daftarBulanUrut) {

                        return $tagihan->tahun .
                            str_pad(
                                $daftarBulanUrut[$tagihan->bulan] ?? 0,
                                2,
                                '0',
                                STR_PAD_LEFT
                            );
                    })
                    ->first();

                $tanggalMasuk = $tagihanPertamaLunas
                    ? $tagihanPertamaLunas->bulan . ' ' .
                      $tagihanPertamaLunas->tahun
                    : null;

                // ==========================================
                // LOGIKA INDEKS KEDISIPLINAN / GAMIFIKASI
                // ==========================================

                $poin = $penghuniKu->poin;

                if ($poin < 0) {

                    $level = 'Beresiko 🚨';

                } elseif ($poin < 150) {

                    $level = 'Penghuni Baru 🌱';

                } elseif ($poin < 350) {

                    $level = 'Penghuni Aktif ✨';

                } elseif ($poin < 600) {

                    $level = 'Penghuni Konsisten 🌟';

                } else {

                    $level = 'Penghuni Teladan 🏆';
                }
            }

            // ==========================================
            // KIRIM DATA KE DASHBOARD PENGHUNI
            // ==========================================

            return view('dashboard', compact(
                'penghuniKu',
                'tagihanBulanIni',
                'pengaduanTerakhir',
                'totalLunas',
                'level',
                'bulanIni',
                'tahunIni',
                'tanggalMasuk'
            ));
        }
    }
}