@php
    $setting = \App\Models\Setting::first();
@endphp

<nav x-data="{ open: false }" class="border-b border-gray-200 bg-teal-50 dark:border-gray-700 dark:bg-gray-800">
    <div class="w-full border-b border-gray-100 bg-teal-50 px-4 py-3 sm:px-6 lg:px-8 dark:border-gray-700 dark:bg-gray-800">
        <div class="mx-auto flex h-14 max-w-7xl items-center justify-between">

            <div class="flex shrink-0 items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center transition hover:opacity-80">
                    <x-application-logo :logo-path="$setting?->logo" class="block h-14 w-auto" />
                </a>
            </div>

            <div class="mx-4 hidden w-full max-w-md md:block">
                <form method="GET" action="{{ route('listkursus') }}" class="relative">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kursus..." class="w-full rounded-full border-gray-600 bg-gray-50 px-4 py-2 pl-10 text-sm text-gray-800 placeholder-gray-400 shadow-sm transition focus:border-[#20C896] focus:ring-2 focus:ring-[#20C896]/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </form>
            </div>

            <div class="flex items-center">
                @if (Route::has('login'))
                    @auth
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="ml-2 flex max-w-[150px] items-center gap-2 rounded-full border border-gray-600 p-1 pr-3 transition hover:bg-gray-50 focus:outline-none sm:max-w-[200px] dark:border-gray-600 dark:hover:bg-gray-700">
                                    {{-- Avatar Area --}}
                                    <div class="shrink-0"> {{-- shrink-0 supaya lingkaran avatar gak kegencet --}}
                                        @if (Auth::user()->avatar_url)
                                            <img src="{{ asset('storage/' . Auth::user()->avatar_url) }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full object-cover">
                                        @else
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#20C896] text-xs font-bold text-white">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Nama User dengan Truncate --}}
                                    <span class="hidden truncate text-sm font-semibold text-gray-700 md:block dark:text-gray-200">
                                        {{ Auth::user()->name }}
                                    </span>

                                    {{-- Icon Chevron (Opsional tapi bagus untuk indikator dropdown) --}}
                                    <svg class="hidden h-4 w-4 text-gray-400 md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">

                                <x-dropdown-link :href="route('profile.edit')">{{ __('Profil') }}</x-dropdown-link>
                                <x-dropdown-link :href="route('listkelas')">{{ __('Kelas Saya') }}</x-dropdown-link>
                                <div class="my-1 border-t border-gray-200 dark:border-gray-700"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                                        {{ __('Keluar') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <div class="space-x-4">
                            <a href="{{ route('login') }}" class="text-sm font-bold text-gray-700 hover:text-[#20C896] dark:text-gray-300">Masuk</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-[#20C896] px-5 py-2 text-sm font-bold text-white shadow-md transition hover:bg-[#1bb386]">Daftar</a>
                        </div>
                    @endauth
                @endif

                <button @click="open = !open" class="ml-4 inline-flex items-center justify-center rounded-md p-2 text-gray-500 hover:bg-gray-100 focus:outline-none sm:hidden dark:hover:bg-gray-700">
                    <svg :class="{ 'hidden': open, 'block': !open }" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg :class="{ 'block': open, 'hidden': !open }" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="w-full bg-[#20C896] px-4 shadow-inner sm:px-6 lg:px-8">
        <div class="mx-auto flex h-11 max-w-7xl items-center justify-center">
            <div class="hidden space-x-8 sm:flex"> <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="!border-none px-4 py-2 text-sm font-bold text-white transition hover:bg-teal-50/10">
                    {{ __('Beranda') }}
                </x-nav-link>
                <x-nav-link :href="route('listkursus')" :active="request()->routeIs('listkursus')" class="!border-none px-4 py-2 text-sm font-bold text-white transition hover:bg-teal-50/10">
                    {{ __('Kursus') }}
                </x-nav-link>
            </div>
        </div>

        <div x-show="open" x-transition class="border-t border-white/20 pb-4 pt-2 sm:hidden">
            <div class="space-y-1">
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="!text-white">
                    {{ __('Beranda') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('listkursus')" :active="request()->routeIs('listkursus')" class="!text-white">
                    {{ __('Kursus') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('listkelas')" :active="request()->routeIs('listkelas')" class="!text-white">
                    {{ __('Kelas Saya') }}
                </x-responsive-nav-link>
            </div>
        </div>
    </div>
</nav>
