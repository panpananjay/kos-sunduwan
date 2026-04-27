<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight flex items-center gap-2">
            <a href="{{ route('pengaduan.index') }}" class="text-slate-400 hover:text-rose-500 transition-colors mr-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <span class="text-rose-500">📝</span> {{ __('Buat Laporan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white p-6 sm:p-10 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-rose-500 to-fuchsia-600"></div>

                <div class="mb-8 mt-2 text-center sm:text-left">
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Form Pengaduan</h3>
                    <p class="text-slate-500 mt-1 text-sm font-medium">Ceritakan secara detail kendala fasilitas yang kamu alami agar Admin bisa segera menindaklanjutinya.</p>
                </div>

                <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="judul" :value="__('Judul Keluhan / Masalah')" class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2" />
                        <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 font-bold focus:ring-rose-500 focus:border-rose-500 shadow-sm py-3" placeholder="Contoh: Kran Air Kamar Mandi Bocor" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('judul')" />
                    </div>

                    <div>
                        <x-input-label for="deskripsi" :value="__('Detail Keluhan')" class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2" />
                        <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 font-medium text-slate-700 focus:ring-rose-500 focus:border-rose-500 shadow-sm p-3" placeholder="Ceritakan detail masalahnya di sini..." required></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('deskripsi')" />
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <x-input-label for="foto" :value="__('Foto Bukti (Opsional)')" class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2" />
                        
                        <input id="foto" name="foto" type="file" accept="image/jpeg, image/png, image/jpg" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:tracking-widest file:bg-rose-100 file:text-rose-600 hover:file:bg-rose-200 transition-all border border-slate-200 rounded-xl bg-white p-1 cursor-pointer">
                        
                        <p class="text-[10px] text-slate-400 mt-2 font-bold uppercase tracking-widest">* Maksimal ukuran foto 2MB (Format: JPG, JPEG, PNG)</p>
                        <x-input-error class="mt-2" :messages="$errors->get('foto')" />
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                        <a href="{{ route('pengaduan.index') }}" class="w-full sm:w-auto text-center px-6 py-3.5 rounded-xl font-bold text-xs uppercase tracking-widest text-slate-500 hover:bg-slate-100 transition duration-300">
                            Batal
                        </a>
                        <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-rose-500 to-fuchsia-600 hover:from-rose-600 hover:to-fuchsia-700 text-white font-black py-3.5 px-8 rounded-xl shadow-lg shadow-rose-200 transition-all duration-300 transform hover:-translate-y-0.5 text-xs uppercase tracking-widest flex items-center justify-center gap-2">
                            <span>🚀</span> Kirim Laporan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>