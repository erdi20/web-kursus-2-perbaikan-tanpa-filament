@php
    $setting = \App\Models\Setting::first();
@endphp

<footer class="bg-[#0f172a] py-16 text-gray-400">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-12 grid grid-cols-1 gap-12 md:grid-cols-4">

            {{-- Kolom 1: Branding --}}
            <div class="col-span-1 md:col-span-1">
                <div class="mb-6">
                    @if($setting?->logo)
                        <img src="{{ asset('storage/' . $setting->logo) }}" alt="Logo" class="h-12 w-auto brightness-0 invert">
                    @else
                        <span class="text-2xl font-black tracking-tight text-white">
                            {{ $setting?->site_name ?: 'Qualitative Research' }}
                        </span>
                    @endif
                </div>
                <p class="text-sm leading-relaxed">
                    {{ $setting?->site_description ?: 'Membantu Anda menguasai metode penelitian kualitatif dengan panduan praktis dan dukungan komunitas.' }}
                </p>
                {{-- Social Media Icons --}}
                <div class="mt-6 flex space-x-4">
                    @foreach(['facebook', 'twitter', 'instagram', 'linkedin'] as $social)
                        @php $url = $social . '_url'; @endphp
                        @if ($setting?->$url)
                            <a href="{{ $setting->$url }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-800 text-gray-400 transition-all hover:bg-[#20C896] hover:text-white" target="_blank">
                                <i class="fab fa-{{ $social === 'linkedin' ? 'linkedin-in' : $social }} text-lg"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Kolom 2: Navigasi Cepat --}}
            <div>
                <h3 class="mb-6 text-sm font-bold uppercase tracking-wider text-white">Navigasi</h3>
                <ul class="space-y-4">
                    <li><a href="{{ route('dashboard') }}" class="text-sm transition hover:text-[#20C896]">Beranda</a></li>
                    <li><a href="{{ route('listkursus') }}" class="text-sm transition hover:text-[#20C896]">Kursus</a></li>
                    <li><a href="{{ route('listkelas') }}" class="text-sm transition hover:text-[#20C896]">Kelas Saya</a></li>
                </ul>
            </div>

            {{-- Kolom 3: Bantuan --}}
            <div>
                <h3 class="mb-6 text-sm font-bold uppercase tracking-wider text-white">Bantuan</h3>
                <ul class="space-y-4">
                    <li><a href="{{ route('dashboard') }}#faq-section" class="text-sm transition hover:text-[#20C896]">FAQ</a></li>
                    <li><a href="{{ route('contact.us') }}" class="text-sm transition hover:text-[#20C896]">Hubungi Kami</a></li>
                    <li><a href="{{ route('privacy.policy') }}" class="text-sm transition hover:text-[#20C896]">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('terms') }}" class="text-sm transition hover:text-[#20C896]">Syarat & Ketentuan</a></li>
                </ul>
            </div>

            {{-- Kolom 4: Kontak (Pengganti Subscribe) --}}
            <div>
                <h3 class="mb-6 text-sm font-bold uppercase tracking-wider text-white">Kontak Kami</h3>
                <ul class="space-y-4">
                    @if($setting?->email)
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 shrink-0 text-[#20C896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-sm">{{ $setting->email }}</span>
                    </li>
                    @endif

                    @if($setting?->phone)
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 shrink-0 text-[#20C896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="text-sm">{{ $setting->phone }}</span>
                    </li>
                    @endif

                    @if($setting?->address)
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 shrink-0 text-[#20C896]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-sm leading-relaxed">{{ $setting->address }}</span>
                    </li>
                    @endif
                </ul>
            </div>

        </div>

        {{-- Bagian Copyright --}}
        <div class="mt-8 border-t border-gray-800 pt-8 text-center text-sm">
            <p class="text-gray-500">
                {!! $setting?->copyright_text ?: '&copy; ' . date('Y') . ' ' . ($setting?->site_name ?: 'Qualitative Research Class') . '. All rights reserved.' !!}
            </p>
        </div>
    </div>
</footer>
