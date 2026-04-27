<section>
    <header class="mb-6">
        <h2 class="text-xl font-black text-slate-800 tracking-tight">
            {{ __('Informasi Profil') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500 font-medium">
            {{ __("Perbarui nama akun dan username Anda di sini.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 font-bold focus:ring-rose-500 focus:border-rose-500 shadow-sm" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('username')" class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 font-bold focus:ring-rose-500 focus:border-rose-500 shadow-sm" :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-gradient-to-r from-rose-500 to-fuchsia-600 hover:from-rose-600 hover:to-fuchsia-700 text-white font-black py-3 px-8 rounded-xl shadow-lg shadow-rose-100 transition duration-300 transform hover:-translate-y-0.5 text-xs uppercase tracking-widest">
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm font-bold text-emerald-600">
                    {{ __('Berhasil Diperbaharui! ✅') }}
                </p>
            @endif
        </div>
    </form>
</section>