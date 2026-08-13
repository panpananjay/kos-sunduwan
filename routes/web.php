<?php

use App\Models\Kamar;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengaduanController;

/*
|--------------------------------------------------------------------------
| Web Routes - SISTEM MANAJEMEN KOS SUNDUWAN
|--------------------------------------------------------------------------
*/

// --- 1. RUTE PUBLIK (Bisa diakses tanpa login) ---
Route::get('/', function () {
    $kamarKosong = Kamar::where('status', 'kosong')->get();
    return view('welcome', compact('kamarKosong'));
});

Route::get('/kamar-detail/{id}', function ($id) {
    $kamar = Kamar::findOrFail($id);
    return view('kamar_detail', compact('kamar'));
})->name('kamar.detail.public');


// --- 2. WEBHOOK MIDTRANS (PENTING: Di luar middleware Auth) ---
// Server Midtrans akan mengirim laporan pembayaran otomatis ke sini
Route::post('/midtrans-callback', [TagihanController::class, 'callback'])->name('midtrans.callback');


// --- 3. RUTE DASHBOARD (Wajib Login) ---
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// --- 4. ZONA USER LOGIN (Admin & Penghuni) ---
Route::middleware('auth')->group(function () {
    
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fitur Tagihan & Midtrans (SUDAH SIMPEL TANPA DETAIL/SHOW)
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::post('/tagihan/{id}/bayar', [TagihanController::class, 'bayar'])->name('tagihan.bayar'); 

    // Fitur Pengaduan
    Route::get('/pengaduan', [PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/buat', [PengaduanController::class, 'create'])->name('pengaduan.create');
    Route::post('/pengaduan', [PengaduanController::class, 'store'])->name('pengaduan.store');
    Route::delete('/pengaduan/{pengaduan}', [PengaduanController::class, 'destroy'])->name('pengaduan.destroy');
});


// --- 5. ZONA KHUSUS ADMIN (Middleware CheckAdmin) ---
Route::middleware(['auth', \App\Http\Middleware\CheckAdmin::class])->group(function () {
    
    // Kelola Data Master Kamar
    Route::resource('kamar', KamarController::class)->except(['show']);

    // Kelola Data Penghuni
    Route::resource('penghuni', PenghuniController::class)->except(['show']);
    Route::post('/penghuni/{id}/reset-password', [PenghuniController::class, 'resetPassword'])->name('penghuni.reset_password');

    // Fitur Manajemen Tagihan Admin
    Route::post('/tagihan/generate', [TagihanController::class, 'generate'])->name('tagihan.generate');
    Route::patch('/tagihan/{id}/verifikasi', [TagihanController::class, 'verifikasi'])->name('tagihan.verifikasi');
    Route::post('/tagihan/{id}/tolak', [TagihanController::class, 'tolak'])->name('tagihan.tolak');
    Route::delete('/tagihan/{id}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');

    // Respon Pengaduan oleh Admin
    Route::patch('/pengaduan/{id}/respon', [PengaduanController::class, 'respon'])->name('pengaduan.respon');
});

require __DIR__.'/auth.php';