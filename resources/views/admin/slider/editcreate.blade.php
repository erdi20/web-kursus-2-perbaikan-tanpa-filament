<x-admin-layout>
    <div class="bg-[#F8FAFC] py-10"> {{-- Tambah bg soft biar card putih lebih kontras --}}
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-10 flex items-end justify-between border-b border-slate-200 pb-6">
                <div>
                    <nav class="mb-2 flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <li><a href="{{ route('admin.slider') }}" class="hover:text-[#20C896]">Slider Banner</a></li>
                            <li><svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                                </svg></li>
                            <li class="text-slate-600">{{ isset($slider) ? 'Edit' : 'Tambah Baru' }}</li>
                        </ol>
                    </nav>
                    <h2 class="text-3xl font-black uppercase tracking-tight text-slate-800">
                        {{ isset($slider) ? 'Modifikasi Slider' : 'Tambah Slider Utama' }}
                    </h2>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.slider') }}" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-500 transition hover:bg-slate-50">
                        Batal
                    </a>
                </div>
            </div>

            <form action="{{ isset($slider) ? route('admin.slider.update', $slider) : route('admin.slider.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($slider))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

                    {{-- KOLOM KIRI: Media & Info (4 Cols) --}}
                    <div class="space-y-6 lg:col-span-4">
                        {{-- Image Section --}}
                        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <label class="mb-4 block text-[10px] font-black uppercase tracking-widest text-slate-400">Visual Banner</label>

                            <div class="group relative flex aspect-[16/9] items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 transition hover:border-[#20C896]/50">
                                @if (isset($slider) && $slider->image)
                                    <img src="{{ asset('storage/' . $slider->image) }}" id="preview-img" class="h-full w-full object-cover">
                                @else
                                    <img id="preview-img" class="hidden h-full w-full object-cover">
                                    <div id="placeholder-icon" class="p-6 text-center">
                                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-white shadow-sm">
                                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-[10px] font-bold uppercase text-slate-400">Upload Image</p>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-4">
                                <input type="file" name="image" id="image-input" class="hidden" accept="image/*">
                                <label for="image-input" class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#20C896]/10 px-4 py-3 text-xs font-black uppercase tracking-widest text-[#20C896] transition hover:bg-[#20C896] hover:text-white">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Pilih Gambar
                                </label>
                            </div>
                        </div>

                        {{-- Help Card --}}
                        <div class="rounded-3xl bg-slate-900 p-6 text-white shadow-xl shadow-slate-200">
                            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-amber-400">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h4 class="text-sm font-black uppercase tracking-tight">Standard Banner</h4>
                            <p class="mt-2 text-xs font-medium leading-relaxed text-slate-400">Pastikan teks pada gambar tidak terlalu ramai karena sistem akan menumpuk "Judul Banner" secara otomatis di atas background ini.</p>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: Content Form (8 Cols) --}}
                    <div class="space-y-6 lg:col-span-8">
                        <div class="rounded-3xl border border-slate-200 bg-white p-10 shadow-sm">
                            <div class="grid grid-cols-1 gap-8">

                                {{-- Judul --}}
                                <div>
                                    <label class="mb-3 block text-[10px] font-black uppercase tracking-widest text-slate-400">Judul Utama (Heading)</label>
                                    <input type="text" name="title" value="{{ $slider->title ?? old('title') }}" placeholder="Masukkan judul promo atau informasi..." class="w-full rounded-2xl border-slate-200 bg-slate-50/30 p-5 text-lg font-bold text-slate-700 transition focus:border-[#20C896] focus:ring-[#20C896]">
                                </div>

                                {{-- Deskripsi --}}
                                <div>
                                    <label class="mb-3 block text-[10px] font-black uppercase tracking-widest text-slate-400">Sub-Deskripsi (Optional)</label>
                                    <textarea name="description" rows="4" placeholder="Jelaskan lebih detail tentang banner ini..." class="w-full rounded-2xl border-slate-200 bg-slate-50/30 p-5 text-base font-medium text-slate-600 transition focus:border-[#20C896] focus:ring-[#20C896]">{{ $slider->description ?? old('description') }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                                    {{-- Order --}}
                                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-6">
                                        <label class="mb-3 block text-[10px] font-black uppercase tracking-widest text-slate-400">Urutan Tampil</label>
                                        <div class="flex items-center gap-4">
                                            <input type="number" name="order" value="{{ $slider->order ?? ($nextOrder ?? 1) }}" class="w-32 rounded-xl border-slate-200 p-4 text-center text-xl font-black text-[#20C896] focus:border-[#20C896] focus:ring-[#20C896]">
                                            <p class="text-[10px] font-medium uppercase leading-tight text-slate-400">Urutan terkecil akan muncul pertama kali.</p>
                                        </div>
                                    </div>

                                    {{-- Status --}}
                                    <div class="flex flex-col justify-center rounded-2xl border border-slate-100 bg-slate-50/50 p-6">
                                        <label class="mb-3 block text-[10px] font-black uppercase tracking-widest text-slate-400">Visibilitas Publik</label>
                                        <label class="relative inline-flex cursor-pointer items-center">
                                            <input type="checkbox" name="is_active" class="peer sr-only" {{ (isset($slider) && $slider->is_active) || !isset($slider) ? 'checked' : '' }}>
                                            <div class="peer h-8 w-16 rounded-full bg-slate-200 after:absolute after:left-[4px] after:top-[4px] after:h-6 after:w-8 after:rounded-lg after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#20C896] peer-checked:after:translate-x-full peer-focus:outline-none"></div>
                                            <span class="ml-4 text-xs font-black uppercase tracking-widest text-slate-600">Aktif</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Floating Submit --}}
                        <div class="flex items-center justify-between rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            <p class="px-4 text-xs font-medium italic text-slate-400">Perubahan akan langsung diterapkan setelah disimpan.</p>
                            <button type="submit" class="flex items-center gap-3 rounded-2xl bg-[#20C896] px-10 py-5 text-xs font-black uppercase tracking-widest text-white shadow-xl shadow-emerald-200 transition hover:-translate-y-1 hover:bg-emerald-600 active:scale-95">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ isset($slider) ? 'Perbarui Slider' : 'Simpan & Publikasikan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('image-input').onchange = evt => {
            const [file] = document.getElementById('image-input').files
            if (file) {
                document.getElementById('preview-img').src = URL.createObjectURL(file)
                document.getElementById('preview-img').classList.remove('hidden')
                document.getElementById('placeholder-icon').classList.add('hidden')
            }
        }
    </script>
</x-admin-layout>
