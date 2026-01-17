<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $siteSettings->site_name ?? config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="flex min-h-screen flex-col items-center bg-gray-50 px-4 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-900">
        {{-- Logo Area --}}
        <div class="mb-6">
            <a href="/" class="flex flex-col items-center transition duration-300 hover:scale-105">
                {{-- Box logo dibuat lebih besar dan transparan agar fokus ke gambar --}}
                <div class="flex items-center justify-center p-2">
                    @if ($siteSettings && $siteSettings->logo)
                        {{-- Ukuran h-20 (80px) agar teks di dalam logo terbaca jelas --}}
                        <img src="{{ asset('storage/' . $siteSettings->logo) }}" alt="Logo {{ $siteSettings->site_name }}" class="h-20 w-auto object-contain">
                    @else
                        {{-- Fallback jika logo tidak ada --}}
                        <div class="rounded-2xl bg-[#20C896] p-4 shadow-lg shadow-green-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-2xl font-black tracking-tight text-gray-800 dark:text-white">
                            {{ $siteSettings->site_name ?? config('app.name') }}
                        </h2>
                    @endif
                </div>
            </a>
        </div>

        {{-- Card Box --}}
        <div class="{{ request()->routeIs('login', 'register', 'password.*') ? 'sm:max-w-md' : 'max-w-6xl' }} w-full rounded-[2.5rem] border border-green-50 bg-white px-8 py-10 shadow-[0_20px_60px_-15px_rgba(32,200,150,0.15)] dark:border-gray-700 dark:bg-gray-800">
            {{ $slot }}
        </div>


    </div>
</body>

</html>
