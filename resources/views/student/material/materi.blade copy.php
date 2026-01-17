<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-6 text-sm text-gray-600">
            {{-- <a href="{{ route('my-classes') }}" class="hover:underline">Kelas Saya</a> --}}
            <a href="" class="hover:underline">Kelas Saya</a>
            <span class="mx-2">/</span>
            {{-- <a href="{{ route('kelas', $class->id) }}" class="hover:underline">{{ $class->course->name }}</a> --}}
            <a href="" class="hover:underline">{{ $class->course->name }}</a>
            <span class="mx-2">/</span>
            <span class="font-medium text-gray-800">{{ $material->name }}</span>
        </nav>

        <!-- Header Materi -->
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-slate-800">{{ $material->name }}</h1>
            {{-- <p class="mt-2 text-gray-600">Materi {{ $class->materials->search($material)->key + 1 ?? '?' }} dari {{ $class->materials->count() }}</p> --}}
        </div>

        <!-- Konten Utama -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- KIRI: Konten Materi -->
            <div class="lg:col-span-2">
                <div class="rounded-xl bg-white p-6 shadow-sm">

                    <!-- Video (jika ada) -->
                    @if ($material->link_video)
                        <div class="mb-6 aspect-video overflow-hidden rounded-lg bg-black">
                            <iframe src="https://www.youtube.com/embed/{{ $material->link_video }}" class="h-full w-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
                            </iframe>
                        </div>
                    @endif

                    <!-- Gambar (jika ada) -->
                    @if ($material->image)
                        <div class="mb-6">
                            <img src="{{ asset('storage/' . $material->image) }}" alt="Ilustrasi Materi" class="w-full rounded-lg shadow">
                        </div>
                    @endif

                    <!-- PDF (jika ada) -->
                    @if ($material->pdf)
                        <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <h3 class="mb-2 font-semibold text-gray-800">Dokumen Pendukung</h3>
                            <a href="{{ asset('storage/' . $material->pdf) }}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Lihat PDF
                            </a>
                        </div>
                    @endif

                    <!-- Konten Teks -->
                    @if ($material->content)
                        <div class="prose prose-slate max-w-none">
                            {!! $material->content !!}
                        </div>
                    @else
                        <p class="italic text-gray-500">Tidak ada konten teks untuk materi ini.</p>
                    @endif

                </div>
            </div>

            <!-- KANAN: Navigasi & Progress -->
            <div class="lg:col-span-1">
                <div class="sticky top-6 rounded-xl bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-bold text-slate-800">Daftar Materi</h3>

                    <div class="space-y-2">
                        @php
                            $service = app(\App\Services\MaterialCompletionService::class);
                        @endphp

                        @foreach ($class->materials as $mat)
                            {{-- Dapatkan class_material_id untuk materi ini --}}
                            @php
                                $classMaterial = $class->classMaterials->firstWhere('material_id', $mat->id);
                                $canAccess = $classMaterial ? $service->arePreviousMaterialsCompleted(Auth::id(), $class->id, $classMaterial->order) : false;
                                $isActive = $canAccess && $classMaterial?->visibility === 'visible';
                                $isCurrent = $mat->id == $material->id;
                            @endphp

                            @if ($isActive)
                                <a href="{{ route('materials.show', [$class->id, $mat->id]) }}" class="{{ $isCurrent ? 'bg-blue-50 border border-blue-200 text-blue-700' : 'hover:bg-gray-50 text-gray-700' }} block rounded-lg p-3">
                                    <span class="font-medium">{{ $loop->iteration }}.</span>
                                    <span class="ml-2">{{ Str::limit($mat->name, 30) }}</span>
                                </a>
                            @else
                                {{-- Nonaktifkan link jika tidak bisa diakses --}}
                                <div class="block cursor-not-allowed rounded-lg p-3 opacity-50">
                                    <span class="font-medium text-gray-400">{{ $loop->iteration }}.</span>
                                    <span class="ml-2 text-gray-400">{{ Str::limit($mat->name, 30) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Progress -->
                    <div class="mt-6 border-t border-gray-200 pt-4">
                        <div class="mb-1 flex justify-between text-sm text-gray-600">
                            <span>Kemajuan Kelas</span>
                            <span>{{ $enrollment->progress_percentage ?? 0 }}%</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-gray-200">
                            <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-600" style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Tombol Kembali -->
                    <a href="{{ route('kelas', $class->id) }}" class="mt-6 flex items-center text-sm text-gray-600 hover:text-gray-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Kelas
                    </a>
                    <section class="mt-8">
                        <h3 class="mb-4 text-xl font-bold text-gray-800">Tugas untuk Materi Ini</h3>

                        @php
                            $allTasks = [];
                            foreach ($material->essayAssignments as $task) {
                                $allTasks[] = (object) [
                                    'id' => $task->id,
                                    'title' => $task->title,
                                    'type' => 'essay',
                                    'is_submitted' => in_array($task->id, $userEssaySubmissions),
                                ];
                            }
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
                            <p class="italic text-gray-500">Belum ada tugas untuk materi ini.</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($allTasks as $task)
                                    @php
                                        $status = $task->is_submitted ? 'Selesai' : 'Belum Dikerjakan';
                                        $statusColor = $task->is_submitted ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                                        $badgeColor = $task->is_submitted ? 'bg-green-200' : 'bg-red-200';
                                    @endphp

                                    <div class="{{ $statusColor }} rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <span class="font-semibold">{{ $task->title }}</span>
                                                <div class="mt-1 flex items-center">
                                                    <span class="{{ $badgeColor }} rounded px-2 py-0.5 text-xs text-gray-800">
                                                        {{ ucfirst($task->type) }}
                                                    </span>
                                                    <span class="ml-2 text-xs text-gray-700">{{ $status }}</span>
                                                </div>
                                            </div>
                                            @if ($task->is_submitted && $task->type === 'essay')
                                                <button type="button" data-assignment-id="{{ $task->id }}" class="open-essay-modal text-sm font-bold text-indigo-600 hover:underline">
                                                    Lihat Hasil
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Hasil Essay -->
    <div id="essay-result-modal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-xl">
            <div class="flex justify-between">
                <h3 class="text-lg font-bold text-gray-800">Hasil Tugas Essay</h3>
                <button id="close-essay-modal" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="essay-content" class="mt-4 space-y-4">
                <!-- Isi akan diisi oleh JS -->
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.open-essay-modal').forEach(button => {
            button.addEventListener('click', function() {
                const assignmentId = this.dataset.assignmentId;
                const essayData = @json($userEssayDetails); // Kirim data dari PHP ke JS

                if (essayData[assignmentId]) {
                    const essay = essayData[assignmentId];
                    let html = '';

                    // Jawaban Teks
                    if (essay.answer_text) {
                        html += `
                        <div>
                            <h4 class="font-semibold text-gray-700">Jawaban Anda:</h4>
                            <p class="mt-2 p-3 bg-gray-50 rounded-lg text-gray-800">${essay.answer_text}</p>
                        </div>
                    `;
                    }

                    // File Terupload
                    if (essay.file_path) {
                        const fileUrl = '{{ asset('storage/') }}' + essay.file_path;
                        const fileName = essay.file_path.split('/').pop();
                        html += `
                        <div>
                            <h4 class="font-semibold text-gray-700">File Terupload:</h4>
                            <a href="${fileUrl}" target="_blank" class="mt-1 inline-block text-indigo-600 hover:underline">
                                ${fileName}
                            </a>
                        </div>
                    `;
                    }

                    // Nilai & Feedback
                    if (essay.is_graded) {
                        html += `
                        <div class="border-t pt-4">
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-amber-600">${essay.score ?? 0}/100</span>
                                <span class="ml-3 inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">
                                    Sudah Dinilai
                                </span>
                            </div>
                            ${essay.feedback ? `
                                    <div class="mt-3">
                                        <h4 class="font-semibold text-gray-700">Komentar Mentor:</h4>
                                        <p class="mt-1 p-3 bg-blue-50 rounded-lg text-gray-800">${essay.feedback}</p>
                                    </div>
                                ` : ''}
                        </div>
                    `;
                    } else {
                        html += `
                        <div class="border-t pt-4">
                            <p class="text-gray-600">Belum dinilai oleh mentor.</p>
                        </div>
                    `;
                    }

                    document.getElementById('essay-content').innerHTML = html;
                    document.getElementById('essay-result-modal').classList.remove('hidden');
                }
            });
        });

        document.getElementById('close-essay-modal').addEventListener('click', function() {
            document.getElementById('essay-result-modal').classList.add('hidden');
        });
    </script>
    {{-- ✅ GUNAKAN INI — TIDAK ADA $todayMaterial --}}
    @if ($isCurrentMaterialForAttendance)
        <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <h4 class="font-bold text-blue-800">Absensi Hari Ini</h4>
                    <p class="text-sm text-blue-700">Pertemuan: {{ $material->name }}</p>
                </div>
                @if ($hasAttended)
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                        <svg class="mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Hadir
                    </span>
                @else
                    <button type="button" id="absen-btn" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700">
                        Absen Sekarang
                    </button>
                @endif
            </div>
        </div>
    @endif
    <script>
        document.getElementById('absen-btn')?.addEventListener('click', function() {
            // Buat modal popup
            const modal = document.createElement('div');
            modal.innerHTML = `
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="w-full max-w-md rounded-xl bg-white p-6">
                <h3 class="text-lg font-bold text-gray-800">Ambil Foto Absensi</h3>
                <p class="mt-1 text-sm text-gray-600">Pastikan wajah terlihat jelas dan sedang membaca materi.</p>

                <div class="mt-4 flex justify-center">
                    <video id="video" class="w-full rounded-lg border" autoplay playsinline></video>
                </div>

                <div class="mt-4 flex justify-center">
                    <canvas id="canvas" class="hidden"></canvas>
                    <img id="photo-preview" class="hidden max-h-40 rounded-lg" />
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" id="close-modal" class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="button" id="capture-btn" class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Ambil Foto
                    </button>
                    <button type="button" id="submit-btn" class="hidden flex-1 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white">
                        Kirim Absen
                    </button>
                </div>
            </div>
            </div>
            `;
            document.body.appendChild(modal);

            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const photoPreview = document.getElementById('photo-preview');
            const captureBtn = document.getElementById('capture-btn');
            const submitBtn = document.getElementById('submit-btn');
            const closeModal = document.getElementById('close-modal');

            let stream;

            // Akses kamera
            navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false
                })
                .then(function(s) {
                    stream = s;
                    video.srcObject = s;
                })
                .catch(function(err) {
                    alert('Gagal mengakses kamera: ' + err.message);
                    modal.remove();
                });

            // Ambil foto
            captureBtn.addEventListener('click', function() {
                const ctx = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const dataUrl = canvas.toDataURL('image/jpeg');

                photoPreview.src = dataUrl;
                photoPreview.classList.remove('hidden');
                submitBtn.classList.remove('hidden');
                captureBtn.classList.add('hidden');
            });

            // Kirim absen
            submitBtn.addEventListener('click', function() {
                const formData = new FormData();
                const blob = dataURLtoBlob(photoPreview.src);
                formData.append('photo', blob, 'absensi_' + Date.now() + '.jpg');
                formData.append('material_id', '{{ $material->id }}');
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('attendance.store', ['classId' => $class->id]) }}', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Absensi berhasil!');
                            location.reload(); // refresh halaman
                        } else {
                            alert('Gagal: ' + data.error);
                            modal.remove();
                        }
                    })
                    .catch(err => {
                        alert('Error: ' + err.message);
                        modal.remove();
                    });
            });

            // Tutup modal
            closeModal.addEventListener('click', function() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                modal.remove();
            });

            // Helper: convert data URL to Blob
            function dataURLtoBlob(dataurl) {
                const arr = dataurl.split(',');
                const mime = arr[0].match(/:(.*?);/)[1];
                const bstr = atob(arr[1]);
                let n = bstr.length;
                const u8arr = new Uint8Array(n);
                while (n--) {
                    u8arr[n] = bstr.charCodeAt(n);
                }
                return new Blob([u8arr], {
                    type: mime
                });
            }
        });
    </script>
</x-app-layout>
