<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-rose-500">💳</span> 
            {{ auth()->user()->role == 'admin' ? __('Manajemen Keuangan & Tagihan') : __('Tagihan Saya') }}
        </h2>
    </x-slot>

    {{-- Script Snap Midtrans --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Flash Messages / Alerts --}}
            @if(session('success'))
                <div id="admin-alert" class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r shadow-sm flex items-center transition-opacity duration-500">
                    <span class="text-xl mr-2">✅</span> 
                    <p class="font-bold text-sm sm:text-base">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r shadow-sm flex items-center">
                    <span class="text-xl mr-2">⚠️</span> 
                    <p class="font-bold text-sm sm:text-base">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Admin Search Bar --}}
            @if(auth()->user()->role == 'admin')
            <div class="bg-white p-6 rounded-2xl mb-8 border border-slate-100 flex flex-col md:flex-row items-center justify-between shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center gap-4 mb-4 md:mb-0">
                    <div class="bg-rose-50 text-rose-500 w-14 h-14 flex items-center justify-center rounded-2xl text-2xl border border-rose-100 shadow-inner">
                        🛡️
                    </div>
                    <div>
                        <h4 class="font-black text-slate-800 text-lg tracking-tight">Cari Data Pembayaran</h4>
                        <p class="text-xs font-medium text-slate-500 mt-0.5">Masukkan Nama Penghuni untuk filter cepat</p>
                    </div>
                </div>
                
                <form action="{{ route('tagihan.index') }}" method="GET" class="flex w-full md:w-1/2 gap-3">
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari Nama Penghuni..." class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-rose-500 focus:border-rose-500 shadow-inner px-4 bg-slate-50">
                    <button type="submit" class="bg-slate-800 hover:bg-rose-600 text-white font-black py-2 px-6 rounded-xl shadow-md transition duration-300 transform hover:-translate-y-0.5">
                        CEK
                    </button>
                    @if(request('cari'))
                        <a href="{{ route('tagihan.index') }}" class="bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-600 font-bold py-2 px-4 rounded-xl transition flex items-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
            @endif

            <div class="grid grid-cols-1 {{ auth()->user()->role == 'admin' ? 'lg:grid-cols-3' : '' }} gap-6 mb-8">
                {{-- Filter Section --}}
                <div class="{{ auth()->user()->role == 'admin' ? 'lg:col-span-2' : '' }} bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-slate-50 p-3 rounded-xl text-slate-500 border border-slate-100 shadow-inner">🔍</div>
                        <div>
                            <h3 class="font-black text-slate-800 text-lg">Filter Periode</h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">
                                {{ auth()->user()->role == 'admin' ? 'Pantau arus kas masuk tiap bulan.' : 'Gunakan filter untuk melihat riwayat tagihan lama.' }}
                            </p>
                        </div>
                    </div>
                    
                    <form action="{{ route('tagihan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <select name="bulan" class="rounded-xl border-slate-200 bg-slate-50 text-sm font-bold text-slate-700 focus:ring-rose-500 focus:border-rose-500 transition duration-200">
                            <option value="">Semua Bulan</option>
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $b)
                                <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>

                        <input type="number" name="tahun" value="{{ request('tahun', date('Y')) }}" class="rounded-xl border-slate-200 bg-slate-50 text-sm font-bold text-slate-700 text-center focus:ring-rose-500 focus:border-rose-500">

                        <select name="status" class="rounded-xl border-slate-200 bg-slate-50 text-sm font-bold text-slate-700 focus:ring-rose-500 focus:border-rose-500">
                            <option value="">Semua Status</option>
                            <option value="belum_bayar" {{ request('status') == 'belum_bayar' ? 'selected' : '' }}>❌ Belum Bayar</option>
                            <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>✅ Lunas</option>
                        </select>

                        <button type="submit" class="bg-slate-800 hover:bg-rose-600 text-white font-bold py-2.5 rounded-xl transition duration-300 shadow-md transform hover:-translate-y-0.5">
                            Tampilkan
                        </button>
                    </form>
                </div>

                {{-- Action Card Admin --}}
                @if(auth()->user()->role == 'admin')
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-center items-center text-center">
                    <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mb-3 text-xl shadow-inner border border-rose-100">📢</div>
                    <h3 class="font-black text-slate-800 text-base mb-1">Terbitkan Tagihan</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Otomatis Kirim WhatsApp</p>
                    
                    <form action="{{ route('tagihan.generate') }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="bulan" value="{{ request('bulan', \Carbon\Carbon::now()->translatedFormat('F')) }}">
                        <input type="hidden" name="tahun" value="{{ request('tahun', \Carbon\Carbon::now()->year) }}">
                        
                        <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-black py-3 rounded-xl transition duration-300 shadow-lg shadow-rose-100 transform hover:-translate-y-1 text-xs uppercase tracking-widest" onclick="return confirm('Sistem akan membuat tagihan massal & mengirim WA. Lanjutkan?')">
                            Mulai Generate
                        </button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Main Table Card --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100">
                <div class="p-6">
                    <div class="mb-8 flex justify-between items-center border-b border-slate-50 pb-6">
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Daftar Tagihan Kos</h3>
                        <div class="bg-white border border-slate-200 px-4 py-2 rounded-lg shadow-sm">
                            <p class="text-sm font-bold text-slate-800">Total Data: <span class="text-xl text-rose-600">{{ $tagihans->count() }}</span></p>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto pb-4 custom-scrollbar">
                        <table class="w-full text-left table-auto min-w-[950px]">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-widest font-black border-b border-slate-100">
                                <tr>
                                    <th class="py-5 px-6 rounded-tl-xl whitespace-nowrap">Bulan & Tahun</th>
                                    @if(auth()->user()->role == 'admin')
                                        <th class="py-5 px-6 whitespace-nowrap">Penghuni</th>
                                    @endif
                                    <th class="py-5 px-6 text-center whitespace-nowrap">Jumlah Tagihan</th>
                                    <th class="py-5 px-6 text-center whitespace-nowrap">Status Pembayaran</th>
                                    <th class="py-5 px-6 text-center rounded-tr-xl whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($tagihans as $tagihan)
                                <tr class="hover:bg-rose-50/20 transition duration-150">
                                    <td class="py-5 px-6">
                                        <div class="flex flex-col">
                                            <span class="font-black text-slate-800 text-base leading-tight">{{ $tagihan->bulan }}</span>
                                            <span class="text-[11px] font-bold text-slate-400 tracking-widest uppercase">{{ $tagihan->tahun }}</span>
                                        </div>
                                    </td>
                                    
                                    @if(auth()->user()->role == 'admin')
                                    <td class="py-5 px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-lg shadow-inner border border-rose-100">
                                                {{ substr($tagihan->penghuni->nama, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">{{ $tagihan->penghuni->nama }}</p>
                                                <p class="text-[10px] bg-slate-100 text-slate-500 font-bold px-2.5 py-0.5 rounded-md border border-slate-200 w-max mt-0.5">
                                                    Kamar {{ $tagihan->penghuni->kamar?->nomor_kamar ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    @endif

                                    <td class="py-5 px-6 text-center">
                                        <span class="font-black text-rose-600 text-lg">Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</span>
                                    </td>

                                    <td class="py-5 px-6 text-center">
                                        @if($tagihan->status == 'belum_bayar')
                                            <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-600 font-bold px-4 py-2 rounded-xl border border-rose-100 text-xs shadow-sm">
                                                ❌ Belum Lunas
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-600 font-bold px-4 py-2 rounded-xl border border-emerald-100 text-xs shadow-sm">
                                                ✅ Lunas
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-5 px-6">
                                        <div class="flex items-center justify-center gap-2">
                                            @if(auth()->user()->role == 'penghuni')
                                                @if($tagihan->status == 'belum_bayar')
                                                    <button onclick="payNow('{{ $tagihan->id }}')" id="pay-button-{{ $tagihan->id }}" class="bg-rose-600 hover:bg-rose-700 text-white px-6 py-2.5 rounded-xl font-black transition duration-300 text-sm shadow-md shadow-rose-200 transform hover:-translate-y-0.5">
                                                        💳 Bayar Sekarang
                                                    </button>
                                                @else
                                                    <a href="{{ asset('storage/invoices/Invoice_' . $tagihan->id . '.png') }}" target="_blank" class="bg-slate-50 hover:bg-slate-800 text-slate-600 hover:text-white px-5 py-2.5 rounded-xl font-bold transition duration-300 text-sm border border-slate-200 flex items-center gap-2">
                                                        <span>📄</span> Lihat Invoice
                                                    </a>
                                                @endif
                                            @endif

                                            @if(auth()->user()->role == 'admin')
                                                @if($tagihan->status == 'belum_bayar')
                                                    <form action="{{ route('tagihan.verifikasi', $tagihan->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="bg-slate-50 hover:bg-slate-800 text-slate-600 hover:text-white px-4 py-2.5 rounded-xl font-bold transition border border-slate-200 text-sm shadow-sm" onclick="return confirm('Verifikasi pembayaran ini secara manual?')">
                                                            ✅ Verifikasi
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('tagihan.destroy', $tagihan->id) }}" method="POST" onsubmit="return confirm('Hapus permanen data tagihan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white px-3.5 py-2.5 rounded-xl font-bold transition border border-rose-200 text-sm shadow-sm">
                                                        🗑️ Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center">
                                        <div class="text-6xl mb-4 grayscale opacity-30">📂</div>
                                        <p class="text-xl font-black text-slate-800 uppercase tracking-widest">Tidak Ada Data</p>
                                        <p class="text-sm font-medium text-slate-400 mt-1">Coba ganti filter periode atau buat tagihan baru.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Scripts --}}
    <script type="text/javascript">
        function payNow(tagihanId) {
            const button = document.getElementById('pay-button-' + tagihanId);
            button.disabled = true;
            button.innerHTML = '⌛ Memproses...';

            fetch(`/tagihan/${tagihanId}/bayar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) { location.reload(); },
                        onPending: function(result) { location.reload(); },
                        onError: function(result) { 
                            alert("Pembayaran gagal!"); 
                            button.disabled = false; 
                            button.innerHTML = '💳 Bayar Sekarang'; 
                        },
                        onClose: function() { 
                            button.disabled = false; 
                            button.innerHTML = '💳 Bayar Sekarang'; 
                        }
                    });
                } else {
                    alert('Gagal mendapatkan token pembayaran.');
                    button.disabled = false;
                    button.innerHTML = '💳 Bayar Sekarang';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.innerHTML = '💳 Bayar Sekarang';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('admin-alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.opacity = '0'; 
                    setTimeout(() => alert.remove(), 500); 
                }, 3000);
            }
        });
    </script>
</x-app-layout>