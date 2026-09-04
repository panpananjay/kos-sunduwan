<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kamar;
use App\Models\Tagihan;
use App\Models\Penghuni;
use App\Models\Pengaduan;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Jumlah periode (bulan) yang digunakan sebagai basis
     * perhitungan Single Moving Average.
     */
    private const PERIODE_SMA = 3;

    /**
     * Mapping nama bulan (Indonesia) ke nomor urut bulan.
     * Dipakai untuk mengurutkan data historis tagihan secara kronologis.
     */
    private array $daftarBulanUrut = [
        'Januari'   => 1,
        'Februari'  => 2,
        'Maret'     => 3,
        'April'     => 4,
        'Mei'       => 5,
        'Juni'      => 6,
        'Juli'      => 7,
        'Agustus'   => 8,
        'September' => 9,
        'Oktober'   => 10,
        'November'  => 11,
        'Desember'  => 12,
    ];

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
            // (dipakai sebagai FALLBACK saja jika riwayat tagihan lunas
            // belum cukup untuk menghitung Single Moving Average)
            $estimasiPendapatanPerBulan = Kamar::where(
                'status',
                'terisi'
            )->sum('harga');

            // ==========================================
            // FORECASTING 3 BULAN — SINGLE MOVING AVERAGE
            // ==========================================

            $forecasting = $this->hitungForecastingSma(
                $bulanIni,
                $tahunIni,
                $estimasiPendapatanPerBulan
            );

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
            | Nama penghuni yang digunakan di dashboard berasal dari:
            | $penghuniKu->nama (penghunis.nama) BUKAN users.name
            */

            $penghuniKu = Penghuni::with('kamar')
                ->where('user_id', $user->id)
                ->first();

            $tagihanBulanIni = null;

            $pengaduanTerakhir = null;

            $totalLunas = 0;

            $level = 'Penghuni Baru 🌱';

            $tanggalMasuk = null;

            // Info keterlambatan (diisi di bawah jika penghuni ada)
            $labelTerlambatBulanIni = null;
            $tagihanTerlambat       = null;
            $labelTerlambatLama     = null;

            // Info akumulasi tunggakan (hanya yang SUDAH lewat deadline —
            // diisi setelah blok pengecekan keterlambatan di bawah)
            $totalTunggakan    = 0;
            $tagihanBelumLunas = collect();

            // Data voucher penghuni
            $vouchers = collect();

            if ($penghuniKu) {

                // ==========================================
                // AMBIL SEMUA TAGIHAN BELUM_BAYAR
                // ==========================================
                //
                // Catatan: koleksi ini masih berisi SEMUA tagihan
                // berstatus belum_bayar, termasuk yang masih dalam
                // masa tenggang 7 hari (belum telat). Jangan langsung
                // di-count() di sini — filter deadline dilakukan
                // di bawah, setelah blok pengecekan keterlambatan.
                $tagihanBelumLunas = Tagihan::where('penghuni_id', $penghuniKu->id)
                    ->where('status', 'belum_bayar')
                    ->orderBy('created_at', 'asc')
                    ->get();

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
                // CEK KETERLAMBATAN TAGIHAN
                // ==========================================
                //
                // Prioritas notifikasi:
                // 1. Tagihan BULAN INI (baik masih dalam masa tenggang
                //    maupun sudah lewat deadline) SELALU diprioritaskan
                //    untuk ditampilkan sebagai banner utama di dashboard.
                // 2. Tagihan LAMA yang masih menunggak & sudah lewat
                //    deadline hanya ditampilkan sebagai notifikasi
                //    kalau TIDAK ADA tagihan bulan ini yang perlu
                //    ditampilkan.

                // 1. Apakah tagihan BULAN INI sendiri sudah lewat deadline?
                if ($tagihanBulanIni && $tagihanBulanIni->status != 'lunas') {
                    $deadlineBulanIni = Carbon::parse($tagihanBulanIni->created_at)
                        ->addDays(7)->endOfDay();

                    if (Carbon::now()->gt($deadlineBulanIni)) {
                        $labelTerlambatBulanIni = $this->formatKeterlambatan($deadlineBulanIni);
                    }
                }

                // 2. Cari tagihan LAMA (selain bulan ini) yang masih
                //    belum_bayar dan sudah lewat deadline — ambil yang
                //    paling lama menunggak (created_at paling awal).
                $kandidatTerlambat = $tagihanBelumLunas->reject(function ($t) use ($tagihanBulanIni) {
                    return $tagihanBulanIni && $t->id === $tagihanBulanIni->id;
                });

                foreach ($kandidatTerlambat as $t) {
                    $deadlineT = Carbon::parse($t->created_at)->addDays(7)->endOfDay();

                    if (Carbon::now()->gt($deadlineT)) {
                        if (!$tagihanTerlambat || $t->created_at->lt($tagihanTerlambat->created_at)) {
                            $tagihanTerlambat   = $t;
                            $labelTerlambatLama = $this->formatKeterlambatan($deadlineT);
                        }
                    }
                }

                // ==========================================
                // HITUNG TOTAL TUNGGAKAN (hanya yang sudah lewat deadline)
                // ==========================================
                //
                // Tagihan berstatus belum_bayar yang MASIH dalam masa
                // tenggang 7 hari TIDAK dihitung sebagai tunggakan —
                // itu wajar/belum telat. Yang dihitung hanya tagihan
                // yang deadline-nya (created_at + 7 hari) sudah lewat,
                // baik itu tagihan bulan ini maupun tagihan lama.
                $totalTunggakan = $tagihanBelumLunas->filter(function ($t) {
                    $deadlineT = Carbon::parse($t->created_at)->addDays(7)->endOfDay();
                    return Carbon::now()->gt($deadlineT);
                })->count();

                // ==========================================
                // LOGIKA INVENTORY VOUCHER PENGHUNI
                // ==========================================

                // Bulk update voucher yang sudah lewat masa berlaku
                // sekaligus lewat query, sebelum datanya diambil —
                // menghindari N query update satu-satu di blade.
                Voucher::where('penghuni_id', $penghuniKu->id)
                    ->where('status', 'aktif')
                    ->whereNotNull('masa_berlaku')
                    ->where('masa_berlaku', '<', Carbon::now())
                    ->update(['status' => 'expired']);

                $vouchers = Voucher::where('penghuni_id', $penghuniKu->id)
                    ->where('status', '!=', 'terpakai')
                    ->orderByRaw("FIELD(status, 'aktif', 'expired')")
                    ->latest()
                    ->get();

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
                // Dari tagihan LUNAS pertama, diurutkan langsung
                // lewat SQL (created_at) supaya tidak menarik
                // seluruh data ke memori PHP hanya untuk sorting.
                // ==========================================

                $tagihanPertamaLunas = Tagihan::where(
                    'penghuni_id',
                    $penghuniKu->id
                )
                    ->where('status', 'lunas')
                    ->orderBy('created_at', 'asc')
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
                'tanggalMasuk',
                'labelTerlambatBulanIni',
                'tagihanTerlambat',
                'labelTerlambatLama',
                'totalTunggakan',
                'tagihanBelumLunas',
                'vouchers'
            ));
        }
    }

    /**
     * Mengubah selisih waktu sejak deadline terlampaui menjadi label
     * yang mudah dibaca: hari, minggu, atau bulan.
     *
     * Ambang batas:
     * - < 7 hari   -> ditampilkan dalam satuan hari
     * - < 30 hari  -> ditampilkan dalam satuan minggu (dibulatkan ke bawah)
     * - >= 30 hari -> ditampilkan dalam satuan bulan (dibulatkan ke bawah)
     *
     * Dipakai baik untuk notifikasi dashboard penghuni maupun badge
     * "Terlambat" pada menu Tagihan, supaya perhitungannya konsisten
     * di seluruh sistem.
     *
     * @param  Carbon  $deadline  Batas waktu (created_at + 7 hari, endOfDay)
     * @return string
     */
    private function formatKeterlambatan(Carbon $deadline): string
    {
        $hari = (int) floor($deadline->diffInDays(Carbon::now()));

        if ($hari < 7) {
            return $hari . ' hari';
        }

        if ($hari < 30) {
            return intdiv($hari, 7) . ' minggu';
        }

        return intdiv($hari, 30) . ' bulan';
    }

    /**
     * Menghitung proyeksi pendapatan 3 bulan ke depan menggunakan
     * metode Single Moving Average (SMA) dengan periode
     * self::PERIODE_SMA (default 3 bulan).
     *
     * Logika:
     * 1. Ambil riwayat pendapatan riil (total tagihan LUNAS per bulan)
     *    dari seluruh histori transaksi, sampai bulan/tahun yang
     *    sedang difilter di dashboard.
     * 2. Forecast bulan ke-(t+1) = rata-rata dari PERIODE_SMA bulan
     *    terakhir pada deret tersebut.
     * 3. Untuk forecast bulan ke-(t+2) dan (t+3), hasil forecast
     *    sebelumnya ikut dimasukkan ke deret sebagai "data" sebelum
     *    rata-rata dihitung ulang — pendekatan standar SMA untuk
     *    proyeksi multi-periode ke depan.
     * 4. Jika riwayat data historis belum ada sama sekali, forecast
     *    jatuh kembali (fallback) ke estimasi berbasis kamar terisi
     *    saat ini, supaya dashboard tidak menampilkan angka nol.
     * 5. Titik awal penambahan bulan (addMonths) mengikuti bulan/tahun
     *    yang SEDANG DIFILTER di dashboard ($bulanIni/$tahunIni), bukan
     *    selalu tanggal hari ini — supaya forecasting tetap akurat saat
     *    admin melihat data periode lampau.
     *
     * @param  string  $bulanIni
     * @param  int|string  $tahunIni
     * @param  int|float  $estimasiFallback  Estimasi kamar terisi × harga, dipakai sebagai fallback
     * @return array<int, array{bulan: string, estimasi: float}>
     */
    private function hitungForecastingSma(
        string $bulanIni,
        int|string $tahunIni,
        int|float $estimasiFallback
    ): array {

        // Urutan kronologis (tahun*12 + bulan) dari titik "sekarang"
        // yang sedang difilter di dashboard, dipakai sebagai batas
        // agar hanya riwayat sampai bulan tersebut yang dipakai.
        $nomorBulanSekarang = $this->daftarBulanUrut[$bulanIni]
            ?? Carbon::now()->month;

        $urutanSekarang = ((int) $tahunIni * 12) + $nomorBulanSekarang;

        // Ambil total pendapatan LUNAS per (bulan, tahun) dari seluruh
        // riwayat transaksi, diurutkan kronologis.
        $riwayatPendapatan = Tagihan::where('status', 'lunas')
            ->selectRaw('bulan, tahun, SUM(jumlah_tagihan) as total')
            ->groupBy('bulan', 'tahun')
            ->get()
            ->map(function ($item) {
                $item->urutan = ((int) $item->tahun * 12) +
                    ($this->daftarBulanUrut[$item->bulan] ?? 0);
                return $item;
            })
            ->filter(fn ($item) => $item->urutan <= $urutanSekarang)
            ->sortBy('urutan')
            ->values();

        // Deret nilai pendapatan aktual (angka saja, urut kronologis)
        $deretPendapatan = $riwayatPendapatan->pluck('total')
            ->map(fn ($nilai) => (float) $nilai)
            ->toArray();

        $forecasting = [];

        for ($i = 1; $i <= 3; $i++) {

            // Titik awal penambahan bulan mengikuti bulan/tahun yang
            // SEDANG DIFILTER ($tahunIni, $nomorBulanSekarang), bukan
            // Carbon::now() — supaya forecasting tetap benar walau
            // admin sedang melihat data periode lampau.
            $date = Carbon::createFromDate((int) $tahunIni, $nomorBulanSekarang, 1)
                ->addMonths($i);

            // Ambil PERIODE_SMA data terakhir dari deret
            // (data aktual, atau campuran aktual + hasil forecast
            // sebelumnya untuk proyeksi bulan ke-2 dan ke-3)
            $dataUntukRataRata = array_slice(
                $deretPendapatan,
                -self::PERIODE_SMA
            );

            if (count($dataUntukRataRata) > 0) {
                // Rumus Single Moving Average:
                // SMA = (X1 + X2 + ... + Xn) / n
                $estimasi = array_sum($dataUntukRataRata)
                    / count($dataUntukRataRata);
            } else {
                // Fallback: belum ada riwayat tagihan lunas sama sekali
                $estimasi = $estimasiFallback;
            }

            $forecasting[] = [
                'bulan'    => $date->translatedFormat('F'),
                'estimasi' => round($estimasi),
            ];

            // Masukkan hasil forecast ke deret, agar bulan berikutnya
            // tetap menghitung rata-rata bergerak dari PERIODE_SMA
            // data terakhir (standar proyeksi SMA multi-periode).
            $deretPendapatan[] = $estimasi;
        }

        return $forecasting;
    }
}