@php
    $setting = \App\Models\Setting::first();
@endphp

<nav x-data="{ open: false }" class="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
    {{-- Bar Atas: Logo & User --}}
    <div class="w-full bg-white px-4 py-2 sm:px-6 lg:px-8 dark:bg-gray-800">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between">

            {{-- Logo Section --}}
            <div class="flex shrink-0 items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center transition hover:opacity-80">
                    <x-application-logo :logo-path="$setting?->logo" class="block h-12 w-auto" />
                </a>
                <div class="mx-2 h-8 border-l border-gray-200"></div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold leading-none text-[#20C896]">ADMINISTRATOR</span>
                    <span class="text-[10px] font-medium uppercase tracking-tighter text-gray-500">Main Control Center</span>
                </div>
            </div>

            {{-- Right Side Section --}}
            <div class="flex items-center gap-4">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center gap-3 rounded-full border border-gray-200 bg-gray-50 p-1.5 pr-4 transition hover:bg-gray-100 focus:outline-none dark:border-gray-600 dark:bg-gray-700">
                                <div class="shrink-0">
                                    @if (Auth::user()->avatar_url)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar_url) }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-white">
                                    @else
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#20C896] text-xs font-bold text-white shadow-sm">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="hidden text-left md:block">
                                    <p class="text-xs font-bold leading-tight text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] leading-tight text-gray-500">Super Admin</p>
                                </div>

                                <svg class="hidden h-4 w-4 text-gray-400 md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">System</div>
                            <x-dropdown-link :href="route('admin.profile.edit')">{{ __('Profil Admin') }}</x-dropdown-link>
                            <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="font-semibold text-red-600">
                                    {{ __('Keluar Panel') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                {{-- Hamburger Mobile --}}
                <button @click="open = !open" class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 focus:outline-none sm:hidden dark:hover:bg-gray-700">
                    <svg class="h-6 w-6" :class="{ 'hidden': open, 'block': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="h-6 w-6" :class="{ 'block': open, 'hidden': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    {{-- Bar Bawah: Menu Navigasi (Warna Hijau #20C896) --}}
    <div class="w-full bg-[#20C896] px-4 shadow-lg sm:px-6 lg:px-8">
        <div class="mx-auto flex h-14 max-w-7xl items-center justify-start"> {{-- Tinggi naik dikit ke h-14 biar lega --}}

            {{-- Desktop Navigation --}}
            <div class="hidden h-full space-x-2 text-white sm:flex">

                @php
                    // Helper array untuk memudahkan management menu
                    $menus = [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard'],
                        ['route' => 'admin.users.index', 'label' => 'Data Pengguna'],
                        ['route' => 'admin.withdrawal.index', 'label' => 'Withdrawal Mentor'],
                        ['route' => 'admin.slider', 'label' => 'Slider Banner'],
                        ['route' => 'admin.faq', 'label' => 'FAQ'],
                        ['route' => 'admin.settings.edit', 'label' => 'Setting Web'],
                    ];
                @endphp

                @foreach ($menus as $menu)
                    <a href="{{ route($menu['route']) }}" class="{{ request()->routeIs($menu['route']) ? 'bg-white/20' : '' }} group relative flex h-full items-center px-5 text-[11px] font-black uppercase tracking-widest text-white transition-all duration-300 hover:bg-white/10">

                        {{ __($menu['label']) }}

                        {{-- Indikator Aktif: Garis bawah putih yang glow --}}
                        @if (request()->routeIs($menu['route']))
                            <div class="absolute bottom-0 left-0 h-1 w-full bg-white shadow-[0_-4px_10px_rgba(255,255,255,0.5)]"></div>
                        @endif
                    </a>
                @endforeach
            </div>

            {{-- Mobile Menu Button (Hamburger) --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-white transition hover:bg-white/10 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Links --}}
        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:leave="transition ease-in duration-150" class="border-t border-white/10 pb-4 pt-2 sm:hidden" x-cloak>
            <div class="space-y-1">
                @foreach ($menus as $menu)
                    <x-responsive-nav-link :href="route($menu['route'])" :active="request()->routeIs($menu['route'])" class="{{ request()->routeIs($menu['route']) ? 'bg-white/20' : '' }} block border-none py-3 font-black uppercase tracking-widest !text-white hover:bg-white/10">
                        {{ __($menu['label']) }}
                    </x-responsive-nav-link>
                @endforeach
            </div>
        </div>
    </div>
</nav>
