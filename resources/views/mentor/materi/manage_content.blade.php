@extends('layouts.managecourse')

@section('manage-content')
    {{-- SATU x-data UNTUK SEMUA MODAL --}}
    <div x-data="{
        innerTab: 'essay',
        openModal: false,
        openEditModal: false,
        openQuizModal: false,
        {{-- Modal Tambah Quiz --}}
        openEditQuizModal: false,
        {{-- Modal Edit Quiz --}}
        editData: { url: '', title: '', desc: '', due: '', published: false },
        editQuizData: { url: '', title: '', duration: '', due: '', published: false }
    }" @open-essay-modal.window="openModal = true" class="space-y-6">

        {{-- Header & Tab Nav --}}
        <div class="flex items-center justify-between border-b border-slate-200 pb-6">
            <div>
                <nav class="mb-1 flex text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <a href="{{ route('mentor.kelolakursusmateri', $material->course_id) }}" class="hover:text-emerald-500">Daftar Materi</a>
                    <span class="mx-2">/</span>
                    <span>Kelola Isi Materi</span>
                </nav>
                <h2 class="text-2xl font-bold tracking-tight text-slate-950">{{ $material->name }}</h2>
            </div>
        </div>

        <div class="flex gap-4 border-b border-slate-100">
            <button @click="innerTab = 'essay'" :class="innerTab === 'essay' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500'" class="border-b-2 pb-3 text-sm font-bold transition-all">Tugas Essay</button>
            <button @click="innerTab = 'quiz'" :class="innerTab === 'quiz' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500'" class="border-b-2 pb-3 text-sm font-bold transition-all">Quiz</button>
            <button @click="innerTab = 'absen'" :class="innerTab === 'absen' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500'" class="border-b-2 pb-3 text-sm font-bold transition-all">Absensi</button>
        </div>

        {{-- CONTENT: ESSAY (Sesuai kode aslimu, tidak dirubah) --}}
        <div x-show="innerTab === 'essay'" class="space-y-4">
            <div class="flex justify-end">
                <button @click="openCreateEssayModal()" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-black">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" />
                    </svg>
                    Buat Tugas Essay
                </button>
            </div>
            <div class="grid grid-cols-1 gap-4">
                @forelse($material->essayAssignments as $essay)
                    <div class="group rounded-xl border border-slate-200 bg-white p-5 transition hover:border-emerald-200 hover:shadow-sm">
                        <div class="flex items-start justify-between">
                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $essay->title }}</h4>
                                    <p class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" />
                                        </svg>
                                        Deadline: {{ \Carbon\Carbon::parse($essay->due_date)->format('d M Y, H:i') }}
                                        @if (!$essay->is_published)
                                            <span class="ml-2 rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-tighter text-slate-400">Draft</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('mentor.materi.essay.submissions', $essay->id) }}" class="mr-2 text-xs font-bold text-emerald-600 transition hover:underline">Jawaban ({{ $essay->submissions->count() }})</a>

                                <button type="button"
                                    @click="openEditModal = true;
                editData = {
                    url: '{{ route('mentor.materi.essay.update', $essay->id) }}',
                    title: '{{ addslashes($essay->title) }}',
                    desc: '{{ addslashes($essay->description) }}',
                    due: '{{ \Carbon\Carbon::parse($essay->due_date)->format('Y-m-d\TH:i') }}',
                    published: {{ $essay->is_published ? 'true' : 'false' }}
                }"
                                    class="p-2 text-slate-400 transition hover:text-amber-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>

                                <form action="{{ route('mentor.materi.essay.destroy', $essay->id) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 transition hover:text-red-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 py-12 text-center">
                        <p class="text-sm font-medium italic text-slate-400">Belum ada tugas essay.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- CONTENT: QUIZ (BARU) --}}
        <div x-show="innerTab === 'quiz'" class="space-y-4" x-cloak>
            <div class="flex justify-end">
                <button @click="openQuizModal = true" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" />
                    </svg>
                    Buat Quiz Baru
                </button>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @forelse($material->quizAssignments as $quiz)
                    <div class="group rounded-xl border border-slate-200 bg-white p-5 transition hover:border-blue-200 hover:shadow-sm">
                        <div class="flex items-start justify-between">
                            <div class="flex gap-4">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" stroke-width="2" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $quiz->title }}</h4>
                                    <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                        <span class="flex items-center gap-1"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" />
                                            </svg> {{ $quiz->duration_minutes }} Menit</span>
                                        <span class="flex items-center gap-1"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" />
                                            </svg> {{ $quiz->questions->count() }} Soal</span>
                                        @if (!$quiz->is_published)
                                            <span class="rounded bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-400">Draft</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('mentor.materi.quiz.questions', $quiz->id) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50">Kelola Soal</a>

                                <button type="button"
                                    @click="openEditQuizModal = true;
                                        editQuizData = {
                                            url: '{{ route('mentor.materi.quiz.update', $quiz->id) }}',
                                            title: '{{ addslashes($quiz->title) }}',
                                            duration: '{{ $quiz->duration_minutes }}',
                                            due: '{{ \Carbon\Carbon::parse($quiz->due_date)->format('Y-m-d\TH:i') }}',
                                            published: {{ $quiz->is_published ? 'true' : 'false' }}
                                        }"
                                    class="rounded-lg border border-slate-200 p-1.5 text-slate-400 transition hover:border-amber-100 hover:bg-amber-50 hover:text-amber-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>

                                {{-- Tombol Hapus Quiz --}}
                                <button type="button" onclick="confirmDeleteQuiz('{{ route('mentor.hapusquiz', $quiz->id) }}')" class="rounded-lg border border-slate-200 p-1.5 text-slate-400 transition hover:border-red-100 hover:bg-red-50 hover:text-red-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                <a href="{{ route('mentor.materi.quiz.submissions', $quiz->id) }}" class="text-xs font-bold text-emerald-600 hover:underline">Nilai ({{ $quiz->submissions->count() }})</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 py-12 text-center">
                        <p class="text-sm font-medium italic text-slate-400">Belum ada quiz.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- MODAL TAMBAH QUIZ --}}
        <div x-show="openQuizModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-[2px]" x-cloak x-transition>
            <div @click.away="openQuizModal = false" class="relative w-full max-w-md overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                <div class="border-b border-slate-100 bg-slate-50 px-6 py-4">
                    <h3 class="text-lg font-bold text-slate-900">Buat Quiz Baru</h3>
                </div>
                <form action="{{ route('mentor.materi.quiz.store') }}" method="POST" class="space-y-4 p-6">
                    @csrf
                    <input type="hidden" name="material_id" value="{{ $material->id }}">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Judul Quiz</label>
                        <input type="text" name="title" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Deskripsi / Instruksi</label>
                        <textarea name="description" rows="3" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-emerald-500" placeholder="Contoh: Kerjakan dengan jujur, waktu terbatas!"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Durasi (Menit)</label>
                            <input type="number" name="duration_minutes" value="30" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Batas Waktu</label>
                            <input type="datetime-local" name="due_date" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-emerald-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-slate-50 pt-4">
                        <button type="button" @click="openQuizModal = false" class="text-sm font-bold text-slate-500 hover:text-slate-700">Batal</button>
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700">Simpan & Lanjut</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- modal edit quiz --}}
        <div x-show="openEditQuizModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-[2px]" x-cloak x-transition>
            <div @click.away="openEditQuizModal = false" class="relative w-full max-w-md overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <h3 class="text-lg font-bold text-slate-900">Edit Pengaturan Quiz</h3>
                </div>
                <form :action="editQuizData.url" method="POST" class="space-y-4 p-6">
                    @csrf @method('PUT')
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Judul Quiz</label>
                        <input type="text" name="title" x-model="editQuizData.title" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Deskripsi / Instruksi</label>
                        <textarea name="description" x-model="editQuizData.desc" rows="3" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Durasi (Menit)</label>
                            <input type="number" name="duration_minutes" x-model="editQuizData.duration" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-700">Batas Waktu</label>
                            <input type="datetime-local" name="due_date" x-model="editQuizData.due" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_published" id="quiz_published_edit" x-model="editQuizData.published" class="rounded border-slate-300 text-blue-600">
                        <label for="quiz_published_edit" class="text-sm font-medium text-slate-600">Publikasikan Quiz</label>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-slate-50 pt-4">
                        <button type="button" @click="openEditQuizModal = false" class="text-sm font-bold text-slate-500 transition hover:text-slate-700">Batal</button>
                        <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700">Update Quiz</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL TAMBAH ESSAY --}}
        <div x-show="openModal" @click.away="closeEssayModal($data)" id="essayModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-[2px]" x-cloak x-transition>
            <div @click.away="closeEssayModal()" id="essayModalContent" class="relative w-full max-w-lg overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl transition-transform duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-lg font-bold tracking-tight text-slate-900">Buat Tugas Essay Baru</h3>
                    <button @click="closeEssayModal($data)" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" />
                        </svg>
                    </button>
                </div>

                <form id="essayForm" action="{{ route('mentor.materi.essay.store', $material->id) }}" method="POST" class="space-y-4 p-6">
                    @csrf
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Judul Tugas</label>
                        <input type="text" id="e_title" name="title" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Instruksi/Deskripsi</label>
                        <textarea id="e_description" name="description" rows="4" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-emerald-500"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Batas Waktu</label>
                        <input type="datetime-local" id="e_due" name="due_date" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-emerald-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_published" id="e_published" class="rounded border-slate-300 text-emerald-600">
                        <label for="e_published" class="text-sm font-medium text-slate-600">Publikasikan sekarang</label>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-50 pt-4">
                        <button type="button" @click="closeEssayModal($data)" class="rounded-lg px-4 py-2 text-sm font-bold text-slate-500 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-700">Simpan Tugas</button>
                    </div>
                </form>
            </div>
        </div> {{-- PENUTUP MODAL TAMBAH ESSAY HARUS DI SINI --}}

        {{-- MODAL EDIT ESSAY (Berdiri Sendiri) --}}
        <div x-show="openEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-[2px]" x-cloak x-transition>
            <div @click.away="openEditModal = false" class="relative w-full max-w-lg overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <h3 class="text-lg font-bold text-slate-900">Edit Tugas Essay</h3>
                    <button @click="openEditModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" />
                        </svg>
                    </button>
                </div>
                <form :action="editData.url" method="POST" class="space-y-4 p-6">
                    @csrf @method('PUT')
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Judul Tugas</label>
                        <input type="text" name="title" x-model="editData.title" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Instruksi/Deskripsi</label>
                        <textarea name="description" x-model="editData.desc" rows="4" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-emerald-500"></textarea>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-700">Batas Waktu</label>
                        <input type="datetime-local" name="due_date" x-model="editData.due" required class="w-full rounded-lg border-slate-200 text-sm focus:ring-emerald-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_published" id="edit_is_published" x-model="editData.published" class="rounded border-slate-300 text-emerald-600">
                        <label for="edit_is_published" class="text-sm font-medium text-slate-600">Publikasikan tugas</label>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-slate-50 pt-4">
                        <button type="button" @click="openEditModal = false" class="rounded-lg px-4 py-2 text-sm font-bold text-slate-500 transition hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-lg bg-slate-900 px-6 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-black">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- TAB ABSEN (Berdiri Sendiri) --}}
        {{-- TAB ABSEN --}}
        <div x-show="innerTab === 'absen'" class="space-y-6" x-cloak x-transition>
            <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Rekap Presensi Mahasiswa</h3>
                    <p class="text-sm text-slate-500">Waktu otomatis dikonversi ke **WIB**.</p>
                </div>
                <div class="text-right">
                    <span class="block text-2xl font-black text-emerald-600">{{ $material->classMaterials->flatMap->attendances->count() }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Hadir</span>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 font-bold text-slate-700">Mahasiswa</th>
                            <th class="px-6 py-4 font-bold text-slate-700">Foto Bukti</th>
                            <th class="px-6 py-4 font-bold text-slate-700">Kelas</th>
                            <th class="px-6 py-4 font-bold text-slate-700">Waktu Absen (WIB)</th>
                            <th class="px-6 py-4 text-center font-bold text-slate-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            // Ambil semua attendance, konversi ke collection, urutkan terbaru
                            $allAttendances = $material->classMaterials->flatMap->attendances->sortByDesc('created_at');
                        @endphp

                        @forelse($allAttendances as $attendance)
                            <tr class="transition hover:bg-slate-50/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                                            @if ($attendance->student->profile_photo_path)
                                                <img src="{{ asset('storage/' . $attendance->student->profile_photo_path) }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center font-bold text-slate-400">
                                                    {{ strtoupper(substr($attendance->student->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="block font-bold text-slate-700">{{ $attendance->student->name }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $attendance->student->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div x-data="{ showImg: false }" class="flex items-center gap-3">
                                        @if ($attendance->photo_path)
                                            {{-- Thumbnail Foto --}}
                                            <div class="group relative h-12 w-16 shrink-0 overflow-hidden rounded-lg border border-slate-200 shadow-sm">
                                                <img @click="showImg = true" src="{{ asset('storage/' . $attendance->photo_path) }}" class="h-full w-full cursor-pointer object-cover transition duration-300 group-hover:scale-110">

                                                {{-- Overlay Zoom Icon --}}
                                                <div @click="showImg = true" class="absolute inset-0 flex cursor-pointer items-center justify-center bg-slate-900/40 opacity-0 transition-opacity group-hover:opacity-100">
                                                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </div>

                                            {{-- Tombol Hapus Foto Saja --}}
                                            <button type="button" onclick="confirmDeletePhoto('{{ $attendance->id }}')" class="flex h-8 w-8 items-center justify-center rounded-lg border border-rose-100 bg-rose-50 text-rose-500 shadow-sm transition-all hover:bg-rose-500 hover:text-white" title="Hapus file foto saja">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>

                                            {{-- Form Tersembunyi untuk Hapus Foto --}}
                                            <form id="delete-photo-form-{{ $attendance->id }}" action="{{ route('mentor.attendance.delete-photo', $attendance->id) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('PATCH')
                                            </form>

                                            {{-- Lightbox Zoom --}}
                                            <div x-show="showImg" x-transition.opacity class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/90 p-4" @click="showImg = false" x-cloak>
                                                <div class="relative max-h-full max-w-5xl">
                                                    <img src="{{ asset('storage/' . $attendance->photo_path) }}" class="rounded-2xl shadow-2xl">
                                                    <button class="absolute -top-12 right-0 text-white hover:text-slate-300">
                                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2 italic text-slate-400">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <span class="text-xs">Foto dihapus</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-600">
                                    {{ $attendance->classMaterial->courseClass->name ?? 'Kelas Tidak Ditemukan' }}
                                </td>
                                <td class="px-6 py-4 text-slate-600">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-700">
                                            {{ $attendance->created_at->timezone('Asia/Jakarta')->format('H:i:s') }}
                                        </span>
                                        <span class="text-[11px]">
                                            {{ $attendance->created_at->timezone('Asia/Jakarta')->translatedFormat('d F Y') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase tracking-tight text-emerald-700">
                                        Hadir
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center text-slate-400">
                                    <div class="flex flex-col items-center">
                                        <div class="mb-4 rounded-full bg-slate-50 p-4">
                                            <svg class="h-10 w-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-bold text-slate-900">Belum ada aktivitas presensi</p>
                                        <p class="text-xs">Data mahasiswa yang membuka materi akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div> {{-- PENUTUP x-data UTAMA --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDeletePhoto(id) {
            Swal.fire({
                title: 'Hapus Bukti Foto?',
                text: "File foto akan dihapus permanen untuk menghemat storage, tapi data absensi mahasiswa tetap ada.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48', // rose-600
                cancelButtonColor: '#64748b', // slate-500
                confirmButtonText: 'Ya, Hapus Foto!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-5 py-2.5 text-sm font-bold',
                    cancelButton: 'rounded-xl px-5 py-2.5 text-sm font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form berdasarkan ID unik tadi
                    document.getElementById('delete-photo-form-' + id).submit();
                }
            })
        }

        const essayForm = document.getElementById('essayForm');
        const ESSAY_STORAGE_KEY = 'draft_essay_assignment';

        const EssayToast = Swal.mixin({
            customClass: {
                confirmButton: 'px-6 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-bold mx-2 shadow-lg hover:bg-slate-800 transition',
                cancelButton: 'px-6 py-2.5 rounded-xl bg-slate-100 text-slate-600 text-sm font-bold mx-2 hover:bg-slate-200 transition'
            },
            buttonsStyling: false
        });

        // Fungsi Pembuka Modal (Dipanggil dari @click Alpine)
        function openCreateEssayModal() {
            // 1. Cek Draf dulu
            const draft = localStorage.getItem(ESSAY_STORAGE_KEY);
            if (draft) {
                EssayToast.fire({
                    title: 'Draf Essay Ditemukan',
                    text: "Kamu punya draf tugas yang belum disimpan. Pulihkan?",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Pulihkan',
                    cancelButtonText: 'Hapus Draf'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const data = JSON.parse(draft);
                        document.getElementById('e_title').value = data.title;
                        document.getElementById('e_description').value = data.description;
                        document.getElementById('e_due').value = data.due;
                        document.getElementById('e_published').checked = data.published;

                        // Setelah restore, baru buka modal
                        window.dispatchEvent(new CustomEvent('open-essay-modal'));
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        localStorage.removeItem(ESSAY_STORAGE_KEY);
                        essayForm.reset();
                        window.dispatchEvent(new CustomEvent('open-essay-modal'));
                    }
                });
            } else {
                // 2. Jika tidak ada draf, langsung buka modal
                window.dispatchEvent(new CustomEvent('open-essay-modal'));
            }
        }

        // Auto-Save Logic saat mengetik
        essayForm.addEventListener('input', () => {
            const formData = {
                title: document.getElementById('e_title').value,
                description: document.getElementById('e_description').value,
                due: document.getElementById('e_due').value,
                published: document.getElementById('e_published').checked,
            };
            localStorage.setItem(ESSAY_STORAGE_KEY, JSON.stringify(formData));
        });

        // Proteksi saat modal ditutup
        function closeEssayModal(alpineObj) {
            const titleText = document.getElementById('e_title').value;
            const descText = document.getElementById('e_description').value;

            if (titleText.length > 5 || descText.length > 10) {
                EssayToast.fire({
                    title: 'Tutup Jendela?',
                    text: "Tugas essay belum disimpan. Tenang, draf tetap aman di browser ini.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tutup',
                    cancelButtonText: 'Lanjut Edit'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Gunakan objek alpine yang dilempar dari HTML
                        alpineObj.openModal = false;
                    }
                });
            } else {
                alpineObj.openModal = false;
            }
        }
        // Hapus draf jika form berhasil disubmit
        essayForm.onsubmit = function() {
            localStorage.removeItem(ESSAY_STORAGE_KEY);
        };

        function confirmDeleteQuiz(url) {
            Swal.fire({
                title: 'Hapus Quiz?',
                text: "Hati-hati, Bro! Menghapus quiz ini juga akan menghapus seluruh data pertanyaan dan nilai siswa di dalamnya.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus Permanen!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = url;
                    form.method = 'POST';
                    form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }

        function confirmDeleteEssay(url) {
            Swal.fire({
                title: 'Hapus Tugas Essay?',
                text: "Seluruh jawaban siswa yang sudah masuk ke tugas ini juga akan terhapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus Tugas',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = url;
                    form.method = 'POST';
                    form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
@endsection
