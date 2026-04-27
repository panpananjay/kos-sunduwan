<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            ➕ Tambah Kamar Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100">
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-100 p-6 sm:p-10 text-center">
                    <span class="text-6xl drop-shadow-md inline-block mb-4">🛏️</span>
                    <h3 class="text-2xl font-black text-indigo-900">Formulir Kamar Baru</h3>
                    <p class="text-sm text-gray-500 mt-1">Silakan isi detail kamar kos yang akan ditambahkan ke dalam sistem.</p>
                </div>

                <div class="p-6 sm:p-10 bg-white">
                    <form method="POST" action="{{ route('kamar.store') }}">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-sm font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Nomor / Nama Kamar</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-400">🏷️</span>
                                </div>
                                <input type="text" name="nama_kamar" class="bg-gray-50 border border-gray-200 text-gray-900 text-base rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-11 p-3.5 transition duration-300" placeholder="Contoh: Kamar A1, Paviliun B..." required autofocus>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Harga per Bulan</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="font-bold text-gray-500">Rp</span>
                                </div>
                                <input type="number" name="harga" class="bg-gray-50 border border-gray-200 text-gray-900 text-base font-bold rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-12 p-3.5 transition duration-300" placeholder="1000000" required>
                            </div>
                            <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">ℹ️ Tulis angka saja tanpa titik (Contoh: 1000000)</p>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Status Awal</label>
                            <select name="status" class="bg-gray-50 border border-gray-200 text-gray-900 text-base rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block w-full p-3.5 transition duration-300 cursor-pointer" required>
                                <option value="kosong" class="font-medium text-emerald-600">🟢 Kosong (Siap Dihuni)</option>
                                <option value="terisi" class="font-medium text-rose-600">🔴 Terisi</option>
                            </select>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4 mt-8 pt-6 border-t border-gray-100">
                            <a href="{{ route('kamar.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 bg-gray-100 border border-transparent rounded-xl font-bold text-gray-700 hover:bg-gray-200 hover:text-gray-900 transition duration-300">
                                ⬅️ Kembali
                            </a>
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3.5 bg-indigo-600 border border-transparent rounded-xl font-black text-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 gap-2">
                                💾 Simpan Kamar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>