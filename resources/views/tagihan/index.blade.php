<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight flex items-center gap-2">
            <span class="text-rose-500">💳</span> 
            {{ auth()->user()->role == 'admin' ? __('Manajemen Keuangan & Tagihan') : __('Tagihan Saya') }}
        </h2>
    </x-slot>

    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div id="admin-alert" class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-2xl shadow-sm flex items-center transition-opacity duration-500">
                    <span class="text-xl mr-3">✅</span> 
                    <p class="font-bold text-sm sm:text-base">{{ session('success') }}</p>
                </div>
            @endif

            @if(auth()->user()->role == 'admin')
            <div class="bg-white p-6 rounded-[2rem] mb-8 border border-slate-100 flex flex-col md:flex-row items-center justify-between shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center gap-4 mb-4 md:mb-0">
                    <div class="bg-rose-50 text-rose-500 w-14 h-14 flex items-center justify-center rounded-2xl text-2xl">
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
                <div class="{{ auth()->user()->role == 'admin' ? 'lg:col-span-2' : '' }} bg-white p-6 sm:p-8 rounded-[2rem] shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-slate-50 p-3 rounded-xl text-slate-500">🔍</div>
                        <div>
                            <h3 class="font-black text-slate-800 text-lg">Filter Periode</h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">
                                {{ auth()->user()->role == 'admin' ? 'Pantau arus kas masuk tiap bulan.' : 'Gunakan filter untuk melihat riwayat tagihan lama.' }}
                            </p>
                        </div>
                    </div>
                    
                    <form action="{{ route('tagihan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <select name="bulan" class="rounded-xl border-slate-200 bg-slate-50 text-sm font-bold text-slate-700 focus:ring-rose-500 focus:border-rose-500">
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

                        <button type="submit" class="bg-slate-800 hover:bg-rose-600 text-white font-bold py-2.5 rounded-xl transition duration-300 shadow-sm transform hover:-translate-y-0.5">
                            Tampilkan
                        </button>
                    </form>
                </div>

                @if(auth()->user()->role == 'admin')
                <div class="bg-gradient-to-br from-rose-500 to-fuchsia-600 p-8 rounded-[2rem] shadow-lg shadow-rose-200 text-white relative overflow-hidden flex flex-col justify-center">
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-white/20 p-2.5 rounded-xl backdrop-blur-sm text-lg">⚡</div>
                            <h3 class="font-black text-xl tracking-tight">Kirim Tagihan</h3>
                        </div>
                        <p class="text-xs text-rose-100 mb-6 leading-relaxed font-medium">Terbitkan tagihan baru dan kirim notifikasi WhatsApp otomatis ke semua penghuni.</p>
                        
                        <form action="{{ route('tagihan.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="bulan" value="{{ request('bulan', \Carbon\Carbon::now()->translatedFormat('F')) }}">
                            <input type="hidden" name="tahun" value="{{ request('tahun', \Carbon\Carbon::now()->year) }}">
                            
                            <button type="submit" class="w-full bg-white text-rose-600 hover:bg-rose-50 hover:text-rose-700 font-black py-3.5 rounded-xl transition duration-300 shadow-lg transform hover:-translate-y-1 text-sm uppercase tracking-wider" onclick="return confirm('Sistem akan membuat tagihan massal & mengirim WA. Lanjutkan?')">
                                📢 Terbitkan Tagihan
                            </button>
                        </form>
                    </div>
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-[2rem] border border-slate-100">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6 border-b border-slate-50 pb-5">
                        <h3 class="text-xl font-black text-slate-800">Daftar Tagihan</h3>
                        <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total:</span>
                            <span class="text-rose-600 font-black text-sm">{{ $tagihans->count() }} Data</span>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left table-auto min-w-[850px]">
                            <thead class="bg-slate-50 text-slate-400 text-xs uppercase tracking-widest font-bold">
                                <tr>
                                    <th class="py-4 px-6 rounded-l-xl">Periode</th>
                                    <th class="py-4 px-6">Nama & Kamar</th>
                                    <th class="py-4 px-6">Total</th>
                                    <th class="py-4 px-6">Status</th>
                                    <th class="py-4 px-6 text-center rounded-r-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($tagihans as $tagihan)
                                <tr class="hover:bg-rose-50/30 transition duration-150">
                                    <td class="py-5 px-6 font-bold text-slate-700">{{ $tagihan->bulan }} {{ $tagihan->tahun }}</td>
                                    <td class="py-5 px-6">
                                        <div class="flex flex-col">
                                            <span class="font-black text-slate-800 text-base">{{ $tagihan->penghuni->nama }}</span>
                                            <span class="text-[10px] bg-slate-100 text-slate-500 font-bold px-2.5 py-1 rounded-md w-max mt-1.5 uppercase">
                                                No. {{ $tagihan->penghuni->kamar?->nomor_kamar ?? '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-5 px-6 font-black text-rose-600 text-lg">Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</td>
                                    <td class="py-5 px-6">
                                        @if($tagihan->status == 'belum_bayar')
                                            <span class="bg-rose-50 text-rose-600 font-bold px-3 py-1.5 rounded-lg text-xs border border-rose-100">❌ Belum Bayar</span>
                                        @else
                                            <span class="bg-emerald-50 text-emerald-600 font-bold px-3 py-1.5 rounded-lg text-xs border border-emerald-100">✅ Lunas</span>
                                        @endif
                                    </td>
                                    <td class="py-5 px-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($tagihan->status == 'belum_bayar' && auth()->user()->role == 'penghuni')
                                                <button onclick="payNow('{{ $tagihan->id }}')" id="pay-button-{{ $tagihan->id }}" class="bg-rose-600 hover:bg-rose-700 text-white px-5 py-2 rounded-xl font-black transition duration-300 text-sm shadow-md shadow-rose-200 transform hover:-translate-y-0.5">
                                                    💳 Bayar Sekarang
                                                </button>
                                            @endif

                                            <a href="{{ route('tagihan.show', $tagihan->id) }}" class="bg-white hover:bg-slate-800 text-slate-600 hover:text-white px-4 py-2 rounded-xl font-bold transition duration-300 text-sm border border-slate-200">
                                                🔍 Detail
                                            </a>

                                            @if($tagihan->status == 'lunas')
                                                <a href="{{ asset('storage/invoices/Invoice_' . $tagihan->id . '.png') }}" download class="bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white px-4 py-2 rounded-xl font-bold transition duration-300 text-sm border border-emerald-200">
                                                    ⬇️ Invoice
                                                </a>
                                            @endif

                                            @if(auth()->user()->role == 'admin')
                                                <form action="{{ route('tagihan.destroy', $tagihan->id) }}" method="POST" onsubmit="return confirm('Hapus permanen?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white px-3 py-2 rounded-xl font-bold transition duration-300 text-sm border border-rose-100">
                                                        🗑️
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-20 text-center text-slate-400 font-medium">Tidak ada data ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        function payNow(tagihanId) {
            const button = document.getElementById('pay-button-' + tagihanId);
            button.disabled = true;
            button.innerHTML = '⌛ Memproses...';

            // Panggil rute POST yang kita buat di controller
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
                        onError: function(result) { alert("Pembayaran gagal!"); button.disabled = false; button.innerHTML = '💳 Bayar Sekarang'; },
                        onClose: function() { button.disabled = false; button.innerHTML = '💳 Bayar Sekarang'; }
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

        // Auto hide alert
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