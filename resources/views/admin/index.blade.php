<x-admin-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header Tetap Sama --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black uppercase tracking-tight text-slate-800">Admin Overview</h2>
                    <p class="text-sm font-medium text-slate-500">Selamat datang kembali, {{ Auth::user()->name }}!</p>
                </div>
                <div class="text-right">
                    <span class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Tanggal</span>
                    <span class="text-sm font-bold text-slate-700">{{ now()->format('d F Y') }}</span>
                </div>
            </div>

            {{-- 4 Card Utama --}}
            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                {{-- Revenue --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="rounded-lg bg-emerald-50 p-2 text-[#20C896]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Revenue</span>
                    </div>
                    <h4 class="text-2xl font-black text-slate-800">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h4>
                </div>

                {{-- Students --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="rounded-lg bg-blue-50 p-2 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Students</span>
                    </div>
                    <h4 class="text-2xl font-black text-slate-800">{{ number_format($stats['total_students']) }}</h4>
                </div>

                {{-- Mentor Platform (Gabung Sini) --}}
                <div class="group cursor-pointer rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition-all hover:border-[#20C896]" onclick="window.location='{{ route('admin.users.index') }}'">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="rounded-lg bg-purple-50 p-2 text-purple-600 transition-colors group-hover:bg-[#20C896] group-hover:text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Mentors</span>
                    </div>
                    <h4 class="text-2xl font-black text-slate-800">{{ $stats['total_mentors'] }}</h4>
                </div>

                {{-- Withdrawal --}}
                <div class="rounded-3xl border border-rose-100 bg-rose-50 p-6 shadow-sm">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="rounded-lg bg-rose-100 p-2 text-rose-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-rose-400">WD Pending</span>
                    </div>
                    <h4 class="text-2xl font-black text-rose-600">{{ $stats['pending_wd'] }}</h4>
                </div>
            </div>

            {{-- Section Grafik --}}
            <div class="mb-8 overflow-hidden rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
                <div class="mb-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="mb-1 flex items-center gap-2">
                            <span class="flex h-2 w-2">
                                <span class="absolute inline-flex h-2 w-2 animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            </span>
                            <h3 class="text-xl font-black uppercase tracking-tight text-slate-800">Analisis Pendapatan</h3>
                        </div>
                        <p class="text-[13px] font-medium text-slate-500">Statistik transaksi masuk dari seluruh akademi</p>
                    </div>

                    <div class="flex items-center gap-1 rounded-[18px] bg-slate-100 p-[6px] ring-1 ring-slate-200/50">
                        @foreach (['7days' => '7 Hari', '30days' => '30 Hari', '1year' => '1 Tahun'] as $key => $label)
                            <button onclick="updateChartData('{{ $key }}', this)" id="btn-{{ $key }}" class="chart-filter-btn {{ $key == '7days' ? 'active-btn' : '' }} rounded-[12px] px-6 py-2 text-[11px] font-black uppercase tracking-[0.05em] text-slate-500 transition-all duration-300">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="relative h-[350px] w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>



            {{-- Transaksi Terbaru & Sidebar --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-8 lg:col-span-2">
                <div class="mb-6 flex items-center justify-between">
                    <h4 class="text-sm font-black uppercase tracking-tight text-slate-800">Transaksi Terbaru</h4>
                </div>
                <div class="overflow-x-auto text-sm">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-400">
                                <th class="pb-4">Student</th>
                                <th class="pb-4">Kursus</th>
                                <th class="pb-4">Amount</th>
                                <th class="pb-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentTransactions as $payment)
                                <tr class="font-medium">
                                    <td class="py-4 font-bold text-slate-700">{{ $payment->user?->name ?? 'Guest' }}</td>
                                    <td class="py-4 text-slate-500">{{ $payment->course?->name }}</td>
                                    <td class="py-4 font-bold text-slate-700">Rp {{ number_format($payment->gross_amount, 0, ',', '.') }}</td>
                                    <td class="py-4 text-right">
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-black uppercase text-emerald-600">
                                            {{ $payment->transaction_status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-10 text-center text-slate-400">Belum ada transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
    <style>
        .active-btn {
            background-color: white !important;
            color: #10b981 !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important;
            --tw-ring-opacity: 1 !important;
            ring-width: 1px !important;
            border: 1px solid #e2e8f0;
        }
    </style>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            let revenueChart;

            async function updateChartData(range, element) {
                // 1. Update UI Tombol
                document.querySelectorAll('.chart-filter-btn').forEach(btn => {
                    btn.classList.remove('active-btn');
                    btn.classList.add('text-slate-500');
                });
                element.classList.add('active-btn');
                element.classList.remove('text-slate-500');

                // 2. Fetch Data AJAX
                try {
                    const response = await fetch(`{{ route('admin.dashboard') }}?range=${range}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const json = await response.json();

                    if (revenueChart) {
                        revenueChart.data.labels = json.labels;
                        revenueChart.data.datasets[0].data = json.data;
                        revenueChart.update();
                    } else {
                        initRevenueChart(json.labels, json.data);
                    }
                } catch (error) {
                    console.error("Gagal ambil data:", error);
                }
            }

            function initRevenueChart(labels, data) {
                const ctx = document.getElementById('revenueChart').getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
                gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

                revenueChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Settlement',
                            data: data,
                            borderColor: '#10b981',
                            borderWidth: 4,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#10b981',
                            pointBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                },
                                ticks: {
                                    callback: (v) => 'Rp ' + v.toLocaleString('id-ID'),
                                    font: {
                                        weight: 'bold',
                                        size: 10
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        weight: 'bold',
                                        size: 10
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Auto-load saat halaman siap
            document.addEventListener('DOMContentLoaded', function() {
                updateChartData('7days', document.getElementById('btn-7days'));
            });
        </script>
    @endpush
</x-admin-layout>
