<x-guest-layout>
    <div class="mx-auto max-w-4xl px-4 py-12">
        {{-- Card Container --}}
        <div class="overflow-hidden rounded-[2.5rem] border border-gray-100 bg-white shadow-xl shadow-green-900/5 dark:border-gray-700 dark:bg-gray-800">

            {{-- Decorative Header Aksen Hijau --}}
            <div class="h-3 w-full bg-[#20C896]"></div>

            <div class="p-8 md:p-12">
                {{-- Title & Meta --}}
                <div class="mb-10 flex flex-col items-start justify-between gap-4 border-b border-gray-50 pb-8 md:flex-row md:items-end dark:border-gray-700">
                    <div>
                        <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-green-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-[#20C896] dark:bg-green-900/30">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#20C896] opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-[#20C896]"></span>
                            </span>
                            Dokumen Resmi
                        </div>
                        <h1 class="text-4xl font-black text-gray-900 dark:text-white">Syarat & Ketentuan</h1>
                        <p class="mt-2 text-sm text-gray-500">
                            Terakhir diperbarui: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $setting->updated_at->format('d F Y') }}</span>
                        </p>
                    </div>

                    {{-- Tombol Cetak Kecil di Atas --}}
                    <button onclick="window.print()" class="hidden items-center gap-2 text-sm font-bold text-gray-400 transition hover:text-[#20C896] md:flex">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Cetak
                    </button>
                </div>

                {{-- Konten Utama --}}
                <div class="prose prose-slate prose-lg max-w-none prose-headings:font-bold prose-headings:text-gray-900 prose-p:leading-relaxed prose-p:text-gray-600 prose-li:text-gray-600 prose-strong:text-[#20C896] dark:prose-invert">
                    @if($setting->terms_conditions)
                        {!! $setting->terms_conditions !!}
                    @else
                        <div class="flex flex-col items-center py-20 text-center">
                            <div class="mb-4 rounded-full bg-gray-50 p-6 dark:bg-gray-700">
                                <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-gray-400 italic">Belum ada konten syarat & ketentuan.</p>
                        </div>
                    @endif
                </div>

                {{-- Footer dalam Card --}}
                <div class="mt-16 flex flex-col items-center justify-between gap-6 border-t border-gray-50 pt-10 sm:flex-row dark:border-gray-700">
                    <a href="/" class="group flex items-center gap-2 text-sm font-bold text-gray-500 transition hover:text-[#20C896]">
                        <svg class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Beranda
                    </a>

                    <button onclick="window.print()" class="w-full rounded-2xl bg-[#20C896] px-8 py-4 text-center text-sm font-black text-white shadow-lg shadow-green-200 transition hover:bg-[#1bb386] hover:shadow-xl active:scale-95 sm:w-auto dark:shadow-none">
                        SAYA SETUJU & CETAK
                    </button>
                </div>
            </div>
        </div>

        {{-- Footer luar card --}}
        <p class="mt-8 text-center text-xs font-medium text-gray-400 uppercase tracking-widest">
            {{ $setting->site_name }} &copy; {{ date('Y') }}
        </p>
    </div>
</x-guest-layout>
