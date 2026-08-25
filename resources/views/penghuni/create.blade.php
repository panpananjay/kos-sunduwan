<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Penghuni Baru
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ✅ Notifikasi sukses --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 text-sm font-semibold rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ⚠️ Notifikasi error umum (misal dari redirect()->back()->with('error', ...)) --}}
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ❌ Notifikasi jika ada error validasi (ringkasan semua error) --}}
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-xl">
                    <p class="mb-1">Data belum sesuai, mohon periksa kembali:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-gray-100">
                    <h3 class="text-xl font-black text-slate-800">Form Pendaftaran Penghuni</h3>
                    <p class="text-xs text-gray-500 mt-1">Lengkapi data pribadi dan alokasikan kamar untuk penghuni baru.</p>
                </div>

                <div class="p-6 sm:p-10">
                    <form action="{{ route('penghuni.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-xs font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                                <input type="text" name="nama" value="{{ old('nama') }}"
                                    class="bg-gray-50 border @error('nama') border-red-400 @else border-gray-200 @enderror text-sm font-semibold rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="Contoh: Nisa Sunduwan"
                                    pattern="[A-Za-z\s]+"
                                    title="Nama hanya boleh berisi huruf dan spasi"
                                    required>
                                @error('nama')
                                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Nomor WhatsApp / HP</label>
                                <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                    class="bg-gray-50 border @error('no_hp') border-red-400 @else border-gray-200 @enderror text-sm font-semibold rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="Contoh: 08123456789"
                                    inputmode="numeric"
                                    pattern="08[0-9]{8,11}"
                                    title="Nomor harus diawali 08 dan berupa nomor HP Indonesia yang valid"
                                    required>
                                @error('no_hp')
                                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="p-5 bg-blue-50/50 border border-blue-100 rounded-2xl">
                                <h4 class="font-bold text-blue-900 text-sm mb-4 flex items-center gap-2">
                                    <span>🔐</span> Buat akun penghuni
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-2">Username</label>
                                        <input type="text" name="username" value="{{ old('username') }}"
                                            class="bg-white border @error('username') border-red-400 @else border-gray-200 @enderror text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3"
                                            placeholder="nisasunduwan" required>
                                        @error('username')
                                            <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-2">Password</label>
                                        <input type="password" name="password"
                                            class="bg-white border @error('password') border-red-400 @else border-gray-200 @enderror text-sm rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3"
                                            placeholder="••••••••" required>
                                        @error('password')
                                            <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-gray-700 mb-2 uppercase tracking-wide">Pilih Kamar</label>
                                <select name="kamar_id"
                                    class="bg-gray-50 border @error('kamar_id') border-red-400 @else border-gray-200 @enderror text-sm font-semibold rounded-xl focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    required>
                                    <option value="" disabled {{ old('kamar_id') ? '' : 'selected' }}>-- Pilih Kamar Kosong --</option>
                                    @foreach($kamarKosong as $kamar)
                                        <option value="{{ $kamar->id }}" {{ old('kamar_id') == $kamar->id ? 'selected' : '' }}>
                                            Kamar {{ $kamar->nomor_kamar }} - Rp {{ number_format($kamar->harga, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('kamar_id')
                                    <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-8 mt-8 border-t border-gray-100">
                            <a href="{{ route('penghuni.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3.5 bg-gray-100 hover:bg-gray-200 rounded-xl font-bold text-gray-700 transition duration-300 text-sm">
                                ⬅️ Kembali
                            </a>
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl transition duration-300 shadow-sm text-sm">
                                💾 Simpan Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>