@extends('layouts.managecourse')

@section('manage-content')
    <div class="mx-auto w-full space-y-8">
        {{-- Header --}}
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm md:flex-row md:items-center md:justify-between">
            <div>
                <nav class="mb-2 flex items-center gap-2 text-xs font-semibold text-slate-500">
                    <a href="{{ route('mentor.materi.manage', $quiz->material_id) }}" class="transition hover:text-emerald-600">Materi</a>
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 5l7 7-7 7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="text-slate-900">Kelola Soal</span>
                </nav>
                <h1 class="text-2xl font-black tracking-tight text-slate-900">{{ $quiz->title }}</h1>
                <p class="text-sm font-medium italic text-slate-500">Total {{ $quiz->questions->count() }} Pertanyaan tersimpan</p>
            </div>

            <button onclick="openCreateModal()" class="flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-200 transition-all hover:bg-emerald-700 active:scale-95">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round" />
                </svg>
                Tambah Soal Baru
            </button>
        </div>

        {{-- List Section --}}
        <div class="space-y-6">
            <div class="flex items-center gap-4 px-2">
                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-400">Daftar Pertanyaan</h3>
                <div class="h-[1px] flex-1 bg-slate-200"></div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                @forelse($quiz->questions as $index => $q)
                    <div class="group relative rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm transition-all hover:border-emerald-300 hover:shadow-xl">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex-1 space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900 text-xs font-black text-white">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 ring-1 ring-emerald-200/50">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-700">{{ $q->points }} Points</span>
                                    </div>
                                </div>
                                {{-- Content Panjang Soal --}}
                                <div class="pr-4">
                                    <h4 class="whitespace-pre-line break-words text-lg font-bold leading-relaxed text-slate-800">
                                        {{ $q->question_text }}
                                    </h4>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex shrink-0 gap-2 lg:flex-col">
                                <button onclick="openEditModal({{ json_encode($q) }})" class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-100 bg-slate-50 text-slate-400 shadow-sm transition-all hover:bg-blue-50 hover:text-blue-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <form action="{{ route('mentor.materi.quiz.question.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                                    @csrf @method('DELETE')
                                    <button class="flex h-11 w-11 items-center justify-center rounded-xl border border-slate-100 bg-slate-50 text-slate-400 shadow-sm transition-all hover:bg-red-50 hover:text-red-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Options - Responsive Grid --}}
                        <div class="mt-8 grid grid-cols-1 gap-4 lg:grid-cols-2">
                            @foreach (['a', 'b', 'c', 'd'] as $opt)
                                <div class="{{ $q->correct_option == $opt ? 'bg-emerald-50 border-emerald-200 ring-1 ring-emerald-100' : 'border-slate-100 bg-slate-50/50' }} flex items-start gap-4 rounded-2xl border p-4 transition-all">
                                    <div class="{{ $q->correct_option == $opt ? 'bg-emerald-600 border-emerald-500 text-white' : 'bg-white border-slate-200 text-slate-400' }} flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border text-[10px] font-black">
                                        {{ strtoupper($opt) }}
                                    </div>
                                    <div class="{{ $q->correct_option == $opt ? 'font-bold text-emerald-900' : 'font-medium text-slate-600' }} break-words text-sm leading-relaxed">
                                        {{ $q->{'option_' . $opt} }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-[3rem] border-2 border-dashed border-slate-200 py-24 text-center">
                        <p class="text-lg font-bold text-slate-900">Belum ada pertanyaan</p>
                        <p class="text-sm text-slate-400">Klik tombol "Tambah Soal Baru" untuk memulai.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MODAL SYSTEM (Create & Edit) --}}
    <div id="quizModal" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-slate-950/60 backdrop-blur-sm transition-all">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-4xl scale-95 overflow-hidden rounded-[2.5rem] bg-white shadow-2xl transition-all" id="modalContent">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-8 py-6">
                    <div>
                        <h3 id="modalTitle" class="text-xl font-black text-slate-900">Buat Pertanyaan</h3>
                        <p class="text-xs font-medium uppercase tracking-widest text-slate-400">Lengkapi formulir di bawah ini</p>
                    </div>
                    <button onclick="closeModal()" class="rounded-full border border-slate-200 bg-white p-2 text-slate-400 shadow-sm transition-all hover:text-red-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <form id="quizForm" method="POST" class="p-8">
                    @csrf
                    <div id="method_field"></div>
                    <input type="hidden" name="quiz_assignment_id" value="{{ $quiz->id }}">

                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                        {{-- Kiri --}}
                        <div class="space-y-5">
                            <div class="group">
                                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600">Narasi Pertanyaan</label>
                                <textarea name="question_text" id="q_text" rows="8" required placeholder="Tuliskan pertanyaan..." class="w-full rounded-[1.5rem] border-slate-200 bg-slate-50/50 p-5 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-emerald-600">Kunci Jawaban</label>
                                    <select name="correct_option" id="q_correct" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold focus:ring-emerald-500">
                                        <option value="a">Opsi A</option>
                                        <option value="b">Opsi B</option>
                                        <option value="c">Opsi C</option>
                                        <option value="d">Opsi D</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600">Bobot Poin</label>
                                    <input type="number" name="points" id="q_points" value="10" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-bold focus:ring-emerald-500">
                                </div>
                            </div>
                        </div>

                        {{-- Kanan --}}
                        <div class="space-y-4">
                            <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-600">Opsi Jawaban</label>
                            @foreach (['a', 'b', 'c', 'd'] as $opt)
                                <div class="group relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300 group-focus-within:text-emerald-500">{{ strtoupper($opt) }}</span>
                                    <input type="text" name="option_{{ $opt }}" id="q_option_{{ $opt }}" required placeholder="..." class="w-full rounded-2xl border-slate-200 py-3.5 pl-10 pr-4 text-sm shadow-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                                </div>
                            @endforeach

                            <div class="pt-4">
                                <button type="submit" class="w-full rounded-2xl bg-slate-900 py-4 text-sm font-black text-white shadow-xl shadow-slate-200 transition-all hover:bg-emerald-600 active:scale-95">
                                    Simpan Data Pertanyaan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const modal = document.getElementById('quizModal');
        const form = document.getElementById('quizForm');
        const modalTitle = document.getElementById('modalTitle');
        const methodField = document.getElementById('method_field');
        const STORAGE_KEY = 'draft_quiz_question';

        // Konfigurasi SweetAlert Custom Theme (Filament Style)
        const Toast = Swal.mixin({
            customClass: {
                confirmButton: 'px-6 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-bold mx-2 shadow-lg hover:bg-slate-800 transition',
                cancelButton: 'px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold mx-2 hover:bg-slate-200 transition'
            },
            buttonsStyling: false
        });

        function openCreateModal() {
            modalTitle.innerText = "Buat Pertanyaan Baru";
            form.action = "{{ route('mentor.materi.quiz.question.store') }}";
            methodField.innerHTML = "";
            form.reset();

            const draft = localStorage.getItem(STORAGE_KEY);
            if (draft) {
                Toast.fire({
                    title: 'Draf Ditemukan',
                    text: "Kamu punya ketikan yang belum tersimpan. Mau dipulihkan?",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Pulihkan',
                    cancelButtonText: 'Hapus Draf'
                }).then((result) => {
                    if (result.isConfirmed) {
                        loadDraft(JSON.parse(draft));
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        localStorage.removeItem(STORAGE_KEY);
                    }
                });
            }
            showModal();
        }

        function openEditModal(q) {
            modalTitle.innerText = "Edit Pertanyaan";
            form.action = `/mentor/quiz/question/${q.id}`;
            methodField.innerHTML = `@method('PUT')`;

            document.getElementById('q_text').value = q.question_text;
            document.getElementById('q_option_a').value = q.option_a;
            document.getElementById('q_option_b').value = q.option_b;
            document.getElementById('q_option_c').value = q.option_c;
            document.getElementById('q_option_d').value = q.option_d;
            document.getElementById('q_correct').value = q.correct_option;
            document.getElementById('q_points').value = q.points;
            showModal();
        }

        // Auto-Save Logic
        form.addEventListener('input', () => {
            if (methodField.innerHTML === "") {
                const formData = {
                    text: document.getElementById('q_text').value,
                    a: document.getElementById('q_option_a').value,
                    b: document.getElementById('q_option_b').value,
                    c: document.getElementById('q_option_c').value,
                    d: document.getElementById('q_option_d').value,
                    correct: document.getElementById('q_correct').value,
                    points: document.getElementById('q_points').value,
                };
                localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
            }
        });

        function loadDraft(data) {
            document.getElementById('q_text').value = data.text;
            document.getElementById('q_option_a').value = data.a;
            document.getElementById('q_option_b').value = data.b;
            document.getElementById('q_option_c').value = data.c;
            document.getElementById('q_option_d').value = data.d;
            document.getElementById('q_correct').value = data.correct;
            document.getElementById('q_points').value = data.points;
        }

        function showModal() {
            modal.classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('modalContent').classList.remove('scale-95');
                document.getElementById('modalContent').classList.add('scale-100');
            }, 10);
        }

        // Modal Close dengan SweetAlert Proteksi
        function closeModal() {
            const qText = document.getElementById('q_text').value;
            if (qText.length > 20) {
                Toast.fire({
                    title: 'Tutup Jendela?',
                    text: "Data yang kamu ketik akan tetap jadi draf, tapi belum masuk ke database.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tutup',
                    cancelButtonText: 'Lanjut Ngetik'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeClose();
                    }
                });
            } else {
                executeClose();
            }
        }

        function executeClose() {
            document.getElementById('modalContent').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 150);
        }

        // Integrasi Tombol Hapus Soal agar Pakai SweetAlert
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.getAttribute('onsubmit') && e.target.getAttribute('onsubmit').includes('confirm')) {
                e.preventDefault(); // Stop confirm() default browser
                const formHapus = e.target;

                Toast.fire({
                    title: 'Hapus Soal?',
                    text: "Soal ini akan hilang permanen dari kuis.",
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formHapus.removeAttribute('onsubmit'); // Lepas proteksi
                        formHapus.submit(); // Kirim beneran
                    }
                });
            }
        });

        form.onsubmit = function() {
            localStorage.removeItem(STORAGE_KEY);
        };

        window.onclick = function(event) {
            if (event.target == modal) closeModal();
        }
    </script>
@endsection
