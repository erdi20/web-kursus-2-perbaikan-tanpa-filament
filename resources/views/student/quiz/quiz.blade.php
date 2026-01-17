<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('kelas', $classId) }}" class="inline-flex items-center text-blue-600 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Kelas
            </a>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            @if (!$submission && $assignment->duration_minutes)
                <div class="lg:col-span-1">
                    <div class="sticky top-16 space-y-6">
                        <div class="rounded-xl border border-red-200 bg-white p-6 shadow-lg">
                            <h3 class="mb-3 flex items-center gap-2 text-xl font-bold text-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Sisa Waktu
                            </h3>
                            <div id="countdown-timer" class="text-center text-4xl font-extrabold text-red-600">
                                --:--:--
                            </div>
                            <p class="mt-2 text-center text-sm text-gray-500">Waktu akan berakhir secara otomatis saat 00:00:00</p>
                        </div>

                        <div class="rounded-xl bg-white p-6 shadow-lg">
                            <h3 class="mb-4 text-xl font-bold text-gray-800">Navigasi Soal</h3>
                            <div id="question-navigator" class="flex flex-wrap gap-2">
                                @foreach ($questions as $index => $question)
                                    <a href="#question-{{ $question->id }}" id="nav-{{ $question->id }}" class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-gray-100 text-sm font-semibold text-gray-600 transition duration-150 hover:bg-indigo-100">
                                        {{ $loop->iteration }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="{{ !$submission && $assignment->duration_minutes ? 'lg:col-span-2' : 'lg:col-span-3' }}">
                <div class="overflow-hidden rounded-xl bg-white shadow-lg">
                    <div class="bg-gradient-to-r from-[#20C896] to-[#259D7A] px-6 py-5 text-white">
                        <h1 class="text-3xl font-extrabold">{{ $assignment->title }}</h1>
                        <p class="mt-2 text-sm opacity-90">
                            Durasi: **{{ $assignment->duration_minutes }} menit**
                            @if ($assignment->due_date)
                                | Batas Akhir: {{ \Carbon\Carbon::parse($assignment->due_date)->translatedFormat('d F Y, H:i') }}
                            @endif
                        </p>
                    </div>

                    <div class="p-6">
                        @if ($assignment->description)
                            <div class="prose prose-slate mb-6 max-w-none border-b pb-4">
                                <h2 class="text-xl font-semibold text-gray-700">Petunjuk Pengerjaan</h2>
                                {!! $assignment->description !!}
                            </div>
                        @endif

                        @if ($submission)
                            <div class="mb-6 rounded-lg border border-blue-400 bg-blue-100 p-6 text-center">
                                <h3 class="flex items-center justify-center gap-2 text-xl font-bold text-blue-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Kuis Selesai!
                                </h3>
                                <p class="mt-2 text-gray-600">Anda telah menyelesaikan kuis ini pada {{ \Carbon\Carbon::parse($submission->created_at)->translatedFormat('d F Y, H:i') }}. Silakan tunggu penilaian dari mentor/sistem.</p>
                            </div>
                        @else
                            <form id="quiz-form" method="POST" action="{{ route('quiz.submit', ['classId' => $classId, 'assignmentId' => $assignment->id]) }}">
                                @csrf
                                <div id="quiz-questions" class="space-y-10">
                                    @foreach ($questions as $index => $question)
                                        <div id="question-{{ $question->id }}" class="question-item ...">
                                            {{-- ... kode nomor dan teks soal ... --}}

                                            <div class="space-y-3">
                                                @php
                                                    // 1. Masukkan semua pilihan yang ada ke dalam array
                                                    $options = [];
                                                    if ($question->option_a) {
                                                        $options[] = ['key' => 'A', 'text' => $question->option_a];
                                                    }
                                                    if ($question->option_b) {
                                                        $options[] = ['key' => 'B', 'text' => $question->option_b];
                                                    }
                                                    if ($question->option_c) {
                                                        $options[] = ['key' => 'C', 'text' => $question->option_c];
                                                    }
                                                    if ($question->option_d) {
                                                        $options[] = ['key' => 'D', 'text' => $question->option_d];
                                                    }

                                                    // 2. Acak urutan array tersebut
                                                    shuffle($options);
                                                @endphp

                                                @foreach ($options as $opt)
                                                    <label class="flex cursor-pointer items-start gap-4 rounded-lg border border-gray-200 bg-white p-4 transition duration-150 hover:bg-indigo-50 peer-checked:border-indigo-500">
                                                        {{-- Value tetap menggunakan key asli (A, B, C, atau D) agar validasi di Controller tidak berubah --}}
                                                        <input type="radio" name="question_{{ $question->id }}" value="{{ $opt['key'] }}" class="peer mt-1 h-5 w-5 text-indigo-600" required data-question-id="{{ $question->id }}">

                                                        <span class="prose font-medium text-gray-700">
                                                            {{-- Kita tidak menampilkan huruf A/B/C/D di sini agar tidak membingungkan karena sudah diacak --}}
                                                            {!! $opt['text'] !!}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-10 flex justify-end">
                                    <button type="submit" id="submit-button" class="rounded-xl bg-gradient-to-r from-[#20C896] to-[#259D7A] px-8 py-4 text-lg font-bold text-white shadow-xl transition duration-300 hover:opacity-90">
                                        Kirim Jawaban dan Selesaikan Kuis
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if (!$submission && $assignment->duration_minutes)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const durationMinutes = {{ $assignment->duration_minutes }};
                const timerElement = document.getElementById('countdown-timer');
                const form = document.getElementById('quiz-form');
                const submitButton = document.getElementById('submit-button');
                const questionNavigator = document.getElementById('question-navigator');

                // Waktu Akhir: Kita asumsikan kuis dimulai saat halaman dimuat
                // Ini akan memerlukan penyesuaian di sisi backend jika Anda ingin
                // waktu tetap berjalan saat user menutup/membuka halaman.
                // Untuk demo ini, kita menggunakan Local Storage.

                const startKey = 'quiz_start_time_{{ $assignment->id }}';
                const endKey = 'quiz_end_time_{{ $assignment->id }}';

                let endTime;

                // Cek apakah ada waktu mulai tersimpan
                let startTime = localStorage.getItem(startKey);

                if (!startTime) {
                    // Jika belum ada, set waktu mulai sekarang
                    startTime = Date.now();
                    localStorage.setItem(startKey, startTime);

                    // Hitung waktu akhir
                    endTime = startTime + (durationMinutes * 60 * 1000);
                    localStorage.setItem(endKey, endTime);
                } else {
                    // Jika ada, ambil waktu akhir yang tersimpan
                    endTime = parseInt(localStorage.getItem(endKey));
                }

                function formatTime(ms) {
                    const totalSeconds = Math.floor(ms / 1000);
                    const hours = Math.floor(totalSeconds / 3600);
                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                    const seconds = totalSeconds % 60;

                    const pad = (num) => String(num).padStart(2, '0');

                    return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
                }

                function updateTimer() {
                    const now = Date.now();
                    const remainingTime = endTime - now;

                    if (remainingTime <= 0) {
                        // Waktu Habis!
                        clearInterval(timerInterval);
                        timerElement.textContent = "00:00:00";
                        submitButton.disabled = true;
                        submitButton.textContent = 'Waktu Habis. Mengirim Jawaban...';

                        // Otomatis submit form
                        form.submit();

                        // Clear storage setelah submit
                        localStorage.removeItem(startKey);
                        localStorage.removeItem(endKey);

                        return;
                    }

                    timerElement.textContent = formatTime(remainingTime);
                }

                // --- Navigasi Soal dan Penanda Jawaban ---
                const radioButtons = form.querySelectorAll('input[type="radio"]');

                radioButtons.forEach(radio => {
                    radio.addEventListener('change', function() {
                        const questionId = this.dataset.questionId;
                        const navItem = document.getElementById(`nav-${questionId}`);

                        // Tandai di navigasi
                        if (navItem) {
                            navItem.classList.remove('bg-gray-100', 'border-gray-300', 'text-gray-600');
                            navItem.classList.add('bg-indigo-600', 'border-indigo-600', 'text-white');
                        }
                    });
                });

                // Mulai hitungan mundur setiap 1 detik
                const timerInterval = setInterval(updateTimer, 1000);
                updateTimer(); // Panggil sekali untuk menampilkan waktu segera

                // Cleanup storage saat form disubmit normal
                form.addEventListener('submit', function() {
                    localStorage.removeItem(startKey);
                    localStorage.removeItem(endKey);
                });
            });
        </script>
    @endif
</x-app-layout>
