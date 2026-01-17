<x-mentor-layout>
    {{-- Container utama dengan padding dan max-width agar tidak mepet --}}
    <div class="mx-auto max-w-[1400px] px-4 py-10 sm:px-6 lg:px-8" x-data="{
        essay: 40,
        quiz: 40,
        attendance: 20,
        get total() { return parseInt(this.essay) + parseInt(this.quiz) + parseInt(this.attendance) }
    }">

        {{-- Header Bar --}}
        <div class="mb-10 flex flex-col justify-between gap-4 border-b border-slate-100 pb-8 md:flex-row md:items-center">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-800">Buat Kursus Baru</h2>
                <p class="mt-2 text-base text-slate-500">Isi detail di bawah untuk menyusun kurikulum kursus Anda.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('mentor.kursus') }}" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-bold text-slate-600 transition-all hover:bg-slate-50">Batal</a>
                <button form="courseForm" type="submit" class="rounded-xl bg-emerald-500 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-200 transition-all hover:bg-emerald-600 active:scale-95">
                    Terbitkan Kursus
                </button>
            </div>
        </div>

        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="mb-8 rounded-2xl border-l-4 border-red-500 bg-red-50 p-6 text-red-700 shadow-sm">
                <div class="mb-2 flex items-center gap-3">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-bold">Ada kesalahan input:</p>
                </div>
                <ul class="list-inside list-disc space-y-1 text-sm opacity-90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="courseForm" action="{{ route('mentor.tambahkursus') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-10 lg:grid-cols-3">
            @csrf

            {{-- Bagian Kiri: Konten Utama --}}
            <div class="space-y-8 lg:col-span-2">

                {{-- Detail Informasi --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition-all hover:shadow-md">
                    <h3 class="mb-8 flex items-center gap-3 text-xl font-bold text-slate-800">
                        <span class="rounded-xl bg-emerald-50 p-2 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </span>
                        Informasi Utama
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label class="mb-2.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nama Kursus</label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Misal: Fullstack Laravel 12 Mastery" class="w-full rounded-2xl border-slate-200 py-3.5 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                        </div>

                        <div>
                            <label class="mb-2.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Deskripsi Singkat</label>
                            <div class="rounded-2xl border border-slate-200 p-1.5 transition-all focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/10 [&_trix-toolbar]:mb-2 [&_trix-toolbar]:border-slate-100">
                                <input id="short_description" type="hidden" name="short_description" value="{{ old('short_description') }}">
                                <trix-editor input="short_description" placeholder="Ringkasan kursus yang menarik..." class="trix-content prose min-h-[120px] max-w-none border-none outline-none focus:outline-none"></trix-editor>
                            </div>
                            @error('short_description')
                                <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Deskripsi Lengkap</label>
                            <div class="rounded-2xl border border-slate-200 p-1.5 transition-all focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/10 [&_trix-toolbar]:mb-2 [&_trix-toolbar]:border-slate-100">
                                <input id="description" type="hidden" name="description" value="{{ old('description') }}">
                                <trix-editor input="description" placeholder="Jelaskan detail kursus secara mendalam..." class="trix-content prose min-h-[300px] max-w-none border-none outline-none focus:outline-none"></trix-editor>
                            </div>
                            @error('description')
                                <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Penilaian & Kelulusan --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition-all hover:shadow-md">
                    <div class="mb-10 flex items-center justify-between border-b border-slate-50 pb-6">
                        <h3 class="flex items-center gap-3 text-xl font-bold text-slate-800">
                            <span class="rounded-xl bg-blue-50 p-2 text-blue-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            Bobot Nilai & Standar Lulus
                        </h3>
                        <div class="rounded-full px-4 py-1.5 text-sm font-bold transition-colors" :class="total == 100 ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'">
                            Total Bobot: <span x-text="total"></span>%
                        </div>
                    </div>

                    <div class="mb-10 grid grid-cols-1 gap-8 md:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Bobot Essay (%)</label>
                            <input type="number" name="essay_weight" x-model="essay" class="w-full rounded-xl border-slate-200 py-3 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                        </div>
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Bobot Quiz (%)</label>
                            <input type="number" name="quiz_weight" x-model="quiz" class="w-full rounded-xl border-slate-200 py-3 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                        </div>
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Bobot Absen (%)</label>
                            <input type="number" name="attendance_weight" x-model="attendance" class="w-full rounded-xl border-slate-200 py-3 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 rounded-3xl border border-slate-100 bg-slate-50/50 p-8 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-600">Minimal Kehadiran (%)</label>
                            <input type="number" name="min_attendance_percentage" value="{{ old('min_attendance_percentage', 80) }}" class="w-full rounded-xl border-slate-200 py-3 transition-all focus:ring-emerald-500/10">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-600">Skor Kelulusan (Min)</label>
                            <input type="number" name="min_final_score" value="{{ old('min_final_score', 70) }}" class="w-full rounded-xl border-slate-200 py-3 transition-all focus:ring-emerald-500/10">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Panel Pengaturan --}}
            <div class="space-y-8">
                {{-- Media Card --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition-all hover:shadow-md">
                    <h3 class="mb-6 text-base font-bold text-slate-800">Thumbnail Kursus</h3>
                    <div x-data="{ photoPreview: null }">
                        <input type="file" name="thumbnail" class="hidden" x-ref="photo" x-on:change="
                                const reader = new FileReader();
                                reader.onload = (e) => { photoPreview = e.target.result; };
                                reader.readAsDataURL($refs.photo.files[0]);
                            ">
                        <div class="group relative cursor-pointer" @click="$refs.photo.click()">
                            <div x-show="!photoPreview" class="flex h-56 flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 transition-all hover:border-emerald-200 hover:bg-emerald-50/50">
                                <svg class="mb-3 h-10 w-10 text-slate-300 transition-colors group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-widest transition-colors group-hover:text-emerald-600">Pilih Cover</span>
                            </div>
                            <div x-show="photoPreview" class="h-56 overflow-hidden rounded-2xl bg-slate-50 shadow-inner">
                                <img :src="photoPreview" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 flex items-center justify-center bg-slate-900/40 opacity-0 backdrop-blur-[2px] transition-all group-hover:opacity-100">
                                    <span class="rounded-full border border-white/30 bg-white/20 px-4 py-2 text-xs font-bold uppercase tracking-widest text-white">Ubah Gambar</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pricing & Discount --}}
                <div class="space-y-6 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition-all hover:shadow-md">
                    <h3 class="text-base font-bold text-slate-800">Harga & Diskon</h3>
                    <div>
                        <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-slate-400">Harga Normal (IDR)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-sm font-bold text-slate-400">Rp</span>
                            <input type="number" name="price" value="{{ old('price', 0) }}" class="w-full rounded-2xl border-slate-200 py-3.5 pl-12 font-bold text-slate-700 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                        </div>
                    </div>
                    <div class="space-y-5 rounded-2xl border border-amber-100 bg-amber-50/50 p-6">
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-amber-600">Harga Diskon (Opsional)</label>
                            <input type="number" name="discount_price" value="{{ old('discount_price') }}" class="w-full rounded-xl border-amber-200 py-3 text-sm focus:ring-amber-500/10">
                        </div>
                        <div>
                            <label class="mb-2 block text-[11px] font-bold uppercase tracking-widest text-amber-600">Berlaku Hingga</label>
                            <input type="datetime-local" name="discount_end_date" class="w-full rounded-xl border-amber-200 py-3 text-xs focus:ring-amber-500/10">
                        </div>
                    </div>
                </div>

                {{-- Schedule & Status --}}
                <div class="space-y-6 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition-all hover:shadow-md">
                    <h3 class="text-base font-bold text-slate-800">Status Publikasi</h3>
                    <div>
                        <select name="status" class="w-full rounded-2xl border-slate-200 py-3.5 text-sm font-bold text-slate-700 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
                            <option value="draft">Sembunyikan (Draft)</option>
                            <option value="open">Buka Pendaftaran (Open)</option>
                            <option value="closed">Pendaftaran Tutup (Closed)</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-mentor-layout>
