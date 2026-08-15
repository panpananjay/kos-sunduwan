<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            ✏️ Edit Data Kamar
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-100 p-6 sm:p-10 text-center">
                    <span class="text-6xl drop-shadow-md inline-block mb-4">🛠️</span>
                    <h3 class="text-2xl font-black text-amber-900">Ubah Profil Kamar</h3>
                    <p class="text-sm text-gray-500 mt-1">Perbarui tipe, nomor, harga, fasilitas, dan foto galeri kamar ini.</p>
                </div>

                <div class="p-6 sm:p-10 bg-white">
                    <form method="POST" action="{{ route('kamar.update', $kamar->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Tipe Kamar</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-400">🏷️</span>
                                    </div>
                                    <select name="tipe_kamar" class="bg-gray-100 border border-gray-200 text-gray-500 text-base font-bold rounded-xl block w-full pl-11 p-3.5 cursor-not-allowed" disabled>
                                        <option value="Isian" {{ $kamar->tipe_kamar == 'Isian' ? 'selected' : '' }}>Isian</option>
                                        <option value="Kosongan" {{ $kamar->tipe_kamar == 'Kosongan' ? 'selected' : '' }}>Kosongan</option>
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1.5">*Tipe kamar otomatis ditentukan berdasarkan pilihan fasilitas di bawah.</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Nomor Kamar</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-gray-400 font-bold">#</span>
                                    </div>
                                    <input type="text" name="nomor_kamar" value="{{ old('nomor_kamar', $kamar->nomor_kamar) }}" placeholder="Contoh: 01" class="bg-gray-50 border border-gray-200 text-gray-900 text-base font-bold rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full pl-11 p-3.5 transition duration-300" required>
                                </div>
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

                        <div class="mb-8 p-6 bg-slate-50 border border-slate-200 rounded-2xl">
                            <label class="block text-base font-black text-slate-800 mb-1 uppercase tracking-wide">Fasilitas Kamar</label>
                            <p class="text-xs text-slate-500 mb-4">Pilih fasilitas yang tersedia di kamar ini sesuai realita saat ini.</p>
                            
                            @php
                                $fasilitasTerpilih = isset($kamar->fasilitas) ? (is_array($kamar->fasilitas) ? $kamar->fasilitas : explode(',', $kamar->fasilitas)) : [];
                                
                                $masterFasilitas = [
                                    'Kasur' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
                                    'AC' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                                    'Lemari' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>',
                                    'Kamar Mandi Dalam' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>',
                                    'Dapur Kecil' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 10.001-18.001A9 9 0 0012 21zM9 12h6M12 9v6"/></svg>',
                                    'Meja Belajar' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'
                                ];
                            @endphp

                            <div id="fasilitas-container" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach($masterFasilitas as $nama => $icon)
                                    @php $checked = in_array($nama, $fasilitasTerpilih); @endphp
                                    <label class="fasilitas-card relative flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer select-none transition-all duration-200 {{ $checked ? 'border-amber-500 bg-amber-50/60 text-amber-900 font-bold shadow-sm shadow-amber-100' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}">
                                        <input type="checkbox" name="fasilitas[]" value="{{ $nama }}" class="hidden fasilitas-checkbox" {{ $checked ? 'checked' : '' }}>
                                        <div class="p-1.5 rounded-lg {{ $checked ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-500' }} icon-box">
                                            {!! $icon !!}
                                        </div>
                                        <span class="text-sm tracking-wide nama-text">{{ $nama }}</span>
                                    </label>
                                @endforeach

                                @foreach($fasilitasTerpilih as $customItem)
                                    @if(!array_key_exists($customItem, $masterFasilitas) && !empty($customItem))
                                        <label class="fasilitas-card relative flex items-center gap-3 p-3.5 rounded-xl border-2 border-amber-500 bg-amber-50/60 text-amber-900 font-bold shadow-sm shadow-amber-100 cursor-pointer select-none transition-all duration-200">
                                            <input type="checkbox" name="fasilitas[]" value="{{ $customItem }}" class="hidden fasilitas-checkbox" checked>
                                            <div class="p-1.5 rounded-lg bg-amber-500 text-white icon-box">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                            </div>
                                            <span class="text-sm tracking-wide nama-text">{{ $customItem }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>

                            <div class="mt-4 pt-4 border-t border-dashed border-gray-200 flex gap-2">
                                <input type="text" id="input-fasilitas-baru" placeholder="Tambah fasilitas lain... (Misal: WiFi)" class="bg-white border border-gray-300 text-sm rounded-xl focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5">
                                <button type="button" id="btn-tambah-fasilitas" class="px-4 py-2.5 bg-gray-800 text-white text-xs font-black rounded-xl hover:bg-gray-700 tracking-wide uppercase shadow-sm whitespace-nowrap transition">
                                    + Add
                                </button>
                            </div>
                        </div>

                        <div class="mb-8 mt-10 p-6 bg-slate-50 border border-slate-200 rounded-2xl">
                            <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">📸 Manajemen Foto Galeri</h3>
                            <p class="text-xs text-slate-500 mb-6">Kosongkan jika tidak ingin mengubah foto saat ini. Ukuran maksimal yang disarankan: 2MB per foto.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">🛏️ Ruang Utama</label>
                                    <div class="relative w-full h-32 mb-3 rounded-xl overflow-hidden shadow-sm border border-slate-200 group bg-slate-200 flex items-center justify-center">
                                        <img id="preview_utama" src="{{ $kamar->foto_utama ? asset('storage/' . $kamar->foto_utama) : '' }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-110 {{ $kamar->foto_utama ? '' : 'hidden' }}" alt="Preview">
                                        <div id="placeholder_utama" class="flex flex-col items-center justify-center text-slate-400 {{ $kamar->foto_utama ? 'hidden' : '' }}">
                                            <span class="text-2xl mb-1">🖼️</span>
                                            <span class="text-[10px] uppercase font-black tracking-widest text-amber-600">BELUM ADA</span>
                                        </div>
                                    </div>
                                    <input type="file" name="foto_utama" data-preview="preview_utama" data-placeholder="placeholder_utama" class="foto-input block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer" accept="image/*">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">🍳 Dapur Kecil</label>
                                    <div class="relative w-full h-32 mb-3 rounded-xl overflow-hidden shadow-sm border border-slate-200 group bg-slate-200 flex items-center justify-center">
                                        <img id="preview_dapur" src="{{ $kamar->foto_dapur ? asset('storage/' . $kamar->foto_dapur) : '' }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-110 {{ $kamar->foto_dapur ? '' : 'hidden' }}" alt="Preview">
                                        <div id="placeholder_dapur" class="flex flex-col items-center justify-center text-slate-400 {{ $kamar->foto_dapur ? 'hidden' : '' }}">
                                            <span class="text-2xl mb-1">🖼️</span>
                                            <span class="text-[10px] uppercase font-black tracking-widest text-slate-400">BELUM ADA</span>
                                        </div>
                                    </div>
                                    <input type="file" name="foto_dapur" data-preview="preview_dapur" data-placeholder="placeholder_dapur" class="foto-input block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer" accept="image/*">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">🚿 Kamar Mandi</label>
                                    <div class="relative w-full h-32 mb-3 rounded-xl overflow-hidden shadow-sm border border-slate-200 group bg-slate-200 flex items-center justify-center">
                                        <img id="preview_kamar_mandi" src="{{ $kamar->foto_kamar_mandi ? asset('storage/' . $kamar->foto_kamar_mandi) : '' }}" class="w-full h-full object-cover transition duration-300 group-hover:scale-110 {{ $kamar->foto_kamar_mandi ? '' : 'hidden' }}" alt="Preview">
                                        <div id="placeholder_kamar_mandi" class="flex flex-col items-center justify-center text-slate-400 {{ $kamar->foto_kamar_mandi ? 'hidden' : '' }}">
                                            <span class="text-2xl mb-1">🖼️</span>
                                            <span class="text-[10px] uppercase font-black tracking-widest text-slate-400">BELUM ADA</span>
                                        </div>
                                    </div>
                                    <input type="file" name="foto_kamar_mandi" data-preview="preview_kamar_mandi" data-placeholder="placeholder_kamar_mandi" class="foto-input block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200 cursor-pointer" accept="image/*">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('fasilitas-container');
        const inputBaru = document.getElementById('input-fasilitas-baru');
        const btnTambah = document.getElementById('btn-tambah-fasilitas');

        // 1. Efek Klik/Toggle Warna yang Diperketat (Mencegah Ikon Hilang & Teks Uppercase)
        container.addEventListener('change', function(e) {
            if (e.target.classList.contains('fasilitas-checkbox')) {
                const card = e.target.closest('.fasilitas-card');
                const iconBox = card.querySelector('.icon-box');
                const textSpan = card.querySelector('.nama-text');
                
                // Hentikan efek bawaan browser jika ada script global luar yang mengintervensi
                e.stopPropagation(); 
                
                if (e.target.checked) {
                    card.className = "fasilitas-card relative flex items-center gap-3 p-3.5 rounded-xl border-2 border-amber-500 bg-amber-50/60 text-amber-900 font-bold shadow-sm shadow-amber-100 cursor-pointer select-none transition-all duration-200";
                    if (iconBox) {
                        iconBox.className = "p-1.5 rounded-lg bg-amber-500 text-white icon-box";
                        iconBox.style.display = "block"; // Paksa ikon tetap tampil
                    }
                    if (textSpan) {
                        textSpan.className = "text-sm tracking-wide nama-text"; // Cegah timpaan class uppercase luar
                    }
                } else {
                    card.className = "fasilitas-card relative flex items-center gap-3 p-3.5 rounded-xl border-2 border-gray-200 bg-white text-gray-600 hover:border-gray-300 cursor-pointer select-none transition-all duration-200";
                    if (iconBox) {
                        iconBox.className = "p-1.5 rounded-lg bg-gray-100 text-gray-500 icon-box";
                        iconBox.style.display = "block"; // Paksa ikon tetap tampil
                    }
                    if (textSpan) {
                        textSpan.className = "text-sm tracking-wide nama-text";
                    }
                }
            }
        });

        // 2. Logika Menambahkan Fasilitas Kustom Dinamis via JavaScript
        btnTambah.addEventListener('click', function() {
            const namaFasilitas = inputBaru.value.trim();
            
            if (namaFasilitas === '') {
                alert('Nama fasilitas tidak boleh kosong!');
                return;
            }

            const existingCheckboxes = container.querySelectorAll('.fasilitas-checkbox');
            let isDuplicate = false;
            existingCheckboxes.forEach(cb => {
                if (cb.value.toLowerCase() === namaFasilitas.toLowerCase()) {
                    isDuplicate = true;
                }
            });

            if (isDuplicate) {
                alert('Fasilitas ini sudah terdaftar di list!');
                inputBaru.value = '';
                return;
            }

            const cardBaru = document.createElement('label');
            cardBaru.className = "fasilitas-card relative flex items-center gap-3 p-3.5 rounded-xl border-2 border-amber-500 bg-amber-50/60 text-amber-900 font-bold shadow-sm shadow-amber-100 cursor-pointer select-none transition-all duration-200";
            cardBaru.innerHTML = `
                <input type="checkbox" name="fasilitas[]" value="${namaFasilitas}" class="hidden fasilitas-checkbox" checked>
                <div class="p-1.5 rounded-lg bg-amber-500 text-white icon-box" style="display: block !important;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <span class="text-sm tracking-wide nama-text">${namaFasilitas}</span>
            `;

            container.appendChild(cardBaru);
            inputBaru.value = '';
        });

        inputBaru.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                btnTambah.click();
            }
        });

        // 3. LOGIKA LIVE PREVIEW UNTUK FOTO YANG BARU DIUPLOAD
        const inputsFoto = document.querySelectorAll('.foto-input');
        
        inputsFoto.forEach(input => {
            input.addEventListener('change', function() {
                const file = this.files[0];
                const idPreview = this.getAttribute('data-preview');
                const idPlaceholder = this.getAttribute('data-placeholder');
                
                const imgTarget = document.getElementById(idPreview);
                const placeholderTarget = document.getElementById(idPlaceholder);
                
                if (file) {
                    const reader = new FileReader();
                    
                    reader.addEventListener('load', function() {
                        imgTarget.setAttribute('src', this.result);
                        imgTarget.classList.remove('hidden');
                        if(placeholderTarget) {
                            placeholderTarget.classList.add('hidden');
                        }
                    });
                    
                    reader.readAsDataURL(file);
                }
            });
        });
    });
</script>