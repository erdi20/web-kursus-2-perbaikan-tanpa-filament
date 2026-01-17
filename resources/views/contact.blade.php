<x-guest-layout>
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-16 text-center">
            <h1 class="text-4xl font-black tracking-tight text-gray-900 sm:text-5xl dark:text-white">
                Hubungi <span class="text-[#20C896]">Kami</span>
            </h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600 dark:text-gray-400">
                Ada pertanyaan atau butuh bimbingan mengenai penelitian kualitatif? Tim kami siap mendampingi langkah akademis Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">

            {{-- Info Kontak Sidebar --}}
            <div class="space-y-4">
                {{-- Card Email --}}
                <div class="group relative overflow-hidden rounded-[2rem] border border-gray-100 bg-white p-8 transition-all hover:border-[#20C896] hover:shadow-xl hover:shadow-green-900/5 dark:border-gray-800 dark:bg-gray-800">
                    <div class="flex items-center gap-6">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#20C896] text-white shadow-lg shadow-green-200 transition-transform group-hover:scale-110 dark:shadow-none">
                            <i class="fas fa-envelope text-xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-bold uppercase tracking-widest text-gray-400">Email Resmi</h4>
                            <p class="truncate text-lg font-bold text-gray-900 dark:text-white">{{ $setting->email }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card Telepon --}}
                <div class="group relative overflow-hidden rounded-[2rem] border border-gray-100 bg-white p-8 transition-all hover:border-[#20C896] hover:shadow-xl hover:shadow-green-900/5 dark:border-gray-800 dark:bg-gray-800">
                    <div class="flex items-center gap-6">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#20C896] text-white shadow-lg shadow-green-200 transition-transform group-hover:scale-110 dark:shadow-none">
                            <i class="fas fa-phone text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold uppercase tracking-widest text-gray-400">Telepon / WA</h4>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $setting->phone }}</p>
                        </div>
                    </div>
                </div>

                {{-- Card Alamat --}}
                <div class="group relative overflow-hidden rounded-[2rem] border border-gray-100 bg-white p-8 transition-all hover:border-[#20C896] hover:shadow-xl hover:shadow-green-900/5 dark:border-gray-800 dark:bg-gray-800">
                    <div class="flex items-center gap-6">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-[#20C896] text-white shadow-lg shadow-green-200 transition-transform group-hover:scale-110 dark:shadow-none">
                            <i class="fas fa-map-marker-alt text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold uppercase tracking-widest text-gray-400">Kantor Kami</h4>
                            <p class="text-sm font-medium leading-relaxed text-gray-600 dark:text-gray-400">
                                {{ $setting->address }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Google Maps --}}
            <div class="lg:col-span-2">
                <div class="relative h-full min-h-[450px] overflow-hidden rounded-[3rem] border-[12px] border-white bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-800">
                    @if($setting->gmaps_embed_url)
                        <iframe
                            src="{{ $setting->gmaps_embed_url }}"
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            class="absolute inset-0 h-full w-full grayscale transition-all duration-700 hover:grayscale-0 focus:outline-none"
                        ></iframe>
                    @else
                        <div class="flex h-full w-full flex-col items-center justify-center gap-4 bg-gray-50 dark:bg-gray-700/50 text-gray-400 italic">
                            <i class="fas fa-map-marked-alt text-4xl"></i>
                            <p>Data lokasi peta belum tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Footer Action Area (Samakan dengan View Sebelumnya) --}}
        <div class="mt-16 flex flex-col items-center justify-between gap-6 border-t border-gray-100 pt-10 sm:flex-row dark:border-gray-700">
            <a href="/" class="group flex items-center gap-2 text-sm font-bold text-gray-500 transition hover:text-[#20C896]">
                <svg class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>

            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $setting->phone) }}" target="_blank"
               class="flex items-center gap-3 rounded-2xl bg-[#20C896] px-8 py-4 text-sm font-black text-white shadow-lg shadow-green-100 transition hover:bg-[#1bb386] hover:shadow-xl active:scale-95">
                <i class="fab fa-whatsapp text-lg"></i>
                KONSULTASI VIA WHATSAPP
            </a>
        </div>

        {{-- Copyright luar --}}
        <p class="mt-12 text-center text-xs font-medium text-gray-400 uppercase tracking-widest">
            {{ $setting->site_name }} &copy; {{ date('Y') }}
        </p>
    </div>
</x-guest-layout>
