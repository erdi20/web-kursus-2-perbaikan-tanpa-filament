@extends('layouts.managecourse')

@section('manage-content')
    <div class="space-y-6">

        {{-- Breadcrumbs & Header --}}
        <div class="border-b border-slate-200 pb-6">
            <nav class="mb-2 flex text-[10px] font-bold uppercase tracking-wider text-slate-400">
                <a href="{{ route('mentor.materi.manage', $quiz->material_id) }}" class="hover:text-emerald-500">Kembali ke Materi</a>
                <span class="mx-2">/</span>
                <span>Hasil Quiz</span>
            </nav>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-950">{{ $quiz->title }}</h2>
                    <p class="text-sm text-slate-500">Materi: <span class="font-bold text-slate-700">{{ $quiz->material->name ?? $quiz->material->title }}</span></p>
                </div>
                <div class="flex gap-6 text-right">
                    <div>
                        <p class="text-[10px] font-bold uppercase text-slate-400">Total Peserta</p>
                        <p class="text-right text-sm font-bold text-slate-900">{{ $submissions->count() }} Siswa</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase text-slate-400">Rata-rata</p>
                        <p class="text-right text-sm font-bold text-blue-600">{{ number_format($submissions->avg('score') ?? 0, 1) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel List Jawaban --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Nama Mahasiswa</th>
                        <th class="px-6 py-4 text-center">Skor</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4">Waktu Selesai</th>
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
                                    <div>
                                        <p class="font-semibold leading-none text-slate-700">{{ $sub->student->name }}</p>
                                        <p class="mt-1 text-[10px] text-slate-400">{{ $sub->student->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $minScore = $quiz->material->classMaterials->first()->courseClass->min_final_score ?? 70;
                                @endphp
                                <span class="{{ $sub->score >= $minScore ? 'text-emerald-600' : 'text-red-500' }} text-base font-black">
                                    {{ round($sub->score) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center gap-1 rounded-full border border-blue-100 bg-blue-50 px-2.5 py-1 text-[10px] font-bold uppercase text-blue-600">
                                    Ternilai
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-600">{{ $sub->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-400">{{ $sub->created_at->format('H:i') }} WIB</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                {{-- Karena detail jawaban dilewati, kita kasih status saja --}}
                                <span class="text-[10px] font-bold uppercase italic text-slate-400">Otomatis</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center italic text-slate-400">
                                Belum ada mahasiswa yang mengerjakan quiz ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Info SKM Box --}}
        <div class="rounded-xl border border-blue-100 bg-blue-50/30 p-4">
            <div class="flex gap-3">
                <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs leading-relaxed text-blue-700">
                    Siswa dinyatakan <strong>Lulus</strong> jika mencapai skor minimal <strong>{{ $minScore }}</strong>. Nilai dikalkulasi secara otomatis oleh sistem saat mahasiswa menyelesaikan pengerjaan.
                </p>
            </div>
        </div>

    </div>
@endsection
