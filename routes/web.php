<?php

use App\Models\Kamar;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\PoinController;
use App\Http\Controllers\VoucherController;

/*
|--------------------------------------------------------------------------
| Web Routes - SISTEM MANAJEMEN KOS SUNDUWAN
|--------------------------------------------------------------------------
*/

// ==========================================================================
// 1. RUTE PUBLIK
// ==========================================================================

Route::get('/', function () {
    $kamarKosong = Kamar::where('status', 'kosong')
        ->orderBy('nomor_kamar', 'asc')
        ->get();

    return view('welcome', compact('kamarKosong'));
});

Route::get('/kamar-detail/{id}', function ($id) {
    $kamar = Kamar::findOrFail($id);

    return view('kamar_detail', compact('kamar'));
})->name('kamar.detail.public');


// ==========================================================================
// 2. WEBHOOK MIDTRANS (TANPA CSRF)
// ==========================================================================

Route::post('/midtrans-callback', [TagihanController::class, 'callback'])
    ->name('midtrans.callback');


// ==========================================================================
// 3. DASHBOARD
// ==========================================================================

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// ==========================================================================
// 4. ZONA USER LOGIN (PENGHUNI & ADMIN)
// ==========================================================================

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tagihan
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::post('/tagihan/{id}/bayar', [TagihanController::class, 'bayar'])->name('tagihan.bayar');
    Route::get('/tagihan/{id}/unduh', [TagihanController::class, 'unduhInvoice'])->name('tagihan.unduh');

    // Pengaduan
    Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/buat', [PengaduanController::class, 'create'])->name('pengaduan.create');
    Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
    Route::delete('/pengaduan/{pengaduan}', [PengaduanController::class, 'destroy'])->name('pengaduan.destroy');

    // Poin & Voucher
    Route::get('/voucher', [VoucherController::class, 'index'])->name('voucher.index');
    Route::post('/poin/tukar', [PoinController::class, 'tukarPoin'])->name('poin.tukar');
    Route::post('/voucher/{id}/gunakan', [PoinController::class, 'gunakanVoucher'])->name('voucher.gunakan');
});


// ==========================================================================
// 5. ZONA KHUSUS ADMIN
// ==========================================================================

Route::middleware([
    'auth',
    \App\Http\Middleware\CheckAdmin::class,
])->group(function () {

    // Kelola Data Kamar
    Route::resource('kamar', KamarController::class)->except(['show']);

    // Kelola Data Penghuni
    Route::resource('penghuni', PenghuniController::class)->except(['show']);
    Route::post('/penghuni/{id}/reset-password', [PenghuniController::class, 'resetPassword'])
        ->name('penghuni.reset_password');

    // Manajemen Tagihan Admin
    Route::post('/tagihan/generate', [TagihanController::class, 'generate'])
        ->name('tagihan.generate');

    Route::post('/tagihan/{id}/lunasi-manual', [TagihanController::class, 'lunasiManual'])
        ->name('tagihan.lunasi_manual');

    Route::delete('/tagihan/{id}', [TagihanController::class, 'destroy'])
        ->name('tagihan.destroy');

    // Respon Pengaduan Admin
    Route::patch('/pengaduan/{id}/respon', [PengaduanController::class, 'respon'])
        ->name('pengaduan.respon');
});


// ==========================================================================
// 6. AUTHENTICATION
// ==========================================================================

require __DIR__ . '/auth.php';