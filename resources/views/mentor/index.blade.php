<x-mentor-layout>
    <div class="bg-slate-50/50 py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header Section --}}
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-3xl font-black tracking-tight text-slate-800">Dashboard Mentor</h2>
                    <p class="font-medium text-slate-500">Selamat datang kembali, {{ Auth::user()->name }}! 👋</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">
                        <span class="mr-1.5 h-2 w-2 rounded-full bg-emerald-500"></span>
                        Akun Terverifikasi
                    </span>
                </div>
            </div>

            {{-- Statistik Grid --}}
            <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                {{-- Total Siswa --}}
                <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="mb-4 inline-flex rounded-2xl bg-blue-50 p-3 text-blue-600 transition-colors group-hover:bg-blue-600 group-hover:text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Total Siswa</p>
                    <h3 class="mt-1 text-2xl font-black text-slate-800">{{ number_format($stats['total_students']) }}</h3>
                </div>

                {{-- Pendapatan Bersih --}}
                <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="mb-4 inline-flex rounded-2xl bg-emerald-50 p-3 text-emerald-600 transition-colors group-hover:bg-emerald-600 group-hover:text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Total Revenue</p>
                    <h3 class="mt-1 text-2xl font-black text-slate-800">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                </div>

                {{-- Kursus Aktif --}}
                <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="mb-4 inline-flex rounded-2xl bg-purple-50 p-3 text-purple-600 transition-colors group-hover:bg-purple-600 group-hover:text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Kursus Aktif</p>
                    <h3 class="mt-1 text-2xl font-black text-slate-800">{{ $stats['active_courses'] }}</h3>
                </div>

                {{-- Avg Rating --}}
                <div class="group rounded-3xl border border-slate-200 bg-white p-6 shadow-sm transition hover:shadow-md">
                    <div class="mb-4 inline-flex rounded-2xl bg-amber-50 p-3 text-amber-600 transition-colors group-hover:bg-amber-600 group-hover:text-white">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Avg Rating</p>
                    <h3 class="mt-1 text-2xl font-black text-slate-800">{{ $stats['avg_rating'] }} <span class="text-sm font-normal text-slate-400">/ 5.0</span></h3>
                </div>
            </div>
            {{-- ------ / Stats ------ --}}
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
                        <p class="text-[13px] text-sm font-medium text-slate-500">Statistik transaksi masuk dari akademi lo</p>
                    </div>

                    <div class="flex items-center gap-1 rounded-[18px] bg-slate-100 p-[6px] ring-1 ring-slate-200/50">
                        <button onclick="updateChartData('7days', this)" id="btn-7days" class="chart-filter-btn active-btn rounded-[12px] px-6 py-2 text-[11px] font-black uppercase tracking-[0.05em] transition-all duration-300">
                            7 Hari
                        </button>
                        <button onclick="updateChartData('30days', this)" id="btn-30days" class="chart-filter-btn rounded-[12px] px-6 py-2 text-[11px] font-black uppercase tracking-[0.05em] text-slate-500 transition-all duration-300 hover:text-slate-800">
                            30 Hari
                        </button>
                        <button onclick="updateChartData('1year', this)" id="btn-1year" class="chart-filter-btn rounded-[12px] px-6 py-2 text-[11px] font-black uppercase tracking-[0.05em] text-slate-500 transition-all duration-300 hover:text-slate-800">
                            1 Tahun
                        </button>
                    </div>
                </div>

                <div class="relative h-[350px] w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>




            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                {{-- Kursus Terpopuler (Berdasarkan Revenue & Payments) --}}
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-slate-100 p-6">
                        <h3 class="font-black uppercase tracking-tight text-slate-800">Kursus Terlaris</h3>
                        <a href="{{ route('mentor.kursus') }}" class="text-xs font-bold text-blue-600 hover:underline">Kelola Semua</a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            @forelse($popularCourses as $course)
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="h-14 w-20 flex-shrink-0 overflow-hidden rounded-xl border border-slate-100">
                                            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/80x60' }}" class="h-full w-full object-cover">
                                        </div>
                                        <div>
                                            <h4 class="line-clamp-1 text-sm font-bold text-slate-800">{{ $course->name }}</h4>
                                            <div class="mt-1 flex items-center gap-3 text-[10px] font-bold uppercase text-slate-400">
                                                <span class="flex items-center gap-1">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2" />
                                                    </svg>
                                                    {{ $course->students_count }} Siswa
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-black text-emerald-600">Rp {{ number_format($course->revenue, 0, ',', '.') }}</p>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-300">Total Settle</p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center">
                                    <p class="text-sm italic text-slate-400">Belum ada data penjualan.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Siswa Terbaru --}}
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 p-6">
                        <h3 class="text-center font-black uppercase tracking-tight text-slate-800 lg:text-left">Pendaftaran Terbaru</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-5">
                            @forelse($recentStudents as $enroll)
                                <div class="flex items-center gap-3 border-b border-slate-50 pb-4 last:border-0 last:pb-0">
                                    <div class="flex-shrink-0">
                                        @if ($enroll->user->avatar_url)
                                            <img src="{{ asset('storage/' . $enroll->user->avatar_url) }}" alt="{{ $enroll->user->name }}" class="h-10 w-10 rounded-full border border-slate-100 object-cover shadow-sm">
                                        @else
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#20C896] text-xs font-bold text-white shadow-sm">
                                                {{ strtoupper(substr($enroll->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-bold text-slate-800">{{ $enroll->user->name }}</p>
                                        <p class="truncate text-[10px] font-medium text-slate-400">{{ $enroll->courseClass->name }}</p>
                                        <p class="text-[10px] font-bold text-blue-500">{{ $enroll->enrolled_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-center text-xs text-slate-400">
                                    Belum ada siswa yang mendaftar.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            async function updateChartData(range, element) {
                // 1. Reset semua tombol ke style default (Slate 500, tanpa BG)
                document.querySelectorAll('.chart-filter-btn').forEach(btn => {
                    btn.classList.remove('bg-white', 'text-emerald-600', 'shadow-sm', 'ring-1', 'ring-slate-200/50', 'active-btn');
                    btn.classList.add('text-slate-500');
                });

                // 2. Set tombol yang diklik ke style Aktif (Putih, Emerald, Shadow)
                element.classList.remove('text-slate-500');
                element.classList.add('bg-white', 'text-emerald-600', 'shadow-sm', 'ring-1', 'ring-slate-200/50', 'active-btn');

                // 3. Sisanya tetap (Ambil Data API)
                try {
                    const response = await fetch(`{{ route('mentor.chart-data') }}?range=${range}`);
                    const json = await response.json();

                    if (revenueChart) {
                        revenueChart.data.labels = json.labels;
                        revenueChart.data.datasets[0].data = json.data;
                        revenueChart.update();
                    } else {
                        initRevenueChart(json.labels, json.data);
                    }
                } catch (error) {
                    console.error("Error fetch data:", error);
                }
            }

            let revenueChart;

            // Fungsi Utama yang dipanggil Onclick
            async function updateChartData(range, element) {
                // 1. Update Visual Tombol
                document.querySelectorAll('.chart-filter-btn').forEach(btn => btn.classList.remove('active-btn'));
                element.classList.add('active-btn');

                // 2. Ambil Data dari API
                try {
                    const response = await fetch(`{{ route('mentor.chart-data') }}?range=${range}`);
                    const json = await response.json();

                    if (revenueChart) {
                        // Update data chart yang sudah ada
                        revenueChart.data.labels = json.labels;
                        revenueChart.data.datasets[0].data = json.data;
                        revenueChart.update();
                    } else {
                        // Inisialisasi pertama kali
                        initRevenueChart(json.labels, json.data);
                    }
                } catch (error) {
                    console.error("Gagal mengambil data grafik:", error);
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
                            label: 'Pendapatan',
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

            // Load pertama kali otomatis saat halaman siap
            document.addEventListener('DOMContentLoaded', function() {
                const defaultBtn = document.getElementById('btn-7days');
                updateChartData('7days', defaultBtn);
            });
        </script>
    @endpush
</x-mentor-layout>
