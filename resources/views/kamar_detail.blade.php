<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Kamar {{ $kamar->nomor_kamar }} | Kos Putri Sunduwan</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> 
        body { font-family: 'Montserrat', sans-serif; background-color: #f8fafc; } 
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .kotak-galeri-sakti {
            width: 100%;
            height: 250px;
            position: relative;
            border-radius: 2rem;
            overflow: hidden;
            background-color: #1e293b;
        }
        @media (min-width: 768px) {
            .kotak-galeri-sakti {
                height: 430px;
            }
        }
    </style>
</head>
<body class="antialiased text-slate-800 pb-20">

    <!-- NAV BAR -->
    <nav class="bg-white/90 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="font-bold text-slate-500 hover:text-rose-500 flex items-center gap-2 text-sm transition">
                ← Kembali
            </a>
            <div class="font-black text-slate-800 tracking-tight text-sm md:text-base uppercase">Kos Putri Sunduwan</div>
        </div>
    </nav>

    <!-- HEADER INFORMASI UTAMA -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-6 md:pt-8">
        
        <div class="mb-6 border-b border-slate-100 pb-5">
            <!-- Badge Tipe Kamar Dinamis -->
            <span class="bg-rose-50 text-rose-600 border border-rose-100 text-[10px] md:text-xs font-black px-3 py-1.5 rounded-lg uppercase tracking-wider capitalize">
                Tipe {{ $kamar->tipe_kamar }}
            </span>
            <h1 class="text-2xl md:text-4xl font-black mt-3 mb-2 text-slate-900">Kamar {{ $kamar->nomor_kamar }}</h1>
            <p class="text-slate-400 text-xs md:text-sm font-medium flex items-center gap-1">
                <span>📍</span> Jl. Ir. Ida Bagus Oka Gg. Sundu No.1, Panjer
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">
            
            <!-- KOLOM KIRI: SLIDER & DESKRIPSI -->
            <div class="lg:col-span-8">
                
                <!-- GALERI SLIDER SAKTI -->
                <div class="kotak-galeri-sakti shadow-sm border border-slate-100 mb-8 group">
                    <div id="image-slider" class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbar w-full h-full scroll-smooth">
                        
                        <!-- Foto 1: Utama -->
                        <div class="min-w-full flex-shrink-0 h-full snap-center relative">
                            <img src="{{ $kamar->foto_utama ? asset('storage/' . $kamar->foto_utama) : 'https://placehold.co/1200x600/e2e8f0/475569?text=Ruang+Utama' }}" onerror="this.src='https://placehold.co/1200x600/e2e8f0/475569?text=Ruang+Utama'" class="w-full h-full object-cover absolute top-0 left-0" alt="Ruang Utama">
                            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/60 to-transparent pointer-events-none"></div>
                            <div class="absolute bottom-4 left-4 bg-white/95 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-800 shadow-sm z-10">
                                🛏️ Ruang Utama
                            </div>
                        </div>

                        <!-- Foto 2: Dapur -->
                        <div class="min-w-full flex-shrink-0 h-full snap-center relative">
                            <img src="{{ $kamar->foto_dapur ? asset('storage/' . $kamar->foto_dapur) : 'https://placehold.co/1200x600/e2e8f0/475569?text=Dapur+Kecil' }}" onerror="this.src='https://placehold.co/1200x600/e2e8f0/475569?text=Dapur+Kecil'" class="w-full h-full object-cover absolute top-0 left-0" alt="Dapur Kecil">
                            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/60 to-transparent pointer-events-none"></div>
                            <div class="absolute bottom-4 left-4 bg-white/95 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-800 shadow-sm z-10">
                                🍳 Dapur Kecil
                            </div>
                        </div>

                        <!-- Foto 3: Kamar Mandi -->
                        <div class="min-w-full flex-shrink-0 h-full snap-center relative">
                            <img src="{{ $kamar->foto_kamar_mandi ? asset('storage/' . $kamar->foto_kamar_mandi) : 'https://placehold.co/1200x600/e2e8f0/475569?text=Kamar+Mandi' }}" onerror="this.src='https://placehold.co/1200x600/e2e8f0/475569?text=Kamar+Mandi'" class="w-full h-full object-cover absolute top-0 left-0" alt="Kamar Mandi">
                            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/60 to-transparent pointer-events-none"></div>
                            <div class="absolute bottom-4 left-4 bg-white/95 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-800 shadow-sm z-10">
                                🚿 Kamar Mandi Dalam
                            </div>
                        </div>
                    </div>

                    <!-- Navigasi Slider Panah -->
                    <button onclick="geserKiri()" class="absolute top-1/2 -translate-y-1/2 left-4 w-10 h-10 bg-white/90 hover:bg-white text-slate-800 rounded-full flex items-center justify-center shadow-md transition-all opacity-0 group-hover:opacity-100 focus:outline-none z-20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                    </button>
                    
                    <button onclick="geserKanan()" class="absolute top-1/2 -translate-y-1/2 right-4 w-10 h-10 bg-white/90 hover:bg-white text-slate-800 rounded-full flex items-center justify-center shadow-md transition-all opacity-0 group-hover:opacity-100 focus:outline-none z-20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                    </button>

                    <div id="slider-angka" class="absolute top-4 right-4 bg-black/60 backdrop-blur-sm text-white px-3 py-1.5 rounded-xl text-xs font-bold tracking-widest z-20">
                        1 / 3
                    </div>
                </div>

                <!-- DESKRIPSI -->
                <h2 class="text-lg font-black mb-3 text-slate-900 uppercase tracking-wide">Deskripsi Kamar</h2>
                <div class="text-slate-600 leading-relaxed text-sm text-justify mb-8 bg-white p-5 md:p-6 rounded-2xl border border-slate-100 shadow-sm">
                    {{ $kamar->deskripsi ?? 'Kamar ini berukuran 3,5 x 3,5 meter, didesain dengan tembok berlapis keramik sehingga ruangan terasa lebih bersih dan sejuk. Ruangan sudah dilengkapi juga dengan fasilitas dapur kecil (pantry) serta kamar mandi dalam. Sangat cocok untuk mahasiswi yang mengutamakan privasi dan kenyamanan.' }}
                </div>

                <!-- RENDERING FASILITAS DINAMIS -->
                <h2 class="text-lg font-black mb-4 text-slate-900 uppercase tracking-wide">Fasilitas Tersedia</h2>
                
                @php
                    // Logika konversi data string database ke array
                    $fasilitasKamar = isset($kamar->fasilitas) ? (is_array($kamar->fasilitas) ? $kamar->fasilitas : explode(',', $kamar->fasilitas)) : [];

                    // Mapping icon khusus halaman detail agar tampak premium dan serasi
                    $iconMapping = [
                        'Kasur' => '🛏️',
                        'Spring Bed' => '🛏️',
                        'AC' => '❄️',
                        'AC (Air Conditioner)' => '❄️',
                        'Lemari' => '🚪',
                        'Lemari Pakaian' => '🚪',
                        'Kamar Mandi Dalam' => '🚿',
                        'Dapur Kecil' => '🍳',
                        'Meja Belajar' => '📝'
                    ];
                    $defaultIcon = '⭐';
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                    @forelse($fasilitasKamar as $item)
                        @if(!empty(trim($item)))
                            <div class="border border-slate-100 rounded-2xl p-4 flex items-center gap-3 bg-white shadow-sm hover:border-rose-200 transition duration-300">
                                <span class="text-xl md:text-2xl">{{ $iconMapping[trim($item)] ?? $defaultIcon }}</span> 
                                <span class="text-sm font-bold text-slate-700">{{ trim($item) }}</span>
                            </div>
                        @endif
                    @empty
                        <div class="col-span-full border border-dashed border-slate-200 rounded-2xl p-4 text-center text-xs text-slate-400 italic bg-white">
                            Tidak ada fasilitas terdaftar pada kamar ini.
                        </div>
                    @endforelse
                </div>
                
                <!-- DOKUMEN NOTICE AMBER -->
                <div class="bg-amber-50/60 border border-amber-100 rounded-2xl p-4 mt-6">
                    <p class="text-xs md:text-sm text-amber-800 font-medium leading-relaxed">
                        <span class="font-black">Info Tambahan:</span> Jika Anda memilih kamar dengan tipe <span class="font-bold underline">Kosongan</span>, fasilitas utama yang didapatkan hanya Dapur Kecil dan Kamar Mandi Dalam.
                    </p>
                </div>

            </div>

            <!-- KOLOM KANAN: FLOATING CARD HARGA & CTA -->
            <div class="lg:col-span-4">
                <div class="bg-white border border-slate-100 rounded-[2rem] p-6 md:p-8 shadow-xl shadow-slate-100 lg:sticky lg:top-24">
                    
                    <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Harga Sewa Per Bulan</p>
                    <div class="text-3xl md:text-4xl font-black text-rose-500 mb-6">
                        Rp {{ number_format($kamar->harga, 0, ',', '.') }}<span class="text-xs font-bold text-slate-400"> /bln</span>
                    </div>

                    <div class="bg-emerald-50/60 border border-emerald-100 p-3.5 rounded-xl mb-6 flex justify-between items-center">
                        <span class="font-bold text-emerald-800 text-xs">Status Kamar</span>
                        <span class="bg-white text-emerald-600 font-black px-3 py-1 rounded-lg text-[10px] uppercase shadow-sm border border-emerald-100">
                            {{ $kamar->status }}
                        </span>
                    </div>
                    
                    <!-- WHATSAPP CTA DENGAN WARNA EMERALD PREMIUM -->
                    <a href="https://wa.me/6281237460936?text=Halo%20Admin%20Sunduwan,%20saya%20tertarik%20dengan%20Kamar%20{{ $kamar->nomor_kamar }}%20tipe%20{{ $kamar->tipe_kamar }}." target="_blank" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 rounded-xl flex items-center justify-center gap-2 transition shadow-lg shadow-emerald-100 text-sm transform hover:-translate-y-0.5 duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.347-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.876 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        Hubungi WhatsApp
                    </a>

                </div>
            </div>

        </div>
    </div>

    <!-- JAVASCRIPT SLIDER -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('image-slider');
            const angka = document.getElementById('slider-angka');
            const totalFoto = 3;

            window.geserKiri = function() {
                slider.scrollBy({ left: -slider.clientWidth, behavior: 'smooth' });
            };
            window.geserKanan = function() {
                slider.scrollBy({ left: slider.clientWidth, behavior: 'smooth' });
            };

            slider.addEventListener('scroll', () => {
                let indeks = Math.round(slider.scrollLeft / slider.offsetWidth) + 1;
                if(indeks < 1) indeks = 1;
                if(indeks > totalFoto) indeks = totalFoto;
                angka.innerText = indeks + ' / ' + totalFoto;
            });
        });
    </script>
</body>
</html>