<x-app-layout>
    {{-- @dd([
        'is_attendance_required' => $material->is_attendance_required,
        'attendance_start' => $material->attendance_start,
        'attendance_end' => $material->attendance_end,
        'now' => now(),
        'in_range' => now()->between($material->attendance_start, $material->attendance_end, true),
        'isCurrentMaterialForAttendance' => $isCurrentMaterialForAttendance,
        'hasAttended' => $hasAttended,
    ]) --}}
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <nav class="mb-6 flex items-center space-x-2 text-sm text-slate-500">
            <a href="{{ route('kelas', $class->id) }}" class="transition hover:text-indigo-600">Kelas Saya</a>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="font-medium text-slate-800">{{ $material->name }}</span>
        </nav>

        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900 md:text-4xl">{{ $material->name }}</h1>
                <p class="mt-2 flex items-center text-slate-500">
                    <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold uppercase tracking-wider text-indigo-700">
                        Modul {{ $loop->iteration ?? 'Active' }}
                    </span>
                    <span class="ml-3 text-sm italic">{{ $class->course->name }}</span>
                </p>
            </div>

            @php
                $incompleteTasks = count(array_diff(array_merge($material->essayAssignments->pluck('id')->toArray(), $material->quizAssignments->pluck('id')->toArray()), array_merge($userEssaySubmissions, $userQuizSubmissions)));
            @endphp
            @if ($incompleteTasks > 0)
                <div class="flex animate-pulse items-center gap-3 rounded-2xl border border-rose-100 bg-rose-50 p-4">
                    <div class="rounded-full bg-rose-500 p-2 text-white">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-rose-800">Tugas Belum Selesai!</p>
                        <p class="text-xs text-rose-600">Selesaikan {{ $incompleteTasks }} tugas untuk lanjut ke materi berikutnya.</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

            <div class="space-y-8 lg:col-span-8">

                @if ($isCurrentMaterialForAttendance)
                    <div class="overflow-hidden rounded-2xl border border-blue-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between bg-blue-50 px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>
                                <div>
                                    <h3 class="font-bold text-blue-900">Verifikasi Kehadiran</h3>
                                    <p class="text-xs text-blue-700">Wajib mengambil foto selfie untuk absen hari ini.</p>
                                </div>
                            </div>
                            @if ($hasAttended)
                                <span class="flex items-center rounded-full bg-green-100 px-4 py-1.5 text-sm font-bold text-green-700">
                                    <svg class="mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Hadir
                                </span>
                            @else
                                <button type="button" id="absen-btn" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-200 transition hover:bg-blue-700 active:scale-95">
                                    Absen Sekarang
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
                    @if ($material->link_video)
                        <div class="aspect-video w-full bg-slate-900">
                            <iframe src="https://www.youtube.com/embed/{{ $material->link_video }}" class="h-full w-full" frameborder="0" allowfullscreen></iframe>
                        </div>
                    @elseif($material->image)
                        <img src="{{ asset('storage/' . $material->image) }}" class="max-h-[500px] w-full object-cover">
                    @endif

                    <div class="p-8 md:p-12">
                        <article class="prose prose-lg prose-slate max-w-none prose-headings:text-slate-900 prose-a:text-indigo-600">
                            {!! $material->content ?? '<p class="italic text-slate-400 text-center py-10">Materi tulisan tidak tersedia.</p>' !!}
                        </article>

                        @if ($material->pdf)
                            <div class="group relative mt-12 flex items-center justify-between rounded-2xl border-2 border-dashed border-slate-200 p-6 transition-all hover:border-indigo-400 hover:bg-indigo-50/30">
                                <div class="flex items-center space-x-4">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-rose-50 text-rose-500">
                                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 012-2h4.586A1 1 0 0111 2.414l4.586 4.586a1 1 0 01.293.707V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800">Modul Pendukung (PDF)</h4>
                                        <p class="text-sm text-slate-500">Silahkan unduh sebagai referensi tambahan.</p>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $material->pdf) }}" target="_blank" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 shadow-sm transition-colors hover:bg-slate-50">
                                    Buka Dokumen
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div id="section-tugas" class="rounded-3xl bg-slate-900 p-8 text-white shadow-xl shadow-slate-200">
                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold">Tugas & Evaluasi</h3>
                            <p class="text-sm text-slate-400">Pastikan semua tugas terkirim sebelum batas waktu.</p>
                        </div>
                        <span class="rounded-lg bg-slate-800 px-3 py-1 font-mono text-xs text-slate-300">
                            TASK_LIST v.1
                        </span>
                    </div>

                    @php
                        $allTasks = [];
                        // Gabungkan Essay
                        foreach ($material->essayAssignments as $task) {
                            $allTasks[] = (object) [
                                'id' => $task->id,
                                'title' => $task->title,
                                'type' => 'essay',
                                'is_submitted' => in_array($task->id, $userEssaySubmissions),
                            ];
                        }
                        // Gabungkan Quiz
                        foreach ($material->quizAssignments as $task) {
                            $allTasks[] = (object) [
                                'id' => $task->id,
                                'title' => $task->title,
                                'type' => 'quiz',
                                'is_submitted' => in_array($task->id, $userQuizSubmissions),
                            ];
                        }
                    @endphp

                    @if (empty($allTasks))
                        <div class="rounded-2xl border border-slate-800 bg-slate-800/50 py-10 text-center">
                            <p class="text-sm italic text-slate-500">Tidak ada tugas yang perlu dikerjakan pada materi ini.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($allTasks as $task)
                                <div class="group relative flex items-center justify-between rounded-2xl bg-slate-800 p-5 transition hover:bg-slate-700/80">
                                    <div class="flex items-center space-x-4">
                                        {{-- Icon Logic --}}
                                        <div class="{{ $task->is_submitted ? 'bg-green-500/20 text-green-400' : 'bg-indigo-500/20 text-indigo-400' }} flex h-12 w-12 items-center justify-center rounded-xl">
                                            @if ($task->type == 'essay')
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            @else
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            @endif
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-slate-100">{{ $task->title }}</h4>
                                            <div class="mt-1 flex items-center space-x-3">
                                                <span class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ $task->type }}</span>
                                                <span class="h-1 w-1 rounded-full bg-slate-600"></span>
                                                <span class="{{ $task->is_submitted ? 'text-green-400' : 'text-amber-400' }} text-xs">
                                                    {{ $task->is_submitted ? 'Terkirim' : 'Belum Dikerjakan' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        @if ($task->is_submitted)
                                            @if ($task->type == 'essay')
                                                {{-- Tombol Lihat Review Essay (Memicu Modal JS) --}}
                                                <button type="button" data-assignment-id="{{ $task->id }}" class="open-essay-modal flex items-center rounded-lg px-4 py-2 text-sm font-bold text-indigo-400 hover:bg-indigo-400/10">
                                                    Lihat Review
                                                </button>
                                            @else
                                                {{-- Tombol Lihat Hasil Quiz (Pindah Halaman) --}}
                                                <a href="{{ route('quiz.result', [$class->id, $task->id]) }}" class="flex items-center rounded-lg px-4 py-2 text-sm font-bold text-green-400 hover:bg-green-400/10">
                                                    Lihat Hasil
                                                </a>
                                            @endif
                                        @else
                                            {{-- Tombol Kerjakan (Link Dinamis berdasarkan Tipe) --}}
                                            @php
                                                $targetRoute = $task->type == 'essay' ? route('essay.show', [$class->id, $task->id]) : route('quiz.show', [$class->id, $task->id]);
                                            @endphp

                                            <a href="{{ $targetRoute }}" class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-bold text-white shadow-lg shadow-indigo-900/20 transition hover:bg-indigo-500">
                                                Kerjakan
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-4">
                <div class="sticky top-8 space-y-6">

                    <div class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                        <h3 class="font-bold text-slate-900">Progress Belajar</h3>
                        <div class="mt-4">
                            <div class="mb-2 flex items-center justify-between text-sm">
                                <span class="font-medium text-slate-500">Total Selesai</span>
                                <span class="font-bold text-indigo-600">{{ $enrollment->progress_percentage ?? 0 }}%</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-blue-600 transition-all duration-700" style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-100 bg-white p-2 shadow-sm">
                        <div class="p-4">
                            <h3 class="font-bold text-slate-900">Materi Kelas</h3>
                        </div>
                        <div class="space-y-1">
                            @foreach ($class->materials as $mat)
                                @php
                                    $classMaterial = $class->classMaterials->firstWhere('material_id', $mat->id);
                                    $canAccess = $classMaterial ? app(\App\Services\MaterialCompletionService::class)->arePreviousMaterialsCompleted(Auth::id(), $class->id, $classMaterial->order) : false;
                                    $isActive = $canAccess && $classMaterial?->visibility === 'visible';
                                    $isCurrent = $mat->id == $material->id;
                                @endphp

                                <a href="{{ $isActive ? route('materials.show', [$class->id, $mat->id]) : 'javascript:void(0)' }}" class="{{ $isCurrent ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : ($isActive ? 'text-slate-600 hover:bg-slate-50' : 'cursor-not-allowed opacity-40') }} group flex items-center rounded-2xl p-4 transition-all">
                                    <span class="{{ $isCurrent ? 'border-indigo-400 bg-indigo-500 text-white' : 'border-slate-200 bg-slate-50' }} mr-4 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border text-xs font-bold">
                                        {{ $loop->iteration }}
                                    </span>
                                    <span class="truncate text-sm font-semibold">{{ $mat->name }}</span>
                                    @if (!$isActive)
                                        <svg class="ml-auto h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($isCurrent)
                                        <div class="ml-auto flex h-2 w-2 animate-ping rounded-full bg-white"></div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('kelas', $class->id) }}" class="flex items-center justify-center rounded-2xl bg-slate-100 py-4 text-sm font-bold text-slate-600 transition hover:bg-slate-200">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Kelas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="essay-result-modal" class="fixed inset-0 z-[60] flex hidden items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
        <div class="w-full max-w-2xl transform overflow-hidden rounded-3xl bg-white shadow-2xl transition-all">
            <div class="flex items-center justify-between border-b border-slate-100 p-6">
                <h3 class="text-center text-xl font-bold text-slate-900">Hasil Evaluasi Tugas</h3>
                <button id="close-essay-modal" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="essay-content" class="max-h-[70vh] overflow-y-auto p-8">
            </div>
        </div>
    </div>

    <script>
        // JS MODAL LOGIC
        document.querySelectorAll('.open-essay-modal').forEach(button => {
            button.addEventListener('click', function() {
                const assignmentId = this.dataset.assignmentId;
                const essayData = @json($userEssayDetails);

                if (essayData[assignmentId]) {
                    const essay = essayData[assignmentId];
                    let html = `<div class="space-y-6">`;

                    if (essay.answer_text) {
                        html += `<div><label class="text-xs font-black uppercase text-slate-400 tracking-widest">Jawaban Anda</label>
                                 <div class="mt-2 rounded-2xl bg-slate-50 p-5 text-slate-700 leading-relaxed">${essay.answer_text}</div></div>`;
                    }

                    if (essay.file_path) {
                        html += `<div><label class="text-xs font-black uppercase text-slate-400 tracking-widest">Lampiran</label>
                                 <a href="{{ asset('storage/') }}/${essay.file_path}" target="_blank" class="mt-2 flex items-center rounded-xl border border-slate-200 p-4 text-indigo-600 hover:bg-indigo-50 transition">
                                 <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                 Lihat Dokumen</a></div>`;
                    }

                    if (essay.is_graded) {
                        html += `<div class="rounded-2xl bg-indigo-900 p-6 text-white">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold text-indigo-300">Skor Akhir</span>
                                        <span class="text-3xl font-black">${essay.score ?? 0}/100</span>
                                    </div>
                                    <div class="mt-4 border-t border-indigo-800 pt-4">
                                        <p class="text-xs font-bold uppercase text-indigo-400">Feedback Mentor</p>
                                        <p class="mt-2 italic text-indigo-100">${essay.feedback ?? 'Tidak ada komentar.'}</p>
                                    </div>
                                 </div>`;
                    } else {
                        html += `<div class="rounded-2xl bg-amber-50 border border-amber-100 p-4 text-amber-700 text-sm font-medium">⏳ Menunggu penilaian mentor.</div>`;
                    }

                    html += `</div>`;
                    document.getElementById('essay-content').innerHTML = html;
                    document.getElementById('essay-result-modal').classList.remove('hidden');
                }
            });
        });

        document.getElementById('close-essay-modal').addEventListener('click', () => {
            document.getElementById('essay-result-modal').classList.add('hidden');
        });

        // ABSENSI LOGIC
        document.getElementById('absen-btn')?.addEventListener('click', function() {
            const modalAbsen = document.createElement('div');
            modalAbsen.className = "fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-md p-4";
            modalAbsen.innerHTML = `
                <div class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-900">Ambil Foto Absen</h3>
                        <p class="text-sm text-slate-500">Pastikan wajah Anda berada di dalam bingkai.</p>
                        <div class="mt-4 overflow-hidden rounded-2xl border-4 border-slate-100 bg-black aspect-square relative">
                            <video id="video-stream" class="h-full w-full object-cover" autoplay playsinline></video>
                            <canvas id="canvas-capture" class="hidden"></canvas>
                            <img id="photo-result" class="hidden h-full w-full object-cover">
                        </div>
                        <div class="mt-6 flex gap-3">
                            <button type="button" id="cancel-absen" class="flex-1 rounded-xl bg-slate-100 py-3 font-bold text-slate-600 hover:bg-slate-200 transition">Batal</button>
                            <button type="button" id="snap-absen" class="flex-1 rounded-xl bg-indigo-600 py-3 font-bold text-white hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">Ambil Foto</button>
                            <button type="button" id="send-absen" class="hidden flex-1 rounded-xl bg-green-600 py-3 font-bold text-white hover:bg-green-700 transition shadow-lg shadow-green-200">Kirim Sekarang</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modalAbsen);

            const video = modalAbsen.querySelector('#video-stream');
            const canvas = modalAbsen.querySelector('#canvas-capture');
            const resultImg = modalAbsen.querySelector('#photo-result');
            const snapBtn = modalAbsen.querySelector('#snap-absen');
            const sendBtn = modalAbsen.querySelector('#send-absen');
            let videoStream;

            navigator.mediaDevices.getUserMedia({
                video: true
            }).then(stream => {
                videoStream = stream;
                video.srcObject = stream;
            }).catch(err => {
                alert('Akses kamera ditolak: ' + err.message);
                modalAbsen.remove();
            });

            snapBtn.addEventListener('click', () => {
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0);
                resultImg.src = canvas.toDataURL('image/jpeg');
                resultImg.classList.remove('hidden');
                video.classList.add('hidden');
                snapBtn.classList.add('hidden');
                sendBtn.classList.remove('hidden');
            });

            sendBtn.addEventListener('click', () => {
                sendBtn.disabled = true;
                sendBtn.innerText = 'Mengirim...';

                canvas.toBlob(blob => {
                    const formData = new FormData();
                    formData.append('photo', blob, 'absen.jpg');
                    formData.append('material_id', '{{ $material->id }}');
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route('attendance.store', ['classId' => $class->id]) }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert('Gagal: ' + data.error);
                                modalAbsen.remove();
                            }
                        });
                }, 'image/jpeg');
            });

            modalAbsen.querySelector('#cancel-absen').addEventListener('click', () => {
                if (videoStream) videoStream.getTracks().forEach(t => t.stop());
                modalAbsen.remove();
            });
        });
    </script>
</x-app-layout>
