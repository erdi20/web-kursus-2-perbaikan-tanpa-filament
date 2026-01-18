<x-admin-layout>
    <div class="min-h-screen bg-[#F8FAFC] py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tight text-slate-800">Permintaan Pencairan Dana</h2>
                    <p class="text-sm font-medium text-slate-500">Kelola dan proses pembayaran komisi untuk para mentor.</p>
                </div>
            </div>

            {{-- Filter Status Simpel --}}
            <div class="mb-6 flex gap-2">
                <a href="{{ route('admin.withdrawal.index') }}" class="{{ !request('status') ? 'bg-slate-800 text-white' : 'bg-white text-slate-500 border border-slate-200' }} rounded-xl px-4 py-2 text-xs font-bold">Semua</a>
                <a href="{{ route('admin.withdrawal.index', ['status' => 'pending']) }}" class="{{ request('status') == 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-slate-500 border border-slate-200' }} rounded-xl px-4 py-2 text-xs font-bold">Menunggu</a>
                <a href="{{ route('admin.withdrawal.index', ['status' => 'processed']) }}" class="{{ request('status') == 'processed' ? 'bg-blue-500 text-white' : 'bg-white text-slate-500 border border-slate-200' }} rounded-xl px-4 py-2 text-xs font-bold">Diproses</a>
                <a href="{{ route('admin.withdrawal.index', ['status' => 'completed']) }}" class="{{ request('status') == 'completed' ? 'bg-emerald-500 text-white' : 'bg-white text-slate-500 border border-slate-200' }} rounded-xl px-4 py-2 text-xs font-bold">Selesai</a>
            </div>

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-6 py-4">Mentor & Tanggal</th>
                            <th class="px-6 py-4">Rekening Tujuan</th>
                            <th class="px-6 py-4">Jumlah</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($withdrawals as $wd)
                            <tr class="transition hover:bg-slate-50/50">
                                <td class="px-6 py-4">
                                    <span class="block font-bold text-slate-700">{{ $wd->mentor->name }}</span>
                                    <span class="text-[10px] font-medium uppercase text-slate-400">{{ $wd->created_at->format('d M Y, H:i') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="block font-bold text-slate-600">{{ $wd->bank_name }}</span>
                                    <span class="block text-xs text-slate-500">{{ $wd->account_number }} a/n {{ $wd->account_name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-black text-slate-700">Rp {{ number_format($wd->amount, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $colors = [
                                            'pending' => 'bg-amber-100 text-amber-600',
                                            'processed' => 'bg-blue-100 text-blue-600',
                                            'completed' => 'bg-emerald-100 text-emerald-600',
                                        ];
                                    @endphp
                                    <span class="{{ $colors[$wd->status] }} rounded-full px-3 py-1 text-[10px] font-black uppercase">
                                        {{ $wd->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($wd->status == 'pending')
                                        <form action="{{ route('admin.withdrawal.process', $wd) }}" method="POST" class="confirm-action inline-block" data-title="Proses Pencairan?">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="rounded-xl bg-amber-500 px-4 py-2 text-[10px] font-black uppercase text-white hover:bg-amber-600">
                                                Proses
                                            </button>
                                        </form>
                                    @elseif($wd->status == 'processed')
                                        <form action="{{ route('admin.withdrawal.complete', $wd) }}" method="POST" class="confirm-action inline-block" data-title="Tandai Selesai?" data-text="Pastikan dana sudah ditransfer ke rekening mentor.">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="rounded-xl bg-emerald-500 px-4 py-2 text-[10px] font-black uppercase text-white hover:bg-emerald-600">
                                                Selesaikan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] font-bold uppercase italic text-slate-400">No Action Needed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm italic text-slate-400">Tidak ada data pencairan dana.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $withdrawals->links() }}
            </div>
        </div>
    </div>

    {{-- Script SWAL untuk Konfirmasi --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.confirm-action').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const title = this.getAttribute('data-title');
                const text = this.getAttribute('data-text') || "Status akan diperbarui.";

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#20C896',
                    cancelButtonColor: '#f43f5e',
                    confirmButtonText: 'Ya, Lanjutkan!',
                    customClass: {
                        popup: 'rounded-3xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</x-admin-layout>
