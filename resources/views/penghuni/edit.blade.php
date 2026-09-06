<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight flex items-center gap-2">
            <span class="text-slate-400">✏️</span>
            Edit Data Penghuni
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">

            {{-- ALERTS --}}
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-2xl shadow-sm">
                    <p class="font-bold text-sm">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl shadow-sm">
                    <p class="font-bold text-sm">{{ session('error') }}</p>
                </div>
            @endif

            {{-- STATUS KEANGGOTAAN --}}
            <div class="bg-white rounded-[1.75rem] shadow-sm border border-slate-100 mb-6 overflow-hidden">
                <div class="p-6 {{ $penghuni->status === 'nonaktif' ? 'bg-slate-50' : 'bg-emerald-50' }} flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-wide {{ $penghuni->status === 'nonaktif' ? 'text-slate-500' : 'text-emerald-600' }}">
                            Status: {{ $penghuni->status === 'nonaktif' ? 'Nonaktif' : 'Aktif' }}
                        </h3>
                        @if($penghuni->status === 'nonaktif')
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                                Penghuni ini sudah dinonaktifkan (kamar sebelumnya sudah dikosongkan). Aktifkan kembali kalau dia masuk ulang.
                            </p>
                        @else
                            <p class="text-xs text-emerald-700 mt-1.5 leading-relaxed">
                                Penghuni ini masih berstatus aktif dan menempati kamar seperti biasa.
                            </p>
                        @endif
                    </div>

                    @if($penghuni->status === 'nonaktif')
                        <form action="{{ route('penghuni.activate', $penghuni->id) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Aktifkan kembali {{ $penghuni->nama }}? Kamu perlu memilih kamar untuknya di form di bawah.');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition-colors whitespace-nowrap text-sm">
                                ✅ Aktifkan Kembali
                            </button>
                        </form>
                    @else
                        <form action="{{ route('penghuni.destroy', $penghuni->id) }}" method="POST" class="w-full sm:w-auto" onsubmit="return confirm('Yakin ingin menonaktifkan {{ $penghuni->nama }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full sm:w-auto bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white font-bold py-2.5 px-5 rounded-xl shadow-sm transition-colors whitespace-nowrap text-sm">
                                🗑️ Nonaktifkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- FORM PENDAFTARAN PENGHUNI --}}
            <div class="bg-white rounded-[1.75rem] shadow-sm border border-slate-100 mb-6">
                <div class="p-6 sm:p-8">

                    <h3 class="font-black text-slate-800 text-lg mb-4">
                        Form Pendaftaran Penghuni
                    </h3>
                    <div class="border-b border-slate-100 mb-6"></div>

                    <form action="{{ route('penghuni.update', $penghuni->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">
                                Nama Lengkap
                            </label>
                            <input
                                type="text"
                                name="nama"
                                value="{{ old('nama', $penghuni->nama) }}"
                                pattern="[A-Za-z\s]+"
                                title="Nama hanya boleh berisi huruf dan spasi"
                                required
                                class="w-full rounded-xl border-0 bg-slate-100 py-3 px-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-rose-400 @error('nama') ring-2 ring-rose-400 @enderror"
                            >
                            @error('nama')
                                <p class="text-rose-600 text-xs font-medium mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">
                                Username Akun
                            </label>
                            <input
                                type="text"
                                name="username"
                                value="{{ old('username', $penghuni->username) }}"
                                required
                                class="w-full rounded-xl border-0 bg-slate-100 py-3 px-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-rose-400 @error('username') ring-2 ring-rose-400 @enderror"
                            >
                            @error('username')
                                <p class="text-rose-600 text-xs font-medium mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">
                                Nomor WhatsApp / HP
                            </label>
                            <input
                                type="text"
                                name="no_hp"
                                value="{{ old('no_hp', $penghuni->no_hp) }}"
                                inputmode="numeric"
                                pattern="08[0-9]{8,11}"
                                title="Nomor harus diawali 08 dan berupa nomor HP Indonesia yang valid"
                                required
                                class="w-full rounded-xl border-0 bg-slate-100 py-3 px-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-rose-400 @error('no_hp') ring-2 ring-rose-400 @enderror"
                            >
                            @error('no_hp')
                                <p class="text-rose-600 text-xs font-medium mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2.5">
                                Pilih Kamar (Bisa Ganti Kamar)
                            </label>
                            <select
                                name="kamar_id"
                                required
                                class="w-full rounded-xl border-0 bg-slate-100 py-3 px-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-rose-400 @error('kamar_id') ring-2 ring-rose-400 @enderror"
                            >
                                @foreach($kamars as $kamar)
                                    <option value="{{ $kamar->id }}" {{ old('kamar_id', $penghuni->kamar_id) == $kamar->id ? 'selected' : '' }}>
                                        Kamar {{ $kamar->nomor_kamar }} - Rp {{ number_format($kamar->harga, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kamar_id')
                                <p class="text-rose-600 text-xs font-medium mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-4 space-y-3">
                            <button
                                type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-sm transition-colors text-sm"
                            >
                                Simpan Perubahan
                            </button>
                            <a
                                href="{{ route('penghuni.index') }}"
                                class="block text-center w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 rounded-xl transition-colors text-sm"
                            >
                                Batal / Kembali
                            </a>
                        </div>
                    </form>

                </div>
            </div>

            {{-- ZONA ADMIN: RESET PASSWORD --}}
            <div class="bg-white rounded-[1.75rem] border-2 border-rose-100 p-6 sm:p-8 text-center">
                <div class="text-3xl mb-3">🔒</div>

                <h3 class="font-black text-slate-800 text-base mb-1.5">
                    Reset Password
                </h3>

                <p class="text-rose-500 text-xs font-medium leading-relaxed mb-5 max-w-xs mx-auto">
                    Klik tombol di bawah untuk mereset sandi penghuni kembali ke default: <strong>12345678</strong>
                </p>

                <form action="{{ route('penghuni.reset_password', $penghuni->id) }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        onclick="return confirm('Yakin ingin mereset password penghuni ini menjadi 12345678?')"
                        class="w-full bg-rose-500 hover:bg-rose-600 text-white font-bold py-3.5 rounded-xl shadow-sm transition-colors text-sm"
                    >
                        Reset
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>