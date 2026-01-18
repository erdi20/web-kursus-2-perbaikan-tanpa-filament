<x-admin-layout>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <div class="min-h-screen bg-[#F8FAFC] py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-10 flex items-end justify-between border-b border-slate-200 pb-6">
                <div>
                    <h2 class="text-3xl font-black uppercase tracking-tight text-slate-800">
                        {{ isset($faq) ? 'Edit FAQ' : 'Buat FAQ Baru' }}
                    </h2>
                </div>
                <a href="{{ route('admin.faq') }}" class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-xs font-black uppercase tracking-widest text-slate-500 hover:bg-slate-50">Batal</a>
            </div>

            <form action="{{ isset($faq) ? route('admin.faq.update', $faq) : route('admin.faq.store') }}" method="POST">
                @csrf
                @if (isset($faq))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    {{-- KOLOM KIRI (Konten FAQ) --}}
                    <div class="space-y-6 lg:col-span-2">
                        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                            <h3 class="mb-6 text-sm font-black uppercase tracking-widest text-[#20C896]">Konten Utama</h3>

                            <div class="space-y-6">
                                <div>
                                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Pertanyaan</label>
                                    <input type="text" name="question" value="{{ $faq->question ?? old('question') }}" placeholder="Contoh: Bagaimana cara mendaftar?" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 p-4 font-bold text-slate-700 focus:border-[#20C896] focus:ring-[#20C896]">
                                </div>

                                <div>
                                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Jawaban Lengkap</label>
                                    <input id="answer" type="hidden" name="answer" value="{{ $faq->answer ?? old('answer') }}">
                                    <trix-editor input="answer" class="prose max-w-none rounded-2xl border-slate-200 bg-slate-50/50 p-4 focus:border-[#20C896]"></trix-editor>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN (Pengaturan) --}}
                    <div class="space-y-6">
                        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                            <h3 class="mb-6 text-sm font-black uppercase tracking-widest text-[#20C896]">Pengaturan</h3>

                            <div class="space-y-8">
                                {{-- Status Toggle --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black uppercase tracking-widest text-slate-500">Status Aktif</span>
                                    <label class="relative inline-flex cursor-pointer items-center">
                                        <input type="checkbox" name="is_active" class="peer sr-only" {{ (isset($faq) && $faq->is_active) || !isset($faq) ? 'checked' : '' }}>
                                        <div class="w-13 peer h-7 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-6 after:w-6 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-[#20C896] peer-checked:after:translate-x-6"></div>
                                    </label>
                                </div>

                                {{-- Order --}}
                                <div>
                                    <label class="mb-2 block text-[10px] font-black uppercase tracking-widest text-slate-400">Urutan Tampil</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400">No.</span>
                                        <input type="number" name="order" value="{{ $faq->order ?? ($nextOrder ?? 1) }}" class="w-full rounded-2xl border-slate-200 bg-slate-50/50 py-4 pl-12 pr-4 font-black text-[#20C896] focus:border-[#20C896]">
                                    </div>
                                </div>

                                <button type="submit" class="w-full rounded-2xl bg-[#20C896] py-4 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-emerald-100 transition hover:bg-emerald-600">
                                    Simpan FAQ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        trix-toolbar .trix-button-group--file-tools {
            display: none !important;
        }

        trix-editor {
            min-height: 250px !important;
            border: 1px solid #e2e8f0 !important;
        }
    </style>
</x-admin-layout>
