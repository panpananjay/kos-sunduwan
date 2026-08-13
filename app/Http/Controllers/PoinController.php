<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penghuni;
use App\Models\Voucher;
use App\Models\Tagihan;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PoinController extends Controller
{
    // Logika menukar 550 poin menjadi voucher fisik (Masuk Inventory)
    public function tukarPoin(Request $request)
    {
        $user = auth()->user();
        
        // Cari biodata penghuni berdasarkan user yang login
        $penghuniKu = Penghuni::where('user_id', $user->id)->firstOrFail();

        // Cek jumlah voucher aktif yang dimiliki
        $jumlahVoucherAktif = Voucher::where('penghuni_id', $penghuniKu->id)
            ->where('status', 'aktif')
            ->count();

        if ($jumlahVoucherAktif >= 2) {
            return redirect()->back()->with('error', 'Gagal! Anda sudah memiliki maksimal 2 voucher aktif.');
        }

        // Validasi ketat jika poin kurang dari 550
        if ($penghuniKu->poin < 550) {
            return redirect()->back()->with('error', 'Gagal! Poin Anda tidak mencukupi untuk klaim voucher.');
        }

        // Dibungkus Transaction: Harus sukses dua-duanya, atau gagal sekalian
        return DB::transaction(function () use ($penghuniKu) {
            
            // 🆕 PERBAIKAN: Menggunakan properti Eloquent agar tetap memicu Mutator min(600, max(0, $value))
            $penghuniKu->poin -= 550;
            $penghuniKu->save();

            // 2. Buat data voucher diskon baru aktif 1 tahun kedepan (tagihan_id otomatis NULL)
            Voucher::create([
                'penghuni_id'  => $penghuniKu->id,
                'kode_voucher' => 'DSK50K-' . strtoupper(Str::random(6)),
                'nominal'      => 50000,
                'status'       => 'aktif',
                'masa_berlaku' => Carbon::now()->addYear(),
            ]);

            return redirect()->back()->with('success', '🎉 Selamat! Voucher diskon sewa Rp 50.000 berhasil diklaim. Silakan cek inventory Anda!');
        });
    }

    // Logika ketika anak kos klik tombol "Gunakan" pada voucher di inventory
    public function gunakanVoucher(Request $request, $id)
    {
        $user = auth()->user();
        $penghuniKu = Penghuni::where('user_id', $user->id)->firstOrFail();
        
        // Ambal data voucher yang dipilih
        $voucher = Voucher::where('id', $id)
            ->where('penghuni_id', $penghuniKu->id)
            ->where('status', 'aktif')
            ->where('masa_berlaku', '>=', Carbon::now())
            ->firstOrFail();

        // Cari apakah ada tagihan bulan berjalan yang berstatus "belum_bayar"
        $tagihanAktif = Tagihan::where('penghuni_id', $penghuniKu->id)
            ->where('status', 'belum_bayar')
            ->first();

        // Jika tidak ada tagihan yang belum dibayar, batalkan proses
        if (!$tagihanAktif) {
            return redirect()->back()->with('error', 'Gagal memakai voucher! Anda tidak memiliki tagihan aktif/belum lunas saat ini.');
        }

        // Dibungkus Transaction: Biar aman kalau proses potong tagihan & kunci voucher tabrakan
        return DB::transaction(function () use ($tagihanAktif, $voucher) {
            // 1. Potong nilai tagihan berjalan sebesar Rp 50.000 (tidak boleh minus dari 0)
            $tagihanAktif->jumlah_tagihan = max(0, $tagihanAktif->jumlah_tagihan - $voucher->nominal);
            $tagihanAktif->save();

            // 2. Kunci voucher agar statusnya berubah jadi 'terpakai' dan ikat ke tagihan_id
            $voucher->update([
                'status' => 'terpakai',
                'tagihan_id' => $tagihanAktif->id
            ]);

            return redirect()->back()->with('success', '👍 Voucher berhasil diterapkan! Tagihan Anda bulan ini dipotong sebesar Rp 50.000.');
        });
    }
}