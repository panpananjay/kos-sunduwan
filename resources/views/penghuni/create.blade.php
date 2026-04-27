<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Penghuni Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('penghuni.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap Penghuni</label>
                            <input type="text" name="nama" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" placeholder="Contoh: Nisa Sunduwan" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nomor WhatsApp / HP</label>
                            <input type="text" name="no_hp" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" placeholder="Contoh: 08123456789" required>
                        </div>

                        <div class="mb-4 mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                            <h3 class="font-bold text-gray-800 mb-3 border-b pb-2">Buatkan Akun Login Penghuni</h3>
                            
                            <div class="mb-3">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Username (Tanpa spasi)</label>
                                <input type="text" name="username" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" placeholder="Contoh: nisasunduwan" required>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Password Sementara</label>
                                <input type="password" name="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" placeholder="Minimal 8 karakter" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Kamar</label>
                            <select name="kamar_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" required>
                                <option value="" disabled selected>-- Pilih Kamar Kosong --</option>
                                @foreach($kamarKosong as $kamar)
                                    <option value="{{ $kamar->id }}">Kamar {{ $kamar->nomor_kamar }} (Rp {{ number_format($kamar->harga, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @if($kamarKosong->isEmpty())
                                <p class="text-red-500 text-xs italic mt-2">Maaf, saat ini tidak ada kamar kosong. Silakan tambah data kamar dulu.</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-gray-800 hover:bg-black text-white font-bold py-2 px-4 rounded-md">
                                Simpan Data Penghuni
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>