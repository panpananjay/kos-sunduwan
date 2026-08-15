<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Kos Putri Sunduwan</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <meta name="theme-color" content="#f43f5e">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sunduwan-pwa.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-sunduwan-pwa.png') }}">
    
    <link rel="manifest" href="/build/manifest.webmanifest">

    <style>
        body { font-family: 'Montserrat', sans-serif !important; }
        
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 8s infinite alternate ease-in-out; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }

        /* TRIK SAKTI: Matiin mata bawaan browser biar gak double */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-rose-500 selection:text-white min-h-screen flex items-center justify-center relative overflow-hidden py-10">

    <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-rose-200 opacity-60 blur-3xl animate-blob pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 rounded-full bg-orange-100 opacity-60 blur-3xl animate-blob animation-delay-2000 pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-fuchsia-100 opacity-40 blur-3xl animate-blob animation-delay-4000 pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md px-6 sm:px-10 py-10 bg-white/90 backdrop-blur-xl shadow-2xl rounded-[2.5rem] border border-white/50 mx-4">
        
        <div class="text-center mb-8">
            <div class="flex justify-center mb-5">
                <img src="{{ asset('images/logo-sunduwan.png') }}" class="h-24 sm:h-28 w-auto object-contain drop-shadow-sm" alt="Logo Sunduwan">
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Selamat Datang 👋</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Silakan masuk ke akun Anda.</p>
        </div>

        <x-auth-session-status class="mb-4 text-center font-bold text-emerald-600 bg-emerald-50 p-3 rounded-xl" :status="session('status')" />

        <form id="loginForm" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-5">
                <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-slate-400">👤</span>
                    </div>
                    <input id="username" 
                           type="text" 
                           name="username" 
                           value="{{ old('username') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           placeholder="Masukkan username" 
                           class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 text-slate-900 font-medium rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-transparent transition duration-300 outline-none shadow-sm">
                </div>
                <x-input-error :messages="$errors->get('username')" class="mt-2 text-xs text-rose-500 font-bold" />
            </div>

            <div class="mb-6" x-data="{ showPassword: false }">
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-slate-400">🔒</span>
                    </div>
                    
                    <input id="password" 
                        :type="showPassword ? 'text' : 'password'" 
                        name="password" 
                        required 
                        autocomplete="current-password" 
                        placeholder="••••••••" 
                        class="w-full pl-11 pr-12 py-3.5 bg-slate-50 border border-slate-200 text-slate-900 font-bold tracking-widest rounded-xl focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-transparent transition duration-300 outline-none shadow-sm">
                    
                    <button type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center group">
                        
                        <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-hover:text-rose-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>

                        <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-hover:text-rose-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-rose-500 font-bold" />
            </div>

            <div class="flex items-center justify-between mb-8">
                <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-rose-500 shadow-sm focus:ring-rose-500 w-4 h-4 cursor-pointer transition">
                    <span class="ms-2 text-sm font-medium text-slate-600 group-hover:text-slate-900 transition">Ingat saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="javascript:void(0)" 
                    onclick="reportForgotPassword()"
                    class="text-sm font-bold text-rose-500 hover:text-rose-600 hover:underline transition duration-300">
                        Lupa sandi?
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full inline-flex justify-center items-center px-8 py-3.5 bg-gradient-to-r from-rose-500 to-fuchsia-600 text-white font-black text-sm uppercase tracking-widest rounded-xl shadow-lg shadow-rose-200 hover:shadow-xl hover:from-rose-600 hover:to-fuchsia-700 transform hover:-translate-y-0.5 transition duration-300 outline-none focus:ring-4 focus:ring-rose-100 mb-6">
                LOGIN
            </button>

            <div class="text-center border-t border-slate-100 pt-6">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-rose-600 transition duration-300 group">
                    <span class="transform group-hover:-translate-x-1 transition duration-300">←</span> 
                    Kembali ke Beranda
                </a>
            </div>
        </form>
    </div>

    {{-- Script PWA Service Worker --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => { console.log('Aplikasi Aktif!', reg); })
                    .catch(err => { console.log('Aplikasi Gagal', err); });
            });
        }
    </script>

    {{-- Script Lupa Sandi --}}
    <script>
    function reportForgotPassword() {
        const username = document.getElementById('username').value || '(Nama Penghuni)';
        const phone = "6287876904661";
        const message = `Halo pak, saya ${username} lupa sandi akun kos saya. Mohon dibantu untuk direset`;
        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
    }
    </script>

    {{-- SCRIPT REMEMBER ME (AUTO-FILL USERNAME) --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const usernameInput = document.getElementById("username");
        const rememberCheckbox = document.getElementById("remember_me");
        const loginForm = document.getElementById("loginForm");

        // 1. Cek apakah ada username tersimpan di localStorage
        const savedUsername = localStorage.getItem("sunduwan_saved_username");
        if (savedUsername && usernameInput) {
            usernameInput.value = savedUsername;
            rememberCheckbox.checked = true;
        }

        // 2. Simpan username ke localStorage saat tombol Login ditekan (jika centang "Ingat saya")
        if (loginForm) {
            loginForm.addEventListener("submit", function () {
                if (rememberCheckbox.checked) {
                    localStorage.setItem("sunduwan_saved_username", usernameInput.value);
                } else {
                    localStorage.removeItem("sunduwan_saved_username");
                }
            });
        }
    });
    </script>
</body>
</html>