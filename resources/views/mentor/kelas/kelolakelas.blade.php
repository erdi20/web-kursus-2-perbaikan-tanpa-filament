@extends('layouts.managecourse')

@section('manage-content')
    <div x-data="{ tab: 'materi', openModal: false }" class="space-y-6">

        {{-- 1. SIMPLE HEADER (Filament Style) --}}
        <div class="flex items-center justify-between border-b border-slate-200 pb-6">
            <div>
                <nav class="mb-1 flex text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <a href="{{ route('mentor.kelolakursuskelas', $course->id) }}" class="hover:text-emerald-500">Daftar Kelas</a>
                    <span class="mx-2">/</span>
                    <span>{{ $kelas->name }}</span>
                </nav>
                <h2 class="text-2xl font-bold tracking-tight text-slate-950">{a{ $kelas->name }}</h2>
                <p class="text-sm text-slate-500">Kelola kurikulum dan siswa dalam satu tempat.</p>
            </div>

            <div class="flex items-center gap-3 text-right">
                <div class="px-3">
                    <p class="text-[10px] font-bold uppercase text-slate-400">Kuota</p>
                    <p class="text-lg font-bold text-slate-900">{{ $kelas->max_quota }}</p>
                </div>
                <div class="h-8 w-[1px] bg-slate-200"></div>
                <div class="px-3">
                    <p class="text-[10px] font-bold uppercase text-slate-400">Terisi</p>
                    <p class="text-lg font-bold text-emerald-600">{{ $currentEnrolled }}</p>
                </div>
            </div>
        </div>

        {{-- 2. NAVIGASI --}}
        <div class="flex items-center justify-between">
            <div class="flex w-full gap-4 border-b border-slate-100">
                <button @click="tab = 'materi'" :class="tab === 'materi' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="border-b-2 px-2 pb-3 text-sm font-bold transition-all">Tautkan Materi</button>
                <button @click="tab = 'siswa'" :class="tab === 'siswa' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700'" class="border-b-2 px-2 pb-3 text-sm font-bold transition-all">Daftar Siswa</button>
            </div>
        </div>

        {{-- 3. CONTENT AREA --}}
        <div x-show="tab === 'materi'" class="space-y-4">
            <div class="flex justify-end">
                <button @click="openModal = true" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" />
                    </svg>
                    Tautkan Materi
                </button>
            </div>

            <div class="grid grid-cols-1 gap-3">
                @forelse($kelas->materials as $material)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-white p-4 transition hover:bg-slate-50">
                        <div class="flex items-center gap-4">
                            <span class="flex h-8 w-8 items-center justify-center rounded-md bg-slate-100 text-xs font-bold text-slate-400">
                                {{ $material->pivot->order }}
                            </span>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $material->name }}</p>
                                {{-- <p class="text-[10px] font-bold uppercase text-slate-400">{{ $material->link_video ? 'Video' : 'PDF' }}</p> --}}
                            </div>
                        </div>
                        <form action="{{ route('mentor.hapusmaterikelas', [$course->id, $kelas->id, $material->id]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 text-slate-300 transition-colors hover:text-red-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 py-12 text-center">
                        <p class="text-sm text-slate-400">Belum ada materi yang ditautkan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- FILAMENT STYLE MODAL                       --}}
        {{-- ========================================== --}}
        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-[2px] transition-opacity" @click="openModal = false"></div>

            {{-- Modal Dialog --}}
            <div class="relative flex min-h-screen items-center justify-center p-4">
                <div class="relative w-full max-w-lg overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl" x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-slate-900">Tautkan Materi</h3>
                        <button @click="openModal = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('mentor.kelasmaterisync', [$course->id, $kelas->id]) }}" method="POST" class="space-y-5 p-6">
                        @csrf

                        {{-- Input Materi --}}
                        <div>
                            <label for="material_id" class="mb-1.5 block text-[13px] font-semibold text-slate-700">Materi Kursus</label>
                            <div class="group relative">
                                <select name="material_id" id="material_id" required class="block w-full appearance-none rounded-lg border-slate-200 bg-white px-4 py-2.5 pr-10 text-sm transition focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                    <option value="" disabled selected>Pilih materi untuk ditautkan...</option>
                                    @foreach ($availableMaterials as $m)
                                        <option value="{{ $m->id }}">
                                            {{ $m->name }}
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Custom Arrow --}}
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 group-focus-within:text-emerald-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Input Order --}}
                        <div>
                            <label for="order" class="mb-1.5 block text-[13px] font-semibold text-slate-700">Urutan Materi</label>
                            <div class="relative">
                                <input type="number" name="order" id="order" required value="{{ $nextOrder }}" placeholder="Contoh: 1" class="block w-full rounded-lg border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm font-medium transition focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                                <div class="absolute inset-y-0 right-3 flex items-center">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Auto-filled</span>
                                </div>
                            </div>
                            <p class="mt-2 text-[11px] leading-relaxed text-slate-400">
                                Sistem mendeteksi ini adalah materi ke-{{ $nextOrder }} di kelas ini. Kamu tetap bisa mengubahnya manual jika perlu.
                            </p>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="mt-2 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                            <button type="button" @click="openModal = false" class="rounded-lg px-4 py-2 text-sm font-bold text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                                Batal
                            </button>
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                Tautkan Materi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div x-show="tab === 'siswa'" class="space-y-4" x-cloak>
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-4 font-bold text-slate-700">Mahasiswa</th>
                            <th class="px-6 py-4 text-center font-bold text-slate-700">Progres</th>
                            <th class="px-6 py-4 font-bold text-slate-700">Status</th>
                            <th class="px-6 py-4 text-right font-bold text-slate-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($kelas->enrollments as $enrollment)
                            <tr class="group transition hover:bg-slate-50/50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        {{-- Avatar Siswa --}}
                                        <div class="flex-shrink-0">
                                            @if ($enrollment->user->avatar_url)
                                                <img src="{{ asset('storage/' . $enrollment->user->avatar_url) }}" alt="{{ $enrollment->user->name }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-50">
                                            @else
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700 shadow-sm">
                                                    {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Info Siswa --}}
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-slate-900">{{ $enrollment->user->name }}</p>
                                            <p class="truncate text-[11px] text-slate-500">{{ $enrollment->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="mx-auto max-w-[140px]">
                                        <div class="mb-1 flex items-center justify-between text-[10px] font-bold uppercase">
                                            <span class="text-slate-400">Completion</span>
                                            <span class="text-emerald-600">{{ $enrollment->progress_percentage }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full rounded-full bg-slate-100">
                                            <div class="h-1.5 rounded-full bg-emerald-500 transition-all duration-500" style="width: {{ $enrollment->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($enrollment->progress_percentage >= 100)
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold uppercase text-emerald-600">Lulus</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold uppercase text-amber-600">Belajar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        {{-- Link untuk melihat detail raport/progres siswa --}}
                                        {{-- Ganti bagian ikon mata di tabel siswa --}}
                                        <button
                                            @click="$dispatch('open-student-detail', {
                                                    name: '{{ $enrollment->user->name }}',
                                                    email: '{{ $enrollment->user->email }}',
                                                    progress: {{ $enrollment->progress_percentage }},
                                                    enroll_id: {{ $enrollment->id }}
                                                })"
                                            class="rounded-lg border border-slate-200 bg-white p-2 text-slate-400 transition hover:text-emerald-600 hover:shadow-sm">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" />
                                                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2" />
                                            </svg>
                                        </button>
                                        {{-- Tombol Kick Siswa --}}
                                        <form action="#" method="POST" onsubmit="return confirm('Keluarkan mahasiswa dari kelas?')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg border border-slate-200 bg-white p-2 text-slate-400 transition hover:text-rose-600 hover:shadow-sm">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="rounded-full bg-slate-50 p-3">
                                            <svg class="h-6 w-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <p class="mt-2 text-sm italic text-slate-400">Belum ada siswa yang terdaftar di kelas ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    {{-- modal detail siswa --}}
    <div x-data="{
        showDetail: false,
        isLoading: false,
        data: {
            name: '',
            email: '',
            progress: 0,
            avatar_url: null,
            activities: {
                materials: { completed: 0, total: 0 },
                essays: { completed: 0, total: 0 },
                quizzes: { completed: 0, total: 0 }
            }
        },

        async fetchDetail(id) {
            this.showDetail = true;
            this.isLoading = true;

            // Kita susun URL secara manual agar tidak merusak sintaks Alpine
            let url = '/mentor/enrollment/' + id + '/detail';

            try {
                let response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal mengambil data dari server (404/500)');
                }

                const result = await response.json();
                this.data = result;

                console.log('Data sukses dimuat:', this.data);
            } catch (error) {
                console.error('Detail Error:', error);
                alert('Waduh, ada masalah: ' + error.message);
            } finally {
                this.isLoading = false;
            }
        }
    }" @open-student-detail.window="fetchDetail($event.detail.enroll_id)" class="relative">

        <div x-show="showDetail" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm" x-cloak x-transition>

            <div @click.away="showDetail = false" class="w-full max-w-lg overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">

                {{-- Header --}}
                <div class="flex items-center justify-between p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-600 text-xl font-bold text-white">
                            <template x-if="data.avatar_url">
                                <img :src="'/storage/' + data.avatar_url" class="h-full w-full rounded-full object-cover">
                            </template>
                            <template x-if="!data.avatar_url">
                                <span x-text="data.name ? data.name.charAt(0) : '?'"></span>
                            </template>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900" x-text="data.name"></h3>
                            <p class="text-sm text-slate-500" x-text="data.email"></p>
                        </div>
                    </div>
                    <button @click="showDetail = false" class="rounded-full p-2 hover:bg-slate-100"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" />
                        </svg></button>
                </div>

                <div class="p-6 pt-0">
                    {{-- Loading State --}}
                    <div x-show="isLoading" class="py-12 text-center">
                        <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-emerald-500 border-t-transparent"></div>
                        <p class="mt-4 text-sm font-bold uppercase tracking-widest text-slate-400">Mengambil Data...</p>
                    </div>

                    {{-- Content State --}}
                    <div x-show="!isLoading" x-transition>
                        <div class="mb-8 grid grid-cols-2 gap-4">
                            <div class="rounded-2xl bg-slate-50 p-6 text-center">
                                <p class="text-[10px] font-bold uppercase text-slate-400">Total Progres</p>
                                <p class="text-3xl font-black text-emerald-600" x-text="data.progress + '%'"></p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-6 text-center">
                                <p class="text-[10px] font-bold uppercase text-slate-400">Status Kursus</p>

                                <p class="mt-1 text-sm font-bold uppercase" :class="{
                                    'text-emerald-600': data.status === 'completed',
                                    'text-amber-500': data.status === 'active',
                                    'text-rose-600': data.status === 'dropped'
                                }" x-text="data.status === 'completed' ? 'SELESAI' : (data.status === 'dropped' ? 'KELUAR' : 'DALAM PROSES')">
                                </p>
                            </div>
                        </div>

                        <h4 class="mb-4 text-[11px] font-black uppercase tracking-[0.2em] text-slate-900">Rincian Aktivitas</h4>

                        <div class="space-y-3">
                            {{-- Materi --}}
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
                                    <span class="text-sm font-bold text-slate-700">Materi Dibaca</span>
                                </div>
                                <span class="text-xs font-bold text-slate-500" x-text="data.activities.materials?.completed + ' / ' + data.activities.materials?.total"></span>
                            </div>

                            {{-- Essay --}}
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 w-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></div>
                                    <span class="text-sm font-bold text-slate-700">Tugas Essay</span>
                                </div>
                                <span class="text-xs font-bold text-slate-500" x-text="data.activities.essays?.completed + ' / ' + data.activities.essays?.total"></span>
                            </div>

                            {{-- Kuis --}}
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 w-2 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.6)]"></div>
                                    <span class="text-sm font-bold text-slate-700">Ujian Kuis</span>
                                </div>
                                <span class="text-xs font-bold text-slate-500" x-text="data.activities.quizzes?.completed + ' / ' + data.activities.quizzes?.total"></span>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-between border-t border-slate-100 pt-6">
                            <button class="text-xs font-bold uppercase tracking-tighter text-rose-500 hover:text-rose-700">Reset Progres</button>
                            <button @click="showDetail = false" class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-bold text-white shadow-lg transition-all hover:bg-black">Tutup Detail</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
    </style>
@endsection
