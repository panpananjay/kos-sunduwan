<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur-xl border-b border-slate-100 shadow-sm sticky top-0 z-50 transition-all duration-300">
    
    {{-- LOGIKA FORCE NOTIF - DYNAMIC UPDATE SYSTEM --}}
    @php
        $user = auth()->user();
        $notifTagihanCount = 0;
        $notifPengaduanCount = 0;

        if($user) {
            if($user->role == 'admin') {
                // ADMIN: Cek data verifikasi & pengaduan
                $dbTagihan = \App\Models\Tagihan::where('status', 'menunggu_verifikasi')->count();
                $dbPengaduan = \App\Models\Pengaduan::where('status', 'proses')->count();

                // Reset session jika sedang di halaman terkait
                if (request()->routeIs('tagihan.*')) { session(['admin_last_tagihan' => $dbTagihan]); }
                if (request()->routeIs('pengaduan.*')) { session(['admin_last_pengaduan' => $dbPengaduan]); }

                // Muncul badge jika jumlah di DB berubah dari terakhir dilihat
                if ($dbTagihan != session('admin_last_tagihan', 0)) {
                    $notifTagihanCount = $dbTagihan;
                }
                if ($dbPengaduan != session('admin_last_pengaduan', 0)) {
                    $notifPengaduanCount = $dbPengaduan;
                }
            } else {
                // PENGHUNI: Cek tagihan baru & tanggapan admin
                $penghuni = \App\Models\Penghuni::where('user_id', $user->id)->first();
                if($penghuni) {
                    $dbTagihanUser = \App\Models\Tagihan::where('penghuni_id', $penghuni->id)
                                        ->where('status', 'belum_bayar')->count();
                    
                    if (request()->routeIs('tagihan.*')) { session(['user_last_tagihan' => $dbTagihanUser]); }
                    
                    if ($dbTagihanUser != session('user_last_tagihan', 0)) {
                        $notifTagihanCount = $dbTagihanUser;
                    }
                }

                $dbPengaduanUser = \App\Models\Pengaduan::where('user_id', $user->id)
                                    ->where('status', 'diproses')
                                    ->whereNotNull('tanggapan_admin')->count();

                if (request()->routeIs('pengaduan.*')) { session(['user_last_pengaduan' => $dbPengaduanUser]); }

                if ($dbPengaduanUser != session('user_last_pengaduan', 0)) {
                    $notifPengaduanCount = $dbPengaduanUser;
                }
            }
        }
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center group">
                        <img src="{{ asset('images/logo-sunduwan.png') }}" alt="Logo Sunduwan" class="h-12 w-auto object-contain transform scale-[1.3] origin-left transition duration-300 group-hover:scale-[1.4]">
                        <span class="font-black text-xl text-slate-800 tracking-tight hidden md:block ml-4 group-hover:text-rose-600 transition duration-300">SUNDUWAN</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-12 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-bold hover:text-rose-500 transition-colors">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('tagihan.index')" :active="request()->routeIs('tagihan.*')" class="font-bold hover:text-rose-500 transition-colors relative">
                        {{ auth()->user()->role == 'admin' ? __('Data Tagihan') : __('Tagihan') }}

                        @if($notifTagihanCount > 0)
                            <span class="absolute top-4 -right-2 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-rose-500 text-[9px] text-white font-black items-center justify-center border border-white">
                                    {{ $notifTagihanCount }}
                                </span>
                            </span>
                        @endif
                    </x-nav-link>

                    <x-nav-link :href="route('pengaduan.index')" :active="request()->routeIs('pengaduan.*')" class="font-bold hover:text-rose-500 transition-colors relative">
                        {{ __('Pengaduan') }}

                        @if($notifPengaduanCount > 0)
                            <span class="absolute top-4 -right-2 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-rose-500 text-[9px] text-white font-black items-center justify-center border border-white">
                                    {{ $notifPengaduanCount }}
                                </span>
                            </span>
                        @endif
                    </x-nav-link>

                    @if(auth()->user()->role == 'admin')
                        <x-nav-link :href="route('kamar.index')" :active="request()->routeIs('kamar.*')" class="font-bold hover:text-rose-500 transition-colors">
                            {{ __('Data Kamar') }}
                        </x-nav-link>
                        <x-nav-link :href="route('penghuni.index')" :active="request()->routeIs('penghuni.*')" class="font-bold hover:text-rose-500 transition-colors">
                            {{ __('Data Penghuni') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2.5 border border-slate-200 text-sm leading-4 font-bold rounded-full text-slate-700 bg-slate-50 hover:text-rose-600 hover:border-rose-200 hover:bg-rose-50 focus:outline-none transition ease-in-out duration-300 shadow-sm">
                            <div class="flex items-center gap-2">
                                <span class="bg-rose-100 text-rose-600 rounded-full w-6 h-6 flex items-center justify-center text-xs">👤</span>
                                {{ Auth::user()->name }}
                            </div>
                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="py-1 bg-white rounded-xl shadow-lg border border-slate-100">
                            <x-dropdown-link :href="route('profile.edit')" class="hover:bg-rose-50 hover:text-rose-600 font-medium transition">
                                ⚙️ {{ __('Profile Saya') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();" class="hover:bg-rose-50 hover:text-rose-600 font-medium transition text-rose-500">
                                    🚪 {{ __('Keluar / Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 focus:outline-none transition duration-300 ease-in-out relative">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>

                    @if($notifTagihanCount > 0 || $notifPengaduanCount > 0)
                        <span x-show="!open" class="absolute top-2 right-2 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 border-2 border-white"></span>
                        </span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-b border-slate-100 shadow-lg absolute w-full z-50">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-bold">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('tagihan.index')" :active="request()->routeIs('tagihan.*')" class="font-bold flex justify-between items-center">
                <span>{{ auth()->user()->role == 'admin' ? __('Data Tagihan') : __('Tagihan') }}</span>
                @if($notifTagihanCount > 0)
                    <span class="bg-rose-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black">{{ $notifTagihanCount }}</span>
                @endif
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('pengaduan.index')" :active="request()->routeIs('pengaduan.*')" class="font-bold flex justify-between items-center">
                <span>{{ __('Pengaduan') }}</span>
                @if($notifPengaduanCount > 0)
                    <span class="bg-rose-500 text-white text-[10px] px-2 py-0.5 rounded-full font-black">{{ $notifPengaduanCount }}</span>
                @endif
            </x-responsive-nav-link>

            @if(auth()->user()->role == 'admin')
                <x-responsive-nav-link :href="route('kamar.index')" :active="request()->routeIs('kamar.*')" class="font-bold">
                    {{ __('Data Kamar') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('penghuni.index')" :active="request()->routeIs('penghuni.*')" class="font-bold">
                    {{ __('Data Penghuni') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-4 border-t border-slate-100 bg-slate-50">
            <div class="px-4 flex items-center gap-3">
                <div class="bg-rose-100 text-rose-600 rounded-full w-10 h-10 flex items-center justify-center text-lg shadow-sm">👤</div>
                <div>
                    <div class="font-bold text-base text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-slate-500">{{ Auth::user()->username }}</div>
                </div>
            </div>

            <div class="mt-4 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="font-medium hover:text-rose-600">
                    ⚙️ {{ __('Profile Saya') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="font-medium text-rose-500 hover:text-rose-700 hover:bg-rose-50">
                        🚪 {{ __('Keluar / Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>