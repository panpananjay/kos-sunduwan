<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard Utama
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-8 mt-2 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h3 class="text-2xl sm:text-4xl font-black text-slate-800 tracking-tight">
                        Selamat Datang, {{ explode(' ', auth()->user()->name)[0] }}! 👋
                    </h3>
                    
                    @if(auth()->user()->role == 'admin')
                        <p class="text-slate-500 mt-2 text-sm sm:text-base font-medium">
                            Berikut adalah ringkasan operasional Kos Putri Sunduwan untuk periode 
                            <strong class="text-rose-600">{{ request('bulan', $bulanIni) }} {{ request('tahun', date('Y')) }}</strong>.
                        </p>
                    @else
                        <p class="text-slate-500 mt-2 text-sm sm:text-base font-medium">Semoga harimu menyenangkan dan nyaman di Kos Putri Sunduwan. ✨</p>
                    @endif
                </div>

                @if(auth()->user()->role == 'admin')
                <div class="flex">
                    <form action="{{ route('dashboard') }}" method="GET" class="inline-flex items-center bg-white border border-slate-100 p-1 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex items-center px-3 border-r border-slate-50 text-rose-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        
                        <select name="bulan" onchange="this.form.submit()" class="border-none bg-transparent text-[10px] sm:text-xs font-black text-slate-700 focus:ring-0 cursor-pointer pr-8 py-2 uppercase tracking-widest">
                            <option value="Semua" {{ request('bulan') == 'Semua' ? 'selected' : '' }}>SEMUA</option>
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $b)
                                <option value="{{ $b }}" {{ request('bulan', $bulanIni) == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>

                        <div class="h-4 w-[1px] bg-slate-100"></div>

                        <select name="tahun" onchange="this.form.submit()" class="border-none bg-transparent text-[10px] sm:text-xs font-black text-slate-700 focus:ring-0 cursor-pointer pr-8 py-2 uppercase tracking-widest">
                            @for($i = date('Y'); $i >= date('Y')-3; $i--)
                                <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </form>
                </div>
                @endif
            </div>

            @if(auth()->user()->role == 'admin')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-all duration-300">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Kamar</p>
                            <p class="text-4xl font-black text-slate-800">{{ $totalKamar }}</p>
                        </div>
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">🚪</div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-all duration-300">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Kamar Terisi</p>
                            <p class="text-4xl font-black text-emerald-600">{{ $kamarTerisi }}</p>
                        </div>
                        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">🛏️</div>
                    </div>
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-all duration-300">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Kamar Kosong</p>
                            <p class="text-4xl font-black text-rose-500">{{ $kamarKosong }}</p>
                        </div>
                        <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">✨</div>
                    </div>
                </div>

                <div class="mb-4 flex items-center gap-2">
                    <span class="bg-rose-100 text-rose-600 p-2 rounded-lg text-sm">💰</span>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest">Laporan Keuangan</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-gradient-to-br from-rose-500 to-fuchsia-600 p-8 rounded-[2rem] shadow-lg shadow-rose-200 text-white relative overflow-hidden">
                        <p class="text-rose-100 text-xs font-bold uppercase tracking-wider mb-2">Target Pendapatan</p>
                        <p class="text-3xl font-black">Rp {{ number_format($totalTagihanBulanIni, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-400 to-teal-500 p-8 rounded-[2rem] shadow-lg shadow-emerald-200 text-white relative overflow-hidden">
                        <p class="text-emerald-50 text-xs font-bold uppercase tracking-wider mb-2">Uang Sudah Masuk</p>
                        <p class="text-3xl font-black">Rp {{ number_format($uangMasuk, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-8 rounded-[2rem] shadow-lg shadow-slate-200 text-white relative overflow-hidden">
                        <p class="text-slate-300 text-xs font-bold uppercase tracking-wider mb-2">Belum Terbayar</p>
                        <p class="text-3xl font-black">Rp {{ number_format($piutang, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 mb-8">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 border-b border-slate-50 pb-6 gap-4">
                        <h3 class="text-xl font-black text-slate-800 flex items-center gap-2">
                            <span class="text-rose-500">📈</span> Grafik Proyeksi Pendapatan
                        </h3>
                        <div class="bg-rose-50 text-rose-700 px-5 py-2.5 rounded-xl font-black text-sm border border-rose-100 shadow-sm">
                            Potensi Aktif: Rp {{ number_format($estimasiPendapatanPerBulan, 0, ',', '.') }} <span class="text-xs font-medium text-rose-500">/ bln</span>
                        </div>
                    </div>

                    <div class="relative h-80 w-full mb-10">
                        <canvas id="forecastChart"></canvas>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 border-t border-slate-50 pt-8">
                        @foreach($forecasting as $index => $data)
                        <div class="bg-slate-50 p-6 rounded-[1.5rem] border border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-2">
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Bulan +{{ $index + 1 }}</p>
                                <p class="text-base font-bold text-slate-700">{{ $data['bulan'] }}</p>
                            </div>
                            <p class="text-xl font-black text-rose-600">Rp {{ number_format($data['estimasi'], 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

            @else
                @if($penghuniKu)
                    
                {{-- NOTIFIKASI TAGIHAN --}}
                @if($tagihanBulanIni && $tagihanBulanIni->status != 'lunas')
                    <div class="mb-8 bg-gradient-to-r from-rose-500 to-orange-500 rounded-[2rem] p-5 md:p-8 text-white shadow-lg shadow-rose-100/50 relative overflow-hidden group animate-pulse-subtle">
                        {{-- Aksen Glassmorphism --}}
                        <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700"></div>
                        
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="flex items-center gap-4 md:gap-6 w-full">
                                {{-- Icon Box --}}
                                <div class="flex-none w-14 h-14 md:w-16 md:h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-2xl md:text-3xl shadow-inner border border-white/30">
                                    🔔
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-black text-base md:text-xl leading-tight tracking-tight truncate">
                                        Tagihan {{ $tagihanBulanIni->bulan }} Belum Lunas
                                    </h4>
                                    <p class="text-rose-100 text-[10px] md:text-xs font-semibold uppercase tracking-wider mt-1 opacity-90">
                                        Sisa Waktu Pembayaran:
                                    </p>
                                    {{-- Timer Digital --}}
                                    <div id="countdown-timer" class="flex items-center gap-1.5 font-mono text-lg md:text-2xl font-black text-white drop-shadow-sm mt-0.5">
                                        <span id="timer-val" class="tracking-widest">00 : 00 : 00 : 00</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol: Rapi & Tidak Terlalu Lebar --}}
                            <div class="w-full md:w-auto md:ml-auto">
                                <a href="{{ route('tagihan.index') }}" class="block w-full md:min-w-[180px] md:max-w-[220px] bg-white text-rose-600 font-extrabold px-8 py-3.5 md:py-4 rounded-xl md:rounded-2xl hover:bg-rose-50 hover:scale-105 active:scale-95 transition-all text-sm md:text-base text-center shadow-md whitespace-nowrap">
                                    CEK TAGIHAN SAYA
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Script Timer --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const timerVal = document.getElementById('timer-val');
                            if (!timerVal) return;

                            const createdDate = new Date("{{ $tagihanBulanIni->created_at->toIso8601String() }}").getTime();
                            const deadline = createdDate + (7 * 24 * 60 * 60 * 1000);

                            const x = setInterval(function() {
                                const now = new Date().getTime();
                                const distance = deadline - now;

                                if (distance < 0) {
                                    clearInterval(x);
                                    timerVal.innerHTML = "<span class='text-sm tracking-widest'>WAKTU HABIS 🚨</span>";
                                    return;
                                }

                                const d = Math.floor(distance / (1000 * 60 * 60 * 24));
                                const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                const s = Math.floor((distance % (1000 * 60)) / 1000);

                                const pad = (n) => n.toString().padStart(2, '0');
                                timerVal.innerHTML = `${pad(d)} : ${pad(h)} : ${pad(m)} : ${pad(s)}`;
                            }, 1000);
                        });
                    </script>

                    {{-- Style Efek Semula --}}
                    <style>
                        @keyframes pulse-subtle { 
                            0%, 100% { transform: scale(1); } 
                            50% { transform: scale(1.01); } 
                        }
                        .animate-pulse-subtle { 
                            animation: pulse-subtle 3s infinite ease-in-out; 
                        }
                    </style>
                @endif

                    <div class="bg-white p-6 sm:p-8 rounded-[2rem] shadow-sm border border-slate-100 mb-8 flex flex-col md:flex-row items-center gap-6 relative overflow-hidden">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 bg-gradient-to-tr from-rose-500 to-fuchsia-600 rounded-full flex items-center justify-center text-white text-4xl shadow-lg border-4 border-white relative z-10 flex-shrink-0">👩🏻</div>
                        <div class="text-center md:text-left relative z-10 flex-1">
                            <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 mb-2">
                                <h2 class="text-2xl font-black text-slate-800">{{ $penghuniKu->nama }}</h2>
                                <span class="bg-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full w-max mx-auto md:mx-0">Penghuni Aktif</span>
                            </div>
                            <div class="flex flex-wrap justify-center md:justify-start gap-4 sm:gap-6 text-sm font-medium text-slate-500 mt-3">
                                <div>🚪 Kamar: <strong class="text-slate-700">{{ $penghuniKu->kamar?->nomor_kamar ?? '-' }}</strong></div>
                                <div>📱 HP: <strong class="text-slate-700">{{ $penghuniKu->no_hp }}</strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-amber-400 to-orange-500 p-6 rounded-[2rem] text-white shadow-lg shadow-orange-100">
                            <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">POIN SAYA</p>
                            <p class="text-3xl font-black">{{ $penghuniKu->poin }} Poin</p>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-[2rem] text-white shadow-lg shadow-indigo-100">
                            <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">LEVEL SAYA</p>
                            <p class="text-xl font-black">{{ $level ?? 'Penghuni Baru' }}</p>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-400 to-teal-500 p-6 rounded-[2rem] text-white shadow-lg shadow-emerald-100">
                            <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">TRACK RECORD</p>
                            <p class="text-xl font-black">{{ $totalLunas ?? 0 }}x Lunas</p>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-lg font-black text-slate-800 flex items-center gap-2">
                                🛠️ Laporan Pengaduan
                            </h4>
                            <a href="{{ route('pengaduan.create') }}" class="bg-slate-800 text-white p-2 rounded-xl hover:bg-slate-700 transition-all shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                                </svg>
                            </a>
                        </div>
                        
                        @if($pengaduanTerakhir)
                            <div class="bg-slate-50 p-6 rounded-2xl flex items-center justify-between border border-slate-100 transition-all hover:border-rose-200">
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">Status Kendala Terakhir:</p>
                                    <p class="font-bold text-slate-700">"{{ $pengaduanTerakhir->judul }}"</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-4 py-2 rounded-full text-[10px] font-black uppercase shadow-sm {{ $pengaduanTerakhir->status == 'Proses' ? 'bg-blue-100 text-blue-600' : 'bg-rose-100 text-rose-600' }}">
                                        {{ $pengaduanTerakhir->status }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                <p class="text-sm font-bold text-slate-400 italic">Tidak ada pengaduan aktif. Fasilitas kos dalam kondisi baik! ✅</p>
                            </div>
                        @endif
                    </div>

                @else
                    <div class="bg-white p-10 rounded-[2rem] shadow-sm text-center border border-rose-100">
                        <h3 class="text-2xl font-black text-slate-800">Profil Belum Lengkap</h3>
                        <p class="text-slate-500 mt-2">Hubungi admin untuk aktivasi akun penghuni kamu.</p>
                    </div>
                @endif
            @endif

        </div>
    </div>

    @if(auth()->user()->role == 'admin')
        <div id="chart-data" 
            data-labels='{!! json_encode(array_values(array_map(function($data, $index) {
                return $data["bulan"] . " " . date("Y", strtotime("+" . ($index + 1) . " month"));
            }, $forecasting, array_keys($forecasting)))) !!}'
            data-points='{!! json_encode(array_column($forecasting, "estimasi")) !!}'
            style="display: none;">
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartCanvas = document.getElementById('forecastChart');
                const dataElement = document.getElementById('chart-data');
                if(!chartCanvas || !dataElement) return;

                const labels = JSON.parse(dataElement.getAttribute('data-labels'));
                const dataPoints = JSON.parse(dataElement.getAttribute('data-points'));

                const ctx = chartCanvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(244, 63, 94, 0.2)');
                gradient.addColorStop(1, 'rgba(244, 63, 94, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Estimasi Pendapatan',
                            data: dataPoints,
                            borderColor: '#f43f5e', 
                            backgroundColor: gradient,
                            borderWidth: 4,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#f43f5e',
                            pointBorderWidth: 3,
                            pointRadius: 6,
                            tension: 0.4, 
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

    <style>
        @keyframes pulse-subtle {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.01); }
        }
        .animate-pulse-subtle {
            animation: pulse-subtle 3s infinite ease-in-out;
        }
    </style>
</x-app-layout>