@extends('layouts.managecourse')

@section('manage-content')
    {{-- Root x-data di paling atas untuk mengatur state modal --}}
    <div x-data="{
        openGradeModal: false,
        gradeData: { url: '', name: '', answer: '', score: '', feedback: '', file: '' }
    }" class="space-y-6">

        {{-- Breadcrumbs & Header --}}
        <div class="border-b border-slate-200 pb-6">
            <nav class="mb-2 flex text-[10px] font-bold uppercase tracking-wider text-slate-400">
                <a href="{{ route('mentor.materi.manage', $essay->material_id) }}" class="hover:text-emerald-500">Kembali ke Materi</a>
                <span class="mx-2">/</span>
                <span>Jawaban Siswa</span>
            </nav>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-950">{{ $essay->title }}</h2>
                    <p class="text-sm text-slate-500">Total {{ $submissions->count() }} siswa telah mengumpulkan jawaban.</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold uppercase text-slate-400">Deadline</p>
                    <p class="text-sm font-bold text-slate-900">{{ \Carbon\Carbon::parse($essay->due_date)->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Tabel List Jawaban --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Nama Siswa</th>
                        <th class="px-6 py-4">Waktu Kumpul</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Nilai</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submissions as $sub)
                        <tr class="transition hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700">
                                        {{ substr($sub->student->name, 0, 2) }}
                                    </div>
                                    <span class="font-semibold text-slate-700">{{ $sub->student->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-slate-600">{{ \Carbon\Carbon::parse($sub->submitted_at)->format('d M, H:i') }}</p>
                                @if ($sub->isLate())
                                    <span class="text-[10px] font-bold uppercase italic tracking-tighter text-red-500">Terlambat</span>
                                @else
                                    <span class="text-[10px] font-bold uppercase italic tracking-tighter text-emerald-500">Tepat Waktu</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($sub->is_graded)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-600">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                                        </svg>
                                        Sudah Dinilai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-600">
                                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        Menunggu Nilai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-black text-slate-700">
                                {{ $sub->is_graded ? $sub->score : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    @click="
                                    openGradeModal = true;
                                    gradeData = {
                                        url: '{{ route('mentor.materi.essay.grade', $sub->id) }}',
                                        name: '{{ addslashes($sub->student->name) }}',
                                        answer: `{{ addslashes($sub->answer_text) }}`,
                                        score: '{{ $sub->score ?? '' }}',
                                        feedback: `{{ addslashes($sub->feedback ?? '') }}`,
                                        file: '{{ $sub->file_path ? asset('storage/' . $sub->file_path) : '' }}'
                                    }"
                                    class="rounded-lg bg-slate-900 px-3 py-1.5 text-[11px] font-bold text-white shadow-sm transition hover:bg-black active:scale-95">
                                    Beri Nilai / Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center italic text-slate-400">
                                Belum ada siswa yang mengumpulkan tugas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL PENILAIAN (Hanya dirender 1 kali di luar table) --}}
        <div x-show="openGradeModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" x-cloak x-transition>

            <div @click.away="openGradeModal = false" class="relative w-full max-w-2xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Penilaian Tugas</h3>
                        <p class="text-xs text-slate-500">Siswa: <span x-text="gradeData.name" class="font-bold text-emerald-600"></span></p>
                    </div>
                    <button @click="openGradeModal = false" class="text-slate-400 transition hover:text-slate-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" />
                        </svg>
                    </button>
                </div>

                <form :action="gradeData.url" method="POST" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- Sisi Kiri: Jawaban Siswa --}}
                        <div class="space-y-4">
                            <div>
                                <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-slate-400">Jawaban Teks</label>
                                <div class="h-48 overflow-y-auto rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm leading-relaxed text-slate-700" x-text="gradeData.answer || 'Tidak ada jawaban teks.'"></div>
                            </div>

                            <template x-if="gradeData.file">
                                <div>
                                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-slate-400">Lampiran File</label>
                                    <a :href="gradeData.file" target="_blank" class="flex items-center gap-2 rounded-lg border border-emerald-100 bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" />
                                        </svg>
                                        Lihat Lampiran Siswa
                                    </a>
                                </div>
                            </template>
                        </div>

                        {{-- Sisi Kanan: Form Nilai --}}
                        <div class="space-y-4">
                            <div>
                                <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-slate-400">Skor (0-100)</label>
                                <input type="number" name="score" x-model="gradeData.score" min="0" max="100" required class="w-full rounded-xl border-slate-200 bg-white text-lg font-black text-slate-900 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div>
                                <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-slate-400">Feedback / Catatan</label>
                                <textarea name="feedback" x-model="gradeData.feedback" rows="5" class="w-full rounded-xl border-slate-200 bg-white text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Ketik catatan di sini..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-8 flex justify-end gap-3 border-t border-slate-50 pt-6">
                        <button type="button" @click="openGradeModal = false" class="px-6 py-2 text-sm font-bold text-slate-400 transition hover:text-slate-600">Batal</button>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-8 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-100 transition hover:bg-emerald-700">
                            Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tambahkan CSS ini jika x-cloak belum ada di file layout utama --}}
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection
