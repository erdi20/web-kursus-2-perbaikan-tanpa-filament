<x-admin-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tight text-slate-800">Banner Sliders</h2>
                    <p class="text-sm font-medium text-slate-500">Kelola banner promo dan informasi di halaman depan.</p>
                </div>
                <a href="{{ route('admin.slider.create') }}" class="rounded-xl bg-[#20C896] px-6 py-2.5 text-xs font-black uppercase tracking-widest text-white transition hover:bg-emerald-600">
                    Tambah Slider
                </a>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-6 py-4">Preview</th>
                            <th class="px-6 py-4">Judul</th>
                            <th class="px-6 py-4 text-center">Urutan</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($sliders as $slider)
                            <tr class="transition hover:bg-slate-50/50">
                                <td class="px-6 py-4">
                                    <img src="{{ asset('storage/' . $slider->image) }}" class="h-12 w-24 rounded-lg border border-slate-200 object-cover">
                                </td>
                                <td class="px-6 py-4">
                                    <span class="block text-sm font-bold text-slate-700">{{ $slider->title ?? 'Tanpa Judul' }}</span>
                                    <span class="text-[10px] font-medium uppercase text-slate-400">{{ $slider->created_at->format('d M Y') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-600">{{ $slider->order }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.slider.toggle', $slider) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="inline-flex items-center">
                                            @if ($slider->is_active)
                                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase text-emerald-600">Aktif</span>
                                            @else
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase text-slate-400">Non-Aktif</span>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="space-x-2 px-6 py-4 text-right">
                                    <a href="{{ route('admin.slider.edit', $slider) }}" class="inline-block rounded-lg p-2 text-amber-500 transition hover:bg-amber-50">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.slider.destroy', $slider) }}" method="POST" class="delete-form inline-block">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-delete rounded-lg p-2 text-rose-500 transition hover:bg-rose-50">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm italic text-slate-400">Belum ada slider yang ditambahkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.delete-form');

                Swal.fire({
                    title: 'Hapus Slider?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#20C896', // Warna hijau brand kamu
                    cancelButtonColor: '#f43f5e', // Warna rose
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    borderRadius: '1.5rem', // Bikin rounded biar sinkron sama UI
                    customClass: {
                        popup: 'rounded-3xl',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Munculkan notifikasi sukses kalau ada session 'success'
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000,
                customClass: {
                    popup: 'rounded-3xl',
                }
            });
        @endif
    </script>
</x-admin-layout>
