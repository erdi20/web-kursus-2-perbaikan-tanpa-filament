<x-app-layout>
    <div class="mx-auto max-w-[1280px] px-4 py-10" role="main">

        @if ($sliders->count())
            <section class="group relative mb-20 overflow-hidden rounded-[2rem] shadow-2xl">
                <div id="slide-container" class="relative h-[400px] md:h-[600px]">
                    @foreach ($sliders as $index => $slider)
                        <div class="{{ $loop->first ? 'opacity-100 scale-100' : 'opacity-0 scale-105' }} absolute inset-0 h-full w-full transform transition-all duration-1000 ease-in-out" id="slide-{{ $index }}">
                            <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title }}" class="h-full w-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>

                            <div class="absolute inset-0 flex flex-col justify-end p-8 text-white md:p-20">
                                <div class="max-w-3xl translate-y-0 transform transition-all duration-700">
                                    <span class="mb-4 inline-block rounded-full bg-green-500 px-4 py-1 text-xs font-bold uppercase tracking-widest text-white">Special Promo</span>
                                    <h2 class="mb-4 text-4xl font-black leading-tight md:text-6xl">{{ $slider->title }}</h2>
                                    <p class="mb-8 line-clamp-2 hidden max-w-xl text-lg text-gray-200 md:block">{{ $slider->description }}</p>
                                    <div class="flex gap-4">
                                        <a href="{{ route('listkursus') }}" class="inline-block rounded-xl bg-green-500 px-8 py-4 font-bold text-white shadow-lg transition hover:bg-green-600 active:scale-95">Jelajahi Kursus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="absolute bottom-10 right-10 z-30 flex gap-3">
                    <button id="prev-btn" class="rounded-xl border border-white/20 bg-white/10 p-4 text-white backdrop-blur-md transition hover:bg-white/20">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button id="next-btn" class="rounded-xl border border-white/20 bg-white/10 p-4 text-white backdrop-blur-md transition hover:bg-white/20">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </section>
        @endif

        {{-- hero --}}
        <section class="mb-24 grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
            <div class="order-2 lg:order-1">
                <div class="relative inline-block">
                    <span class="mb-4 inline-block rounded-lg bg-indigo-100 px-4 py-1 text-sm font-bold uppercase text-indigo-700">
                        Growth Mindset
                    </span>
                </div>

                {{-- Hero Title --}}
                <h1 class="mb-6 text-4xl font-black leading-[1.1] text-slate-900 md:text-6xl">
                    {!! $setting?->hero_title ?? 'Kuasai Skill Baru <br><span class="text-green-600">Tanpa Batas Jarak.</span>' !!}
                </h1>

                {{-- Hero Subtitle --}}
                <p class="mb-10 text-xl leading-relaxed text-slate-500">
                    {{ $setting?->hero_subtitle ?? 'Mulai belajar dari dasar hingga mahir dengan kurikulum yang disusun oleh pakar industri global. Sertifikat resmi siap menantimu.' }}
                </p>

                {{-- Feature Badge (Hanya Materi Terupdate) --}}
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-4 rounded-2xl border border-slate-100 bg-white p-4 pr-8 shadow-sm transition-transform hover:scale-105">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-green-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block font-bold text-slate-700">Materi Terupdate</span>
                            <span class="text-xs text-slate-400">Kurikulum 2025</span>
                        </div>
                    </div>

                    {{-- Tombol CTA Tambahan (Opsional) --}}
                    <a href="{{ route('listkursus') }}" class="flex items-center justify-center rounded-2xl bg-slate-900 px-8 py-4 font-bold text-white transition-all hover:bg-slate-800 hover:shadow-lg">
                        Mulai Belajar
                    </a>
                </div>
            </div>

            <div class="relative order-1 lg:order-2">
                <div class="absolute -bottom-6 -right-6 h-full w-full rounded-[2.5rem] bg-green-500/10"></div>
                <div class="relative overflow-hidden rounded-[2.5rem] shadow-2xl">
                    {{-- Hero Image --}}
                    @if ($setting && $setting->hero_image)
                        <img src="{{ asset('storage/' . $setting->hero_image) }}" alt="Hero Image" class="w-full transform object-cover transition duration-700 hover:scale-105">
                    @else
                        <img src="{{ asset('img/satu.jpg') }}" alt="Default Hero" class="w-full transform object-cover transition duration-700 hover:scale-105">
                    @endif
                </div>
            </div>
        </section>
        {{-- kursus --}}
        @php
            $sections = [
                ['title' => 'Kursus Terpopuler', 'desc' => 'Paling banyak dipelajari', 'data' => $popularCourses, 'badge' => '🔥 Populer', 'color' => 'amber'],
                ['title' => 'Kursus Terbaik', 'desc' => 'Rating tertinggi dari alumni', 'data' => $topRatedCourses, 'badge' => '⭐ Terbaik', 'color' => 'indigo'],
                ['title' => 'Pilihan Acak', 'desc' => 'Mungkin kamu tertarik', 'data' => $randomCourses, 'badge' => '🎲 Random', 'color' => 'emerald'],
            ];
        @endphp

        @foreach ($sections as $sec)
            <section class="mb-20">
                <div class="mb-8 flex items-end justify-between px-2">
                    <div>
                        <div class="mb-1 flex items-center gap-2">
                            <span class="bg-{{ $sec['color'] }}-500 h-1 w-8 rounded-full"></span>
                            <span class="text-{{ $sec['color'] }}-600 text-[10px] font-black uppercase tracking-[0.2em]">{{ $sec['badge'] }}</span>
                        </div>
                        <h3 class="text-2xl font-black tracking-tight text-slate-900">{{ $sec['title'] }}</h3>
                    </div>
                    <a href="{{ route('listkursus') }}" class="group flex items-center gap-1 text-xs font-bold text-slate-400 transition hover:text-green-600">
                        LIHAT SEMUA
                        <svg class="h-3 w-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse($sec['data'] as $course)
                        @php
                            $avgRating = $course->avg_rating ?? 0;
                            $reviewCount = $course->review_count ?? 0;
                            $isDiscountActive = $course->discount_price !== null && ($course->discount_end_date === null || now()->lessThan($course->discount_end_date));
                        @endphp

                        <article class="group relative flex flex-col rounded-[2.5rem] border border-slate-100 bg-white p-3 transition-all duration-300 hover:border-transparent hover:shadow-[0_20px_50px_rgba(0,0,0,0.05)]">

                            <div class="relative h-48 w-full overflow-hidden rounded-[2rem]">
                                <img src="{{ asset('storage/' . ($course->thumbnail ?? 'default.jpg')) }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">

                                <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between">
                                    <div class="flex flex-col rounded-2xl bg-white/90 px-4 py-2 shadow-lg backdrop-blur-md">
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
                                        <span class="text-[10px] font-black">{{ number_format($avgRating, 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col px-3 py-4">
                                <div class="mb-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $course->user->avatar_url ? asset('storage/' . $course->user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($course->user->name) }}" class="h-6 w-6 rounded-full border border-slate-100 object-cover">
                                        <span class="text-[11px] font-bold uppercase tracking-tight text-slate-500">{{ $course->user->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
                                        <span class="text-[10px] font-bold text-slate-400">{{ number_format($course->enrollment_count ?? 0) }} Alumni</span>
                                    </div>
                                </div>

                                <h4 class="group-hover:text-{{ $sec['color'] }}-600 mb-4 line-clamp-2 min-h-[2.5rem] text-[17px] font-bold leading-tight text-slate-900 transition-colors">
                                    {{ $course->name }}
                                </h4>

                                <div class="mt-auto flex items-center justify-between pt-2">
                                    <span class="text-[10px] font-medium italic text-slate-400">({{ $reviewCount }} Ulasan terverifikasi)</span>
                                    <a href="{{ route('detailkursus', $course->slug) }}" class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 text-slate-900 transition-all duration-500 group-hover:rotate-[360deg] group-hover:bg-slate-900 group-hover:text-white">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full py-10 text-center font-medium text-slate-400">Belum ada kursus di kategori ini.</div>
                    @endforelse
                </div>
            </section>
        @endforeach

        {{-- TESTIMONI --}}
        <section class="relative mb-20 overflow-hidden rounded-[3rem] bg-slate-950 px-6 py-12 text-white shadow-xl">
            <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-green-500/5 blur-[80px]"></div>
            <div class="absolute -bottom-10 -left-10 h-64 w-64 rounded-full bg-indigo-500/5 blur-[80px]"></div>

            <div class="relative z-10 mx-auto max-w-5xl">
                <div class="flex flex-col items-center justify-between gap-8 text-center md:flex-row md:text-left">

                    <div class="md:w-1/3">
                        <span class="mb-2 inline-block text-[10px] font-black uppercase tracking-[0.3em] text-green-500">
                            Ulasan Belajar
                        </span>
                        <h3 class="text-3xl font-black tracking-tighter">
                            Apa Kata <span class="bg-gradient-to-r from-green-400 to-emerald-500 bg-clip-text italic text-transparent">Peserta?</span>
                        </h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-400">
                            Ulasan jujur dari alumni yang telah menyelesaikan kursus mereka.
                        </p>

                        <div class="mt-6 flex justify-center gap-3 md:justify-start">
                            <button id="testi-prev" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 transition hover:bg-white/10 active:scale-90">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button id="testi-next" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 transition hover:bg-white/10 active:scale-90">
                                <svg class="h-4 w-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="relative overflow-hidden md:w-2/3">
                        <div id="testi-container" class="flex transition-transform duration-700">
                            @foreach ($testimonials as $testimonial)
                                <div class="w-full shrink-0 px-2">
                                    <div class="relative rounded-[2rem] border border-white/5 bg-white/[0.03] p-6 backdrop-blur-sm">
                                        <svg class="absolute right-6 top-6 h-8 w-8 text-white/5" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H16.017C15.4647 8 15.017 8.44772 15.017 9V12C15.017 12.5523 14.5693 13 14.017 13H13.017V21H14.017ZM6.017 21L6.017 18C6.017 16.8954 6.91243 16 8.017 16H11.017C11.5693 16 12.017 15.5523 12.017 15V9C12.017 8.44772 11.5693 8 11.017 8H8.017C7.46472 8 7.017 8.44772 7.017 9V12C7.017 12.5523 6.56929 13 6.017 13H5.017V21H6.017Z" />
                                        </svg>

                                        <div class="flex flex-col gap-4">
                                            <div class="flex text-amber-400">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="h-3 w-3 fill-current" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>

                                            <p class="text-base font-medium leading-relaxed text-slate-200">
                                                "{{ Str::limit($testimonial->review, 120) }}"
                                            </p>

                                            <div class="mt-2 flex items-center gap-3">
                                                <div class="relative">
                                                    <div class="absolute -inset-0.5 rounded-full bg-green-500/50 opacity-50 blur"></div>
                                                    <img src="{{ $testimonial->user->avatar_url ? asset('storage/' . $testimonial->user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($testimonial->user->name) . '&background=000&color=fff' }}" class="relative h-10 w-10 rounded-full border border-slate-900 object-cover">
                                                </div>
                                                <div>
                                                    <h5 class="text-sm font-bold text-white">{{ $testimonial->user->name }}</h5>
                                                    <p class="text-[9px] font-bold uppercase tracking-widest text-green-500">Verified Alumni</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>


        {{-- faq --}}
        <section id="#faq-section" class="mx-auto mb-24 max-w-4xl">
            <div class="mb-12 text-center">
                <h3 class="text-3xl font-black text-slate-900">Masih Bingung?</h3>
                <p class="mt-2 text-slate-500">Kami telah merangkum beberapa hal yang sering ditanyakan</p>
            </div>

            <div class="space-y-4">
                @foreach ($faqs as $faq)
                    <div class="faq-item overflow-hidden rounded-2xl border border-slate-100 bg-white transition-all duration-300">
                        <button class="faq-toggle flex w-full cursor-pointer items-center justify-between p-6 text-left outline-none transition hover:bg-slate-50">
                            <span class="text-lg font-bold text-slate-800">{{ $faq->question }}</span>
                            <span class="faq-icon text-green-500 transition-transform duration-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                        </button>
                        <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                            <div class="mt-2 border-t border-slate-50 p-6 pt-0 leading-relaxed text-slate-600">
                                {!! $faq->answer !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Logic Hero Slider
            const slides = document.querySelectorAll('#slide-container > div');
            const totalSlides = slides.length;
            let currentSlide = 0;

            function updateSlide(target) {
                slides.forEach((slide, index) => {
                    if (index === target) {
                        slide.classList.replace('opacity-0', 'opacity-100');
                        slide.classList.replace('scale-105', 'scale-100');
                        slide.style.zIndex = '10';
                    } else {
                        slide.classList.replace('opacity-100', 'opacity-0');
                        slide.classList.replace('scale-100', 'scale-105');
                        slide.style.zIndex = '0';
                    }
                });
                currentSlide = target;
            }

            document.getElementById('next-btn')?.addEventListener('click', () => {
                updateSlide((currentSlide + 1) % totalSlides);
            });

            document.getElementById('prev-btn')?.addEventListener('click', () => {
                updateSlide((currentSlide - 1 + totalSlides) % totalSlides);
            });

            // Logic Testimoni Carousel
            const testiContainer = document.getElementById('testi-container');
            const testiSlides = testiContainer.querySelectorAll('.shrink-0');
            let currentTesti = 0;

            function updateTesti(target) {
                testiContainer.style.transform = `translateX(-${target * 100}%)`;
                currentTesti = target;
            }

            document.getElementById('testi-next')?.addEventListener('click', () => {
                updateTesti((currentTesti + 1) % testiSlides.length);
            });

            document.getElementById('testi-prev')?.addEventListener('click', () => {
                updateTesti((currentTesti - 1 + testiSlides.length) % testiSlides.length);
            });

            // Logic FAQ Accordion
            document.querySelectorAll('.faq-toggle').forEach(button => {
                button.addEventListener('click', () => {
                    const content = button.nextElementSibling;
                    const icon = button.querySelector('.faq-icon');
                    const isOpen = content.style.maxHeight !== '0px' && content.style.maxHeight !== '';

                    // Close all others
                    document.querySelectorAll('.faq-content').forEach(c => c.style.maxHeight = '0px');
                    document.querySelectorAll('.faq-icon').forEach(i => i.style.transform = 'rotate(0deg)');

                    if (!isOpen) {
                        content.style.maxHeight = content.scrollHeight + 'px';
                        icon.style.transform = 'rotate(45deg)';
                    }
                });
            });

            // Auto slide hero
            setInterval(() => {
                updateSlide((currentSlide + 1) % totalSlides);
            }, 8000);
        });
    </script>

</x-app-layout>
