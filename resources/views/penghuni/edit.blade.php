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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('penghuni.update', $penghuni->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', $penghuni->nama) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">No. HP (WhatsApp)</label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $penghuni->no_hp) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Kamar (Bisa Ganti Kamar)</label>
                            <select name="kamar_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                @foreach($kamars as $kamar)
                                    <option value="{{ $kamar->id }}" {{ $penghuni->kamar_id == $kamar->id ? 'selected' : '' }}>
                                        Kamar {{ $kamar->nomor_kamar }} - Rp {{ number_format($kamar->harga, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
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