<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            ✏️ Edit Data Kamar
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-100 p-6 sm:p-10 text-center">
                    <span class="text-6xl drop-shadow-md inline-block mb-4">🛠️</span>
                    <h3 class="text-2xl font-black text-amber-900">Ubah Profil Kamar</h3>
                    <p class="text-sm text-gray-500 mt-1">Perbarui harga, status, atau nama kamar ini.</p>
                </div>

                <div class="p-6 sm:p-10 bg-white">
                    <form method="POST" action="{{ route('kamar.update', $kamar->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-6">
                            <label class="block text-sm font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Nomor / Nama Kamar</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-400">🏷️</span>
                                </div>
                                <input type="text" name="nomor_kamar" value="{{ old('nomor_kamar', $kamar->nomor_kamar) }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-base font-bold rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full pl-11 p-3.5 transition duration-300" required>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Harga per Bulan</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="font-bold text-gray-500">Rp</span>
                                </div>
                                <input type="number" name="harga" value="{{ old('harga', $kamar->harga) }}" class="bg-gray-50 border border-gray-200 text-gray-900 text-base font-bold rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full pl-12 p-3.5 transition duration-300" required>
                            </div>
                        </div>

                        <div class="mb-8 mt-10 p-6 bg-slate-50 border border-slate-200 rounded-2xl">
                            <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">📸 Manajemen Foto Galeri</h3>
                            <p class="text-xs text-slate-500 mb-6">Kosongkan jika tidak ingin mengubah foto saat ini. Ukuran maksimal yang disarankan: 2MB per foto.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">🛏️ Ruang Utama</label>
                                    @if($kamar->foto_utama)
                                        <div class="relative w-full h-32 mb-3 rounded-xl overflow-hidden shadow-sm border border-slate-200 group">
                                            <img src="{{ asset('storage/' . $kamar->foto_utama) }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-110" alt="Foto Utama Saat Ini">
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                                                <span class="text-white text-xs font-bold">Foto Saat Ini</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-full h-32 mb-3 rounded-xl bg-slate-200 border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400">
                                            <span class="text-2xl mb-1">🖼️</span>
                                            <span class="text-[10px] uppercase font-bold tracking-widest">Belum Ada</span>
                                        </div>
                                    @endif
                                    <input type="file" name="foto_utama" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer" accept="image/*">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">🍳 Dapur Kecil</label>
                                    @if($kamar->foto_dapur)
                                        <div class="relative w-full h-32 mb-3 rounded-xl overflow-hidden shadow-sm border border-slate-200 group">
                                            <img src="{{ asset('storage/' . $kamar->foto_dapur) }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-110" alt="Foto Dapur Saat Ini">
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                                                <span class="text-white text-xs font-bold">Foto Saat Ini</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-full h-32 mb-3 rounded-xl bg-slate-200 border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400">
                                            <span class="text-2xl mb-1">🖼️</span>
                                            <span class="text-[10px] uppercase font-bold tracking-widest">Belum Ada</span>
                                        </div>
                                    @endif
                                    <input type="file" name="foto_dapur" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer" accept="image/*">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">🚿 Kamar Mandi</label>
                                    @if($kamar->foto_kamar_mandi)
                                        <div class="relative w-full h-32 mb-3 rounded-xl overflow-hidden shadow-sm border border-slate-200 group">
                                            <img src="{{ asset('storage/' . $kamar->foto_kamar_mandi) }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-110" alt="Foto Kamar Mandi Saat Ini">
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                                                <span class="text-white text-xs font-bold">Foto Saat Ini</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-full h-32 mb-3 rounded-xl bg-slate-200 border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400">
                                            <span class="text-2xl mb-1">🖼️</span>
                                            <span class="text-[10px] uppercase font-bold tracking-widest">Belum Ada</span>
                                        </div>
                                    @endif
                                    <input type="file" name="foto_kamar_mandi" class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="mb-8">
                            <label class="block text-sm font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Status Saat Ini</label>
                            <select name="status" class="bg-gray-50 border border-gray-200 text-gray-900 text-base rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-3.5 transition duration-300 cursor-pointer" required>
                                <option value="kosong" class="font-medium text-emerald-600" {{ strtolower($kamar->status) == 'kosong' ? 'selected' : '' }}>🟢 Kosong (Siap Dihuni)</option>
                                <option value="terisi" class="font-medium text-rose-600" {{ strtolower($kamar->status) == 'terisi' ? 'selected' : '' }}>🔴 Terisi</option>
                            </select>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4 mt-8 pt-6 border-t border-gray-100">
                            <a href="{{ route('kamar.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 bg-gray-100 border border-transparent rounded-xl font-bold text-gray-700 hover:bg-gray-200 hover:text-gray-900 transition duration-300">
                                ⬅️ Batal / Kembali
                            </a>
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3.5 bg-amber-500 border border-transparent rounded-xl font-black text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 gap-2">
                                🔄 Update Kamar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>