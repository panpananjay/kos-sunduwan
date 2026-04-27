<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            💳 Detail Tagihan: {{ $tagihan->bulan }} {{ $tagihan->tahun }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ openTolak: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div id="success-alert" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r shadow-sm transition-opacity duration-500">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-r shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($tagihan->catatan && $tagihan->status == 'belum_bayar')
                <div class="mb-6 bg-rose-50 border-2 border-rose-200 p-5 rounded-3xl flex gap-4 items-center animate-bounce-short">
                    <div class="bg-rose-500 text-white p-2 rounded-full text-xl">⚠️</div>
                    <div>
                        <h4 class="font-black text-rose-700">Pembayaran Ditolak Admin</h4>
                        <p class="text-rose-600 font-medium italic">"{{ $tagihan->catatan }}"</p>
                        <p class="text-xs text-rose-400 mt-1">*Silakan perbaiki dan upload kembali bukti bayar yang sah.</p>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 flex flex-col md:flex-row">
                
                <div class="p-8 md:w-1/2 border-b md:border-b-0 md:border-r border-gray-100 bg-gray-50 flex flex-col justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800 mb-6 flex items-center gap-2">
                            📄 Informasi Tagihan
                        </h3>

                        <div class="space-y-4 text-lg">
                            <div class="flex justify-between border-b border-gray-200 pb-2">
                                <span class="text-gray-500 font-medium">Nama Penghuni</span>
                                <span class="font-bold text-gray-800">{{ $tagihan->penghuni->nama }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 pb-2">
                                <span class="text-gray-500 font-medium">Kamar</span>
                                <span class="font-bold text-gray-800">{{ $tagihan->penghuni->kamar?->nomor_kamar ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 pb-2">
                                <span class="text-gray-500 font-medium">Periode</span>
                                <span class="font-bold text-gray-800">{{ $tagihan->bulan }} {{ $tagihan->tahun }}</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 pb-2 items-center">
                                <span class="text-gray-500 font-medium">Status</span>
                                @if($tagihan->status == 'belum_bayar')
                                    <span class="bg-red-100 text-red-700 font-bold px-3 py-1 rounded-full text-sm">Belum Bayar</span>
                                @elseif($tagihan->status == 'menunggu_verifikasi')
                                    <span class="bg-yellow-100 text-yellow-700 font-bold px-3 py-1 rounded-full text-sm">Menunggu Verifikasi</span>
                                @else
                                    <span class="bg-emerald-100 text-emerald-700 font-bold px-3 py-1 rounded-full text-sm">Lunas</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 bg-indigo-50 p-6 rounded-2xl border border-indigo-100 shadow-inner">
                        <p class="text-indigo-900 font-semibold mb-1">Total yang harus dibayar:</p>
                        <p class="text-4xl font-black text-indigo-700 mb-4">Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</p>

                        @if($tagihan->status != 'lunas')
                            <div class="bg-white p-5 rounded-xl shadow-sm border border-indigo-50 text-sm">
                                <p class="font-bold text-gray-700 mb-2">📌 Instruksi Pembayaran:</p>
                                <p class="text-gray-600 mb-1">Silakan transfer sesuai nominal ke rekening berikut:</p>
                                <div class="bg-gray-50 p-3 rounded-lg mt-2 font-mono text-lg font-bold text-gray-800 tracking-wider border border-gray-200">
                                    BRI - 1234567890<br>
                                    <span class="text-sm font-normal text-gray-500 font-sans">a.n. Wayan Ediana</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-8 md:w-1/2 bg-white flex flex-col justify-center">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                        📸 Bukti Transfer
                    </h3>

                    @if($tagihan->bukti_bayar)
                        <div class="border border-gray-200 shadow-sm rounded-2xl overflow-hidden bg-gray-50 flex justify-center items-center h-64 mb-6 relative group">
                            <img src="{{ asset('storage/' . $tagihan->bukti_bayar) }}" alt="Bukti Pembayaran" class="max-h-full max-w-full object-contain p-2 transition-transform duration-300 group-hover:scale-105 cursor-zoom-in" onclick="window.open(this.src)">
                            <div class="absolute bottom-2 right-2 bg-black bg-opacity-60 text-white text-xs px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                🔍 Klik untuk perbesar
                            </div>
                        </div>

                        @if(auth()->user()->role == 'admin' && $tagihan->status == 'menunggu_verifikasi')
                            <div class="flex flex-col gap-3">
                                <form action="{{ route('tagihan.verifikasi', $tagihan->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-black py-4 px-6 rounded-xl shadow-lg transform hover:-translate-y-1 transition duration-300 text-lg flex justify-center items-center gap-2">
                                        ✅ Verifikasi & Lunas
                                    </button>
                                </form>

                                <button @click="openTolak = true" class="w-full bg-white border-2 border-rose-500 text-rose-500 hover:bg-rose-50 font-bold py-3 rounded-xl transition duration-300 flex justify-center items-center gap-2">
                                    ❌ Tolak Pembayaran
                                </button>
                            </div>
                        
                        @elseif(auth()->user()->role == 'penghuni' && $tagihan->status == 'menunggu_verifikasi')
                            <div class="bg-yellow-50 text-yellow-700 p-4 rounded-xl text-center border border-yellow-200 font-bold animate-pulse">
                                ⏳ Bukti terkirim! Menunggu diverifikasi admin...
                            </div>
                        
                        @elseif($tagihan->status == 'lunas')
                            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl text-center border border-emerald-200 font-black text-lg mb-4">
                                🎉 Pembayaran Telah Lunas!
                            </div>
                            <a href="{{ asset('storage/invoices/Invoice_' . $tagihan->id . '.png') }}" download class="w-full bg-indigo-600 hover:bg-indigo-800 text-white font-black py-4 px-6 rounded-xl shadow-lg flex justify-center items-center gap-2 text-lg">
                                ⬇️ Unduh Invoice
                            </a>
                        @endif

                    @else
                        @if(auth()->user()->role == 'penghuni')
                            <form action="{{ route('tagihan.upload', $tagihan->id) }}" method="POST" enctype="multipart/form-data" class="bg-indigo-50 p-8 rounded-3xl border-2 border-indigo-200 border-dashed text-center transition hover:border-indigo-400">
                                @csrf
                                @method('PUT')
                                <div class="mb-6">
                                    <div class="text-4xl mb-4">📤</div>
                                    <label class="block text-indigo-900 font-extrabold mb-2 text-lg">Upload Bukti Pembayaran</label>
                                    <p class="text-sm text-gray-500 mb-4 font-italic">Format: JPG, JPEG, atau PNG</p>
                                    <input type="file" name="bukti_bayar" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer" required accept="image/*">
                                </div>
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition duration-300">
                                    Kirim Bukti Pembayaran
                                </button>
                            </form>
                        @else
                            <div class="bg-gray-50 p-10 rounded-3xl border-2 border-gray-200 border-dashed flex flex-col items-center justify-center text-gray-400 h-64">
                                <span class="text-5xl mb-3 grayscale opacity-50">📭</span>
                                <p class="font-medium text-center font-bold">Belum ada bukti pembayaran.</p>
                            </div>
                        @endif
                    @endif

                    <div class="mt-8 text-center">
                        <a href="{{ route('tagihan.index') }}" class="text-gray-500 hover:text-indigo-600 font-bold transition inline-flex items-center gap-1 bg-gray-100 hover:bg-indigo-50 px-4 py-2 rounded-lg text-sm">
                            ⬅ Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="openTolak" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" x-cloak x-transition>
            <div class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl" @click.away="openTolak = false">
                <h3 class="text-2xl font-black text-gray-800 mb-2">Tolak Pembayaran?</h3>
                <p class="text-gray-500 mb-6 text-sm italic">Berikan alasan agar penghuni bisa memperbaiki kesalahannya.</p>
                
                <form action="{{ route('tagihan.tolak', $tagihan->id) }}" method="POST">
                    @csrf
                    <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Penolakan:</label>
                    <textarea name="catatan" rows="3" class="w-full border-gray-200 rounded-2xl focus:ring-rose-500 focus:border-rose-500 p-4 bg-gray-50 text-gray-800" placeholder="Contoh: Bukti tidak terbaca atau nominal kurang..." required></textarea>
                    
                    <div class="flex flex-col gap-3 mt-8">
                        <button type="submit" class="w-full bg-rose-500 text-white font-bold py-4 rounded-2xl shadow-lg shadow-rose-100 hover:bg-rose-600 transition">Kirim Penolakan</button>
                        <button type="button" @click="openTolak = false" class="text-gray-400 font-bold py-2 hover:text-gray-600 transition text-sm">Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        @keyframes bounce-short {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-bounce-short { animation: bounce-short 2s infinite; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.opacity = '0'; 
                    setTimeout(() => alert.remove(), 500); 
                }, 4000);
            }
        });
    </script>
</x-app-layout>