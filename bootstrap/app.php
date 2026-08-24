<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengaduan;
use App\Models\Tagihan;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Fitur PWA
        $middleware->web(append: [
            \App\Http\Middleware\PwaMiddleware::class,
        ]);

        // PERBAIKAN: Kecualikan rute Midtrans dari proteksi CSRF
        $middleware->validateCsrfTokens(except: [
            'midtrans/callback',
            'midtrans-callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->booting(function () {
        View::composer('*', function ($view) {
            try {
                if (Auth::check()) {
                    $user = Auth::user();
                    
                    if ($user->role == 'admin') {
                        // Notif untuk Admin: Laporan yang statusnya masih 'proses'
                        $jumlah = Pengaduan::where('status', 'proses')->count();
                        $view->with('jumlahPengaduan', $jumlah);
                    } else {
                        // Notif untuk Penghuni: 
                        // PERBAIKAN: Relasi tagihan diambil via relasi penghuni (karena tabel tagihans menggunakan penghuni_id)
                        $notifTagihan = Tagihan::whereHas('penghuni', function ($query) use ($user) {
                                            $query->where('user_id', $user->id);
                                        })
                                        ->where('status', 'belum_bayar')
                                        ->count();
                                              
                        // Balasan Pengaduan: Status 'diproses' tapi sudah ada jawaban admin
                        $notifPengaduanPenghuni = Pengaduan::where('user_id', $user->id)
                                                           ->where('status', 'diproses')
                                                           ->whereNotNull('tanggapan_admin')
                                                           ->count();
                        
                        $view->with('notifTagihan', $notifTagihan);
                        $view->with('notifPengaduanPenghuni', $notifPengaduanPenghuni);
                    }
                }
            } catch (\Exception $e) {
                // Beri nilai default 0 jika database belum siap/error
                $view->with('jumlahPengaduan', 0);
                $view->with('notifTagihan', 0);
                $view->with('notifPengaduanPenghuni', 0);
            }
        });
    })
    ->create();