@php
    $setting = \App\Models\Setting::first();
    $menus = [
        ['route' => 'mentor.dashboardmentor', 'label' => 'Dashboard'],
        ['route' => 'mentor.kursus', 'label' => 'Kursus Saya'],
        ['route' => 'mentor.laporan-keuangan', 'label' => 'Laporan Keuangan']
    ];
@endphp

<nav x-data="{ open: false }" class="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
    {{-- Bar Atas: Logo & User --}}
    <div class="w-full bg-white px-4 py-2 sm:px-6 lg:px-8 dark:bg-gray-800">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between">

            {{-- Logo Section --}}
            <div class="flex shrink-0 items-center gap-3">
                <a href="{{ route('mentor.dashboardmentor') }}" class="flex items-center transition hover:opacity-80">
                    <x-application-logo :logo-path="$setting?->logo" class="block h-12 w-auto" />
                </a>
                <div class="mx-2 h-8 border-l border-gray-200"></div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold leading-none text-gray-800 dark:text-white">MENTOR</span>
                    <span class="text-[10px] font-medium uppercase tracking-tighter text-gray-500">Control Panel</span>
                </div>
            </div>

            {{-- Right Side Section --}}
            <div class="flex items-center gap-2 sm:gap-4">
                @auth
                    {{-- Profile Dropdown (Desktop) --}}
                    <div class="hidden sm:block">
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
                                    <div class="text-left">
                                        <p class="text-xs font-bold leading-tight text-gray-800 dark:text-white">{{ Auth::user()->name }}</p>
                                        <p class="text-[10px] leading-tight text-gray-500">Mentor</p>
                                    </div>
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-400">Personal</div>
                                <x-dropdown-link :href="route('mentor.mentoredit')">{{ __('Profil Mentor') }}</x-dropdown-link>
                                <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="font-semibold text-red-600">
                                        {{ __('Keluar Panel') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    {{-- Profile Avatar Mini (Mobile Only - Samping Hamburger) --}}
                    <div class="sm:hidden shrink-0">
                         @if (Auth::user()->avatar_url)
                            <img src="{{ asset('storage/' . Auth::user()->avatar_url) }}" class="h-8 w-8 rounded-full object-cover border border-[#20C896]">
                        @else
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#20C896] text-[10px] font-bold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                @endauth

                {{-- Hamburger Button (Mobile Utama) --}}
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

    {{-- Bar Bawah: Navigasi (Hanya muncul di Desktop) --}}
    <div class="hidden w-full bg-[#20C896] px-4 shadow-lg sm:block sm:px-6 lg:px-8">
        <div class="mx-auto flex h-14 max-w-7xl items-center justify-start">
            <div class="flex h-full space-x-1 text-white">
                @foreach ($menus as $menu)
                    <a href="{{ route($menu['route']) }}"
                       class="{{ request()->routeIs($menu['route']) ? 'bg-white/20' : '' }} group relative flex h-full items-center px-5 text-[11px] font-black uppercase tracking-widest text-white transition-all duration-300 hover:bg-white/10">
                        {{ __($menu['label']) }}
                        @if (request()->routeIs($menu['route']))
                            <div class="absolute bottom-0 left-0 h-1 w-full bg-white shadow-[0_-4px_10px_rgba(255,255,255,0.5)]"></div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Mobile Full Menu (Dropdown saat Hamburger diklik) --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:leave="transition ease-in duration-150"
         class="sm:hidden bg-[#20C896] border-t border-white/10 shadow-xl"
         x-cloak>

        {{-- Profile Info in Mobile --}}
        <div class="px-4 py-4 border-b border-white/10 bg-black/5">
            <div class="flex items-center gap-3">
                <div class="text-white">
                    <p class="text-sm font-bold">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] opacity-70">Mentor Control Panel</p>
                </div>
            </div>
        </div>

        {{-- Navigasi Menu --}}
        <div class="p-2 space-y-1">
            @foreach ($menus as $menu)
                <x-responsive-nav-link :href="route($menu['route'])" :active="request()->routeIs($menu['route'])"
                    class="{{ request()->routeIs($menu['route']) ? 'bg-white/20' : '' }} block border-none rounded-lg py-3 text-[10px] font-black uppercase tracking-widest !text-white hover:bg-white/10">
                    {{ __($menu['label']) }}
                </x-responsive-nav-link>
            @endforeach

            <div class="my-2 border-t border-white/10"></div>

            {{-- Personal Links in Mobile --}}
            <x-responsive-nav-link :href="route('mentor.mentoredit')" class="block border-none py-3 text-[10px] font-black uppercase tracking-widest !text-white opacity-80">
                {{ __('Profil Mentor') }}
            </x-responsive-nav-link>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-3 text-[10px] font-black uppercase tracking-widest text-red-200 hover:bg-red-500/20 rounded-lg">
                    {{ __('Keluar Panel') }}
                </button>
            </form>
        </div>
    </div>
</nav>
