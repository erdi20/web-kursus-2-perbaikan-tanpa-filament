@extends('layouts.managecourse')

@section('manage-content')
    {{-- Container Utama dengan Alpine.js untuk handle Modal Tambah & Edit --}}
    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex items-center justify-between border-b border-slate-100 pb-5">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Kurikulum & Materi</h2>
                <p class="text-xs text-slate-500">Susun materi pembelajaran secara terstruktur.</p>
            </div>
            <a href="{{ route('mentor.materi.create', $course->id) }}" class="flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-black">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Tambah Materi
            </a>
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
                            <a href="{{ route('mentor.materi.edit', [$course->id, $material->id]) }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-50 hover:text-blue-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" />
                                </svg>
                            </a>

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
