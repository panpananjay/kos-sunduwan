<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Penghuni;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VoucherController extends Controller
{
    /**
     * Menampilkan daftar voucher milik penghuni yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();
        $penghuni = Penghuni::where('user_id', $user->id)->firstOrFail();

        // Ambil voucher milik penghuni
        $vouchers = Voucher::where('penghuni_id', $penghuni->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('penghuni.voucher.index', compact('penghuni', 'vouchers'));
    }

    /**
     * Menukarkan poin penghuni menjadi Voucher Diskon.
     * Syarat: Minimal 550 Poin.
     */
    public function redeem(Request $request)
    {
        $user = Auth::user();
        $penghuni = Penghuni::where('user_id', $user->id)->firstOrFail();

        // Ambang batas poin untuk penukaran
        $minPoin = 550;
        $nominalDiskon = 50000; // Nominal potongan harga (misal Rp 50.000)

        // Validasi kecukupan poin
        if ($penghuni->poin < $minPoin) {
            return redirect()->back()->with('error', "Poin Anda tidak mencukupi. Minimal $minPoin poin untuk menukar voucher.");
        }

        DB::beginTransaction();
        try {
            // 1. Potong poin penghuni
            $penghuni->poin -= $minPoin;
            $penghuni->save();

            // 2. Generate Kode Voucher Unik (Contoh: VCHR-SUNDUWAN-ABC12)
            $kodeVoucher = 'VCHR-' . strtoupper(Str::random(8));

            // 3. Simpan data Voucher baru
            Voucher::create([
                'penghuni_id'  => $penghuni->id,
                'tagihan_id'   => null, // Belum dipasang ke tagihan manapun
                'kode_voucher' => $kodeVoucher,
                'nominal'      => $nominalDiskon,
                'status'       => 'aktif', // enum: 'aktif', 'terpakai', 'hangus'
                'masa_berlaku' => Carbon::now()->addDays(30)->toDateString(), // Berlaku 30 hari
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Berhasil menukarkan 550 poin menjadi Voucher Diskon Rp ' . number_format($nominalDiskon, 0, ',', '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menukarkan voucher: ' . $e->getMessage());
        }
    }

    /**
     * Memasang voucher ke tagihan sewa sebelum pembayaran (Midtrans).
     */
    public function applyToTagihan(Request $request)
    {
        $request->validate([
            'tagihan_id'   => 'required|exists:tagihans,id',
            'kode_voucher' => 'required|string',
        ]);

        $user = Auth::user();
        $penghuni = Penghuni::where('user_id', $user->id)->firstOrFail();

        // Cari voucher berdasarkan kode dan pemilik
        $voucher = Voucher::where('kode_voucher', $request->kode_voucher)
            ->where('penghuni_id', $penghuni->id)
            ->first();

        // Validasi keberadaan & status voucher
        if (!$voucher) {
            return redirect()->back()->with('error', 'Kode voucher tidak ditemukan.');
        }

        if ($voucher->status !== 'aktif') {
            return redirect()->back()->with('error', 'Voucher sudah tidak aktif atau telah digunakan.');
        }

        if (Carbon::now()->gt(Carbon::parse($voucher->masa_berlaku))) {
            $voucher->update(['status' => 'hangus']);
            return redirect()->back()->with('error', 'Voucher telah kedaluwarsa.');
        }

        // Cari tagihan yang dituju
        $tagihan = Tagihan::where('id', $request->tagihan_id)
            ->where('penghuni_id', $penghuni->id)
            ->where('status', 'belum_bayar')
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Hitung potongan nominal tagihan
            $tagihanBaru = max(0, $tagihan->jumlah_tagihan - $voucher->nominal);

            // Update status voucher & relasi ke tagihan
            $voucher->update([
                'tagihan_id' => $tagihan->id,
                'status'     => 'terpakai',
            ]);

            // Update total tagihan yang harus dibayar
            $tagihan->update([
                'jumlah_tagihan' => $tagihanBaru,
                'catatan'        => ($tagihan->catatan ? $tagihan->catatan . ' | ' : '') . "Dipotong Voucher {$voucher->kode_voucher} (Rp " . number_format($voucher->nominal, 0, ',', '.') . ")"
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Voucher berhasil dipasang! Total tagihan Anda dipotong.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memasang voucher: ' . $e->getMessage());
        }
    }
}