<x-mentor-layout>
    <div class="bg-[#F8FAFC] py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-10 flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">Kursus Saya</h2>
                    <p class="mt-2 text-slate-500">Buat kurikulum yang menarik dan kelola siswa lo di satu tempat.</p>
                </div>
                <a href="{{ route('mentor.buatkursus') }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-6 py-3 text-sm font-bold text-white shadow-xl shadow-emerald-200 transition-all hover:-translate-y-1 hover:bg-emerald-600">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Kursus Baru
                </a>
            </div>

            {{-- Statik Ringkas (Optional ala Udemy) --}}
            <div class="mb-10 grid grid-cols-2 gap-4 md:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Kursus</p>
                    <p class="mt-1 text-2xl font-black text-slate-800">{{ $courses->count() }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Published</p>
                    <p class="mt-1 text-2xl font-black text-emerald-500">{{ $courses->where('status', 'open')->count() }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Draft</p>
                    <p class="mt-1 text-2xl font-black text-amber-500">{{ $courses->where('status', 'draft')->count() }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Siswa</p>
                    <p class="mt-1 text-2xl font-black text-blue-500">{{ number_format($totalStudentsCount) }}</p>
                </div>
            </div>

            {{-- Grid Kursus --}}
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($courses as $course)
                    <div class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/50">
                        {{-- Thumbnail --}}
                        <div class="relative h-44 overflow-hidden">
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">

                            {{-- Overlay Status --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 transition-opacity group-hover:opacity-100"></div>

                            <div class="absolute left-3 top-3">
                                @if ($course->status == 'open')
                                    <span class="rounded-lg bg-emerald-500 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-lg">LIVE</span>
                                @else
                                    <span class="rounded-lg bg-amber-500 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-lg">DRAFT</span>
                                @endif
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="flex flex-1 flex-col p-5">
                            <h3 class="text-md mb-2 line-clamp-2 font-bold text-slate-800 transition-colors group-hover:text-emerald-600">
                                {{ $course->name }}
                            </h3>
                            <p class="mb-4 line-clamp-2 text-xs text-slate-500">
                                {{ $course->short_description }}
                            </p>

                            <div class="mt-auto space-y-4">
                                {{-- Info Harga --}}
                                <div class="flex items-center justify-between border-t border-slate-50 pt-4">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold uppercase text-slate-400">Harga</span>
                                        <span class="text-sm font-black text-slate-700">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex flex-col text-right">
                                        <span class="text-[10px] font-bold uppercase text-slate-400">Bobot Lulus</span>
                                        <span class="text-sm font-black text-slate-700">{{ $course->min_final_score }}%</span>
                                    </div>
                                </div>

                                {{-- Aksi --}}
                                {{-- Ganti bagian Aksi di grid kursus --}}
                                <div class="flex gap-2">
                                    <a href="{{ route('mentor.kelolakursus', $course->id) }}" class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-center text-xs font-bold text-white transition hover:bg-slate-800">
                                        Kelola Kursus
                                    </a>
                                    {{-- Tombol Edit Baru --}}
                                    <a href="{{ route('mentor.editkursus', $course->id) }}" class="flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-400 transition hover:border-emerald-100 hover:bg-emerald-50 hover:text-emerald-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button type="button" onclick="deleteCourse('{{ route('mentor.hapuskursus', $course->id) }}')" class="flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-slate-400 transition hover:border-red-100 hover:bg-red-50 hover:text-red-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center rounded-3xl border-2 border-dashed border-slate-200 bg-white py-20 text-center">
                        <div class="mb-4 rounded-full bg-slate-50 p-6">
                            <svg class="h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Belum ada kursus</h3>
                        <p class="text-slate-500">Mulai buat kursus pertama lo dan bagikan ilmu ke dunia!</p>
                        <a href="{{ route('mentor.buatkursus') }}" class="mt-6 font-bold text-emerald-500 hover:text-emerald-600">Buat Kursus Sekarang &rarr;</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteCourse(url) {
            Swal.fire({
                title: 'Hapus Kursus?',
                text: "Data yang dihapus nggak bisa balik lagi, Bro!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981', // emerald-500
                cancelButtonColor: '#ef4444', // red-500
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                border: 'none',
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form dinamis untuk submit DELETE
                    let form = document.createElement('form');
                    form.action = url;
                    form.method = 'POST';
                    form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000,
                customClass: {
                    popup: 'rounded-2xl'
                }
            });
        </script>
    @endif
</x-mentor-layout>
