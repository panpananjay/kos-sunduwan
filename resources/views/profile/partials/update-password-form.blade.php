<section>
    <header class="mb-6">
        <h2 class="text-xl font-black text-slate-800 tracking-tight">
            {{ __('Update Password') }}
        </h2>
        <p class="mt-1 text-sm text-slate-500 font-medium">
            {{ __('Gunakan password yang kuat agar akun Anda tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 font-bold focus:ring-rose-500 focus:border-rose-500 shadow-sm" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Password Baru')" class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 font-bold focus:ring-rose-500 focus:border-rose-500 shadow-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password')" class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 font-bold focus:ring-rose-500 focus:border-rose-500 shadow-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-slate-800 hover:bg-black text-white font-black py-3 px-8 rounded-xl shadow-lg shadow-slate-200 transition duration-300 transform hover:-translate-y-0.5 text-xs uppercase tracking-widest">
                {{ __('Ganti Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm font-bold text-emerald-600">
                    {{ __('Password Berhasil Diganti! ✅') }}
                </p>
            @endif
        </div>
    </form>
</section>