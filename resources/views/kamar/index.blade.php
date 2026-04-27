<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span class="text-rose-500">🚪</span> Kelola Kamar Kos
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

            <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                
                <div class="flex gap-3 w-full sm:w-auto overflow-x-auto pb-2 sm:pb-0 hide-scrollbar">
                    <div class="bg-white border border-slate-100 px-5 py-3 rounded-2xl shadow-sm min-w-max">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kamar</p>
                        <p class="text-2xl font-black text-slate-800">{{ $kamars->count() }}</p>
                    </div>
                    <div class="bg-white border border-slate-100 px-5 py-3 rounded-2xl shadow-sm min-w-max">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Terisi</p>
                        <p class="text-2xl font-black text-rose-600">{{ $kamars->where('status', 'terisi')->count() }}</p>
                    </div>
                    <div class="bg-white border border-slate-100 px-5 py-3 rounded-2xl shadow-sm min-w-max">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kosong</p>
                        <p class="text-2xl font-black text-emerald-600">{{ $kamars->where('status', 'kosong')->count() }}</p>
                    </div>
                </div>
                
                <a href="{{ route('kamar.create') }}" class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 text-white font-bold py-3.5 sm:py-3 px-6 rounded-xl shadow-md transition duration-300 flex items-center justify-center gap-2 transform hover:-translate-y-0.5 whitespace-nowrap">
                    <span class="text-lg">➕</span> Tambah Kamar Baru
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($kamars as $kamar)
                    <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transform hover:-translate-y-1 transition duration-300 border border-slate-100 flex flex-col group relative">
                        
                        <div class="absolute top-4 right-4 z-10">
                            @if(strtolower($kamar->status) == 'kosong')
                                <span class="bg-emerald-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider shadow-md border border-emerald-400">
                                    🟢 Kosong
                                </span>
                            @else
                                <span class="bg-rose-500 text-white text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider shadow-md border border-rose-400">
                                    🔴 Terisi
                                </span>
                            @endif
                        </div>

                        <div class="h-32 {{ strtolower($kamar->status) == 'kosong' ? 'bg-gradient-to-br from-emerald-50 to-teal-100' : 'bg-gradient-to-br from-rose-50 to-orange-100' }} relative flex justify-center items-center overflow-hidden border-b border-slate-50">
                            <span class="text-6xl group-hover:scale-110 transition duration-500 {{ strtolower($kamar->status) == 'kosong' ? 'drop-shadow-[0_4px_4px_rgba(16,185,129,0.2)]' : 'drop-shadow-[0_4px_4px_rgba(244,63,94,0.2)] grayscale opacity-50' }}">🛏️</span>
                        </div>
                        
                        <div class="p-6 flex-1 flex flex-col">
                            <h3 class="text-xl font-black text-slate-800 mb-1">
                                {{ $kamar->nomor_kamar ?? $kamar->nama_kamar ?? $kamar->nama ?? 'Kamar Tanpa Nama' }}
                            </h3>
                            
                            <div class="mt-2 mb-6">
                                <span class="text-xs font-bold text-slate-400 uppercase">Harga Sewa</span>
                                <div class="text-xl font-black text-rose-600 mt-1">Rp {{ number_format($kamar->harga, 0, ',', '.') }}<span class="text-xs font-medium text-slate-500">/bln</span></div>
                            </div>
                            
                            <div class="mt-auto flex gap-3 pt-4 border-t border-slate-50">
                                <a href="{{ route('kamar.edit', $kamar->id) }}" class="flex-1 bg-slate-50 hover:bg-slate-100 text-slate-700 hover:text-slate-800 font-bold py-2.5 rounded-xl text-sm text-center transition duration-300 border border-slate-200">
                                    ✏️ Edit
                                </a>
                                <form action="{{ route('kamar.destroy', $kamar->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus kamar ini secara permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 font-bold py-2.5 rounded-xl text-sm transition duration-300 border border-rose-200">
                                        🗑️ Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-1 sm:col-span-2 lg:col-span-3 xl:col-span-4 text-center py-16 bg-white rounded-3xl border-2 border-dashed border-slate-200 shadow-sm mx-2">
                        <span class="text-6xl grayscale opacity-60">📭</span>
                        <h3 class="mt-4 text-xl font-bold text-slate-800">Belum Ada Data Kamar</h3>
                        <p class="mt-2 text-sm text-slate-500">Silakan klik tombol "Tambah Kamar Baru" untuk mulai mengatur kamar kos.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>