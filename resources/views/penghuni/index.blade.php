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
                    
                    <div class="overflow-x-auto pb-4 custom-scrollbar">
                        <table class="w-full text-left table-auto min-w-[900px]">
                            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-widest font-bold border-b border-slate-100">
                                <tr>
                                    <th class="py-4 px-6 rounded-tl-xl whitespace-nowrap">Nama Penghuni</th>
                                    <th class="py-4 px-6 whitespace-nowrap">Kamar</th>
                                    <th class="py-4 px-6 whitespace-nowrap">No. HP / WA</th>
                                    <th class="py-4 px-6 whitespace-nowrap">Akun Login</th>
                                    <th class="py-4 px-6 text-center rounded-tr-xl whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm sm:text-base">
                                @forelse($penghunis as $p)
                                <tr class="hover:bg-rose-50/30 transition duration-150">
                                    
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-lg shadow-inner border border-rose-100">
                                                {{ substr($p->nama, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">{{ $p->nama }}</p>
                                                @if(isset($p->poin) && $p->poin > 0)
                                                    <div class="text-xs text-amber-600 font-bold flex items-center gap-1 mt-0.5 bg-amber-50 w-max px-2 py-0.5 rounded-full border border-amber-100">
                                                        🏆 {{ $p->poin }} Poin
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="bg-slate-100 text-slate-700 font-bold px-3 py-1.5 rounded-lg border border-slate-200 text-sm shadow-sm">
                                            {{ $p->kamar ? $p->kamar->nomor_kamar : 'Belum Ada Kamar' }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-6 font-medium text-gray-600 whitespace-nowrap">
                                        @php 
                                            $noWa = preg_replace('/^0/', '62', $p->no_hp); 
                                        @endphp
                                        <a href="https://wa.me/{{ $noWa }}" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition duration-300">
                                            💬 {{ $p->no_hp }}
                                        </a>
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap text-gray-500 font-medium">
                                        {{ $p->user ? $p->user->username : '-' }}
                                    </td>

                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('penghuni.edit', $p->id) }}" class="inline-flex items-center justify-center bg-slate-50 hover:bg-slate-800 text-slate-600 hover:text-white px-3 py-2 rounded-xl font-bold transition duration-300 text-sm border border-slate-200 shadow-sm" title="Edit Data & Pindah Kamar">
                                                ✏️ Edit
                                            </a>
                                            
                                            <form action="{{ route('penghuni.reset_password', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Reset password {{ $p->nama }} menjadi 12345678?');">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-500 px-3 py-2 rounded-xl font-bold transition duration-300 text-sm border border-slate-200 shadow-sm" title="Kembalikan Password ke 12345678">
                                                    🔑 Reset
                                                </button>
                                            </form>

                                            <form action="{{ route('penghuni.destroy', $p->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus penghuni ini permanen? Kamarnya akan otomatis menjadi kosong.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center bg-rose-50 hover:bg-rose-600 text-rose-700 px-3 py-2 rounded-xl font-bold transition duration-300 text-sm border border-rose-200 shadow-sm" title="Hapus Permanen">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-16 text-center text-gray-500">
                                        <div class="text-6xl mb-4 grayscale opacity-40">📭</div>
                                        <p class="text-xl font-bold text-slate-800">Belum ada data penghuni</p>
                                        <p class="text-sm mt-1">Silakan klik tombol "Tambah Penghuni Baru" di atas.</p>
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
</x-app-layout>