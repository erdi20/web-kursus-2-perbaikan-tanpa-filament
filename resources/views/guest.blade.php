<x-app-layout>
    <div class="mx-auto max-w-[1200px] px-4 py-7" role="main">
        <!-- Carousel Slider -->
        @if ($sliders->count())
            <div class="relative mx-auto my-8 w-full max-w-7xl overflow-hidden rounded-xl shadow-lg">
                <!-- Slide Container -->
                <div id="slide-container" class="relative h-96 transition-transform duration-500 ease-in-out md:h-[500px]">
                    @foreach ($sliders as $index => $slider)
                        <div class="absolute inset-0 h-full w-full transition-opacity duration-500" style="transform: translateX({{ $index * 100 }}%); opacity: {{ $loop->first ? 1 : 0 }};" id="slide-{{ $index }}">
                            <img src="{{ asset('storage/' . $slider->image) }}" alt="{{ $slider->title ?? 'Slider ' . ($index + 1) }}" class="h-full w-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                            <div class="absolute bottom-8 left-8 max-w-2xl text-white">
                                @if ($slider->title)
                                    <h2 class="mb-2 text-3xl font-bold md:text-4xl">{{ $slider->title }}</h2>
                                @endif
                                @if ($slider->description)
                                    <p class="text-lg">{{ $slider->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Navigation Arrows -->
                <button id="prev-btn" class="absolute left-4 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/80 p-2 shadow-md hover:bg-white">
                    <svg class="h-6 w-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button id="next-btn" class="absolute right-4 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/80 p-2 shadow-md hover:bg-white">
                    <svg class="h-6 w-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Indicators -->
                <div class="absolute bottom-4 left-1/2 z-10 flex -translate-x-1/2 transform space-x-2">
                    @foreach ($sliders as $index => $slider)
                        <button class="h-3 w-3 rounded-full bg-white/50 transition hover:bg-white" data-slide="{{ $index }}" id="indicator-{{ $index }}">
                        </button>
                    @endforeach
                </div>
            </div>


        @endif
        <!-- SESSION BANNER / SLIDE BANNER -->
        <section class="mb-8 overflow-hidden rounded-xl bg-green-50">
            <div class="flex flex-col items-center gap-6 p-6 md:flex-row md:p-10">
                <div class="md:w-1/2">
                    <h2 class="mb-3 text-2xl font-bold text-gray-800">Tempat Terbaik untuk Mulai Perjalanan Belajarmu</h2>
                    <p class="mb-5 text-gray-600">Belajar jadi lebih seru dan terasa melejit. Materi terstruktur, latihan interaktif, dan Kursus live yang membantu kamu berkembang.</p>
                    <a href="{{ route('listkelas') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 font-semibold text-white transition hover:bg-green-700">
                        Jelajahi Kelas
                    </a>
                </div>
                <div class="md:w-1/2">
                    <img src="{{ asset('img/satu.jpg') }}" alt="Ilustrasi belajar" class="h-auto w-full rounded-lg object-contain shadow-md">
                </div>
            </div>
        </section>

        <!-- HERO UTAMA -->
        <section class="flex flex-wrap items-center justify-between gap-4 rounded-[14px] bg-gradient-to-r from-[#ffd89b] to-[#ffe0c7] p-9 shadow-[0_6px_20px_rgba(15,23,42,0.06)]" aria-labelledby="home-hero-title">
            <div class="min-w-[260px] flex-1 flex-shrink-0">
                <h1 id="home-hero-title" class="m-0 mb-3 text-[clamp(1.6rem,3.2vw,2.4rem)] font-bold leading-[1.05] text-[#072033]">Tempat Terbaik untuk Mulai Perjalanan Belajarmu</h1>
                <p class="mb-5 text-[#6c757d]">Belajar jadi lebih seru dan terasa melejit. Materi terstruktur, latihan interaktif, dan Kursus live yang membantu kamu berkembang.</p>

                <div class="flex flex-wrap gap-3">
                    <a href="#" class="inline-flex min-w-[140px] flex-1 items-center justify-center gap-2 rounded-[10px] bg-gradient-to-r from-[#ffd89b] to-[#19547b] px-4 py-2.5 font-bold text-[#072033] shadow-[0_8px_20px_rgba(25,84,123,0.08)] transition-transform duration-100 ease-[cubic-bezier(0.25,0.1,0.25,1)] active:translate-y-0.5">Jelajahi Kursus</a>
                    <a href="#" class="inline-flex min-w-[140px] flex-1 items-center justify-center gap-2 rounded-[10px] border border-[rgba(7,32,51,0.08)] bg-transparent px-4 py-2.5 font-bold text-[#072033] transition-transform duration-100 ease-[cubic-bezier(0.25,0.1,0.25,1)] active:translate-y-0.5">Mulai Latihan Soal</a>
                </div>

                <div class="mt-3.5 text-sm text-[#6c757d]">
                    <span class="mr-4.5">• Kurikulum terstruktur</span>
                    <span class="mr-4.5">• Sertifikat kelulusan</span>
                    <span>• Komunitas & jadwal live</span>
                </div>
            </div>

            <div>
                <img src="{{ asset('asset/2324550.png') }}" alt="Ilustrasi hero" class="h-auto w-full max-w-[420px] flex-shrink-0 rounded-[12px] object-contain shadow-[0_6px_20px_rgba(15,23,42,0.06)]">
            </div>
        </section>

        <!-- Kursus POPULER -->
        <section class="mt-8.5" aria-labelledby="popular-classes">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                <h3 id="popular-classes" class="text-base font-medium">Kursus Populer</h3>
                <a href="#" class="inline-flex items-center justify-center gap-2 rounded-[10px] border-none bg-transparent px-4 py-2.5 font-bold text-[#19547b] transition-transform duration-100 ease-[cubic-bezier(0.25,0.1,0.25,1)] active:translate-y-0.5">Lihat semua</a>
            </div>

            <div class="grid grid-cols-3 gap-4" role="list">
                <article class="flex flex-col overflow-hidden rounded-[12px] bg-white shadow-[0_6px_20px_rgba(15,23,42,0.06)] transition-transform duration-100 ease-[cubic-bezier(0.25,0.1,0.25,1)] hover:translate-y-[-6px] hover:shadow-[0_12px_40px_rgba(15,23,42,0.09)]" role="listitem" aria-label="Naratif Teknis">
                    <img src="{{ asset('asset/kjl.jpg') }}" alt="Naratif Teknis" class="block h-40 w-full object-cover">
                    <div class="flex flex-1 flex-col gap-2 p-3.5">
                        <h4 class="m-0 text-base font-bold">Naratif Teknis</h4>
                        <div class="text-sm text-[#6c757d]">Mentor: Gilung Ramadhan</div>
                        <div class="mt-auto flex items-center justify-between gap-3">
                            <div class="text-sm text-[#6c757d]">Durasi: 4 minggu</div>
                            <a href="#" class="inline-block rounded-[8px] bg-[#0f5132] px-3 py-2 font-bold text-white no-underline">Rp 100.000</a>
                        </div>
                    </div>
                </article>

                <article class="flex flex-col overflow-hidden rounded-[12px] bg-white shadow-[0_6px_20px_rgba(15,23,42,0.06)] transition-transform duration-100 ease-[cubic-bezier(0.25,0.1,0.25,1)] hover:translate-y-[-6px] hover:shadow-[0_12px_40px_rgba(15,23,42,0.09)]" role="listitem" aria-label="Kursus Contoh">
                    <img src="{{ asset('asset/hh.jpg') }}" alt="Kursus Contoh" class="block h-40 w-full object-cover">
                    <div class="flex flex-1 flex-col gap-2 p-3.5">
                        <h4 class="m-0 text-base font-bold">Kursus Contoh</h4>
                        <div class="text-sm text-[#6c757d]">Mentor: Mentor Contoh</div>
                        <div class="mt-auto flex items-center justify-between gap-3">
                            <div class="text-sm text-[#6c757d]">Durasi: 3 minggu</div>
                            <a href="#" class="inline-block rounded-[8px] bg-[#0f5132] px-3 py-2 font-bold text-white no-underline">Daftar</a>
                        </div>
                    </div>
                </article>

                <article class="flex flex-col overflow-hidden rounded-[12px] bg-white shadow-[0_6px_20px_rgba(15,23,42,0.06)] transition-transform duration-100 ease-[cubic-bezier(0.25,0.1,0.25,1)] hover:translate-y-[-6px] hover:shadow-[0_12px_40px_rgba(15,23,42,0.09)]" role="listitem" aria-label="Kursus Lainnya">
                    <img src="{{ asset('asset/yy.jpg') }}" alt="Kursus Lainnya" class="block h-40 w-full object-cover">
                    <div class="flex flex-1 flex-col gap-2 p-3.5">
                        <h4 class="m-0 text-base font-bold">Kursus Lainnya</h4>
                        <div class="text-sm text-[#6c757d]">Mentor: Mentor Lain</div>
                        <div class="mt-auto flex items-center justify-between gap-3">
                            <div class="text-sm text-[#6c757d]">Durasi: 2 minggu</div>
                            <a href="#" class="inline-block rounded-[8px] bg-[#0f5132] px-3 py-2 font-bold text-white no-underline">Daftar</a>
                        </div>
                    </div>
                </article>
            </div>

            <div class="mt-3.5 text-center">
                <a href="{{ route('listkelas') }}" class="mt-3.5 inline-block rounded-[10px] bg-black px-4 py-2.5 font-bold text-white no-underline">Lihat Kursus Lainnya</a>
            </div>
        </section>

        <!-- KATEGORI POPULER -->
        <section class="mt-8.5" aria-labelledby="categories">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                <h3 id="categories" class="text-base font-medium">Kategori Populer</h3>
                <small class="text-sm text-[#6c757d]">Kursus berdasarkan kategori</small>
            </div>

            <div class="mt-3 grid grid-cols-3 gap-3" role="list">
                <a class="flex min-h-[84px] items-center gap-3 rounded-[12px] bg-white p-3.5 text-inherit no-underline shadow-[0_6px_20px_rgba(15,23,42,0.06)] transition-transform duration-100 ease-[cubic-bezier(0.25,0.1,0.25,1)] hover:translate-y-[-6px] hover:shadow-[0_12px_40px_rgba(15,23,42,0.09)]" role="listitem" href="#">
                    <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full font-bold text-white" style="background:#8EE4AF;">🎓</div>
                    <div class="flex flex-col gap-1">
                        <strong>Pendidikan</strong>
                        <div class="text-sm text-[#6c757d]">Kursus & materi</div>
                    </div>
                </a>

                <a class="flex min-h-[84px] items-center gap-3 rounded-[12px] bg-white p-3.5 text-inherit no-underline shadow-[0_6px_20px_rgba(15,23,42,0.06)] transition-transform duration-100 ease-[cubic-bezier(0.25,0.1,0.25,1)] hover:translate-y-[-6px] hover:shadow-[0_12px_40px_rgba(15,23,42,0.09)]" role="listitem" href="#">
                    <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full font-bold text-white" style="background:#FFD166;">🗣️</div>
                    <div class="flex flex-col gap-1">
                        <strong>Bahasa</strong>
                        <div class="text-sm text-[#6c757d]">Kursus & materi</div>
                    </div>
                </a>

                <a class="flex min-h-[84px] items-center gap-3 rounded-[12px] bg-white p-3.5 text-inherit no-underline shadow-[0_6px_20px_rgba(15,23,42,0.06)] transition-transform duration-100 ease-[cubic-bezier(0.25,0.1,0.25,1)] hover:translate-y-[-6px] hover:shadow-[0_12px_40px_rgba(15,23,42,0.09)]" role="listitem" href="#">
                    <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full font-bold text-white" style="background:#89C2D9;">🔬</div>
                    <div class="flex flex-col gap-1">
                        <strong>Sains</strong>
                        <div class="text-sm text-[#6c757d]">Kursus & materi</div>
                    </div>
                </a>
            </div>
        </section>

        <!-- ULASAN -->
        <section class="mt-8.5" aria-labelledby="reviews">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                <h3 id="reviews" class="text-base font-medium">Ulasan Mereka</h3>
                <small class="text-sm text-[#6c757d]">Dari siswa yang sudah mencoba</small>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-4">
                <div class="rounded-[12px] bg-gradient-to-b from-white to-[#f8fbff] p-4 shadow-[0_6px_20px_rgba(15,23,42,0.06)]">
                    <p class="m-0 mb-3">“Kursusnya sangat membantu, pengajar jelas dan materi terstruktur.”</p>
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#0f5132] font-bold text-white">S</div>
                        <div>
                            <strong>Siti</strong>
                            <div class="text-sm text-[#6c757d]">Siswa</div>
                        </div>
                        <div class="ml-auto font-bold text-[#0f5132]">Bidang Kemahiran</div>
                    </div>
                </div>

                <div class="rounded-[12px] bg-gradient-to-b from-white to-[#f8fbff] p-4 shadow-[0_6px_20px_rgba(15,23,42,0.06)]">
                    <p class="m-0 mb-3">“Latihan soal dan pembahasannya membuat saya lebih percaya diri menghadapi ujian.”</p>
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#0f5132] font-bold text-white">R</div>
                        <div>
                            <strong>Rian</strong>
                            <div class="text-sm text-[#6c757d]">Siswa</div>
                        </div>
                        <div class="ml-auto font-bold text-[#0f5132]">Bidang Kemahiran</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- KENAPA KAMI BISA DIANDALKAN? -->
        <section class="mt-8.5 rounded-xl bg-green-50 py-8">
            <div class="text-center">
                <h2 class="mb-3 text-2xl font-bold text-gray-800">KENAPA KAMI BISA DIANDALKAN?</h2>
                <p class="text-gray-600">Karena kami memilih sumber belajar. Tak hanya materi yang terjamin.</p>
            </div>
        </section>

        <!-- CTA AKHIR -->
        <section class="p-6.5 mt-7 rounded-[12px] bg-gradient-to-r from-[#ffd89b] to-[#19547b] text-center text-[#072033]" role="region" aria-label="Daftar sekarang">
            <h3 class="m-0 mb-2">Ayo Daftar Sekarang, tunggu apa lagi?</h3>
            <p class="m-0 mb-3 text-[rgba(7,32,51,0.9)]">Dapatkan akses ke Kursus populer, materi terstruktur, dan komunitas belajar.</p>
            <a href="#" class="inline-flex min-w-[140px] flex-1 items-center justify-center gap-2 rounded-[10px] bg-gradient-to-r from-[#ffd89b] to-[#19547b] px-4 py-2.5 font-bold text-[#072033] shadow-[0_8px_20px_rgba(25,84,123,0.08)] transition-transform duration-100 ease-[cubic-bezier(0.25,0.1,0.25,1)] active:translate-y-0.5">Daftar Sekarang</a>
        </section>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sliders = @json($sliders);
            const totalSlides = sliders.length;
            let currentSlide = 0;
            const slideContainer = document.getElementById('slide-container');
            const indicators = document.querySelectorAll('[data-slide]');

            // Update slide
            function updateSlide() {
                // Update position
                slideContainer.style.transform = `translateX(-${currentSlide * 100}%)`;

                // Update opacity for smooth transition
                document.querySelectorAll('#slide-container > div').forEach((slide, index) => {
                    slide.style.opacity = index === currentSlide ? '1' : '0';
                });

                // Update indicators
                indicators.forEach((indicator, index) => {
                    indicator.classList.toggle('bg-white', index === currentSlide);
                    indicator.classList.toggle('bg-white/50', index !== currentSlide);
                });
            }

            // Next slide
            document.getElementById('next-btn').addEventListener('click', () => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlide();
            });

            // Prev slide
            document.getElementById('prev-btn').addEventListener('click', () => {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateSlide();
            });

            // Indicator click
            indicators.forEach(button => {
                button.addEventListener('click', () => {
                    currentSlide = parseInt(button.dataset.slide);
                    updateSlide();
                });
            });

            // Auto slide every 5 seconds
            setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlide();
            }, 5000);
        });
    </script>
</x-app-layout>
