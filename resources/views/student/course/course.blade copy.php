<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-10">

        @if ($errors->any())
            <div class="mx-auto mb-4 rounded-lg border border-red-400 bg-red-100 px-4 py-3 text-red-700">
                <strong class="font-bold">Gagal Mendaftar!</strong>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-auto mb-4 rounded-lg border border-yellow-400 bg-yellow-100 px-4 py-3 text-yellow-700">
                {{ session('error') }}
            </div>
        @endif

        <section class="grid grid-cols-1 items-center gap-6 rounded-lg bg-white p-6 shadow-xl md:grid-cols-2 md:p-10">
            <div class="order-2 w-full md:order-2">
                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Ilustrasi kursus" class="h-64 w-full rounded-lg object-cover shadow-md md:h-56 lg:h-72">
            </div>
            <div class="rounded-lg bg-white">
                <h2 class="mb-3 text-3xl font-extrabold text-gray-900">{{ $course->name }}</h2>
                <div class="prose prose-sm max-w-none text-gray-700 sm:prose-base">
                    {!! $course->short_description !!}
                </div>
            </div>
        </section>

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">

            <article class="lg:col-span-2">
                <div class="rounded-lg bg-white p-6 shadow-xl">
                    <h3 class="mb-3 text-2xl font-bold text-gray-800">Deskripsi Lengkap</h3>
                    <div class="prose max-w-none text-gray-700">
                        {!! $course->description !!}
                    </div>
                </div>
                @if ($isAlreadyEnrolled)
                    <div class="mt-8 rounded-lg bg-white p-6 shadow-xl">
                        <h3 class="mb-4 text-2xl font-bold text-gray-800">Anda telah mendaftar dalam kursus ini!</h3>
                    </div>
                @else
                    <div class="mt-8 rounded-lg bg-white p-6 shadow-xl">
                        <h3 class="mb-4 text-2xl font-bold text-gray-800">Kelas yang Tersedia</h3>

                        @if ($course->classes->isEmpty())
                            <p class="rounded-lg border border-gray-100 p-4 italic text-gray-500">Saat ini tidak ada kelas yang dibuka untuk pendaftaran.</p>
                        @else
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                @foreach ($course->classes as $class)
                                    <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-5 shadow-sm transition duration-150 hover:shadow-md">
                                        <h4 class="mb-2 text-lg font-bold text-indigo-700">{{ $class->name }}</h4>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {!! Str::limit($class->description, 80) !!}
                                        </p>
                                        <div class="mt-3 text-xs font-semibold text-indigo-500">
                                            Kode: #{{ $class->id }}
                                        </div>

                                        {{-- ✅ TOMBOL DAFTAR DI SETIAP CARD --}}
                                        <form action="{{ route('payment.initiate') }}" method="POST" class="mt-4">
                                            @csrf
                                            <input type="hidden" name="course_id" value="{{ $course->id }}">
                                            <input type="hidden" name="course_class_id" value="{{ $class->id }}">
                                            <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-bold text-white shadow transition hover:bg-indigo-700">
                                                Daftar & Bayar
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
                {{-- Bagian Mentor --}}
                <section class="mt-8 rounded-lg bg-white p-6 shadow-xl">
                    <h3 class="mb-3 text-2xl font-bold text-gray-800">Mentor Kelas</h3>
                    <div class="flex items-start gap-4">
                        <img src="{{ asset('storage/' . $course->user->avatar_url) }}" alt="Mentor" class="h-16 w-16 rounded-full border-2 border-indigo-500 object-cover p-0.5" loading="lazy">
                        <div>
                            <div class="text-lg font-bold text-gray-800">{{ $course->user->name }}</div>
                            <p class="mt-1 text-sm text-gray-500">{{ $course->user->bio }}</p>
                        </div>
                    </div>
                </section>

                <section class="mt-8">
                    <h3 class="mb-4 text-2xl font-bold text-gray-800">Ulasan Siswa</h3>

                    @if ($topReviews->isEmpty())
                        <p class="rounded-lg border border-gray-200 bg-gray-50 p-5 italic text-gray-500">
                            Belum ada ulasan untuk kursus ini.
                        </p>
                    @else
                        <div class="grid grid-cols-1 gap-4">
                            @foreach ($topReviews as $review)
                                <div class="rounded-lg border border-amber-200 bg-amber-50 p-5 shadow-sm">
                                    <div class="flex items-center">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="text-amber-500">
                                                @if ($i <= $review->rating)
                                                    ⭐
                                                @else
                                                    ☆
                                                @endif
                                            </span>
                                        @endfor
                                        <span class="ml-2 text-sm font-medium text-gray-600">
                                            ({{ $review->rating }})
                                        </span>
                                    </div>
                                    <p class="mt-2 italic text-gray-700">"{{ $review->review }}"</p>
                                    <div class="mt-3 text-sm text-gray-600">
                                        — {{ $review->user->name }}
                                        @if ($review->courseClass)
                                            <span class="ml-2">| Kelas: {{ $review->courseClass->name }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $review->completed_at->translatedFormat('d F Y') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

            </article>

            <div class="lg:col-span-1">
                <div class="space-y-6 self-start lg:sticky lg:top-4">
                    @if ($isAlreadyEnrolled)
                        <aside class="rounded-lg border border-green-200 bg-green-50 p-6 shadow-xl">
                            <div class="text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-3 text-lg font-bold text-green-700">Anda Sudah Terdaftar!</h3>
                                <p class="mt-2 text-gray-600">
                                    Anda telah terdaftar di salah satu kelas kursus ini. Silakan akses kelas Anda di halaman <strong>"Kelas Saya"</strong>.
                                </p>
                                <a href="{{ route('listkelas') }}" class="mt-4 inline-block rounded-lg bg-green-600 px-4 py-2 font-semibold text-white hover:bg-green-700">
                                    Lihat Kelas Saya
                                </a>
                            </div>
                        </aside>
                    @else
                        <aside class="rounded-lg border border-indigo-200 bg-white p-6 shadow-xl">
                            <h3 class="mb-4 text-xl font-bold text-indigo-600">Informasi & Harga</h3>

                            <div class="mb-5 border-b pb-4">
                                <div class="text-sm text-gray-500">Harga Kursus</div>
                                @php
                                    $isDiscountActive = $course->discount_price !== null && ($course->discount_end_date === null || now()->lessThan($course->discount_end_date));
                                    $finalPrice = $isDiscountActive ? $course->discount_price : $course->price;
                                @endphp

                                @if ($isDiscountActive)
                                    <p class="text-base text-gray-400 line-through">
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    </p>
                                    <p class="text-3xl font-extrabold text-indigo-700">
                                        Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                    </p>
                                    <span class="mt-1 inline-block rounded bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-600">
                                        Diskon Aktif!
                                    </span>
                                @else
                                    <p class="text-3xl font-extrabold text-gray-900">
                                        Rp {{ number_format($finalPrice, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>

                            @if ($selectedClassId)
                                <form action="{{ route('payment.initiate') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                                    <input type="hidden" name="course_class_id" value="{{ $selectedClassId }}">
                                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-6 py-3 text-lg font-bold text-white shadow-lg transition duration-200 hover:bg-indigo-700">
                                        Daftar & Bayar Sekarang
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full cursor-not-allowed rounded-lg bg-gray-300 px-4 py-2 font-semibold text-white">
                                    Kelas Penuh atau Belum Dibuka
                                </button>
                            @endif
                        </aside>
                    @endif
                    <div class="rounded-lg bg-white p-6 shadow-xl">
                        <div class="mb-4">
                            <div class="text-sm text-gray-500">Durasi</div>
                            <div class="font-semibold text-gray-700">16 Minggu</div>
                        </div>

                        <div class="mb-4">
                            <div class="text-sm text-gray-500">Level</div>
                            <div class="font-semibold text-gray-700">Pemula - Menengah</div>
                        </div>

                        <div class="mt-6 border-t pt-4 text-sm text-gray-500">
                            <div class="mb-2 font-bold text-gray-700">Fitur Kursus</div>
                            <ul class="list-disc space-y-1 pl-5">
                                <li>Akses forum diskusi</li>
                                <li>Feedback mentor</li>
                                <li>Materi downloadable</li>
                                <li>Sertifikat Kelulusan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div> {{-- End Grid Container --}}
    </div>
</x-app-layout>
