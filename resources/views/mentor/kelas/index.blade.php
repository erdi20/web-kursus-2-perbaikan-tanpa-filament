@extends('layouts.managecourse')

@section('manage-content')
    <div x-data="{
        openModal: false,
        editMode: false,
        actionUrl: '{{ route('mentor.tambahkelas', $course->id) }}',
        formData: {
            name: '',
            description: '',
            max_quota: '',
            enrollment_start: '',
            enrollment_end: '',
            status: 'open'
        },
        openAdd() {
            this.editMode = false;
            this.actionUrl = '{{ route('mentor.tambahkelas', $course->id) }}';
            this.formData = { name: '', description: '', max_quota: '', enrollment_start: '', enrollment_end: '', status: 'open' };
            this.openModal = true;
        },
        openEdit(item, url) {
            this.editMode = true;
            this.actionUrl = url;
            this.formData = {
                name: item.name,
                description: item.description || '',
                max_quota: item.max_quota,
                enrollment_start: item.enrollment_start.split(' ')[0],
                enrollment_end: item.enrollment_end.split(' ')[0],
                status: item.status
            };
            this.openModal = true;
        }
    }" class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Daftar Kelas (Batch)</h2>
                <p class="text-xs text-slate-500">Kelola kuota dan periode pendaftaran siswa.</p>
            </div>
            <button @click="openAdd()" class="inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-xs font-bold text-white transition hover:bg-emerald-600">
                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kelas Baru
            </button>
        </div>

        {{-- Tabel Daftar Kelas --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full border-collapse text-left">
                <thead class="border-b border-slate-200 bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Nama Kelas</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Kuota</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Periode Pendaftaran</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($classes as $class)
                        <tr class="transition hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <a href="{{ route('mentor.kelolakelas', [$course->id, $class->id]) }}" class="group flex items-center gap-3">
                                    <img src="{{ $class->thumbnail ? asset('storage/' . $class->thumbnail) : 'https://ui-avatars.com/api/?name=' . urlencode($class->name) }}" class="h-10 w-10 rounded-lg object-cover ring-2 ring-transparent transition group-hover:ring-emerald-500">
                                    <div>
                                        <p class="text-sm font-bold text-slate-700 transition group-hover:text-emerald-600">{{ $class->name }}</p>
                                        <span class="{{ $class->status == 'open' ? 'text-emerald-500' : 'text-red-500' }} text-[10px] font-bold uppercase">{{ $class->status }}</span>
                                    </div>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-600">
                                {{ $class->max_quota }} Siswa
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($class->enrollment_start)->format('d M') }} - {{ \Carbon\Carbon::parse($class->enrollment_end)->format('d M Y') }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Tombol Edit --}}
                                    <button @click="openEdit({{ $class }}, '{{ route('mentor.updatekelas', [$course->id, $class->id]) }}')" class="text-slate-400 transition hover:text-blue-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    {{-- Tombol Hapus --}}
                                    <button onclick="confirmDeleteKelas('{{ route('mentor.hapuskelas', [$course->id, $class->id]) }}')" class="text-slate-400 transition hover:text-red-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm italic text-slate-400">Belum ada kelas untuk kursus ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL POP-UP --}}
        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 p-4 backdrop-blur-sm" x-cloak>
            <div @click.away="openModal = false" class="w-full max-w-2xl rounded-3xl bg-white p-8 shadow-2xl transition-all">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-slate-800" x-text="editMode ? 'Edit Batch Kelas' : 'Buat Batch Kelas Baru'"></h3>
                    <button @click="openModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="actionUrl" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <template x-if="editMode">
                        @method('PUT')
                    </template>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="col-span-2">
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-400">Nama Kelas / Batch</label>
                            <input type="text" name="name" x-model="formData.name" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div class="col-span-2">
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-400">Deskripsi Singkat</label>
                            <textarea name="description" x-model="formData.description" rows="2" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-400">Maksimal Kuota Siswa</label>
                            <input type="number" name="max_quota" x-model="formData.max_quota" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-400">Status Kelas</label>
                            <select name="status" x-model="formData.status" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-400">Pendaftaran Mulai</label>
                            <input type="date" name="enrollment_start" x-model="formData.enrollment_start" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-400">Pendaftaran Berakhir</label>
                            <input type="date" name="enrollment_end" x-model="formData.enrollment_end" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div class="col-span-2">
                            <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-400">Thumbnail Kelas (Kosongkan jika tidak diubah)</label>
                            <input type="file" name="thumbnail" class="w-full text-xs text-slate-500 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="openModal = false" class="rounded-xl px-6 py-2.5 text-sm font-bold text-slate-400 transition hover:bg-slate-50">Batal</button>
                        <button type="submit" class="rounded-xl bg-emerald-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-100 transition hover:bg-emerald-600">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmDeleteKelas(url) {
            Swal.fire({
                title: 'Hapus Batch Kelas?',
                text: "Semua data siswa di batch ini tidak bisa diakses lagi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = url;
                    form.method = 'POST';
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            })
        }
    </script>
@endsection
