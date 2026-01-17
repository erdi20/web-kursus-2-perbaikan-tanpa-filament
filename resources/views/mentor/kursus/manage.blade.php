@extends('layouts.managecourse')

@section('manage-content')
    <div class="space-y-6">
        {{-- Statistik Utama --}}
        <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
            {{-- Siswa Aktif --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Siswa</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <h4 class="text-2xl font-black text-slate-800">{{ number_format($stats['total_students']) }}</h4>
                </div>
            </div>

            {{-- Rata-rata Progres --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Rata-rata Progres</p>
                <div class="mt-2 flex items-center gap-3">
                    <h4 class="text-2xl font-black text-slate-800">{{ $stats['avg_progress'] }}%</h4>
                    <div class="h-1.5 w-full rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-blue-500" style="width: {{ $stats['avg_progress'] }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Lulus --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Siswa Lulus</p>
                <h4 class="mt-2 text-2xl font-black text-emerald-600">{{ $stats['completed_students'] }}</h4>
            </div>

            {{-- Konten --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Konten Kursus</p>
                <h4 class="mt-2 text-2xl font-black text-slate-800">{{ $stats['total_materials'] }} <span class="text-xs font-medium text-slate-400">Materi</span></h4>
            </div>
        </div>

        {{-- Welcome Card --}}
        <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-8 text-white shadow-xl shadow-slate-200">
            <div class="relative z-10 md:w-2/3">
                <h3 class="mb-2 text-xl font-bold italic text-emerald-400">Hello, Mentor! 👨‍🏫</h3>
                <h2 class="mb-4 text-3xl font-black leading-tight">Kursus "{{ $course->name }}" <br>siap untuk dikelola.</h2>

                <div class="flex flex-wrap gap-3 text-sm">
                    {{-- Info Terakhir Update --}}
                    <div class="rounded-xl bg-white/10 px-4 py-2 backdrop-blur-md">
                        <span class="block text-[10px] uppercase opacity-60">Terakhir Update</span>
                        <span class="font-bold">{{ $course->updated_at->diffForHumans() }}</span>
                    </div>

                    {{-- Info Status Kursus --}}
                    <div class="rounded-xl bg-white/10 px-4 py-2 backdrop-blur-md">
                        <span class="block text-[10px] uppercase opacity-60">Status Kursus</span>

                        @php
                            // Menentukan warna teks berdasarkan status untuk di dalam banner gelap
                            $statusBannerColors = [
                                'open' => 'text-emerald-400',
                                'draft' => 'text-amber-400',
                                'closed' => 'text-rose-400',
                                'archived' => 'text-slate-400',
                            ];
                            $statusColor = $statusBannerColors[$course->status] ?? 'text-emerald-400';
                        @endphp

                        <span class="{{ $statusColor }} font-bold uppercase">
                            {{ $course->status }}
                        </span>
                    </div>
                </div>
            </div>
            {{-- Background Icon Dekorasi --}}
            <svg class="absolute -right-10 -top-10 h-64 w-64 opacity-10" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L1 21h22L12 2zm0 3.45l8.27 14.3H3.73L12 5.45z" />
            </svg>
        </div>

        {{-- Row Detail --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- List Siswa Terbaru (Dari Model ClassEnrollment) --}}
            <div class="col-span-2 rounded-3xl border border-slate-200 bg-white p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h4 class="font-black uppercase tracking-tight text-slate-800">Siswa yang Baru Bergabung</h4>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold text-slate-500">5 Terbaru</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($course->classes->flatMap->enrollments->sortByDesc('enrolled_at')->take(5) as $enroll)
                        <div class="flex items-center justify-between border-b border-slate-50 py-4 last:border-0">
                            <div class="flex items-center gap-3">
                                {{-- Logika Avatar Siswa (Manage Kursus) --}}
                                <div class="flex-shrink-0">
                                    @if ($enroll->user->avatar_url)
                                        <img src="{{ asset('storage/' . $enroll->user->avatar_url) }}" alt="{{ $enroll->user->name }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-emerald-50">
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">
                                            {{ strtoupper(substr($enroll->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div>
                                    <p class="text-sm font-bold text-slate-700">{{ $enroll->user->name }}</p>
                                    <p class="text-[10px] text-slate-400">Bergabung di: {{ $enroll->courseClass->name }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="flex flex-col items-end">
                                    <span class="block text-xs font-black text-slate-700">{{ $enroll->progress_percentage }}%</span>
                                    <span class="text-[10px] italic text-slate-400">Progress</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-center text-xs text-slate-400">Belum ada siswa di kursus ini.</p>
                    @endforelse
                </div>
            </div>

            {{-- Quick Actions & Status --}}
            <div class="space-y-6">
                <div class="rounded-3xl bg-emerald-50 p-6 text-emerald-900">
                    <h4 class="mb-3 font-bold">Ringkasan Rating</h4>
                    @php
                        $avgRating = $course->classes->flatMap->enrollments->whereNotNull('rating')->avg('rating') ?? 0;
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="text-4xl font-black">{{ number_format($avgRating, 1) }}</span>
                        <div class="flex flex-col">
                            <div class="flex text-amber-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="{{ $i <= $avgRating ? 'fill-current' : 'text-slate-300' }} h-4 w-4" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-[10px] font-bold uppercase text-emerald-700">Berdasarkan ulasan siswa</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <h4 class="mb-4 font-bold text-slate-800">Cek Cepat</h4>
                    <div class="space-y-3">
                        <a href="{{ route('mentor.tambahkelas', $course->id) }}" class="flex items-center justify-between rounded-xl bg-slate-50 p-3 text-xs font-bold text-slate-600 transition hover:bg-emerald-500 hover:text-white">
                            Buka Batch Baru
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
