@php
    $setting = \App\Models\Setting::first();
    $studentMenus = [['route' => 'dashboard', 'label' => 'Beranda'], ['route' => 'listkursus', 'label' => 'Jelajah Kursus'], ['route' => 'listkelas', 'label' => 'Kelas Saya']];
@endphp

<nav x-data="{ open: false }" class="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
    {{-- Bar Atas: Logo, Search, & User --}}
    <div class="w-full bg-white px-4 py-2 sm:px-6 lg:px-8 dark:bg-gray-800">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4">

            {{-- Logo --}}
            <div class="flex shrink-0 items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center transition hover:opacity-80">
                    <x-application-logo :logo-path="$setting?->logo" class="block h-10 w-auto sm:h-12" />
                </a>
            </div>

            {{-- Search Bar (Desktop) --}}
            <div class="mx-4 hidden w-full max-w-md md:block">
                <form method="GET" action="{{ route('listkursus') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Mau belajar apa hari ini?" class="w-full rounded-full border-gray-200 bg-gray-50 px-4 py-2 pl-10 text-sm text-gray-800 transition focus:border-[#20C896] focus:ring-2 focus:ring-[#20C896]/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </form>
            </div>

            {{-- Right Side --}}
            <div class="flex items-center gap-2">
                @auth
                    {{-- Desktop Profile Dropdown --}}
                    <div class="hidden sm:block">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-3 rounded-full border border-gray-200 bg-gray-50 p-1 pr-4 transition hover:bg-gray-100 focus:outline-none dark:border-gray-600 dark:bg-gray-700">
                                    <div class="shrink-0">
                                        @if (Auth::user()->avatar_url)
                                            <img src="{{ asset('storage/' . Auth::user()->avatar_url) }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-white">
                                        @else
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#20C896] text-xs font-bold text-white shadow-sm">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="max-w-[100px] truncate text-xs font-bold text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">{{ __('Profil Saya') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('listkelas')">{{ __('Kelas Saya') }}</x-dropdown-link>
                                <div class="my-1 border-t border-gray-100 dark:border-gray-700"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="font-bold text-red-600">{{ __('Keluar') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="hidden space-x-3 items-center sm:flex">
                        <a href="{{ route('login') }}" class="text-sm font-bold text-gray-700 hover:text-[#20C896] dark:text-gray-300">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-[#20C896] px-5 py-2 text-sm font-bold text-white shadow-md transition hover:bg-[#1bb386]">Daftar</a>
                    </div>
                @endauth

                {{-- Hamburger Button (Mobile Utama) --}}
                <button @click="open = !open" class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 focus:outline-none sm:hidden dark:hover:bg-gray-700">
                    <svg class="h-6 w-6" :class="{ 'hidden': open, 'block': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg class="h-6 w-6" :class="{ 'block': open, 'hidden': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Bar Bawah: Navigasi Desktop (Hijau #20C896) --}}
    <div class="hidden w-full bg-[#20C896] px-4 shadow-lg sm:block sm:px-6 lg:px-8">
        <div class="mx-auto flex h-12 max-w-7xl items-center justify-start">
            <div class="flex h-full space-x-1">
                @foreach ($studentMenus as $menu)
                    <a href="{{ route($menu['route']) }}" class="{{ request()->routeIs($menu['route']) ? 'bg-white/20' : '' }} group relative flex h-full items-center px-5 text-[11px] font-black uppercase tracking-widest text-white transition-all duration-300 hover:bg-white/10">
                        {{ __($menu['label']) }}
                        @if (request()->routeIs($menu['route']))
                            <div class="absolute bottom-0 left-0 h-1 w-full bg-white shadow-[0_-4px_10px_rgba(255,255,255,0.5)]"></div>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Mobile Full Menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" class="border-t border-gray-100 bg-white sm:hidden dark:border-gray-700 dark:bg-gray-800" x-cloak>

        {{-- Search in Mobile --}}
        <div class="p-4">
            <form method="GET" action="{{ route('listkursus') }}" class="relative">
                <input type="text" name="search" placeholder="Cari kursus..." class="w-full rounded-xl border-gray-200 bg-gray-50 py-2 pl-10 text-sm focus:border-[#20C896] focus:ring-[#20C896]/20">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </form>
        </div>

        <div class="space-y-1 px-2 pb-3">
            @foreach ($studentMenus as $menu)
                <x-responsive-nav-link :href="route($menu['route'])" :active="request()->routeIs($menu['route'])" class="{{ request()->routeIs($menu['route']) ? 'bg-[#20C896]/10 !text-[#20C896]' : 'text-gray-600' }} block rounded-lg border-none py-3 font-bold">
                    {{ __($menu['label']) }}
                </x-responsive-nav-link>
            @endforeach

            <div class="my-2 border-t border-gray-100 dark:border-gray-700"></div>

            @auth
                <x-responsive-nav-link :href="route('profile.edit')" class="block border-none py-3 font-bold text-gray-600">
                    {{ __('Profil Saya') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 text-left text-sm font-bold text-red-600">
                        {{ __('Keluar') }}
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-4 py-3 text-sm font-bold text-gray-700">Masuk</a>
                <div class="px-4 pb-2">
                    <a href="{{ route('register') }}" class="block rounded-lg bg-[#20C896] py-2 text-center text-sm font-bold text-white">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
