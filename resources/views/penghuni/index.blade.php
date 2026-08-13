<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-rose-500">👥</span> Daftar Penghuni Kos
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r shadow-sm flex items-center">
                    <span class="text-xl mr-2">✅</span> <p class="font-bold text-sm sm:text-base">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 rounded-r shadow-sm flex items-center">
                    <span class="text-xl mr-2">⚠️</span> <p class="font-bold text-sm sm:text-base">{{ session('error') }}</p>
                </div>
            @endif

            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="bg-white border border-slate-200 px-4 py-2 rounded-lg shadow-sm">
                    <p class="text-sm font-bold text-slate-800">Total Penghuni Aktif: <span class="text-xl text-rose-600">{{ $penghunis->count() }}</span></p>
                </div>
                
                <a href="{{ route('penghuni.create') }}" class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 sm:py-2.5 px-6 rounded-xl shadow-md transition duration-300 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                    <span class="text-lg">➕</span> Tambah Penghuni Baru
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-3xl sm:rounded-2xl border border-slate-100">
                <div class="p-4 sm:p-6 text-gray-900">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($penghunis as $p)
                        
                        {{-- 🆕 LOGIKA HITUNG DURASI TINGGAL (Carbon) --}}
                        @php 
                            // Menggunakan created_at sebagai acuan awal masuk kos. 
                            // Jika kamu punya kolom khusus (misal: tanggal_masuk), ganti bagian $p->created_at di bawah.
                            $tglMasuk = $p->created_at; 
                            $totalBulan = $tglMasuk->diffInMonths(\Carbon\Carbon::now());
                            
                            // Menghitung ordinal tahun ke-berapa dan bulan ke-berapa
                            $tahunKe = floor($totalBulan / 12) + 1;
                            $bulanKe = ($totalBulan % 12) + 1;
                        @endphp

                        <div class="bg-white p-5 rounded-2xl border border-slate-100 flex flex-col justify-between hover:border-rose-200 transition-all duration-300 shadow-sm hover:shadow-md">
                            
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-lg border border-rose-100 shadow-inner">
                                        {{ substr($p->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-sm">{{ $p->nama }}</h4>
                                        <span class="text-[10px] text-slate-500 font-bold bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100">
                                            {{ $p->kamar ? 'Kamar ' . $p->kamar->nomor_kamar : 'Belum Ada Kamar' }}
                                        </span>
                                    </div>
                                </div>
                                @if(isset($p->poin) && $p->poin > 0)
                                    <div class="text-[10px] text-amber-600 font-bold bg-amber-50 px-2 py-1 rounded-lg border border-amber-100">
                                        🏆 {{ $p->poin }} Poin
                                    </div>
                                @endif
                            </div>

                            <div class="mb-4 text-xs font-medium text-slate-600 space-y-2">
                                <p class="flex items-center gap-2">
                                    <span>👤</span> {{ $p->user ? $p->user->username : 'Belum ada akun' }}
                                </p>
                                @php $noWa = preg_replace('/^0/', '62', $p->no_hp); @endphp
                                <a href="https://wa.me/{{ $noWa }}" target="_blank" class="flex items-center gap-2 text-emerald-600 hover:text-emerald-700">
                                    <span>💬</span> {{ $p->no_hp }}
                                </a>
                                
                                {{-- 🆕 TAMPILAN BARU: Informasi Durasi Tinggal --}}
                                <p class="flex items-center gap-2 text-slate-500 border-t border-dashed border-slate-100 pt-2 mt-1">
                                    <span>📅</span> Periode: 
                                    <span class="text-rose-600 font-bold">Tahun ke-{{ $tahunKe }}</span>, 
                                    <span class="text-slate-700 font-bold">Bulan ke-{{ $bulanKe }}</span>
                                </p>
                            </div>

                            <div class="pt-4 border-t border-slate-50 flex gap-2">
                                <a href="{{ route('penghuni.edit', $p->id) }}" class="flex-1 text-center bg-slate-50 hover:bg-slate-800 text-slate-600 hover:text-white px-3 py-2 rounded-xl font-bold transition duration-300 text-xs border border-slate-200">
                                    Edit
                                </a>
                                
                                <form action="{{ route('penghuni.reset_password', $p->id) }}" method="POST" onsubmit="return confirm('Reset password {{ $p->nama }} menjadi 12345678?');">
                                    @csrf
                                    <button type="submit" class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-500 px-3 py-2 rounded-xl font-bold transition duration-300 text-xs border border-slate-200" title="Reset Password">
                                        🔑
                                    </button>
                                </form>

                                <form action="{{ route('penghuni.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus penghuni ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="flex-1 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white px-3 py-2 rounded-xl font-bold transition duration-300 text-xs border border-rose-200" title="Hapus">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-16 text-center border-2 border-dashed border-slate-200 rounded-3xl">
                            <div class="text-6xl mb-4 grayscale opacity-40">📭</div>
                            <p class="text-lg font-bold text-slate-800">Belum ada data penghuni</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>