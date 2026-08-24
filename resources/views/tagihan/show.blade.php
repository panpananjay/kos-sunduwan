<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            💳 Detail Tagihan: {{ $tagihan->bulan }} {{ $tagihan->tahun }}
        </h2>
    </x-slot>

    <div class="py-12">
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
                        <h4 class="font-black text-rose-700">Catatan Pembayaran</h4>
                        <p class="text-rose-600 font-medium italic">"{{ $tagihan->catatan }}"</p>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100 flex flex-col md:flex-row">
                
                {{-- Kolom Kiri: Informasi Tagihan --}}
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
                                <p class="font-bold text-gray-700 mb-1">📌 Pembayaran Otomatis Midtrans:</p>
                                <p class="text-gray-600">Pembayaran diproses secara instant melalui Transfer Bank (Virtual Account), QRIS, atau E-Wallet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kolom Kanan: Aksi Pembayaran / Invoice --}}
                <div class="p-8 md:w-1/2 bg-white flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            💳 Pembayaran
                        </h3>

                        @if($tagihan->status == 'lunas')
                            <div class="bg-emerald-50 text-emerald-700 p-6 rounded-2xl text-center border border-emerald-200 mb-6">
                                <div class="text-5xl mb-3">🎉</div>
                                <h4 class="font-black text-xl mb-1">Pembayaran Telah Lunas!</h4>
                                <p class="text-sm text-emerald-600 mb-5">Terima kasih atas pembayaran tepat waktu Anda.</p>

                                <a href="{{ route('tagihan.unduh', $tagihan->id) }}" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-4 px-6 rounded-xl shadow-lg flex justify-center items-center gap-2 text-lg transition duration-200">
                                    ⬇️ Unduh E-Invoice Resmi
                                </a>
                            </div>

                        @elseif($tagihan->status == 'belum_bayar')
                            @if(auth()->user()->role == 'penghuni')
                                <div class="bg-indigo-50 p-8 rounded-3xl border-2 border-indigo-100 text-center">
                                    <div class="text-5xl mb-4">💳</div>
                                    <h4 class="text-xl font-black text-gray-800 mb-2">Bayar Tagihan Sekarang</h4>
                                    <p class="text-sm text-gray-600 mb-6">Klik tombol di bawah untuk memilih pembayaran via QRIS, Virtual Account Bank, atau E-Wallet.</p>

                                    <button id="pay-button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-6 rounded-xl shadow-lg transform hover:-translate-y-0.5 transition duration-300 text-lg flex justify-center items-center gap-2">
                                        🚀 Bayar Sekarang via Midtrans
                                    </button>
                                </div>
                            @else
                                <div class="bg-gray-50 p-8 rounded-3xl border border-gray-200 text-center text-gray-400">
                                    <div class="text-4xl mb-3">⏳</div>
                                    <p class="font-semibold text-gray-600">Penghuni belum melakukan pembayaran.</p>
                                </div>
                            @endif
                        @endif

                        {{-- Menampilkan Bukti Bayar jika ada data riwayat manual terdahulu --}}
                        @if($tagihan->bukti_bayar)
                            <div class="mt-6 border border-gray-200 shadow-sm rounded-2xl overflow-hidden bg-gray-50 p-4">
                                <p class="text-xs font-bold text-gray-500 mb-2">BUKTI BAYAR LAMA (MANUAL):</p>
                                <div class="flex justify-center items-center h-48 relative group">
                                    <img src="{{ asset('storage/' . $tagihan->bukti_bayar) }}" alt="Bukti Pembayaran" class="max-h-full max-w-full object-contain p-2 cursor-zoom-in" onclick="window.open(this.src)">
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 text-center">
                        <a href="{{ route('tagihan.index') }}" class="text-gray-500 hover:text-indigo-600 font-bold transition inline-flex items-center gap-1 bg-gray-100 hover:bg-indigo-50 px-4 py-2 rounded-lg text-sm">
                            ⬅ Kembali ke Daftar
                        </a>
                    </div>
                </div>
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

    {{-- Script Integration Midtrans Snap --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Auto hide alert
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.opacity = '0'; 
                    setTimeout(() => alert.remove(), 500); 
                }, 4000);
            }

            // Event listener tombol bayar Midtrans
            const payButton = document.getElementById('pay-button');
            if (payButton) {
                payButton.addEventListener('click', function () {
                    @if(isset($tagihan->snap_token) && $tagihan->snap_token)
                        window.snap.pay('{{ $tagihan->snap_token }}', {
                            onSuccess: function(result){
                                window.location.reload();
                            },
                            onPending: function(result){
                                window.location.reload();
                            },
                            onError: function(result){
                                alert("Pembayaran gagal!");
                            },
                            onClose: function(){
                                alert('Anda menutup pop-up pembayaran sebelum selesai.');
                            }
                        });
                    @else
                        alert('Snap token tidak ditemukan. Silakan refresh halaman.');
                    @endif
                });
            }
        });
    </script>
</x-app-layout>