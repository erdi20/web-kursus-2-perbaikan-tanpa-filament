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
                <h2 class="text-xl font-bold text-slate-800">Tambah Materi Baru</h2>
                <p class="text-xs text-slate-500">Buat materi baru untuk kursus ini.</p>
            </div>
        </div>

        <form action="{{ route('mentor.tambahmateri', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8 rounded-[2rem] border border-slate-200 bg-white p-8">
            @csrf
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-[11px] font-black uppercase tracking-widest text-slate-400">Judul Materi</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:ring-emerald-500" placeholder="Contoh: Dasar-dasar Routing">
                </div>
                <div>
                    <label class="mb-2 block text-[11px] font-black uppercase tracking-widest text-slate-400">ID Video YouTube</label>
                    <input type="text" name="link_video" value="{{ old('link_video') }}" class="w-full rounded-xl border-slate-200 bg-slate-50 focus:ring-emerald-500" placeholder="Contoh: dQw4w9WgXcQ">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-[11px] font-black uppercase tracking-widest text-slate-400">Konten Pembelajaran</label>
                <input id="content" type="hidden" name="content" value="{{ old('content') }}">
                <trix-editor input="content" class="trix-content prose min-h-[400px] max-w-none rounded-xl border-slate-200 bg-slate-50 p-4 focus:ring-emerald-500"></trix-editor>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md" x-data="{ fileName: '' }">
                    <label class="mb-4 block text-[11px] font-black uppercase tracking-widest text-slate-400">File Dokumen PDF</label>

                    <div x-show="fileName" x-transition class="mb-4 flex items-center gap-3 rounded-2xl border border-blue-100 bg-blue-50/50 p-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-500 text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2" />
                            </svg>
                        </div>
                        <div class="flex flex-col truncate">
                            <span class="truncate text-xs font-bold text-slate-700" x-text="fileName"></span>
                            <span class="text-[9px] font-bold uppercase text-blue-500">Siap diunggah</span>
                        </div>
                    </div>

                    <div class="relative">
                        <input type="file" name="pdf" accept="application/pdf" class="peer absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0" @change="fileName = $event.target.files[0].name">
                        <div class="flex items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 py-4 transition-all peer-hover:border-blue-400 peer-hover:bg-blue-50/50">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-[11px] font-bold text-slate-500">Pilih Dokumen PDF</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm transition-all hover:shadow-md" x-data="{ imagePreview: '' }">
                    <label class="mb-4 block text-[11px] font-black uppercase tracking-widest text-slate-400">Gambar Ilustrasi</label>

                    <div x-show="imagePreview" x-transition class="group relative mb-4 overflow-hidden rounded-2xl border border-slate-100 shadow-sm">
                        <img :src="imagePreview" class="h-40 w-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-slate-900/20 opacity-0 backdrop-blur-[1px] transition-opacity group-hover:opacity-100">
                            <span class="rounded-lg bg-white px-3 py-1 text-[10px] font-black text-slate-600">Preview Baru</span>
                        </div>
                    </div>

                    <div class="relative">
                        <input type="file" name="image" accept="image/*" class="peer absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0" @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result }; reader.readAsDataURL(file); }">
                        <div class="flex items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 py-4 transition-all peer-hover:border-emerald-400 peer-hover:bg-emerald-50/50">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="text-[11px] font-bold text-slate-500">Pilih Gambar Materi</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Absensi Section (Create) --}}
            <div x-data="{ needAttend: false }" class="rounded-[2rem] border border-emerald-100 bg-emerald-50/30 p-8 shadow-sm transition-all hover:bg-emerald-50/50">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-widest text-emerald-900">Aktifkan Absensi Otomatis</h4>
                        <p class="text-[10px] font-medium italic text-emerald-700/60">Absensi akan tercatat otomatis saat mahasiswa mengakses materi.</p>
                    </div>
                    <input type="checkbox" name="is_attendance_required" x-model="needAttend" class="h-6 w-6 rounded-lg border-emerald-200 text-emerald-600 focus:ring-emerald-500">
                </div>

                <div x-show="needAttend" x-transition class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-wider text-emerald-700">Waktu Mulai (WIB)</label>
                        <input type="datetime-local" name="attendance_start" class="w-full rounded-2xl border-emerald-200 bg-white px-4 py-3 text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-wider text-emerald-700">Waktu Berakhir (WIB)</label>
                        <input type="datetime-local" name="attendance_end" class="w-full rounded-2xl border-emerald-200 bg-white px-4 py-3 text-sm font-medium focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="rounded-2xl bg-slate-900 px-12 py-4 text-sm font-black text-white shadow-xl transition-all hover:bg-black active:scale-95">Terbitkan Materi</button>
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
