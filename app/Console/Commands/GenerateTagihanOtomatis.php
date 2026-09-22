<?php

namespace App\Console\Commands;

use App\Http\Controllers\TagihanController;
use App\Models\Penghuni;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateTagihanOtomatis extends Command
{
    protected $signature = 'tagihan:generate-otomatis';
    protected $description = 'Terbitkan tagihan otomatis untuk penghuni yang hari ini tanggal anniversary bergabungnya (kecuali bulan pertama, yang sudah dibuat saat pendaftaran)';

    public function handle(TagihanController $tagihanController)
    {
        Carbon::setLocale('id');
        $hariIni = Carbon::now();
        $daftarBulan = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];
        $bulanIni = $daftarBulan[$hariIni->month - 1];
        $tahunIni = $hariIni->year;

        $penghunis = Penghuni::whereNotNull('kamar_id')
            ->where('status', 'aktif')
            ->with('kamar')
            ->get();

        $jumlahTerbit = 0;

        foreach ($penghunis as $penghuni) {
            if (!$penghuni->created_at) continue;

            // Skip kalau ini bulan & tahun yang sama dengan bulan dia daftar
            // (tagihan pertama sudah dibuat otomatis di PenghuniController::store())
            if ($bulanIni === $daftarBulan[$penghuni->created_at->month - 1]
                && $tahunIni === $penghuni->created_at->year) {
                continue;
            }

            $tanggalMasuk = $penghuni->created_at->day;
            $anniversaryHariIni = min($tanggalMasuk, $hariIni->daysInMonth);

            if ($hariIni->day !== $anniversaryHariIni) continue;

            if ($tagihanController->terbitkanTagihanUntukPenghuni($penghuni, $bulanIni, $tahunIni)) {
                $jumlahTerbit++;
            }
        }

        $this->info("Selesai. {$jumlahTerbit} tagihan diterbitkan otomatis hari ini.");
    }
}