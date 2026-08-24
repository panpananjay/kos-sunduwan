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

            {{-- HEADER --}}
            <div class="mb-8 mt-2 flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h3 class="text-2xl sm:text-4xl font-black text-slate-800 tracking-tight">
                        Selamat Datang,
                        @if(auth()->user()->role == 'admin')
                            {{ explode(' ', auth()->user()->name)[0] }}
                        @else
                            {{ $penghuniKu->nama ?? auth()->user()->name }}
                        @endif
                        ! 👋
                    </h3>

                    @if(auth()->user()->role == 'admin')
                        <p class="text-slate-500 mt-2 text-sm sm:text-base font-medium">
                            Berikut adalah ringkasan operasional Kos Putri Sunduwan untuk periode
                            <strong class="text-rose-600">
                                {{ request('bulan', $bulanIni) }} {{ request('tahun', date('Y')) }}
                            </strong>.
                        </p>
                    @else
                        <p class="text-slate-500 mt-2 text-sm sm:text-base font-medium">
                            Semoga harimu menyenangkan dan nyaman di Kos Putri Sunduwan. ✨
                        </p>
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
                                    <option value="{{ $b }}" {{ request('bulan', $bulanIni) == $b ? 'selected' : '' }}>
                                        {{ $b }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="h-4 w-[1px] bg-slate-100"></div>

                            <select name="tahun" onchange="this.form.submit()" class="border-none bg-transparent text-[10px] sm:text-xs font-black text-slate-700 focus:ring-0 cursor-pointer pr-8 py-2 uppercase tracking-widest">
                                @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                                    <option value="{{ $i }}" {{ request('tahun', date('Y')) == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                        </form>
                    </div>
                @endif
            </div>

            {{-- NOTIFIKASI --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-100 border border-emerald-200 text-emerald-800 rounded-2xl font-bold text-sm flex items-center gap-2 shadow-sm animate-pulse-subtle">
                    <span>🎉</span>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-100 border border-rose-200 text-rose-800 rounded-2xl font-bold text-sm flex items-center gap-2 shadow-sm animate-pulse-subtle">
                    <span>🚨</span>
                    {{ session('error') }}
                </div>
            @endif

            {{-- DASHBOARD ADMIN --}}
            @if(auth()->user()->role == 'admin')

                {{-- STATISTIK KAMAR --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-all duration-300">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Total Kamar</p>
                            <p class="text-4xl font-black text-slate-800">{{ $totalKamar }}</p>
                        </div>
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                            🚪
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-all duration-300">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Kamar Terisi</p>
                            <p class="text-4xl font-black text-emerald-600">{{ $kamarTerisi }}</p>
                        </div>
                        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                            🛏️
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex items-center justify-between group hover:shadow-md transition-all duration-300">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Kamar Kosong</p>
                            <p class="text-4xl font-black text-rose-500">{{ $kamarKosong }}</p>
                        </div>
                        <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">
                            ✨
                        </div>
                    </div>
                </div>

                {{-- LAPORAN KEUANGAN --}}
                <div class="mb-4 flex items-center gap-2">
                    <span class="bg-rose-100 text-rose-600 p-2 rounded-lg text-sm">💰</span>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest">
                        Laporan Keuangan
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-gradient-to-br from-rose-500 to-fuchsia-600 p-8 rounded-[2rem] shadow-lg shadow-rose-200 text-white relative overflow-hidden">
                        <p class="text-rose-100 text-xs font-bold uppercase tracking-wider mb-2">
                            Target Pendapatan
                        </p>
                        <p class="text-3xl font-black">
                            Rp {{ number_format($totalTagihanBulanIni, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-400 to-teal-500 p-8 rounded-[2rem] shadow-lg shadow-emerald-200 text-white relative overflow-hidden">
                        <p class="text-emerald-50 text-xs font-bold uppercase tracking-wider mb-2">
                            Uang Sudah Masuk
                        </p>
                        <p class="text-3xl font-black">
                            Rp {{ number_format($uangMasuk, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-slate-700 to-slate-900 p-8 rounded-[2rem] shadow-lg shadow-slate-200 text-white relative overflow-hidden">
                        <p class="text-slate-300 text-xs font-bold uppercase tracking-wider mb-2">
                            Belum Terbayar
                        </p>
                        <p class="text-3xl font-black">
                            Rp {{ number_format($piutang, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- GRAFIK PROYEKSI --}}
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 mb-8">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 border-b border-slate-50 pb-6 gap-4">
                        <h3 class="text-xl font-black text-slate-800 flex items-center gap-2">
                            <span class="text-rose-500">📈</span>
                            Grafik Proyeksi Pendapatan
                        </h3>

                        <div class="bg-rose-50 text-rose-700 px-5 py-2.5 rounded-xl font-black text-sm border border-rose-100 shadow-sm">
                            Potensi Aktif:
                            Rp {{ number_format($estimasiPendapatanPerBulan, 0, ',', '.') }}
                            <span class="text-xs font-medium text-rose-500">/ bln</span>
                        </div>
                    </div>

                    <div class="relative h-80 w-full mb-10">
                        <canvas id="forecastChart"></canvas>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 border-t border-slate-50 pt-8">
                        @foreach($forecasting as $index => $data)
                            <div class="bg-slate-50 p-6 rounded-[1.5rem] border border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-2">
                                <div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">
                                        Bulan +{{ $index + 1 }}
                                    </p>
                                    <p class="text-base font-bold text-slate-700">
                                        {{ $data['bulan'] }}
                                    </p>
                                </div>

                                <p class="text-xl font-black text-rose-600">
                                    Rp {{ number_format(floor($data['estimasi']), 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

            {{-- DASHBOARD PENGHUNI --}}
            @else

                @if($penghuniKu)

                    {{-- TAGIHAN BELUM LUNAS --}}
                    @if($tagihanBulanIni && $tagihanBulanIni->status != 'lunas')
                        <div class="mb-8 bg-gradient-to-r from-rose-500 to-orange-500 p-6 rounded-[2rem] shadow-lg shadow-rose-100 text-white flex flex-col items-start gap-6 animate-pulse-subtle">
                            <div class="flex items-start gap-4 w-full">
                                <span class="text-4xl mt-1">📢</span>

                                <div class="flex-1">
                                    <h4 class="font-black text-lg">
                                        Tagihan {{ $tagihanBulanIni->bulan }} Belum Lunas
                                    </h4>

                                    <p class="text-rose-100 text-sm mt-1">
                                        Selesaikan pembayaran sebelum jatuh tempo untuk mendapatkan +50 poin! Terlambat bayar akan mengurangi -50 poin
                                        (Poin saat ini: <strong>{{ $penghuniKu->poin }}</strong>)
                                    </p>

                                    <div class="flex items-center mt-3">
                                        <div id="countdown-timer" class="flex items-center gap-2 font-mono text-xl md:text-2xl font-bold tracking-widest text-white/90 drop-shadow-sm">
                                            <span class="text-[10px] font-light animate-pulse tracking-normal">
                                                LOAD...
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Bayar: pakai fetch() + Midtrans Snap popup, bukan form submit biasa --}}
                            <button type="button" onclick="bayarTagihan({{ $tagihanBulanIni->id }})" id="btnBayar"
                                class="w-full mt-2 bg-white text-rose-600 font-black px-6 py-3.5 rounded-xl hover:bg-rose-50 transition-all shadow-md text-center text-base block transform hover:-translate-y-0.5 duration-300">
                                💳 Bayar Rp {{ number_format($tagihanBulanIni->jumlah_tagihan, 0, ',', '.') }}
                            </button>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const timerDisplay = document.getElementById('countdown-timer');

                                if (!timerDisplay) return;

                                const createdDate = new Date("{{ $tagihanBulanIni->created_at->toIso8601String() }}").getTime();
                                const deadline = createdDate + (7 * 24 * 60 * 60 * 1000);

                                const x = setInterval(function () {
                                    const now = new Date().getTime();
                                    const distance = deadline - now;

                                    if (distance < 0) {
                                        clearInterval(x);
                                        timerDisplay.innerHTML = "<span class='text-sm uppercase tracking-widest'>Waktu Habis 🚨</span>";
                                        return;
                                    }

                                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                    const f = (num) => num.toString().padStart(2, '0');

                                    timerDisplay.innerHTML = `
                                        <span>${f(days)}</span>
                                        <span class="text-xl opacity-40 font-light">:</span>
                                        <span>${f(hours)}</span>
                                        <span class="text-xl opacity-40 font-light">:</span>
                                        <span>${f(minutes)}</span>
                                        <span class="text-xl opacity-40 font-light animate-none">:</span>
                                        <span class="text-rose-200">${f(seconds)}</span>
                                    `;
                                }, 1000);
                            });

                            // Fungsi bayar via Midtrans Snap popup
                            function bayarTagihan(id) {
                                const btn = document.getElementById('btnBayar');
                                const teksAsli = btn.innerHTML;
                                btn.disabled = true;
                                btn.innerHTML = '⏳ Memuat...';

                                fetch(`/tagihan/${id}/bayar`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    btn.disabled = false;
                                    btn.innerHTML = teksAsli;

                                    if (data.snap_token) {
                                        snap.pay(data.snap_token, {
                                            onSuccess: function (result) {
                                                window.location.reload();
                                            },
                                            onPending: function (result) {
                                                window.location.reload();
                                            },
                                            onError: function (result) {
                                                alert('Pembayaran gagal, silakan coba lagi.');
                                            },
                                            onClose: function () {
                                                // User menutup popup tanpa menyelesaikan pembayaran
                                            }
                                        });
                                    } else {
                                        alert('Gagal memuat pembayaran. Silakan coba lagi.');
                                    }
                                })
                                .catch(error => {
                                    btn.disabled = false;
                                    btn.innerHTML = teksAsli;
                                    alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
                                });
                            }
                        </script>
                    @endif

                    {{-- PROFIL PENGHUNI --}}
                    <div class="bg-white p-6 sm:p-8 rounded-[2rem] shadow-sm border border-slate-100 mb-8 flex flex-col md:flex-row items-center gap-6 relative overflow-hidden">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 bg-gradient-to-tr from-rose-500 to-fuchsia-600 rounded-full flex items-center justify-center text-white text-4xl shadow-lg border-4 border-white relative z-10 flex-shrink-0">
                            👩🏻
                        </div>

                        <div class="text-center md:text-left relative z-10 flex-1">
                            <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 mb-2">
                                <h2 class="text-2xl font-black text-slate-800">
                                    {{ $penghuniKu->nama }}
                                </h2>

                                <span class="bg-emerald-100 text-emerald-600 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full w-max mx-auto md:mx-0">
                                    Penghuni Aktif
                                </span>
                            </div>

                            <div class="flex flex-wrap justify-center md:justify-start gap-4 sm:gap-6 text-sm font-medium text-slate-500 mt-3">
                                <div>
                                    🚪 Kamar:
                                    <strong class="text-slate-700">
                                        {{ $penghuniKu->kamar?->nomor_kamar ?? '-' }}
                                    </strong>
                                </div>

                                <div>
                                    📱 HP:
                                    <strong class="text-slate-700">
                                        {{ $penghuniKu->no_hp }}
                                    </strong>
                                </div>

                                <div>
                                    📅 Sejak:
                                    <strong class="text-slate-700">
                                        {{ $tanggalMasuk ?? '-' }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- GAMIFIKASI & POIN --}}
                    <div class="bg-white rounded-[2.5rem] p-1 shadow-sm border border-slate-100 mb-8 relative overflow-hidden group/card bg-gradient-to-r from-rose-500/5 via-transparent to-indigo-500/5">
                        <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-200/20 rounded-full blur-2xl pointer-events-none transition-all duration-700 group-hover/card:scale-150"></div>
                        <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-rose-200/20 rounded-full blur-2xl pointer-events-none transition-all duration-700 group-hover/card:scale-150"></div>

                        <div class="bg-white/80 backdrop-blur-md rounded-[2.3rem] p-6 sm:p-7 grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 relative z-10">

                            {{-- POIN SAYA --}}
                            <button type="button" onclick="cekSyaratPoin({{ $penghuniKu->poin }})" class="pb-6 sm:pb-0 sm:pr-8 group/item focus:outline-none text-left w-full block relative">
                                <div class="flex items-center gap-4 transition-all duration-300 group-hover/item:translate-x-1">
                                    <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center text-white shadow-md shadow-orange-200 transition-transform duration-300 group-hover/item:rotate-12 group-hover/item:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>

                                    <div>
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">
                                            Poin Saya
                                        </span>

                                        <span class="text-2xl font-black text-slate-800 tracking-tight block mt-0.5 group-hover/item:text-orange-500 transition-colors">
                                            {{ $penghuniKu->poin }}
                                            <span class="text-xs font-bold text-slate-400 tracking-normal">Pts</span>
                                        </span>

                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full mt-1.5 border border-amber-100 group-hover/item:bg-amber-500 group-hover/item:text-white group-hover/item:border-transparent transition-all duration-300">
                                            Tukar Voucher ➜
                                        </span>
                                    </div>
                                </div>
                            </button>

                            {{-- LEVEL SAYA --}}
                            <div class="py-6 sm:py-0 sm:px-8 flex items-center gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-md shadow-indigo-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                </div>

                                <div>
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">
                                        Level Saya
                                    </span>

                                    <span class="text-lg font-black text-slate-800 tracking-tight block mt-0.5">
                                        {{ $level ?? 'Penghuni Baru 🌱' }}
                                    </span>

                                    <span class="text-[10px] font-semibold text-slate-400 mt-1 block">
                                        Level membership aktif kamu
                                    </span>
                                </div>
                            </div>

                            {{-- TRACK RECORD --}}
                            <div class="pt-6 sm:pt-0 sm:pl-8 flex items-center gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl flex items-center justify-center text-white shadow-md shadow-emerald-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>

                                <div>
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">
                                        Track Record
                                    </span>

                                    <span class="text-lg font-black text-emerald-600 tracking-tight block mt-0.5">
                                        {{ $totalLunas ?? 0 }}x
                                        <span class="text-slate-800 font-bold">Lunas</span>
                                    </span>

                                    <span class="text-[10px] font-semibold text-slate-400 mt-1 block">
                                        Pembayaran tepat waktu
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- INVENTORY VOUCHER --}}
                    <div class="mt-6 mb-8 p-6 bg-white rounded-[2rem] shadow-sm border border-slate-100">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-xl">🎒</span>
                            <h4 class="font-black text-slate-800 text-base uppercase tracking-wider">
                                Inventory Voucher Diskon
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $vouchers = \App\Models\Voucher::where('penghuni_id', $penghuniKu->id)
                                    ->where('status', '!=', 'terpakai')
                                    ->orderByRaw("FIELD(status, 'aktif', 'expired')")
                                    ->latest()
                                    ->get();
                            @endphp

                            @forelse($vouchers as $v)
                                @php
                                    if ($v->status == 'aktif' && \Carbon\Carbon::now()->gt($v->masa_berlaku)) {
                                        $v->update(['status' => 'expired']);
                                    }
                                @endphp

                                <div class="p-5 rounded-2xl border flex justify-between items-center transition-all duration-300 relative overflow-hidden {{ $v->status == 'aktif' ? 'border-emerald-200 bg-emerald-50/40 shadow-sm shadow-emerald-50' : 'border-slate-200 bg-slate-100/70 opacity-60 select-none' }}">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-black tracking-wide text-sm {{ $v->status == 'aktif' ? 'text-emerald-700' : 'text-slate-500 line-through' }}">
                                                {{ $v->kode_voucher }}
                                            </span>

                                            <span class="text-[9px] px-2 py-0.5 rounded-full font-black tracking-wide {{ $v->status == 'aktif' ? 'bg-emerald-200 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                                                {{ strtoupper($v->status) }}
                                            </span>
                                        </div>

                                        <p class="text-xs mt-1 text-slate-500">
                                            Potongan Sewa:
                                            <strong class="text-slate-700">Rp 50.000</strong>
                                        </p>

                                        <small class="text-[10px] block mt-0.5 text-slate-400">
                                            Expired: {{ \Carbon\Carbon::parse($v->masa_berlaku)->translatedFormat('d F Y') }}
                                        </small>
                                    </div>

                                    <div class="relative flex items-center justify-center min-w-[100px]">
                                        @if($v->status == 'aktif')
                                            <form action="{{ route('voucher.gunakan', $v->id) }}" method="POST" onsubmit="return confirm('Gunakan voucher ini untuk memotong tagihan bulan ini?')">
                                                @csrf
                                                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold py-2 px-4 rounded-xl shadow-sm transition transform active:scale-95">
                                                    Gunakan
                                                </button>
                                            </form>
                                        @else
                                            <div class="border-4 border-dashed border-rose-500/80 text-rose-500/80 font-black text-xs uppercase tracking-widest px-3 py-1 rounded-lg transform -rotate-12 shadow-sm pointer-events-none select-none my-1 font-mono">
                                                EXPIRED
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-1 md:col-span-2 text-center py-8 border border-dashed border-slate-200 rounded-2xl text-slate-400 text-sm font-medium italic">
                                    🎒 Inventory kosong. Yuk, kumpulkan poin disiplin untuk klaim voucher sewa!
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- LAPORAN PENGADUAN --}}
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
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-1">
                                        Status Kendala Terakhir:
                                    </p>
                                    <p class="font-bold text-slate-700">
                                        "{{ $pengaduanTerakhir->judul }}"
                                    </p>
                                </div>

                                <div class="text-right">
                                    <span class="px-4 py-2 rounded-full text-[10px] font-black uppercase shadow-sm {{ $pengaduanTerakhir->status == 'Proses' ? 'bg-blue-100 text-blue-600' : 'bg-rose-100 text-rose-600' }}">
                                        {{ $pengaduanTerakhir->status }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-6 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                <p class="text-sm font-bold text-slate-400 italic">
                                    Tidak ada pengaduan aktif. Fasilitas kos dalam kondisi baik! ✅
                                </p>
                            </div>
                        @endif
                    </div>

                @else
                    <div class="bg-white p-10 rounded-[2rem] shadow-sm text-center border border-rose-100">
                        <h3 class="text-2xl font-black text-slate-800">
                            Profil Belum Lengkap
                        </h3>
                        <p class="text-slate-500 mt-2">
                            Hubungi admin untuk aktivasi akun penghuni kamu.
                        </p>
                    </div>
                @endif

            @endif
        </div>
    </div>

    {{-- MODAL GLOBAL GAMIFIKASI POIN --}}
    <div id="modalPoin" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-md transition-all duration-300">
        <div class="bg-white/95 rounded-[2.5rem] p-8 max-w-md w-full shadow-2xl shadow-slate-900/20 transform scale-95 border border-white/40 text-center relative overflow-hidden animate-fade-in">

            <div id="modalIconContainer" class="w-20 h-20 mx-auto rounded-3xl flex items-center justify-center text-3xl mb-5 shadow-sm transition-all duration-300">
                <span id="modalIcon"></span>
            </div>

            <h3 class="text-xl font-black text-slate-800 tracking-tight mb-2" id="modalTitle">
                Konfirmasi
            </h3>

            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed px-2 mb-6" id="modalBody">
                Detail status poin kamu akan dimuat di sini.
            </p>

            <div class="flex items-center gap-3 justify-center w-full">
                <button type="button" onclick="closeModal()" class="w-full sm:w-auto min-w-[100px] px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black rorounded-xl transition-all">
                    Tutup
                </button>

                <form id="formTukarPoin" action="{{ route('poin.tukar') }}" method="POST" class="hidden w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto min-w-[120px] px-5 py-3 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl shadow-md shadow-rose-200 transition-all">
                        Ya, Tukar Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    {{-- Midtrans Snap.js: WAJIB diload supaya fungsi snap.pay() di atas bisa jalan --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function cekSyaratPoin(poin) {
            const modal = document.getElementById('modalPoin');
            const iconContainer = document.getElementById('modalIconContainer');
            const icon = document.getElementById('modalIcon');
            const title = document.getElementById('modalTitle');
            const body = document.getElementById('modalBody');
            const formTukar = document.getElementById('formTukarPoin');
            const syaratPoin = 550;

            if (poin >= syaratPoin) {
                iconContainer.className = "w-20 h-20 mx-auto rounded-3xl flex items-center justify-center text-3xl mb-5 shadow-sm transition-all duration-300 bg-amber-100 text-amber-600 border border-amber-200";
                icon.innerText = "🎁";
                title.innerText = "Tukar Voucher Diskon";
                body.innerHTML = `Kamu memiliki <strong class="text-amber-600">${poin} Pts</strong>. Tukar ${syaratPoin} poin dengan voucher potongan sewa sebesar <strong>Rp 50.000</strong>?`;
                formTukar.classList.remove('hidden');
            } else {
                const sisa = syaratPoin - poin;

                iconContainer.className = "w-20 h-20 mx-auto rounded-3xl flex items-center justify-center text-3xl mb-5 shadow-sm transition-all duration-300 bg-rose-100 text-rose-600 border border-rose-200";
                icon.innerText = "🔒";
                title.innerText = "Poin Belum Cukup";
                body.innerHTML = `Kamu memiliki <strong class="text-rose-600">${poin} Pts</strong>. Butuh <strong>${sisa} poin lagi</strong> untuk menukarkan voucher diskon sewa Rp 50.000. Bayar tagihan tepat waktu untuk menambah poin!`;
                formTukar.classList.add('hidden');
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('modalPoin');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        @if(auth()->user()->role == 'admin')
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('forecastChart');

                if (!ctx) return;

                const forecastingData = @json($forecasting ?? []);
                const labels = forecastingData.map(item => item.bulan);
                const values = forecastingData.map(item => Math.floor(item.estimasi));

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Proyeksi Pendapatan',
                            data: values,
                            borderColor: '#f43f5e',
                            backgroundColor: 'rgba(244, 63, 94, 0.08)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#f43f5e',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return ' Estimasi: Rp ' + context.raw.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    callback: function (value) {
                                        return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        @endif
    </script>
</x-app-layout>