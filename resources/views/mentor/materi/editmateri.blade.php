@extends('layouts.managecourse')
@section('manage-content')
    <div class="max-w-4xl">
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('mentor.kelolakursusmateri', $course->id) }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-slate-800">Edit Materi</h2>
                <p class="text-xs text-slate-500">Perbarui konten materi "{{ $material->name }}".</p>
            </div>
        </div>

        <form action="{{ route('mentor.updatemateri', [$course->id, $material->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-8 rounded-[2rem] border border-slate-200 bg-white p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-[11px] font-black uppercase tracking-widest text-slate-400">Judul Materi</label>
                    <input type="text" name="name" value="{{ old('name', $material->name) }}" required class="w-full rounded-xl border-slate-200 bg-slate-50">
                </div>
                <div>
                    <label class="mb-2 block text-[11px] font-black uppercase tracking-widest text-slate-400">ID Video YouTube</label>
                    <input type="text" name="link_video" value="{{ old('link_video', $material->link_video) }}" class="w-full rounded-xl border-slate-200 bg-slate-50">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-[11px] font-black uppercase tracking-widest text-slate-400">Konten Pembelajaran</label>
                <input id="content" type="hidden" name="content" value="{{ old('content', $material->content) }}">
                <trix-editor input="content" class="trix-content prose min-h-[400px] max-w-none rounded-xl border-slate-200 bg-slate-50 p-4"></trix-editor>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md">
                    <label class="mb-4 block text-[11px] font-black uppercase tracking-widest text-slate-400">Dokumen PDF</label>

                    @if ($material->pdf)
                        <div class="group relative mb-4 flex items-center justify-between overflow-hidden rounded-2xl border border-blue-100 bg-blue-50/30 p-4 transition-all hover:bg-blue-50" x-data="{ removed: false }" x-show="!removed">
                            <div class="flex items-center gap-3 truncate">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-500 text-white shadow-sm">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2" />
                                    </svg>
                                </div>
                                <div class="flex flex-col truncate">
                                    <span class="truncate text-xs font-bold text-slate-700">{{ basename($material->pdf) }}</span>
                                    <span class="text-[10px] font-bold uppercase text-slate-400">PDF Document</span>
                                </div>
                            </div>

                            <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-rose-100 bg-white px-3 py-1.5 text-[10px] font-bold text-rose-600 shadow-sm transition-colors hover:bg-rose-600 hover:text-white">
                                <input type="checkbox" name="remove_pdf" @change="removed = $el.checked" class="hidden">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Hapus
                            </label>
                        </div>
                    @endif

                    <div class="relative">
                        <input type="file" name="pdf" accept="application/pdf" class="peer absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0">
                        <div class="flex items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 py-4 transition-all peer-hover:border-blue-400 peer-hover:bg-blue-50/50">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-[11px] font-bold text-slate-500">Pilih File PDF Baru</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md" x-data="{ imagePreview: '{{ $material->image ? asset('storage/' . $material->image) : '' }}', showExisting: true }">
                    <label class="mb-4 block text-[11px] font-black uppercase tracking-widest text-slate-400">Gambar Ilustrasi</label>

                    @if ($material->image)
                        <div class="group relative mb-4 overflow-hidden rounded-2xl border border-slate-100 shadow-sm" x-show="showExisting">
                            <img :src="imagePreview" class="h-40 w-full object-cover transition-transform duration-500 group-hover:scale-105">

                            <div class="absolute inset-0 flex items-center justify-center bg-slate-900/40 opacity-0 backdrop-blur-[2px] transition-opacity group-hover:opacity-100">
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl bg-white px-4 py-2 text-xs font-black text-rose-600 shadow-xl transition-transform hover:scale-110 active:scale-95">
                                    <input type="checkbox" name="remove_image" @change="showExisting = !$el.checked" class="hidden">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    Hapus Gambar
                                </label>
                            </div>
                        </div>
                    @endif

                    <div class="relative">
                        <input type="file" name="image" accept="image/*" class="peer absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0" @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; showExisting = true }; reader.readAsDataURL(file); }">
                        <div class="flex items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 py-4 transition-all peer-hover:border-emerald-400 peer-hover:bg-emerald-50/50">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-[11px] font-bold text-slate-500">Ganti / Upload Gambar</span>
                        </div>
                    </div>
                </div>
            </div>

            <div x-data="{ needAttend: {{ $material->is_attendance_required ? 'true' : 'false' }} }" class="rounded-[2rem] border border-amber-100 bg-amber-50/30 p-8 shadow-sm">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-widest text-amber-900">Pengaturan Absensi</h4>
                        <p class="text-[10px] text-amber-700/60">Tentukan batas waktu absensi mahasiswa (WIB).</p>
                    </div>
                    <input type="checkbox" name="is_attendance_required" x-model="needAttend" class="h-6 w-6 rounded-lg border-amber-200 text-amber-600 focus:ring-amber-500">
                </div>

                <div x-show="needAttend" x-transition class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Waktu Mulai (WIB)</label>
                        <input type="datetime-local" name="attendance_start" value="{{ $material->attendance_start ? \Carbon\Carbon::parse($material->attendance_start)->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '' }}" class="w-full rounded-2xl border-amber-200 bg-white px-4 py-3 text-sm focus:border-amber-500 focus:ring-amber-500">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Waktu Berakhir (WIB)</label>
                        <input type="datetime-local" name="attendance_end" value="{{ $material->attendance_end ? \Carbon\Carbon::parse($material->attendance_end)->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i') : '' }}" class="w-full rounded-2xl border-amber-200 bg-white px-4 py-3 text-sm focus:border-amber-500 focus:ring-amber-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="rounded-2xl bg-amber-600 px-12 py-4 text-sm font-black text-white shadow-xl transition-all hover:bg-amber-700 active:scale-95">Simpan Perubahan</button>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener("trix-file-accept", function(event) {
            // Mencegah file apa pun untuk masuk ke editor
            event.preventDefault();
            alert("Fitur upload langsung di editor dimatikan. Gunakan input file di bawah untuk dokumen/gambar.");
        });
    </script>

    <style>
        /* Menghilangkan tombol upload (file tools) di toolbar agar lebih bersih */
        trix-toolbar .trix-button-group--file-tools {
            display: none !important;
        }
    </style>
@endsection
