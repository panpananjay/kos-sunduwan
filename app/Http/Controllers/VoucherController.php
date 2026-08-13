<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Penghuni;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    /**
     * Menampilkan daftar voucher milik penghuni yang sedang login.
     * Logika penukaran poin -> voucher dan pemakaian voucher
     * ditangani oleh PoinController (tukarPoin & gunakanVoucher),
     * supaya tidak ada dua jalur logika berbeda untuk hal yang sama.
     */
    public function index()
    {
        $user = Auth::user();
        $penghuni = Penghuni::where('user_id', $user->id)->firstOrFail();

        $vouchers = Voucher::where('penghuni_id', $penghuni->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('penghuni.voucher.index', compact('penghuni', 'vouchers'));
    }
}