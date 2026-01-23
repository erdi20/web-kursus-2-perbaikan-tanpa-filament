<x-mentor-layout>
    <div class="min-h-screen bg-[#F8FAFC]">
        {{-- Top Bar / Breadcrumb --}}
        <div class="border-b border-slate-200 bg-white px-4 py-4 sm:px-6 lg:px-8">
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('mentor.kursus') }}" class="rounded-full p-2 transition hover:bg-slate-100" title="Kembali ke Daftar Kursus">
                        <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-lg font-bold text-slate-800">{{ $course->name }}</h1>
                        <div class="mt-0.5 flex items-center gap-2">
                            <span class="text-xs font-medium text-slate-400">Status Kursus:</span>

                            @php
                                // Terjemahan status ke Bahasa Indonesia
                                $statusLabels = [
                                    'open' => 'Terbuka',
                                    'draft' => 'Draf',
                                    'closed' => 'Tutup',
                                    'archived' => 'Arsip',
                                ];

                                // Map warna berdasarkan status
                                $statusColors = [
                                    'open' => 'bg-emerald-100 text-emerald-600',
                                    'draft' => 'bg-amber-100 text-amber-600',
                                    'closed' => 'bg-rose-100 text-rose-600',
                                    'archived' => 'bg-slate-100 text-slate-600',
                                ];

                                $label = $statusLabels[$course->status] ?? 'Tidak Diketahui';
                                $colorClass = $statusColors[$course->status] ?? 'bg-slate-100 text-slate-600';
                            @endphp

                            <span class="{{ $colorClass }} rounded px-2 py-0.5 text-[10px] font-black uppercase">
                                {{ $label }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Bagian Tombol Kanan Dihapus Karena Tidak Relevan --}}
                <div class="hidden md:block">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Mode Pengelola Kursus</span>
                </div>
            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-8 lg:flex-row">
                {{-- Sidebar Menu --}}
                <aside class="w-full space-y-1 lg:w-64">
                    <p class="mb-2 px-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Menu Kelola</p>

                    {{-- Nav Item Ringkasan --}}
                    <a href="{{ route('mentor.kelolakursus', $course->id) }}" class="{{ request()->routeIs('mentor.kelolakursus') ? 'bg-white border border-emerald-500 font-bold text-emerald-600 shadow-sm shadow-emerald-100' : 'font-medium text-slate-600 hover:bg-white hover:shadow-sm' }} flex items-center gap-3 rounded-xl px-4 py-3 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Dashboard
                    </a>

                    {{-- Nav Item Materi --}}
                    <a href="{{ route('mentor.kelolakursusmateri', $course->id) }}" class="{{ request()->routeIs('mentor.kelolakursusmateri*') ? 'bg-white border border-emerald-500 font-bold text-emerald-600 shadow-sm shadow-emerald-100' : 'font-medium text-slate-600 hover:bg-white hover:shadow-sm' }} flex items-center gap-3 rounded-xl px-4 py-3 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Kurikulum Materi
                    </a>

                    {{-- Nav Item Manajemen Kelas --}}
                    <a href="{{ route('mentor.kelolakursuskelas', $course->id) }}" class="{{ request()->routeIs('mentor.kelolakursuskelas*') ? 'bg-white border border-emerald-500 font-bold text-emerald-600 shadow-sm shadow-emerald-100' : 'font-medium text-slate-600 hover:bg-white hover:shadow-sm' }} flex items-center gap-3 rounded-xl px-4 py-3 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Daftar Batch Kelas
                    </a>
                </aside>

                {{-- Content Area --}}
                <main class="flex-1">
                    @yield('manage-content')
                </main>
            </div>
        </div>
    </div>
</x-mentor-layout>
