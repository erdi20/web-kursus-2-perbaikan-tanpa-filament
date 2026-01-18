<x-admin-layout>
    <div class="min-h-screen bg-[#F8FAFC] py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tight text-emerald-500 text-slate-800">FAQ Management</h2>
                    <p class="text-sm font-medium text-slate-500">Atur daftar pertanyaan yang sering muncul di website.</p>
                </div>
                <a href="{{ route('admin.faq.create') }}" class="rounded-xl bg-[#20C896] px-6 py-2.5 text-xs font-black uppercase tracking-widest text-white shadow-md hover:bg-emerald-600">
                    Tambah FAQ
                </a>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-6 py-4">Pertanyaan</th>
                            <th class="px-6 py-4 text-center">Urutan</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($faqs as $faq)
                            <tr class="transition hover:bg-slate-50/50">
                                <td class="px-6 py-4">
                                    <span class="block text-sm font-bold text-slate-700">{{ $faq->question }}</span>
                                    <span class="text-[10px] italic text-slate-400">Terakhir diupdate: {{ $faq->updated_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">{{ $faq->order }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.faq.toggle', $faq) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="{{ $faq->is_active ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }} rounded-full px-4 py-1.5 text-[10px] font-black uppercase transition">
                                            {{ $faq->is_active ? 'Aktif' : 'Draft' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="space-x-1 px-6 py-4 text-right">
                                    <a href="{{ route('admin.faq.edit', $faq) }}" class="inline-block rounded-xl p-2 text-amber-500 transition hover:bg-amber-50">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.faq.destroy', $faq) }}" method="POST" class="delete-form inline-block">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-delete rounded-xl p-2 text-rose-500 transition hover:bg-rose-50">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm italic text-slate-400">Belum ada FAQ.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Logika SWAL Hapus (sama seperti Slider)
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('.delete-form');
                Swal.fire({
                    title: 'Hapus FAQ?',
                    text: "Jawaban ini akan hilang dari website!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#20C896',
                    cancelButtonColor: '#f43f5e',
                    confirmButtonText: 'Ya, Hapus!',
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
</x-admin-layout>
