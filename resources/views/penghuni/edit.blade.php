<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Data Penghuni: {{ $penghuni->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <p class="font-bold">{{ session('error') }}</p>
                </div>
            @endif

            {{-- 🆕 STATUS KEANGGOTAAN --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border-t-4 {{ $penghuni->status === 'nonaktif' ? 'border-slate-400' : 'border-emerald-500' }}">
                <div class="p-6 {{ $penghuni->status === 'nonaktif' ? 'bg-slate-50' : 'bg-emerald-50' }} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold {{ $penghuni->status === 'nonaktif' ? 'text-slate-700' : 'text-emerald-800' }}">
                            Status: {{ $penghuni->status === 'nonaktif' ? 'Nonaktif' : 'Aktif' }}
                        </h3>
                        @if($penghuni->status === 'nonaktif')
                            <p class="text-sm text-slate-600">Penghuni ini sudah dinonaktifkan (kamar sebelumnya sudah dikosongkan). Aktifkan kembali kalau dia masuk ulang.</p>
                        @else
                            <p class="text-sm text-emerald-700">Penghuni ini masih berstatus aktif dan menempati kamar seperti biasa.</p>
                        @endif
                    </div>

                    @if($penghuni->status === 'nonaktif')
                        <form action="{{ route('penghuni.activate', $penghuni->id) }}" method="POST" onsubmit="return confirm('Aktifkan kembali {{ $penghuni->nama }}? Kamu perlu memilih kamar untuknya di form di bawah.');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-800 text-white font-bold py-2 px-4 rounded-md shadow transition duration-300 whitespace-nowrap">
                                ✅ Aktifkan Kembali
                            </button>
                        </form>
                    @else
                        <form action="{{ route('penghuni.destroy', $penghuni->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menonaktifkan {{ $penghuni->nama }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-rose-100 hover:bg-rose-600 text-rose-700 hover:text-white font-bold py-2 px-4 rounded-md shadow transition duration-300 whitespace-nowrap">
                                🗑️ Nonaktifkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('penghuni.update', $penghuni->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', $penghuni->nama) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nama') border-red-500 @enderror" required>
                            @error('nama')
                                <p class="text-red-600 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Username (Login)</label>
                            <input type="text" name="username" value="{{ old('username', $penghuni->username) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('username') border-red-500 @enderror" required>
                            @error('username')
                                <p class="text-red-600 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">No. HP (WhatsApp)</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $penghuni->no_hp) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('no_hp') border-red-500 @enderror" required>
                            @error('no_hp')
                                <p class="text-red-600 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Kamar (Bisa Ganti Kamar)</label>
                            <select name="kamar_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('kamar_id') border-red-500 @enderror" required>
                                @foreach($kamars as $kamar)
                                    <option value="{{ $kamar->id }}" {{ old('kamar_id', $penghuni->kamar_id) == $kamar->id ? 'selected' : '' }}>
                                        Kamar {{ $kamar->nomor_kamar }} - Rp {{ number_format($kamar->harga, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kamar_id')
                                <p class="text-red-600 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center space-x-4 mt-8">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-md shadow focus:outline-none focus:shadow-outline transition duration-300">
                                💾 Simpan Perubahan
                            </button>
                            <a href="{{ route('penghuni.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-md shadow transition duration-300">
                                ❌ Batal
                            </a>
                        </div>
                    </form>

                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-500">
                <div class="p-6 bg-red-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-red-800">Zona Admin: Reset Password</h3>
                        <p class="text-sm text-red-600">Jika penghuni lupa sandi aplikasinya, klik tombol di samping untuk mereset sandinya kembali ke: <strong>12345678</strong></p>
                    </div>
                    
                    <form action="{{ route('penghuni.reset_password', $penghuni->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded-md shadow transition duration-300" onclick="return confirm('Yakin ingin mereset password penghuni ini menjadi 12345678?')">
                            🔑 Reset ke "12345678"
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>