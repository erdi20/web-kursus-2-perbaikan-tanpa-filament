@extends('layouts.managecourse')

@section('manage-content')
    {{-- Container Utama dengan Alpine.js untuk handle Modal Tambah & Edit --}}
    <div x-data="{
        openModal: false,
        openEdit: false,
        formEdit: {
            url: '',
            name: '',
            content: '',
            link_video: '',
            is_attendance: false,
            att_start: '',
            att_end: ''
        },
        init() {
            // Ini cara Alpine.js memantau perubahan variabel
            this.$watch('openEdit', value => {
                if (value) {
                    // Kasih jeda dikit biar modal & trix muncul dulu di DOM
                    setTimeout(() => {
                        if (this.$refs.trixEditor) {
                            this.$refs.trixEditor.editor.loadHTML(this.formEdit.content);
                        }
                    }, 200);
                }
            });
        }
    }" @open-edit-modal.window="openEdit = true; formEdit = $event.detail" class="space-y-6">

        {{-- Header Section --}}
        <div class="flex items-center justify-between border-b border-slate-100 pb-5">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Kurikulum & Materi</h2>
                <p class="text-xs text-slate-500">Susun materi pembelajaran secara terstruktur.</p>
            </div>
            <button @click="openModal = true" class="flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-black">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Tambah Materi
            </button>
        </div>

        {{-- List Materi --}}
        <div class="grid grid-cols-1 gap-4">
            @forelse($materials as $index => $material)
                <div class="group relative rounded-2xl border border-slate-200 bg-white p-4 transition-all hover:border-emerald-200 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        {{-- Sisi Kiri: Info Materi --}}
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-sm font-black text-slate-400 transition group-hover:bg-emerald-50 group-hover:text-emerald-600">
                                {{ sprintf('%02d', $index + 1) }}
                            </div>

                            {{-- Klik judul/area ini untuk Kelola Isi (Tugas/Absen) --}}
                            <a href="{{ route('mentor.materi.manage', $material->id) }}" class="block hover:opacity-80">
                                <h4 class="font-bold text-slate-700 group-hover:text-emerald-700">{{ $material->name }}</h4>
                                <div class="mt-1 flex flex-wrap items-center gap-2">
                                    @if ($material->link_video)
                                        <span class="flex items-center gap-1 rounded-md bg-red-50 px-2 py-0.5 text-[9px] font-black uppercase text-red-600">
                                            🎥 Video
                                        </span>
                                    @endif
                                    @if ($material->pdf)
                                        <span class="flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-[9px] font-black uppercase text-blue-600">
                                            📄 PDF
                                        </span>
                                    @endif
                                    @if ($material->is_attendance_required)
                                        <span class="flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-[9px] font-black uppercase text-amber-600">
                                            🕒 Wajib Absen
                                        </span>
                                    @endif
                                    <span class="ml-1 text-[10px] font-bold text-emerald-500 underline">Kelola Tugas & Absen →</span>
                                </div>
                            </a>
                        </div>

                        {{-- Sisi Kanan: Action Buttons --}}
                        <div class="flex items-center gap-1">
                            {{-- Tombol Edit --}}
                            <button
                                @click="$dispatch('open-edit-modal', {
                                        url: '{{ route('mentor.updatemateri', [$course->id, $material->id]) }}',
                                        name: '{{ addslashes($material->name) }}',
                                        content: `{{ addslashes($material->content) }}`,
                                        link_video: '{{ $material->link_video }}',
                                        is_attendance: {{ $material->is_attendance_required ? 'true' : 'false' }},
                                        att_start: '{{ $material->attendance_start }}',
                                        att_end: '{{ $material->attendance_end }}'
                                    })"
                                class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-slate-50 hover:text-blue-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" />
                                </svg>
                            </button>

                            {{-- Tombol Hapus --}}
                            {{-- Tombol Hapus Materi --}}
                            <button type="button" onclick="confirmDeleteMateri('{{ route('mentor.hapusmateri', [$course->id, $material->id]) }}')" class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center rounded-[2rem] border-2 border-dashed border-slate-100 py-20 text-center">
                    <div class="mb-4 rounded-full bg-slate-50 p-4">
                        <svg class="h-8 w-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-400">Belum ada materi yang dibuat.</p>
                    <button @click="openModal = true" class="mt-2 text-xs font-bold text-emerald-500 underline">Klik untuk Tambah Materi</button>
                </div>
            @endforelse
        </div>

        {{-- MODAL TAMBAH MATERI --}}
        <div x-show="openModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" x-cloak x-transition>
            <div @click.away="openModal = false" class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-[2.5rem] bg-white shadow-2xl">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-50 bg-white/80 px-8 py-5 backdrop-blur-md">
                    <h3 class="text-xl font-bold text-slate-800">Tambah Materi Pembelajaran</h3>
                    <button @click="openModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('mentor.tambahmateri', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-8">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 ml-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Judul Materi</label>
                            <input type="text" name="name" required placeholder="Contoh: Pengenalan Laravel" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="mb-1.5 ml-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Link YouTube (ID)</label>
                            <input type="text" name="link_video" placeholder="Contoh: dQw4w9WgXcQ" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 ml-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Isi Materi (Teks)</label>

                        {{-- Trix Editor --}}
                        <input id="content" type="hidden" name="content" value="{{ old('content') }}">
                        <trix-editor input="content" class="trix-content prose max-w-none focus:outline-none focus:ring-1 focus:ring-emerald-500"></trix-editor>

                        @error('content')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-400">File PDF</label>
                            <input type="file" name="pdf" accept="application/pdf" class="w-full text-xs text-slate-500">
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Gambar Ilustrasi</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500">
                        </div>
                    </div>

                    <div x-data="{ needAttend: false }" class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-emerald-900">Aktifkan Absensi</h4>
                                <p class="text-[10px] text-emerald-700">Wajibkan siswa absen untuk materi ini.</p>
                            </div>
                            <input type="checkbox" name="is_attendance_required" x-model="needAttend" class="rounded text-emerald-600 focus:ring-emerald-500">
                        </div>

                        <div x-show="needAttend" x-transition class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1 block text-[10px] font-black uppercase text-emerald-600">Mulai</label>
                                <input type="datetime-local" name="attendance_start" class="w-full rounded-lg border-emerald-200 text-xs">
                            </div>
                            <div>
                                <label class="mb-1 block text-[10px] font-black uppercase text-emerald-600">Berakhir</label>
                                <input type="datetime-local" name="attendance_end" class="w-full rounded-lg border-emerald-200 text-xs">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-50 pt-4">
                        <button type="button" @click="openModal = false" class="px-6 py-2 text-sm font-bold text-slate-400 hover:text-slate-600">Batal</button>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-10 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-100 transition-all hover:bg-emerald-700 active:scale-95">Simpan Materi</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL EDIT MATERI --}}
        <div x-show="openEdit" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" x-cloak x-transition>
            <div @click.away="openEdit = false" class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-[2.5rem] bg-white shadow-2xl">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-50 bg-white/80 px-8 py-5 backdrop-blur-md">
                    <h3 class="text-xl font-bold text-slate-800">Edit Materi</h3>
                    <button @click="openEdit = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" />
                        </svg>
                    </button>
                </div>

                <form :action="formEdit.url" method="POST" enctype="multipart/form-data" class="space-y-6 p-8">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 ml-1 block text-[11px] font-bold uppercase text-slate-400">Judul Materi</label>
                            <input type="text" name="name" x-model="formEdit.name" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
                        </div>
                        <div>
                            <label class="mb-1.5 ml-1 block text-[11px] font-bold uppercase text-slate-400">Link YouTube (ID)</label>
                            <input type="text" name="link_video" x-model="formEdit.link_video" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 ml-1 block text-[11px] font-bold uppercase text-slate-400">Isi Materi (Teks)</label>

                        {{-- Input Hidden untuk Trix --}}
                        <input id="edit_content" type="hidden" name="content" x-model="formEdit.content">

                        {{-- Trix Editor --}}
                        {{-- Kita kasih x-ref supaya bisa dimanipulasi lewat JavaScript --}}
                        <div wire:ignore>
                            <trix-editor input="edit_content" x-ref="trixEditor" @trix-change="formEdit.content = $event.target.value" class="trix-content prose max-w-none focus:outline-none focus:ring-1 focus:ring-emerald-500">
                            </trix-editor>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div class="rounded-2xl border border-slate-100 p-4">
                            <label class="mb-1 block text-[11px] font-bold uppercase text-slate-400">Update PDF</label>
                            <input type="file" name="pdf" class="w-full text-xs">
                        </div>
                        <div class="rounded-2xl border border-slate-100 p-4">
                            <label class="mb-1 block text-[11px] font-bold uppercase text-slate-400">Update Gambar</label>
                            <input type="file" name="image" class="w-full text-xs">
                        </div>
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-amber-50/50 p-5">
                        <div class="mb-4 flex items-center justify-between">
                            <h4 class="text-sm font-bold text-amber-900">Pengaturan Absensi</h4>
                            <input type="checkbox" name="is_attendance_required" x-model="formEdit.is_attendance" class="rounded text-amber-600 focus:ring-amber-500">
                        </div>
                        <div x-show="formEdit.is_attendance" x-transition class="grid grid-cols-2 gap-4">
                            <input type="datetime-local" name="attendance_start" x-model="formEdit.att_start" class="rounded-lg border-amber-200 text-xs">
                            <input type="datetime-local" name="attendance_end" x-model="formEdit.att_end" class="rounded-lg border-amber-200 text-xs">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-50 pt-4">
                        <button type="button" @click="openEdit = false" class="px-6 py-2 text-sm font-bold text-slate-400">Batal</button>
                        <button type="submit" class="rounded-xl bg-slate-900 px-10 py-3 text-sm font-bold text-white shadow-lg transition-all hover:bg-black active:scale-95">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script>
        function confirmDeleteMateri(url) {
            Swal.fire({
                title: 'Hapus Materi?',
                text: "Materi ini akan dihapus permanen, pastikan kamu sudah yakin ya!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Red 500
                cancelButtonColor: '#94a3b8', // Slate 400
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-3xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form bayangan untuk kirim DELETE request
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
