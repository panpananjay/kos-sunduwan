<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight flex items-center gap-2">
            <span class="text-rose-500">💳</span>
            {{ auth()->user()->role == 'admin' ? __('Manajemen Keuangan & Tagihan') : __('Tagihan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ALERT SUCCESS --}}
            @if(session('success'))
                <div id="admin-alert" class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-2xl shadow-sm flex items-center transition-opacity duration-500">
                    <span class="text-xl mr-3">✅</span>
                    <p class="font-bold text-sm sm:text-base">{{ session('success') }}</p>
                </div>
            @endif

            {{-- ALERT ERROR --}}
            @if(session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl shadow-sm flex items-center">
                    <span class="text-xl mr-3">❌</span>
                    <p class="font-bold text-sm sm:text-base">{{ session('error') }}</p>
                </div>
            @endif

            {{-- PENCARIAN INVOICE ADMIN --}}
            @if(auth()->user()->role == 'admin')
                <div class="bg-white p-6 rounded-[2rem] mb-8 border border-slate-100 flex flex-col md:flex-row items-center justify-between shadow-sm hover:shadow-md transition-shadow duration-300">

                    <div class="flex items-center gap-4 mb-4 md:mb-0">
                        <div class="bg-rose-50 text-rose-500 w-14 h-14 flex items-center justify-center rounded-2xl text-2xl">
                            🛡️
                        </div>

                        <div>
                            <h4 class="font-black text-slate-800 text-lg tracking-tight">
                                Pencarian Invoice Kos
                            </h4>

                            <p class="text-xs font-medium text-slate-500 mt-0.5">
                                Masukkan Nomor Invoice (cth: INV-42) atau Nama Penghuni
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('tagihan.index') }}" method="GET" class="flex w-full md:w-1/2 gap-3">

                        <input
                            type="text"
                            name="cari"
                            value="{{ request('cari') }}"
                            placeholder="Ketik INV-... atau Nama"
                            class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-700 focus:ring-rose-500 focus:border-rose-500 shadow-inner px-4 bg-slate-50"
                        >

                        <button
                            type="submit"
                            class="bg-slate-800 hover:bg-rose-600 text-white font-black py-2 px-6 rounded-xl shadow-md transition duration-300 transform hover:-translate-y-0.5"
                        >
                            CEK
                        </button>

                        @if(request('cari'))
                            <a
                                href="{{ route('tagihan.index') }}"
                                class="bg-slate-100 hover:bg-rose-100 text-slate-500 hover:text-rose-600 font-bold py-2 px-4 rounded-xl transition flex items-center"
                            >
                                Reset
                            </a>
                        @endif

                    </form>
                </div>
            @endif

            {{-- FILTER + AKSI CEPAT --}}
            <div class="grid grid-cols-1 {{ auth()->user()->role == 'admin' ? 'lg:grid-cols-3' : '' }} gap-6 mb-8">

                {{-- FILTER --}}
                <div class="{{ auth()->user()->role == 'admin' ? 'lg:col-span-2' : '' }} bg-white p-6 sm:p-8 rounded-[2rem] shadow-sm border border-slate-100">

                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-slate-50 p-3 rounded-xl text-slate-500">
                            🔍
                        </div>

                        <div>
                            <h3 class="font-black text-slate-800 text-lg">
                                Cari & Filter Data
                            </h3>

                            <p class="text-xs font-medium text-slate-500 mt-0.5">
                                {{ auth()->user()->role == 'admin'
                                    ? 'Lihat status pembayaran realtime penghuni kos.'
                                    : 'Berikut adalah data riwayat tagihan Anda.'
                                }}
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('tagihan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">

                        <select
                            name="bulan"
                            class="rounded-xl border-slate-200 bg-slate-50 text-sm font-bold text-slate-700 focus:ring-rose-500 focus:border-rose-500"
                        >
                            <option value="">Semua Bulan</option>

                            @foreach([
                                'Januari',
                                'Februari',
                                'Maret',
                                'April',
                                'Mei',
                                'Juni',
                                'Juli',
                                'Agustus',
                                'September',
                                'Oktober',
                                'November',
                                'Desember'
                            ] as $b)
                                <option
                                    value="{{ $b }}"
                                    {{ request('bulan') == $b ? 'selected' : '' }}
                                >
                                    {{ $b }}
                                </option>
                            @endforeach
                        </select>

                        <input
                            type="number"
                            name="tahun"
                            value="{{ request('tahun', date('Y')) }}"
                            class="rounded-xl border-slate-200 bg-slate-50 text-sm font-bold text-slate-700 text-center focus:ring-rose-500 focus:border-rose-500"
                        >

                        <select
                            name="status"
                            class="rounded-xl border-slate-200 bg-slate-50 text-sm font-bold text-slate-700 focus:ring-rose-500 focus:border-rose-500"
                        >
                            <option value="">Semua Status</option>

                            <option
                                value="belum_bayar"
                                {{ request('status') == 'belum_bayar' ? 'selected' : '' }}
                            >
                                ❌ Belum Bayar
                            </option>

                            <option
                                value="lunas"
                                {{ request('status') == 'lunas' ? 'selected' : '' }}
                            >
                                ✅ Lunas
                            </option>

                            <option
                                value="dibatalkan"
                                {{ request('status') == 'dibatalkan' ? 'selected' : '' }}
                            >
                                🚫 Dibatalkan
                            </option>
                        </select>

                        <button
                            type="submit"
                            class="bg-slate-800 hover:bg-rose-600 text-white font-bold py-2.5 rounded-xl transition duration-300 shadow-sm transform hover:-translate-y-0.5"
                        >
                            Tampilkan
                        </button>

                    </form>
                </div>

                {{-- AKSI CEPAT ADMIN --}}
                @if(auth()->user()->role == 'admin')
                    <div class="bg-gradient-to-br from-rose-500 to-fuchsia-600 p-8 rounded-[2rem] shadow-lg shadow-rose-200 text-white relative overflow-hidden flex flex-col justify-center">

                        <div class="relative z-10">

                            <div class="flex items-center gap-3 mb-3">
                                <div class="bg-white/20 p-2.5 rounded-xl backdrop-blur-sm text-lg">
                                    ⚡
                                </div>

                                <h3 class="font-black text-xl tracking-tight">
                                    Aksi Cepat
                                </h3>
                            </div>

                            <p class="text-xs text-rose-100 mb-6 leading-relaxed font-medium">
                                Terbitkan tagihan baru untuk bulan berjalan dan kirim notifikasi tagihan otomatis WhatsApp ke semua penghuni.
                            </p>

                            <form action="{{ route('tagihan.generate') }}" method="POST">
                                @csrf

                                <input
                                    type="hidden"
                                    name="bulan"
                                    value="{{ request('bulan', \Carbon\Carbon::now()->translatedFormat('F')) }}"
                                >

                                <input
                                    type="hidden"
                                    name="tahun"
                                    value="{{ request('tahun', \Carbon\Carbon::now()->year) }}"
                                >

                                <button
                                    type="submit"
                                    class="w-full bg-white text-rose-600 hover:bg-rose-50 hover:text-rose-700 font-black py-3.5 rounded-xl transition duration-300 shadow-lg transform hover:-translate-y-1 text-sm uppercase tracking-wider"
                                    onclick="return confirm('Sistem akan membuat tagihan massal & mengirim WA. Lanjutkan?')"
                                >
                                    📢 Terbitkan Tagihan
                                </button>
                            </form>

                        </div>

                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-fuchsia-900/20 rounded-full blur-xl"></div>

                    </div>
                @endif

            </div>

            {{-- DAFTAR TAGIHAN --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-[2rem] border border-slate-100">

                <div class="p-6 sm:p-8">

                    <div class="flex items-center justify-between mb-6 border-b border-slate-50 pb-5">

                        <h3 class="text-xl font-black text-slate-800">
                            Daftar Tagihan
                        </h3>

                        <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                                Total:
                            </span>

                            <span class="text-rose-600 font-black text-sm">
                                {{ $tagihans->count() }} Data
                            </span>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                        @forelse($tagihans as $tagihan)

                            @php
                                // Hitung info diskon voucher untuk tagihan ini
                                $jumlahVoucher   = $tagihan->vouchers->count();
                                $adaDiskon       = $jumlahVoucher > 0;
                                $totalDiskon     = $tagihan->vouchers->sum('nominal');
                                // jumlah_tagihan diasumsikan SUDAH harga final (setelah dipotong voucher)
                                $hargaAwal       = $tagihan->jumlah_tagihan + $totalDiskon;
                            @endphp

                            @php
                                // Hitung status keterlambatan (hanya relevan untuk status belum_bayar)
                                $sudahTerlambat = false;
                                $labelTerlambat = null;

                                if ($tagihan->status === 'belum_bayar') {
                                    $deadlineTagihan = \Carbon\Carbon::parse($tagihan->created_at)->addDays(7)->endOfDay();

                                    if (\Carbon\Carbon::now()->gt($deadlineTagihan)) {
                                        $sudahTerlambat = true;
                                        $hariTerlambat  = (int) floor($deadlineTagihan->diffInDays(\Carbon\Carbon::now()));

                                        if ($hariTerlambat < 7) {
                                            $labelTerlambat = $hariTerlambat . ' hari';
                                        } elseif ($hariTerlambat < 30) {
                                            $labelTerlambat = intdiv($hariTerlambat, 7) . ' minggu';
                                        } else {
                                            $labelTerlambat = intdiv($hariTerlambat, 30) . ' bulan';
                                        }
                                    }
                                }
                            @endphp

                            {{-- CARD TAGIHAN --}}
                            <div class="bg-white p-5 rounded-2xl border {{ $sudahTerlambat ? 'border-rose-300' : ($adaDiskon ? 'border-fuchsia-200' : 'border-slate-100') }} flex flex-col justify-between hover:border-rose-200 transition-all duration-300 shadow-sm hover:shadow-md">

                                <div>

                                    {{-- INFORMASI TAGIHAN --}}
                                    <div class="flex justify-between items-start mb-4">

                                        <div>
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                                    {{ $tagihan->bulan }} {{ $tagihan->tahun }}
                                                </p>

                                                @if($adaDiskon)
                                                    <span style="font-size:9px; line-height:1; padding:3px 7px;" class="inline-flex items-center gap-1 font-bold text-slate-600 bg-slate-50 border border-slate-200 rounded-md tracking-wide">
                                                        <svg xmlns="http://www.w3.org/2000/svg" style="width:9px; height:9px; flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                        </svg>
                                                        {{ $jumlahVoucher }}× Voucher
                                                    </span>
                                                @endif
                                            </div>

                                            <h4 class="font-bold text-slate-800 text-sm mt-1">
                                                {{ $tagihan->penghuni->nama }}
                                            </h4>

                                            <p class="text-[10px] text-slate-500 font-bold bg-slate-50 px-2 py-0.5 rounded-md border border-slate-100 inline-block mt-1">
                                                Kamar:
                                                {{ $tagihan->penghuni->kamar?->nomor_kamar ?? '-' }}
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                                Total
                                            </p>

                                            @if($adaDiskon)
                                                {{-- Harga awal dicoret --}}
                                                <p style="font-size:10px; text-decoration:line-through; text-decoration-color:#94a3b8; text-decoration-thickness:1.5px;" class="text-slate-400 font-semibold leading-tight tabular-nums">
                                                    Rp {{ number_format($hargaAwal, 0, ',', '.') }}
                                                </p>

                                                {{-- Harga akhir setelah diskon --}}
                                                <p class="font-black text-slate-800 text-sm leading-tight tabular-nums">
                                                    Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}
                                                </p>
                                            @else
                                                <p class="font-black text-rose-600 text-sm">
                                                    Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}
                                                </p>
                                            @endif
                                        </div>

                                    </div>

                                    {{-- STATUS + BUTTON --}}
                                    <div class="flex flex-col gap-3 pt-4 border-t border-slate-50">

                                        {{-- STATUS --}}
                                        <div class="flex flex-wrap items-center gap-1.5">

                                            @if($tagihan->status == 'lunas')

                                                <span class="bg-emerald-50 text-emerald-600 font-bold px-3 py-1.5 rounded-lg text-[10px] border border-emerald-100 whitespace-nowrap">
                                                    ✅ Lunas
                                                </span>

                                            @elseif($tagihan->status == 'dibatalkan')

                                                <span class="bg-slate-100 text-slate-500 font-bold px-3 py-1.5 rounded-lg text-[10px] border border-slate-200 whitespace-nowrap">
                                                    🚫 Dibatalkan
                                                </span>

                                            @else

                                                <span class="bg-rose-50 text-rose-600 font-bold px-3 py-1.5 rounded-lg text-[10px] border border-rose-100 whitespace-nowrap">
                                                    ❌ Belum Bayar
                                                </span>

                                                @if($sudahTerlambat)
                                                    <span class="bg-rose-600 text-white font-bold px-2.5 py-1.5 rounded-lg text-[10px] border border-rose-700 animate-pulse-subtle whitespace-nowrap">
                                                        ⏰ Terlambat {{ $labelTerlambat }}
                                                    </span>
                                                @endif

                                            @endif

                                        </div>

                                        {{-- BUTTON --}}
                                        <div class="flex flex-wrap gap-2">

                                            {{-- BAYAR PENGHUNI --}}
                                            @if(auth()->user()->role == 'penghuni' && $tagihan->status == 'belum_bayar')

                                                <button
                                                    type="button"
                                                    onclick="ambilTokenMidtrans(event, '{{ $tagihan->id }}')"
                                                    class="flex-1 sm:flex-none whitespace-nowrap bg-rose-500 hover:bg-rose-600 text-white font-bold px-4 py-2 rounded-xl text-[10px] transition-colors"
                                                >
                                                    Bayar
                                                </button>

                                            @endif

                                            {{-- INVOICE --}}
                                            @if($tagihan->status == 'lunas')

                                                <a
                                                    href="{{ route('tagihan.unduh', $tagihan->id) }}"
                                                    class="flex-1 sm:flex-none text-center whitespace-nowrap bg-slate-800 hover:bg-slate-900 text-white font-bold px-3 py-2 rounded-xl text-[10px] transition-colors"
                                                >
                                                    Invoice
                                                </a>

                                            @endif

                                            {{-- ADMIN --}}
                                            @if(auth()->user()->role == 'admin')

                                                {{-- LUNASI CASH MANUAL --}}
                                                @if($tagihan->status == 'belum_bayar')

                                                    <form
                                                        action="{{ route('tagihan.lunasi_manual', $tagihan->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Konfirmasi pembayaran Cash?')"
                                                        class="flex-1 sm:flex-none"
                                                    >
                                                        @csrf

                                                        <button
                                                            type="submit"
                                                            class="w-full whitespace-nowrap bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-2 rounded-xl text-[10px] transition-colors"
                                                        >
                                                            Konfirmasi Cash
                                                        </button>
                                                    </form>

                                                @endif

                                                {{-- BATALKAN TAGIHAN --}}
                                                @if($tagihan->status == 'belum_bayar')

                                                    <form
                                                        action="{{ route('tagihan.destroy', $tagihan->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin membatalkan tagihan ini?')"
                                                        class="flex-1 sm:flex-none"
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="w-full whitespace-nowrap bg-slate-100 hover:bg-rose-100 text-slate-600 hover:text-rose-600 font-bold px-3 py-2 rounded-xl text-[10px] transition-colors"
                                                        >
                                                            Batalkan
                                                        </button>
                                                    </form>

                                                @endif

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <p class="col-span-full text-center py-10 text-slate-400 font-medium">
                                Data tagihan tidak ditemukan.
                            </p>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- MIDTRANS UNTUK PENGHUNI --}}
    @if(auth()->user()->role == 'penghuni')

        <script
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"
        ></script>

        <script type="text/javascript">
            function ambilTokenMidtrans(event, tagihanId) {
                const tombol = event.currentTarget;
                const teksAsli = tombol.innerHTML;

                tombol.disabled = true;
                tombol.innerHTML = '⏳ Memproses...';

                fetch(`/tagihan/${tagihanId}/bayar`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Jaringan bermasalah atau Route tidak ditemukan.');
                    }

                    return response.json();
                })
                .then(data => {
                    tombol.disabled = false;
                    tombol.innerHTML = teksAsli;

                    if (data.error) {
                        alert('Gagal mengambil token Midtrans: ' + data.error);
                        return;
                    }

                    window.snap.pay(data.snap_token, {

                        onSuccess: function(result) {
                            alert('Pembayaran berhasil! Status akan diperbarui otomatis dalam beberapa saat.');
                            window.location.href = "{{ route('tagihan.index') }}";
                        },

                        onPending: function(result) {
                            alert("Silakan selesaikan pembayaran sebelum batas waktu habis!");
                            window.location.reload();
                        },

                        onError: function(result) {
                            alert("Terjadi kesalahan pada transaksi. Silakan coba kembali.");
                        },

                        onClose: function() {
                            alert('Anda menutup halaman pembayaran sebelum selesai.');
                        }

                    });
                })
                .catch(error => {
                    tombol.disabled = false;
                    tombol.innerHTML = teksAsli;

                    console.error('Error:', error);

                    alert('Terjadi kesalahan koneksi sistem atau rute internal salah.');
                });
            }
        </script>

    @endif

    {{-- AUTO HIDE ALERT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const adminAlert = document.getElementById('admin-alert');

            if (adminAlert) {
                setTimeout(() => {
                    adminAlert.style.opacity = '0';

                    setTimeout(() => {
                        adminAlert.remove();
                    }, 500);

                }, 4000);
            }
        });
    </script>

</x-app-layout>