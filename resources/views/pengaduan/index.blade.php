<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight flex items-center gap-2">
            <span class="text-rose-500">📢</span> {{ __('Sistem Pengaduan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Daftar Laporan</h3>
                    <p class="text-slate-500 mt-1 text-sm font-medium">
                        @if(auth()->user()->role == 'admin')
                            Pantau dan tanggapi keluhan dari fasilitas kos di sini.
                        @else
                            Laporkan kendala atau masalah fasilitas kos di sini.
                        @endif
                    </p>
                </div>

                @if(auth()->user()->role != 'admin')
                    <a href="{{ route('pengaduan.create') }}" class="inline-flex justify-center items-center gap-2 bg-gradient-to-r from-rose-500 to-fuchsia-600 hover:from-rose-600 hover:to-fuchsia-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-rose-200 transition-all duration-300 transform hover:-translate-y-1 text-sm">
                        ➕ Buat Laporan Baru
                    </a>
                @endif
            </div>

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-600 px-6 py-4 rounded-2xl font-bold flex items-center gap-3 shadow-sm">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if($pengaduans->isEmpty())
                <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 text-center shadow-sm">
                    <div class="text-6xl mb-4">📭</div>
                    <h4 class="text-xl font-black text-slate-700">Belum Ada Laporan</h4>
                    <p class="text-slate-500 mt-2 font-medium">
                        @if(auth()->user()->role == 'admin')
                            Semua fasilitas kos sedang dalam keadaan aman terkendali.
                        @else
                            Kamu belum pernah membuat laporan pengaduan apapun.
                        @endif
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($pengaduans as $laporan)
                        <div class="bg-white p-6 sm:p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow duration-300 relative overflow-hidden">
                            
                            <div class="absolute top-0 left-0 w-full h-1.5 
                                {{ $laporan->status == 'proses' ? 'bg-rose-500' : ($laporan->status == 'diproses' ? 'bg-amber-400' : 'bg-emerald-500') }}">
                            </div>

                            <div>
                                <div class="flex justify-between items-start mb-4 gap-2 mt-2">
                                    <div>
                                        <h4 class="text-lg font-black text-slate-800 leading-tight">{{ $laporan->judul }}</h4>
                                        <p class="text-[11px] font-bold text-slate-400 mt-1.5 uppercase tracking-widest">
                                            @if(auth()->user()->role == 'admin')
                                                <span class="text-rose-500">{{ $laporan->user->name }}</span> • 
                                            @endif
                                            {{ $laporan->created_at->format('d M Y') }}
                                        </p>
                                    </div>
                                    
                                    @if($laporan->status == 'proses')
                                        <span class="bg-rose-50 text-rose-600 border border-rose-200 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Proses</span>
                                    @elseif($laporan->status == 'diproses')
                                        <span class="bg-amber-50 text-amber-600 border border-amber-200 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Diproses</span>
                                    @else
                                        <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Selesai</span>
                                    @endif
                                </div>

                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 mb-4 text-sm text-slate-600 font-medium">
                                    {{ $laporan->deskripsi }}
                                </div>

                                @if($laporan->foto)
                                    <div class="mb-5">
                                        <a href="{{ asset('storage/' . $laporan->foto) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-rose-500 hover:text-rose-700 bg-rose-50 px-3 py-1.5 rounded-lg transition">
                                            <span>📎</span> Lihat Bukti Foto
                                        </a>
                                    </div>
                                @endif

                                @if($laporan->tanggapan_admin)
                                    <div class="mb-4 p-4 bg-rose-50 rounded-2xl border border-rose-100">
                                        <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-1">Balasan Admin:</p>
                                        <p class="text-sm font-bold text-slate-700">{{ $laporan->tanggapan_admin }}</p>
                                    </div>
                                @endif
                            </div>

                            @if(auth()->user()->role == 'admin')
                                <div class="mt-4 pt-5 border-t border-slate-100">
                                    <form action="{{ route('pengaduan.respon', $laporan->id) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="col-span-2">
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Update Status</label>
                                                <select name="status" class="w-full text-xs rounded-xl border-slate-200 bg-white focus:ring-rose-500 focus:border-rose-500 font-bold text-slate-700">
                                                    <option value="proses" {{ $laporan->status == 'proses' ? 'selected' : '' }}>⏳ Proses</option>
                                                    <option value="diproses" {{ $laporan->status == 'diproses' ? 'selected' : '' }}>🔧 Diproses</option>
                                                    <option value="selesai" {{ $laporan->status == 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
                                                </select>
                                            </div>

                                            <div class="col-span-2">
                                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tanggapan</label>
                                                <textarea name="tanggapan_admin" rows="2" class="w-full text-xs rounded-xl border-slate-200 bg-white focus:ring-rose-500 focus:border-rose-500 font-medium text-slate-700" placeholder="Ketik balasan...">{{ $laporan->tanggapan_admin }}</textarea>
                                            </div>
                                        </div>

                                        <button type="submit" class="w-full bg-slate-800 hover:bg-black text-white text-[10px] sm:text-xs font-black py-3 rounded-xl transition duration-300 uppercase tracking-widest shadow-md">
                                            Kirim Respon
                                        </button>
                                    </form>

                                    <div class="mt-4 flex justify-end">
                                        <form action="{{ route('pengaduan.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus laporan ini permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-[10px] font-bold text-slate-400 hover:text-rose-500 transition-colors flex items-center gap-1 uppercase tracking-tighter">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Bersihkan Laporan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>