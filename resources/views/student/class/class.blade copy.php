<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <!-- KONTEN UTAMA KARTU -->
            <div class="rounded-xl bg-white p-6 shadow-2xl sm:p-10">

                <!-- HEADER DAN MENTOR (FULL WIDTH) -->
                <div class="mb-8 border-b border-gray-100 pb-8">

                    {{-- Bagian Mentor --}}
                    <section class="mb-8">
                        <h2 class="mb-2 text-lg font-bold text-gray-700">Mentor Kelas ini:</h2>

                        {{-- Keterangan Mentor (Disesuaikan dengan data $class Anda) --}}
                        <div class="flex w-fit items-center space-x-4 rounded-xl bg-gradient-to-r from-green-400 to-green-600 p-3 shadow-md">

                            {{-- Gambar Mentor --}}
                            <img alt="Mentor Kelas" src="{{ asset('storage/' . ($class->course->user?->avatar_url ?? 'default-avatar.png')) }}" class="h-10 w-10 flex-shrink-0 rounded-full border-2 border-white bg-gray-300 object-cover" />

                            {{-- Detail Mentor --}}
                            <div>
                                <p class="text-lg font-semibold leading-tight text-white">{{ $class->course->user?->name ?? 'Mentor tidak tersedia' }}</p>
                                <p class="text-sm text-green-100">Mentor</p>
                            </div>
                        </div>
                    </section>

                    {{-- Deskripsi Singkat Kursus --}}
                    <section>
                        <h1 class="mb-3 mt-8 text-2xl font-extrabold text-gray-900">{{ $class->course->name }}</h1>
                        <p class="text-base leading-relaxed text-gray-600">
                            {!! $class->course->short_description ?? 'Deskripsi kursus belum tersedia.' !!}
                        </p>
                    </section>
                </div>

                <!-- BAGIAN UTAMA: MATERI (KIRI) dan TUGAS (KANAN) -->
                <!-- Layout 2 Kolom: Materi (8/12) dan Tugas (4/12) di layar besar -->
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">

                    <!-- KOLOM KIRI: MATERI PEMBELAJARAN (8/12) -->
                    <div class="lg:col-span-8">
                        <section>
                            <h3 class="mb-5 border-b-2 border-indigo-100 pb-2 text-2xl font-bold text-gray-900">Daftar Materi Kelas</h3>

                            <div id="daftar-kelas" class="space-y-4">
                                @if ($class->materialsFE->count())
                                    @foreach ($class->materialsFE as $material)
                                        @php
                                            // ✅ HANYA cek visibility, abaikan schedule_date
                                            $service = app(\App\Services\MaterialCompletionService::class);
                                            $currentOrder = $material->pivot->order;
                                            $canAccess = $service->arePreviousMaterialsCompleted(Auth::id(), $class->id, $currentOrder);
                                            $isActive = $canAccess && $material->pivot->visibility === 'visible';
                                            $isVisible = $material->pivot->visibility === 'visible';
                                            $isActive = $canAccess && $isVisible;

                                            // Styling
                                            $linkClass = $isActive ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-300' : 'bg-gray-50 text-gray-800 hover:bg-gray-100 border border-gray-200 opacity-70 cursor-not-allowed';
                                            $iconColor = $isActive ? 'text-white' : 'text-gray-400';
                                            $counterColor = $isActive ? 'text-indigo-200' : 'text-indigo-500';
                                        @endphp
                                        <a href="{{ $isActive ? route('materials.show', ['classId' => $class->id, 'materialId' => $material->id]) : '#' }}" class="{{ $linkClass }} flex items-center justify-between rounded-xl p-4 transition duration-200">

                                            <div class="flex items-start space-x-3">
                                                <span class="{{ $counterColor }} flex-shrink-0 font-bold">
                                                    P{{ $loop->iteration }}:
                                                </span>
                                                <div class="flex flex-col">
                                                    <span class="font-medium">
                                                        {{ $material->name }}
                                                    </span>

                                                </div>
                                            </div>

                                            @if ($isActive)
                                                <!-- Ikon Sedang Berjalan (Play/Active) -->
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.26a1 1 0 001.555.832l3.197-2.132c.21-.14.21-.497 0-.638z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @else
                                                <!-- Ikon Kunci (Belum Aktif) -->
                                                <svg class="{{ $iconColor }} h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            @endif
                                        </a>
                                    @endforeach
                                @else
                                    <p class="p-4 italic text-gray-500">Belum ada materi tersedia untuk kelas ini.</p>
                                @endif
                            </div>
                        </section>
                    </div>

                    <!-- KOLOM KANAN: TUGAS DAN AKSI (4/12) -->
                    <div class="lg:col-span-4">
                        <section class="rounded-xl border border-gray-200 bg-gray-50 p-6 shadow-inner">


                            <!-- Progress & Sertifikat (Menggunakan data $enrollment Anda) -->
                            <div class="mt-8 space-y-4 border-t border-gray-300 pt-4">
                                <!-- Progres Kelas -->
                                <h4 class="font-semibold text-gray-700">Kemajuan Kelas</h4>
                                <div class="mb-4">
                                    <div class="mb-1 flex justify-between text-sm text-gray-600">
                                        <span>Kemajuan</span>
                                        <span>{{ $enrollment->progress_percentage ?? 0 }}%</span>
                                    </div>
                                    <div class="h-2 w-full rounded-full bg-gray-200">
                                        <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 transition-all duration-500" style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <!-- Nilai Akhir & Status -->
                                @if ($enrollment?->grade !== null)
                                    <div class="rounded-lg bg-gray-50 p-4">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-700">Nilai Akhir</span>
                                            <span class="text-lg font-bold text-indigo-700">{{ $enrollment->grade }}/100</span>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700">Status</span>
                                            {{-- @if ($enrollment->status === 'completed')
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Lulus
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.3 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    Belum Lulus
                                                </span>
                                            @endif --}}
                                        </div>
                                    </div>
                                @endif

                                <!-- Tombol Lihat Nilai (opsional: bisa dihapus jika sudah ditampilkan) -->
                                {{-- <button class="w-full transform rounded-lg bg-indigo-600 px-6 py-3 font-bold text-white shadow-lg transition duration-200 hover:scale-[1.01] hover:bg-indigo-700">
                                        Lihat Nilai Rata-Rata
                                    </button> --}}

                                <!-- Sertifikat -->
                                @if ($enrollment?->status === 'completed')
                                    <a href="{{ route('certificates.download', $class->id) }}" class="inline-block w-full rounded-lg border-2 border-green-500 bg-white px-6 py-3 text-center font-bold text-gray-800 shadow-md transition duration-200 hover:bg-green-500 hover:text-white">
                                        Unduh Sertifikat
                                    </a>
                                @else
                                    <button disabled class="w-full cursor-not-allowed rounded-lg bg-gray-200 px-4 py-2.5 font-semibold text-gray-500 shadow-md">
                                        {{ $enrollment?->grade !== null ? 'Belum Memenuhi Syarat Kelulusan' : 'Selesaikan Kelas untuk Mendapat Sertifikat' }}
                                    </button>
                                @endif
                                @if ($enrollment?->status === 'completed')
                                    <button id="open-review-btn" class="w-full rounded-lg bg-amber-500 px-4 py-2.5 font-semibold text-white shadow-md transition hover:bg-amber-600">
                                        Beri Ulasan untuk Kursus Ini
                                    </button>
                                @endif

                            </div>
                        </section>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <div id="review-modal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <h3 class="text-lg font-bold text-gray-800">Bagikan Pengalaman Belajar Anda</h3>
            <p class="mt-1 text-sm text-gray-600">
                Ulasan Anda akan membantu kami meningkatkan kualitas kursus.
            </p>
           <form action="{{ route('reviews.store', $class->id) }}" method="POST" class="mt-4">
    @csrf

    <!-- Rating 1-5 dengan penjelasan -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Beri Penilaian:</label>
        <div class="space-y-2">
            @php
                $ratings = [
                    1 => 'Kurang Baik',
                    2 => 'Cukup Baik',
                    3 => 'Baik',
                    4 => 'Sangat Baik',
                    5 => 'Luar Biasa'
                ];
            @endphp

            @foreach($ratings as $value => $label)
                <div class="flex items-center">
                    <input
                        type="radio"
                        id="rating_{{ $value }}"
                        name="rating"
                        value="{{ $value }}"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500"
                        required
                        {{ old('rating', $enrollment->rating) == $value ? 'checked' : '' }}
                    />
                    <label for="rating_{{ $value }}" class="ml-2 text-sm text-gray-700">
                        <span class="font-medium">{{ $value }}</span> - {{ $label }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Text Review -->
    <textarea
        name="review"
        rows="4"
        placeholder="Ceritakan pengalaman Anda mengikuti kursus ini..."
        class="w-full rounded-lg border border-gray-300 p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        required
    >{{ old('review', $enrollment->review) }}</textarea>

    <div class="mt-4 flex justify-end gap-3">
        <button type="button" id="close-review-modal" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
            Nanti Saja
        </button>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
            Kirim Ulasan
        </button>
    </div>
</form>
            {{-- <form action="{{ route('reviews.store', $class->id) }}" method="POST" class="mt-4">
                @csrf
                <textarea name="review" rows="4" placeholder="Ceritakan pengalaman Anda mengikuti kursus ini..." class="w-full rounded-lg border border-gray-300 p-3 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>{{ old('review', $enrollment->review) }}</textarea>

                <div class="mt-4 flex justify-end gap-3">
                    <button type="button" id="close-review-modal" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">
                        Nanti Saja
                    </button>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                        Kirim Ulasan
                    </button>
                </div>
            </form> --}}
        </div>
    </div>

    <script>
        document.getElementById('open-review-btn').addEventListener('click', function() {
            document.getElementById('review-modal').classList.remove('hidden');
        });

        document.getElementById('close-review-modal').addEventListener('click', function() {
            document.getElementById('review-modal').classList.add('hidden');
        });
    </script>

</x-app-layout>
