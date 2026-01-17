<x-app-layout>
    <div class="min-h-screen bg-[#f8fafc] py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-8 rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm lg:p-10">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">

                    <div class="flex-1">
                        <div class="mb-6 flex items-center space-x-4">
                            <div class="relative">
                                <img alt="Mentor" src="{{ $class->course->user->avatar_url ? asset('storage/' . $class->course->user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($class->course->user->name) }}" class="h-16 w-16 rounded-2xl object-cover shadow-lg ring-4 ring-indigo-50" />
                                <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full border-2 border-white bg-green-500"></div>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-indigo-500">Mentor Kelas</p>
                                <h2 class="text-xl font-bold text-slate-800">{{ $class->course->user?->name ?? 'Mentor tidak tersedia' }}</h2>
                            </div>
                        </div>
                        <h1 class="text-3xl font-black tracking-tight text-slate-900 md:text-4xl">
                            {{ $class->course->name }}
                        </h1>
                        <p class="mt-4 max-w-2xl text-base leading-relaxed text-slate-500">
                            {!! $class->course->short_description ?? 'Selesaikan semua modul untuk mendapatkan sertifikat kompetensi.' !!}
                        </p>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row lg:w-1/3">
                        <div class="flex-1 rounded-2xl bg-indigo-600 p-5 text-white shadow-xl shadow-indigo-100">
                            <p class="text-xs font-bold uppercase opacity-80">Progress Belajar</p>
                            <div class="mt-2 flex items-end justify-between">
                                <h3 class="text-3xl font-black">{{ $enrollment->progress_percentage ?? 0 }}%</h3>
                                <svg class="h-8 w-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <div class="mt-3 h-1.5 w-full rounded-full bg-indigo-400/30">
                                <div class="h-full rounded-full bg-white" style="width: {{ $enrollment->progress_percentage ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

                <div class="lg:col-span-8">
                    <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm sm:p-8">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-xl font-extrabold text-slate-900">Kurikulum Kelas</h3>
                            <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">{{ $class->materialsFE->count() }} Modul</span>
                        </div>

                        <div class="space-y-3">
                            @if ($class->materialsFE->count())
                                @foreach ($class->materialsFE as $material)
                                    @php
                                        $service = app(\App\Services\MaterialCompletionService::class);
                                        $currentOrder = $material->pivot->order;
                                        $canAccess = $service->arePreviousMaterialsCompleted(Auth::id(), $class->id, $currentOrder);
                                        $isVisible = $material->pivot->visibility === 'visible';
                                        $isActive = $canAccess && $isVisible;
                                    @endphp

                                    <a href="{{ $isActive ? route('materials.show', [$class->id, $material->id]) : '#' }}" class="{{ $isActive ? 'border-slate-100 bg-white hover:border-indigo-500 hover:shadow-md' : 'cursor-not-allowed border-transparent bg-slate-50 opacity-60' }} group flex items-center justify-between rounded-2xl border p-4 transition-all duration-300">

                                        <div class="flex items-center space-x-4">
                                            <div class="{{ $isActive ? 'bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white' : 'bg-slate-200 text-slate-400' }} flex h-12 w-12 shrink-0 items-center justify-center rounded-xl font-bold transition-colors">
                                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                            </div>
                                            <div>
                                                <h4 class="{{ $isActive ? 'group-hover:text-indigo-600' : '' }} font-bold text-slate-800 transition-colors">{{ $material->name }}</h4>
                                                <p class="text-xs text-slate-500">Materi Pembelajaran</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center">
                                            @if ($isActive)
                                                <div class="rounded-full bg-green-50 p-2 text-green-600">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </div>
                                            @else
                                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="py-12 text-center">
                                    <p class="italic text-slate-400">Belum ada materi tersedia.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-4">

                    <div class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm">
                        <h4 class="mb-4 flex items-center text-lg font-bold text-slate-900">
                            <span class="mr-2 flex h-2 w-2 rounded-full bg-amber-500"></span>
                            Tugas Pending
                        </h4>

                        <div class="space-y-3">
                            @forelse ($pendingTasks as $task)
                                <div class="group rounded-2xl border border-slate-100 bg-slate-50 p-4 transition hover:bg-white hover:shadow-sm">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-600">{{ $task->type }}</span>
                                            <h5 class="mt-1 text-sm font-bold text-slate-800">{{ $task->title }}</h5>
                                            <p class="text-xs text-slate-500">{{ $task->material_name }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl bg-green-50 p-4 text-center">
                                    <p class="text-sm font-bold text-green-700">🎉 Semua tugas selesai!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-[2rem] bg-slate-900 p-6 text-white shadow-xl">
                        <h4 class="mb-4 font-bold">Apresiasi Belajar</h4>

                        @if ($enrollment?->status === 'completed')
                            <div class="space-y-3">
                                <a href="{{ route('certificates.download', $class->id) }}" class="flex w-full items-center justify-center rounded-xl bg-indigo-600 py-3 font-bold shadow-lg shadow-indigo-900/50 transition hover:bg-indigo-700">
                                    Unduh Sertifikat
                                </a>
                                <button id="open-review-btn" class="w-full rounded-xl bg-white/10 py-3 font-bold text-white transition hover:bg-white/20">
                                    Beri Ulasan Kelas
                                </button>
                            </div>
                        @else
                            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs italic leading-relaxed text-slate-400">
                                    Sertifikat dan fitur ulasan akan terbuka secara otomatis setelah progres belajar Anda mencapai 100%.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="review-modal" class="fixed inset-0 z-[100] flex hidden items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md transform overflow-hidden rounded-[2rem] bg-white shadow-2xl transition-all">
            <div class="relative p-8">
                <button id="close-review-modal" class="absolute right-6 top-6 text-slate-400 hover:text-slate-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="mb-6 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-100 text-amber-600">
                        <svg class="h-10 w-10" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900">Penilaian Kelas</h3>
                    <p class="mt-1 text-sm text-slate-500">Ulasan Anda membantu kami menjadi lebih baik.</p>
                </div>

                <form action="{{ route('reviews.store', $class->id) }}" method="POST">
                    @csrf
                    <div class="mb-6 space-y-2">
                        @php
                            $ratings = [
                                5 => 'Luar Biasa',
                                4 => 'Sangat Baik',
                                3 => 'Baik',
                                2 => 'Cukup Baik',
                                1 => 'Kurang Baik',
                            ];
                        @endphp

                        @foreach ($ratings as $value => $label)
                            <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-100 p-3 transition-colors hover:bg-indigo-50">
                                <div class="flex items-center">
                                    <input type="radio" name="rating" value="{{ $value }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500" required {{ old('rating', $enrollment->rating) == $value ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm font-bold text-slate-700">{{ $value }} Bintang</span>
                                </div>
                                <span class="text-xs font-medium text-slate-400">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <textarea name="review" rows="3" required placeholder="Tulis masukan Anda di sini..." class="w-full rounded-2xl border-slate-200 bg-slate-50 p-4 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('review', $enrollment->review) }}</textarea>

                    <button type="submit" class="mt-6 w-full rounded-2xl bg-slate-900 py-4 font-bold text-white transition hover:bg-black">
                        Kirim Ulasan Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const openBtn = document.getElementById('open-review-btn');
        const closeBtn = document.getElementById('close-review-modal');
        const modal = document.getElementById('review-modal');

        openBtn?.addEventListener('click', () => modal.classList.remove('hidden'));
        closeBtn?.addEventListener('click', () => modal.classList.add('hidden'));

        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });
    </script>
</x-app-layout>
