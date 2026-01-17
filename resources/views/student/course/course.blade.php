<x-app-layout>
    <div class="relative overflow-hidden bg-slate-900 py-16 lg:py-20">
        <div class="absolute right-0 top-0 -mr-20 -mt-20 h-96 w-96 rounded-full bg-green-500/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-20 -ml-20 h-80 w-80 rounded-full bg-indigo-500/10 blur-3xl"></div>

        <div class="relative mx-auto max-w-7xl px-4">
            <div class="flex flex-col gap-12 lg:flex-row lg:items-center">
                <div class="lg:w-7/12">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-green-500/20 bg-green-500/10 px-4 py-1.5">
                        <span class="flex h-2 w-2 animate-pulse rounded-full bg-green-500"></span>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-green-400">Kursus Terverifikasi</span>
                    </div>
                    <h1 class="mb-6 text-4xl font-black leading-tight text-white md:text-6xl">
                        {{ $course->name }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-6 text-slate-400">
                        <div class="flex items-center gap-3">
                            <img src="{{ $course->user->avatar_url ? asset('storage/' . $course->user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($course->user->name) }}" class="h-10 w-10 rounded-full border-2 border-slate-700 object-cover">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Instruktur</p>
                                <p class="text-sm font-bold text-white">{{ $course->user->name }}</p>
                            </div>
                        </div>
                        <div class="hidden h-8 w-px bg-slate-800 md:block"></div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Alumni</p>
                            <p class="text-sm font-bold text-white">{{ number_format($course->enrollment_count ?? 0) }} Siswa</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Rating</p>
                            <p class="text-sm font-bold text-white">⭐ {{ number_format($course->avg_rating ?? 0, 1) }}</p>
                        </div>
                    </div>
                </div>
                <div class="lg:w-5/12">
                    <div class="relative">
                        <div class="absolute -inset-1 rounded-[3rem] bg-gradient-to-tr from-green-500 to-indigo-600 opacity-30 blur-2xl"></div>
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" class="relative aspect-video w-full rounded-[2.5rem] object-cover shadow-2xl">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-12">
        <div class="flex flex-col gap-12 lg:flex-row">

            <div class="space-y-12 lg:w-8/12">
                <section>
                    <div class="mb-6 flex items-center gap-4">
                        <h3 class="text-2xl font-black text-slate-900">Deskripsi Kursus</h3>
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </div>
                    <article class="prose prose-slate max-w-none leading-relaxed text-slate-600 prose-headings:text-slate-900 prose-strong:text-slate-900">
                        {!! $course->description !!}
                    </article>
                </section>

                <section>
                    <div class="mb-10 flex items-center gap-4">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900">Pilih Batch Kelas</h3>
                            <p class="mt-1 text-xs font-bold uppercase tracking-widest text-slate-400">Tersedia beberapa jadwal untuk Anda</p>
                        </div>
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </div>

                    @if ($isAlreadyEnrolled)
                        <div class="rounded-[2.5rem] border-2 border-dashed border-slate-200 p-10 text-center">
                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-black text-slate-900">Akses Terbuka</h4>
                            <p class="text-sm text-slate-500">Anda telah menjadi bagian dari kursus ini. Silakan cek kurikulum di dashboard.</p>
                        </div>
                    @else
                        <div class="grid gap-8 sm:grid-cols-2">
                            @foreach ($course->classes as $class)
                                @php
                                    $tz = 'Asia/Jakarta';
                                    $now = now($tz);

                                    $isFull = $class->enrollments_count >= $class->max_quota;
                                    $quotaPercentage = ($class->enrollments_count / $class->max_quota) * 100;

                                    /** * KUNCI PERBAIKAN:
                                     * Kita beri tahu Carbon bahwa data dari DB adalah UTC,
                                     * lalu kita ubah (setTimezone) ke Asia/Jakarta.
                                     */
                                    $startDate = \Carbon\Carbon::parse($class->enrollment_start, 'UTC')->setTimezone($tz);
                                    $endDate = \Carbon\Carbon::parse($class->enrollment_end, 'UTC')->setTimezone($tz);

                                    // Hitung sisa waktu dalam jam/menit jika hari ini adalah hari terakhir
                                    $diffInHours = $now->diffInHours($endDate, false);
                                    $daysLeft = ceil($now->diffInDays($endDate, false));

                                    $isUpcoming = $now->lt($startDate);
                                    $isClosed = $now->gt($endDate);
                                @endphp

                                <div class="group relative flex flex-col rounded-[2.5rem] border border-slate-100 bg-white p-4 transition-all duration-500 hover:border-transparent hover:shadow-[0_30px_60px_rgba(0,0,0,0.08)]">

                                    <div class="relative mb-5 h-44 w-full overflow-hidden rounded-[2rem]">
                                        <img src="{{ $class->thumbnail ? asset('storage/' . $class->thumbnail) : asset('storage/' . $course->thumbnail) }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110">

                                        <div class="absolute left-4 top-4 flex gap-2">
                                            @if ($isUpcoming)
                                                <span class="rounded-xl bg-amber-500 px-3 py-1.5 text-[10px] font-black uppercase text-white shadow-lg">Segera Hadir</span>
                                            @elseif ($isClosed)
                                                <span class="rounded-xl bg-slate-500 px-3 py-1.5 text-[10px] font-black uppercase text-white shadow-lg">Ditutup</span>
                                            @elseif ($isFull)
                                                <span class="rounded-xl bg-red-500 px-3 py-1.5 text-[10px] font-black uppercase text-white shadow-lg">Penuh</span>
                                            @else
                                                <span class="rounded-xl bg-green-500 px-3 py-1.5 text-[10px] font-black uppercase text-white shadow-lg">Tersedia</span>
                                            @endif
                                        </div>

                                        <div class="absolute bottom-4 left-4 right-4">
                                            <div class="flex items-center justify-between rounded-2xl border border-white/50 bg-white/90 p-3 backdrop-blur-md">
                                                <div class="flex flex-col">
                                                    <span class="text-[9px] font-black uppercase tracking-tighter text-slate-400">Status Pendaftaran</span>
                                                    <span class="text-[11px] font-bold italic text-slate-900">
                                                        @if ($isUpcoming)
                                                            Mulai {{ $startDate->translatedFormat('d F') }}
                                                        @else
                                                            Tutup {{ $endDate->translatedFormat('d F Y') }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="{{ $isClosed ? 'bg-slate-500' : 'bg-red-600' }} inline-block rounded-lg px-2 py-1 text-[10px] font-bold uppercase tracking-tighter text-white">
                                                        @if ($isUpcoming)
                                                            Belum Buka
                                                        @elseif ($isClosed)
                                                            Ditutup
                                                        @elseif ($diffInHours < 1)
                                                            Berakhir Segera!
                                                        @elseif ($diffInHours < 24)
                                                            Tutup Hari Ini
                                                        @else
                                                            {{ $daysLeft }} Hari Lagi
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 flex-col px-2 pb-2">
                                        <h4 class="text-xl font-black text-slate-900 transition-colors group-hover:text-green-600">{{ $class->name }}</h4>

                                        <div class="mt-3 flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 p-3">
                                            <div class="flex-1 text-center">
                                                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Buka (WIB)</p>
                                                <p class="text-[10px] font-black text-slate-700">
                                                    {{ $startDate->translatedFormat('d M Y') }}
                                                    <span class="block text-indigo-600">{{ $startDate->format('H:i') }}</span>
                                                </p>
                                            </div>
                                            <div class="h-6 w-px bg-slate-200"></div>
                                            <div class="flex-1 text-center">
                                                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Tutup (WIB)</p>
                                                <p class="text-[10px] font-black text-slate-700">
                                                    {{ $endDate->translatedFormat('d M Y') }}
                                                    <span class="block text-red-600">{{ $endDate->format('H:i') }}</span>
                                                </p>
                                            </div>
                                        </div>

                                        <p class="mt-4 line-clamp-2 text-xs italic leading-relaxed text-slate-500">
                                            {{ $class->description ? strip_tags($class->description) : 'Bergabunglah di batch ini untuk pengalaman belajar yang intensif dan terarah.' }}
                                        </p>

                                        <div class="mb-6 mt-6">
                                            <div class="mb-2 flex items-end justify-between">
                                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Kapasitas Kelas</span>
                                                <span class="text-xs font-black text-slate-900">{{ $class->enrollments_count }}/{{ $class->max_quota }}</span>
                                            </div>
                                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                                                <div class="{{ $isFull ? 'bg-red-500' : 'bg-green-500' }} h-full rounded-full transition-all duration-1000" style="width: {{ $quotaPercentage }}%"></div>
                                            </div>
                                        </div>

                                        <form action="{{ route('payment.initiate') }}" method="POST" class="mt-auto">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                                            <input type="hidden" name="course_class_id" value="{{ $class->id }}">

                                            @if ($isFull || $isClosed || $isUpcoming)
                                                <button type="button" disabled class="w-full cursor-not-allowed rounded-2xl bg-slate-100 py-4 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">
                                                    @if ($isUpcoming)
                                                        Belum Dibuka
                                                    @elseif($isFull)
                                                        Kuota Penuh
                                                    @else
                                                        Pendaftaran Ditutup
                                                    @endif
                                                </button>
                                            @else
                                                <button class="group relative w-full overflow-hidden rounded-2xl bg-slate-900 py-4 text-[11px] font-black uppercase tracking-[0.2em] text-white transition-all hover:-translate-y-1 hover:bg-green-600 hover:shadow-xl hover:shadow-green-200">
                                                    Daftar Batch Ini
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
                <section class="mt-16">
                    <div class="mb-10 flex items-center gap-4">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900">Ulasan Alumni</h3>
                            <p class="mt-1 text-xs font-bold uppercase tracking-widest text-slate-400">Apa kata mereka yang sudah belajar</p>
                        </div>
                        <div class="h-px flex-1 bg-slate-100"></div>
                    </div>

                    @if ($topReviews->isEmpty())
                        <div class="rounded-[2.5rem] bg-slate-50 p-10 text-center">
                            <p class="text-sm font-medium text-slate-400">Belum ada ulasan untuk kursus ini.</p>
                        </div>
                    @else
                        <div class="grid gap-6">
                            @foreach ($topReviews as $review)
                                <div class="relative rounded-[2rem] border border-slate-100 bg-white p-8 transition-all hover:shadow-xl hover:shadow-slate-100">
                                    <div class="flex items-start gap-4">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user->name) }}&background=random" class="h-12 w-12 rounded-2xl object-cover">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h4 class="font-black text-slate-900">{{ $review->user->name }}</h4>
                                                    <p class="text-[10px] font-bold uppercase tracking-tighter text-green-600">Alumni {{ $review->courseClass->name }}</p>
                                                </div>
                                                <div class="flex gap-0.5 text-amber-400">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <svg class="{{ $i <= $review->rating ? 'fill-current' : 'text-slate-200' }} h-4 w-4" viewBox="0 0 20 20">
                                                            <path
                                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                            </div>
                                            <blockquote class="mt-4 text-sm leading-relaxed text-slate-600">
                                                "{{ $review->review }}"
                                            </blockquote>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>

            <div class="lg:w-4/12">
                <div class="sticky top-24 space-y-6">
                    <div class="rounded-[2.5rem] border border-slate-100 bg-white p-8 shadow-2xl shadow-slate-200/60">
                        @php
                            $isDiscountActive = $course->discount_price && (!$course->discount_end_date || now()->lt($course->discount_end_date));
                            $finalPrice = $isDiscountActive ? $course->discount_price : $course->price;
                        @endphp

                        <div class="mb-8">
                            <p class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400">Total Investasi</p>
                            <div class="mt-2 flex items-baseline gap-2">
                                <h2 class="text-4xl font-black text-slate-900">Rp{{ number_format($finalPrice, 0, ',', '.') }}</h2>
                            </div>
                            @if ($isDiscountActive)
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="text-sm font-bold text-slate-300 line-through">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                                    <span class="rounded-md bg-red-50 px-1.5 py-0.5 text-[10px] font-black text-red-600">PROMO</span>
                                </div>
                            @endif
                        </div>

                        <div class="mb-8 space-y-4 rounded-3xl bg-slate-50 p-5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold uppercase text-slate-400">Materi</span>
                                <span class="text-xs font-black uppercase text-slate-900">Lifetime Access</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold uppercase text-slate-400">Sertifikat</span>
                                <span class="text-xs font-black uppercase text-green-600">Resmi</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold uppercase text-slate-400">Update</span>
                                <span class="text-xs font-black uppercase text-slate-900">Gratis</span>
                            </div>
                        </div>

                        @if (!$isAlreadyEnrolled && $selectedClassId)
                            <form action="{{ route('payment.initiate') }}" method="POST">
                                @csrf
                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                <input type="hidden" name="course_class_id" value="{{ $selectedClassId }}">
                                <button class="group flex w-full items-center justify-center gap-3 rounded-2xl bg-green-600 py-4 text-xs font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-green-200 transition-all hover:bg-green-700 hover:shadow-green-300">
                                    Daftar Sekarang
                                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                        <p class="mt-6 text-center text-[10px] font-medium text-slate-400">Jaminan akses selamanya setelah sekali bayar.</p>
                    </div>

                    <div class="rounded-[2.5rem] border border-indigo-100 bg-indigo-50/50 p-8">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <img src="{{ $course->user->avatar_url ? asset('storage/' . $course->user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($course->user->name) }}" class="h-14 w-14 rounded-2xl border-2 border-white object-cover shadow-sm">
                                <span class="absolute -bottom-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-indigo-600 text-[8px] text-white">✓</span>
                            </div>
                            <div>
                                <h5 class="text-sm font-black text-slate-900">{{ $course->user->name }}</h5>
                                <p class="text-[10px] font-bold uppercase tracking-tighter text-indigo-500">Instruktur Ahli</p>
                            </div>
                        </div>
                        <p class="mt-4 text-[11px] italic leading-relaxed text-indigo-900/60">{{ $course->user->bio }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
