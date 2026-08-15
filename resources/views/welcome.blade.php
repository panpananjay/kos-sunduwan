<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kos Putri Sunduwan | Hunian Nyaman di Panjer</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta name="theme-color" content="#f43f5e">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sunduwan-pwa.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-sunduwan-pwa.png') }}">
    <link rel="manifest" href="/build/manifest.webmanifest">

    <style>
        body {
            font-family: 'Montserrat', sans-serif !important;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #fdf2f8;
        }

        ::-webkit-scrollbar-thumb {
            background: #f472b6;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #db2777;
        }

        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }

        .animate-blob {
            animation: blob 8s infinite alternate ease-in-out;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient-shift 4s ease infinite;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased selection:bg-rose-500 selection:text-white overflow-x-hidden">

    {{-- NAVBAR --}}
    <nav class="fixed w-full z-50 top-0 bg-white/80 backdrop-blur-lg border-b border-slate-100 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">

                <div class="flex-shrink-0 flex items-center cursor-pointer">
                    <img src="{{ asset('images/logo-sunduwan.png') }}" alt="Logo Kos Putri Sunduwan" class="h-10 sm:h-16 w-auto object-contain transform scale-[1.4] sm:scale-[1.7] origin-left -ml-1 sm:-ml-2">
                    <span class="font-black text-xl sm:text-3xl text-slate-800 tracking-tight ml-3 sm:ml-6">SUNDUWAN</span>
                </div>

                <div>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-5 py-2 sm:px-6 sm:py-2.5 text-xs sm:text-sm font-bold rounded-full text-white bg-slate-800 hover:bg-rose-600 transition duration-300 shadow-md transform hover:-translate-y-0.5 whitespace-nowrap">
                                <span class="block sm:hidden">Dashboard</span>
                                <span class="hidden sm:block">Dashboard Saya →</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2 sm:px-6 sm:py-2.5 text-xs sm:text-sm font-bold rounded-full text-rose-700 bg-rose-50 hover:bg-rose-600 hover:text-white transition duration-300 shadow-sm transform hover:-translate-y-0.5 whitespace-nowrap">
                                <span class="block sm:hidden">Login</span>
                                <span class="hidden sm:block">Masuk / Login</span>
                            </a>
                        @endauth
                    @endif
                </div>

            </div>
        </div>
    </nav>


    {{-- HERO --}}
    <div class="relative pt-28 pb-16 lg:pt-48 lg:pb-32 overflow-hidden bg-white">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-rose-200 opacity-60 blur-3xl animate-blob pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-orange-100 opacity-60 blur-3xl animate-blob animation-delay-2000 pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-fuchsia-100 opacity-40 blur-3xl animate-blob animation-delay-4000 pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-rose-50 border border-rose-100 text-rose-600 text-[10px] sm:text-xs font-bold uppercase tracking-widest mb-6 sm:mb-8 shadow-sm">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                </span>
                Eksklusif & Terbatas
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-black text-slate-900 leading-tight mb-4 sm:mb-6 tracking-tighter">
                Hunian Nyaman di <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-500 via-fuchsia-500 to-orange-400 animate-gradient">Jantung Panjer</span>
            </h1>

            <p class="mt-4 text-sm sm:text-lg text-slate-500 max-w-xl mx-auto font-medium mb-8 sm:mb-10 leading-relaxed px-2 sm:px-0">
                Berlokasi di Jalan Ir. Ida Bagus Oka Gang Sundu No.1 <br>
                Kos Putri Sunduwan menyediakan hunian nyaman dan aman dengan suasana kekeluargaan dan pastinya bikin kamu merasa seperti di rumah sendiri.
            </p>

            <div class="flex justify-center">
                <a href="#katalog-kamar" class="inline-flex items-center justify-center px-6 py-3.5 sm:px-8 sm:py-4 text-sm sm:text-base font-bold rounded-full text-white bg-rose-500 hover:bg-rose-700 shadow-xl shadow-rose-200 transform hover:-translate-y-1 transition duration-300">
                    Lihat Kamar Tersedia 👇
                </a>
            </div>
        </div>
    </div>


    {{-- KATALOG KAMAR --}}
    <div id="katalog-kamar" class="py-16 sm:py-20 bg-slate-50 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-12 gap-4 sm:gap-6">
                <div>
                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 mb-1 sm:mb-2">Pilih Kamarmu</h2>
                    <p class="text-sm sm:text-base text-slate-500 font-medium">Katalog kamar yang siap huni bulan ini.</p>
                </div>

                <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-200 inline-flex items-center gap-2 w-max self-start md:self-auto">
                    <span class="text-xs sm:text-sm font-bold text-slate-700">Total Tersedia:</span>
                    <span class="bg-emerald-100 text-emerald-700 font-black px-2 py-0.5 rounded-lg text-xs sm:text-sm">
                        {{ $kamarKosong->count() }} Kamar
                    </span>
                </div>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">

                @forelse($kamarKosong as $kamar)

                    <a href="{{ route('kamar.detail.public', $kamar->id) }}" class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl transform hover:-translate-y-2 transition duration-500 border border-slate-100 group flex flex-col block cursor-pointer">

                        <div class="h-48 sm:h-56 bg-slate-200 relative overflow-hidden">
                            @if($kamar->foto_utama)
                                <img src="{{ asset('storage/' . $kamar->foto_utama) }}" alt="Foto Kamar {{ $kamar->nomor_kamar }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-200 text-slate-400">
                                    <span class="text-4xl">📸</span>
                                </div>
                            @endif

                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md text-emerald-600 text-[10px] sm:text-xs font-black px-3 py-1.5 sm:px-4 sm:py-2 rounded-full uppercase tracking-wider shadow-sm flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Tersedia
                            </div>
                        </div>

                        <div class="p-6 sm:p-8 flex-1 flex flex-col">

                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <p class="text-rose-600 text-xs sm:text-sm font-black uppercase tracking-widest border border-rose-200 bg-rose-50 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded w-max">
                                    Kamar {{ $kamar->nomor_kamar }}
                                </p>

                                <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider border border-slate-200 bg-slate-100 text-slate-600 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded w-max">
                                    Tipe {{ ucfirst(strtolower($kamar->tipe_kamar)) }}
                                </span>
                            </div>

                            <h3 class="text-lg sm:text-xl font-bold text-slate-900 group-hover:text-rose-600 transition">
                                Kamar {{ $kamar->nomor_kamar }}
                            </h3>

                            <p class="text-slate-500 text-xs sm:text-sm mb-5 sm:mb-6 leading-relaxed line-clamp-3">
                                {{ $kamar->fasilitas ?: 'Fasilitas kamar standar yang nyaman untuk kebutuhan penghuni.' }}
                            </p>

                            <div class="mt-auto pt-5 sm:pt-6 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider">Harga Sewa</span>

                                    <div class="text-xl sm:text-2xl font-black text-slate-900">
                                        Rp {{ number_format($kamar->harga, 0, ',', '.') }}<span class="text-[10px] sm:text-xs font-medium text-slate-500">/bln</span>
                                    </div>
                                </div>

                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-slate-50 group-hover:bg-rose-500 text-slate-400 group-hover:text-white rounded-full flex items-center justify-center transition duration-300 shadow-sm">
                                    <span class="text-lg sm:text-xl">↗️</span>
                                </div>
                            </div>

                        </div>

                    </a>

                @empty

                    <div class="col-span-full text-center py-16 sm:py-20 bg-white rounded-[2rem] border border-slate-100 shadow-sm mx-4 sm:mx-0">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6">
                            <span class="text-3xl sm:text-4xl grayscale opacity-50">🏠</span>
                        </div>

                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 mb-2">
                            Semua Kamar Sedang Penuh
                        </h3>

                        <p class="text-sm sm:text-base text-slate-500 max-w-md mx-auto mb-6 px-4">
                            Saat ini belum ada kamar yang kosong.
                        </p>

                        <a href="https://wa.me/6281237460936?text={{ urlencode('Halo, apakah Kos Putri Sunduwan saat ini tersedia kamar kosong?') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold rounded-full text-slate-700 bg-slate-100 hover:bg-slate-200 transition duration-300">
                            Hubungi Pengelola
                        </a>
                    </div>

                @endforelse

            </div>
        </div>
    </div>


    {{-- LOKASI KOS --}}
    <div id="lokasi-kos" class="py-16 sm:py-20 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
                <span class="text-rose-600 text-xs sm:text-sm font-black uppercase tracking-widest border border-rose-200 bg-rose-50 px-2.5 py-1 rounded w-max mx-auto block mb-3">
                    Lokasi Strategis
                </span>

                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 mb-4">
                    Peta Lokasi Kos
                </h2>

                <p class="text-sm sm:text-base text-slate-500 font-medium leading-relaxed">
                    Berlokasi di Jalan Ir. Ida Bagus Oka Gang Sundu No.1, Panjer. Akses sangat mudah dan dekat dengan berbagai fasilitas umum serta wilayah kampus.
                </p>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch">

                <div class="lg:col-span-2 h-[350px] sm:h-[450px] rounded-[2rem] overflow-hidden shadow-sm border border-slate-100 relative">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3944.1750556415!2d115.21795387478942!3d-8.67489679137296!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd2413c0b03ddd9%3A0x43b761993a3c4541!2sKos%20Putri%20Sunduwan%20Panjer!5e0!3m2!1sen!2sid!4v1781524099366!5m2!1sen!2sid"
                        style="border:0 !important; width:100% !important; height:100% !important; display:block !important;"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Lokasi Kos Putri Sunduwan">
                    </iframe>
                </div>


                <div class="bg-slate-50 rounded-[2rem] p-6 sm:p-8 border border-slate-100 flex flex-col justify-between">

                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                            📍 <span>Detail Alamat</span>
                        </h3>

                        <p class="text-slate-600 text-sm leading-relaxed mb-6">
                            <strong>Kos Putri Sunduwan</strong><br>
                            Jl. Ir. Ida Bagus Oka, Gang Sundu No.1, Panjer, Kec. Denpasar Selatan, Kota Denpasar, Bali.
                        </p>

                        <h4 class="text-xs sm:text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">
                            Tempat Terdekat:
                        </h4>

                        <ul class="space-y-3">
                            <li class="flex items-start gap-2.5 text-sm text-slate-600">
                                <span class="text-emerald-500">🏢</span>
                                <span>Sangat strategis dekat dengan area pendidikan dan perkantoran</span>
                            </li>

                            <li class="flex items-start gap-2.5 text-sm text-slate-600">
                                <span class="text-emerald-500">🛍️</span>
                                <span>Mudah menjangkau minimarket, pusat kuliner Panjer, dan jasa laundry</span>
                            </li>

                            <li class="flex items-start gap-2.5 text-sm text-slate-600">
                                <span class="text-emerald-500">🏥</span>
                                <span>Akses cepat ke apotek dan fasilitas kesehatan terdekat</span>
                            </li>
                        </ul>
                    </div>


                    <div class="pt-6 border-t border-slate-200 mt-6 lg:mt-0">
                        <a href="https://maps.app.goo.gl/SS2oQNTXSdfoxN4n8" target="_blank" rel="noopener noreferrer" class="w-full inline-flex items-center justify-center px-5 py-3 text-sm font-bold rounded-xl text-white bg-slate-800 hover:bg-rose-600 transition duration-300 shadow-md text-center">
                            Buka di Google Maps ➔
                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>


    {{-- FOOTER --}}
    <footer class="bg-white py-10 sm:py-12 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between">

            <div class="flex items-center gap-3 mb-4 md:mb-0">
                <img src="{{ asset('images/logo-sunduwan.png') }}" alt="Logo Kos Putri Sunduwan" class="h-8 sm:h-10 w-auto grayscale opacity-50">
                <span class="font-bold text-slate-500 text-sm sm:text-base">Kos Putri Sunduwan</span>
            </div>

            <div class="text-center md:text-right">
                <p class="text-slate-400 font-medium text-xs sm:text-sm">
                    © {{ date('Y') }} All rights reserved.
                </p>
                <p class="text-slate-400 text-[10px] sm:text-xs mt-1">
                    Kos Putri Sunduwan
                </p>
            </div>

        </div>
    </footer>


    {{-- FLOATING WHATSAPP BUTTON --}}
    <a
        href="https://wa.me/6281237460936?text={{ urlencode('Halo, saya ingin bertanya mengenai kamar di Kos Putri Sunduwan.') }}"
        target="_blank"
        rel="noopener noreferrer"
        title="Hubungi Admin via WhatsApp"
        aria-label="Hubungi Admin via WhatsApp"
        style="position:fixed !important; bottom:24px !important; right:24px !important; z-index:999999 !important; width:60px !important; height:60px !important; display:block !important; padding:0 !important; margin:0 !important; border:none !important; background:none !important; outline:none !important; text-decoration:none !important; transition:all 0.3s ease !important;"
        onmouseover="this.style.transform='translateY(-5px) scale(1.08)'; this.style.filter='drop-shadow(0 12px 20px rgba(0,0,0,0.3))';"
        onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.filter='drop-shadow(0 8px 15px rgba(0,0,0,0.2))';"
    >
        <img
            src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg"
            alt="WhatsApp Admin"
            style="width:100% !important; height:100% !important; display:block !important; max-width:60px !important; max-height:60px !important; min-width:60px !important; min-height:60px !important; border:none !important; padding:0 !important; margin:0 !important; object-fit:contain !important;">
    </a>


    {{-- PWA SERVICE WORKER REGISTRATION --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Sunduwan PWA Guest: Aktif!', reg))
                    .catch(err => console.error('PWA Gagal:', err));
            });
        }
    </script>

</body>
</html>