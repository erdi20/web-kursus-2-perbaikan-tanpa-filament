<x-mentor-layout>
    <div x-data="{ openWithdraw: false }" class="min-h-screen bg-[#F8FAFC] py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-10 flex flex-col justify-between gap-6 md:flex-row md:items-center">
                <div>
                    <h2 class="text-3xl font-[900] tracking-tight text-slate-900">Laporan Keuangan</h2>
                    <p class="mt-2 text-[14px] font-medium text-slate-500">Kelola pendapatan dan riwayat penarikan saldo lo.</p>
                </div>
                <button @click="openWithdraw = true" class="inline-flex items-center justify-center rounded-[14px] bg-emerald-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-200 transition-all hover:bg-emerald-600 active:scale-95">
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Tarik Saldo
                </button>
            </div>

            {{-- Ringkasan Saldo --}}
            <div class="mb-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-[24px] border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-[11px] font-black uppercase tracking-[0.1em] text-slate-400">Total Pendapatan</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-[24px] border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-[11px] font-black uppercase tracking-[0.1em] text-slate-400">Total Ditarik</p>
                    <p class="mt-2 text-3xl font-black text-blue-600">Rp {{ number_format($totalWithdrawn, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-[24px] border border-emerald-100 bg-emerald-50/30 p-8 shadow-sm ring-1 ring-emerald-100">
                    <p class="text-[11px] font-black uppercase tracking-[0.1em] text-emerald-600">Saldo Tersedia</p>
                    <p class="mt-2 text-3xl font-black text-emerald-600">Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                {{-- Tabel Transaksi Masuk (Komisi dari Penjualan) --}}
                <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="mb-6 flex items-center justify-between">
                        <h4 class="text-sm font-black uppercase tracking-tight text-slate-800">Komisi Masuk</h4>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase text-emerald-600">Terbaru</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-3 text-left">
                            <thead>
                                <tr class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    <th class="px-4 pb-2">Kursus</th>
                                    <th class="px-4 pb-2">Nominal</th>
                                    <th class="px-4 pb-2 text-right">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $t)
                                    <tr class="group bg-slate-50/30 transition-all hover:bg-slate-50">
                                        <td class="rounded-l-2xl px-4 py-4">
                                            <p class="line-clamp-1 text-xs font-bold text-slate-700">{{ $t->payment->course->name ?? 'Course Deleted' }}</p>
                                            <p class="text-[10px] uppercase text-slate-400">{{ $t->payment->midtrans_order_id ?? '-' }}</p>
                                        </td>
                                        <td class="px-4 py-4">
                                            {{-- GANTI mentor_earnings jadi amount sesuai controller --}}
                                            <span class="text-xs font-black text-emerald-600">+Rp {{ number_format($t->amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="rounded-r-2xl px-4 py-4 text-right">
                                            <span class="text-[10px] font-bold text-slate-500">
                                                {{ $t->payment->settlement_at ? \Carbon\Carbon::parse($t->payment->settlement_at)->translatedFormat('d M Y') : $t->created_at->format('d M Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-10 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">Belum ada komisi masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $transactions->appends(['w_page' => $withdrawals->currentPage()])->links() }}
                    </div>
                </div>

                {{-- Tabel Riwayat Penarikan (Withdrawals) --}}
                <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="mb-6 flex items-center justify-between">
                        <h4 class="text-sm font-black uppercase tracking-tight text-slate-800">Riwayat Penarikan</h4>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-[10px] font-black uppercase text-blue-600">Status WD</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-3 text-left">
                            <thead>
                                <tr class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    <th class="px-4 pb-2">Nominal</th>
                                    <th class="px-4 pb-2">Status</th>
                                    <th class="px-4 pb-2 text-right">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawals as $w)
                                    <tr class="group bg-slate-50/30 transition-all hover:bg-slate-50">
                                        <td class="rounded-l-2xl px-4 py-4 font-bold text-slate-700">
                                            Rp {{ number_format($w->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-4">
                                            @if ($w->status == 'completed')
                                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[9px] font-black uppercase text-emerald-600">Success</span>
                                            @elseif($w->status == 'pending')
                                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-black uppercase text-amber-600">Pending</span>
                                            @else
                                                <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[9px] font-black uppercase text-rose-600">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="rounded-r-2xl px-4 py-4 text-right text-[10px] font-bold text-slate-500">
                                            {{ $w->created_at->translatedFormat('d M Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-10 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">Belum ada penarikan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $withdrawals->appends(['t_page' => $transactions->currentPage()])->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Pop-up (Tetap di dalam x-data) --}}
        <div x-show="openWithdraw" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-[99] flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-md">

            <div @click.away="openWithdraw = false" class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-[0_20px_50px_rgba(0,0,0,0.1)] transition-all">

                <div class="px-8 pb-4 pt-8 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Pencairan Dana</h3>
                    <p class="text-sm text-slate-500">Konfirmasi nominal yang ingin lo cairkan.</p>
                </div>

                <form action="{{ route('mentor.withdraw') }}" method="POST" class="space-y-6 p-8 pt-4">
                    @csrf

                    <div class="group relative rounded-2xl border border-slate-100 bg-slate-50/50 p-5 transition-all hover:bg-slate-50">
                        <label class="mb-2 block text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">Rekening Tujuan</label>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                                <span class="text-xs font-bold text-emerald-600">{{ substr($user->bank_name, 0, 3) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700">{{ $user->bank_name }} — {{ $user->account_number }}</p>
                                <p class="text-xs font-medium text-slate-500">{{ $user->account_name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">Jumlah Pencairan</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-lg font-bold text-slate-400">Rp</span>
                            </div>
                            <input type="number" name="amount" required min="50000" max="{{ $availableBalance }}" class="block w-full rounded-2xl border-slate-200 bg-white py-4 pl-12 pr-4 text-xl font-bold text-slate-800 transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" placeholder="0">
                        </div>
                        <div class="flex items-center justify-between px-1">
                            <p class="text-[11px] font-medium text-slate-500">Saldo tersedia:</p>
                            <p class="text-[11px] font-bold text-emerald-600">Rp {{ number_format($availableBalance, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="openWithdraw = false" class="flex-1 rounded-xl bg-white py-3.5 text-sm font-bold text-slate-500 ring-1 ring-slate-200 transition hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 rounded-xl bg-emerald-500 py-3.5 text-sm font-bold text-white shadow-[0_10px_20px_rgba(16,185,129,0.2)] transition hover:bg-emerald-600 active:scale-95">
                            Ajukan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-mentor-layout>
