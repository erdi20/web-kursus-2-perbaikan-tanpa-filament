<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-16">

        <div class="mb-12 flex flex-col justify-between gap-4 px-2 md:flex-row md:items-end">
            <div>
                <div class="mb-1 flex items-center gap-2">
                    <span class="h-1 w-8 rounded-full bg-green-500"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-green-600">Eksplorasi</span>
                </div>
                <h3 class="text-3xl font-black tracking-tight text-slate-900 md:text-4xl">Semua <span class="italic text-green-600">Kursus</span></h3>
            </div>
            <p class="max-w-xs text-sm font-medium italic text-slate-400 md:text-right">
                "Investasi terbaik adalah investasi pada leher ke atas."
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($courses as $course)
                @php
                    $isDiscountActive = $course->discount_price !== null && ($course->discount_end_date === null || now()->lessThan($course->discount_end_date));
                @endphp

                <article class="group relative flex flex-col rounded-[2.5rem] border border-slate-100 bg-white p-3 transition-all duration-300 hover:border-transparent hover:shadow-[0_20px_50px_rgba(0,0,0,0.05)]">

                    <div class="relative h-48 w-full overflow-hidden rounded-[2rem]">
                        <img src="{{ asset('storage/' . ($course->thumbnail ?? 'default.jpg')) }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">

                        <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between">
                            <div class="flex flex-col rounded-2xl border border-white/50 bg-white/90 px-4 py-2 shadow-lg backdrop-blur-md">
                                @if ($isDiscountActive)
                                    <span class="text-[9px] font-bold text-slate-400 line-through">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                    <span class="text-sm font-black text-green-600">Rp {{ number_format($course->discount_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-sm font-black text-slate-900">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-1 rounded-xl bg-slate-900/80 px-2.5 py-1.5 text-white backdrop-blur-md">
                                <svg class="h-3 w-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-[10px] font-black">{{ number_format($course->avg_rating ?? 0, 1) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-1 flex-col px-3 py-4">
                        <div class="mb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <img src="{{ $course->user->avatar_url ? asset('storage/' . $course->user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($course->user->name) }}" class="h-6 w-6 rounded-full border border-slate-100 object-cover">
                                <span class="max-w-[100px] truncate text-[11px] font-bold uppercase tracking-tight text-slate-500">{{ $course->user->name }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
                                <span class="text-[10px] font-bold text-slate-400">{{ number_format($course->enrollment_count ?? 0) }} Alumni</span>
                            </div>
                        </div>

                        <h4 class="mb-4 line-clamp-2 min-h-[2.5rem] text-[17px] font-bold leading-tight text-slate-900 transition-colors group-hover:text-green-600">
                            {{ $course->name }}
                        </h4>

                        <div class="mt-auto flex items-center justify-between border-t border-slate-50 pt-2">
                            <span class="text-[10px] font-medium italic text-slate-400">({{ $course->review_count ?? 0 }} Ulasan)</span>
                            <a href="{{ route('detailkursus', $course->slug) }}" class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 text-slate-900 transition-all duration-500 group-hover:rotate-[360deg] group-hover:bg-slate-900 group-hover:text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-20 text-center">
                    <p class="text-lg font-medium text-slate-400">Belum ada kursus yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-16 border-t border-gray-100 pt-10">
            <div class="flex flex-col items-center gap-6">
                {{-- Info Bahasa Indonesia tetap di sini --}}
                <p class="text-sm font-medium text-gray-500">
                    Menampilkan <span class="font-bold text-gray-900">{{ $courses->firstItem() }}</span>
                    sampai <span class="font-bold text-gray-900">{{ $courses->lastItem() }}</span>
                    dari <span class="font-bold text-gray-900">{{ $courses->total() }}</span> kursus
                </p>

                {{-- Ini akan memanggil file yang kita edit di vendor tadi --}}
                <div>
                    {{ $courses->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
