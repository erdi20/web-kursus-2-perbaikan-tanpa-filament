<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Judul Halaman -->
            <h2 class="mb-8 text-3xl font-extrabold tracking-tight text-gray-900">
                Kelas Anda
            </h2>

            <!-- Grid Responsif untuk Kartu Kelas -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($enrolledClasses as $class)
                    <a href="{{ route('kelas', $class->id) }}" class="group block">
                        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:border-indigo-300 hover:shadow-md">
                            <!-- Gambar Course (jika ada) -->
                            @if ($class->thumbnail)
                                <div class="h-32 overflow-hidden">
                                    <img src="{{ $class->thumbnail_url }}" alt="Kelas {{ $class->name }}" class="h-full w-full object-cover">
                                </div>
                            @else
                                <div class="h-32 bg-gradient-to-r from-indigo-400 to-purple-500"></div>
                            @endif

                            <div class="p-5">
                                <!-- Nama Kursus (utama) -->
                                <h3 class="line-clamp-2 text-lg font-extrabold text-slate-800 group-hover:text-indigo-700">
                                    {{ $class->course->name ?? 'Nama Kursus' }}
                                </h3>

                                <!-- Nama Kelas (sub) -->
                                <p class="mt-1 text-sm font-medium text-indigo-600">
                                    Kelas {{ $class->name }}
                                </p>

                                <!-- Mentor -->
                                <div class="mt-3 flex items-center text-xs text-gray-500">
                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>{{ $class->createdBy->name ?? 'Mentor' }}</span>
                                </div>

                                <!-- Progress Bar (opsional, jika ada enrollment) -->
                                @if (isset($class->enrollment) && $class->enrollment->progress_percentage > 0)
                                    <div class="mt-3">
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-500">Kemajuan</span>
                                            <span class="font-semibold text-gray-700">{{ $class->enrollment->progress_percentage }}%</span>
                                        </div>
                                        <div class="mt-1 h-1.5 w-full rounded-full bg-gray-200">
                                            <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-purple-600" style="width: {{ $class->enrollment->progress_percentage }}%">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Status dengan Warna Dinamis -->
                                <div class="mt-4 flex items-center justify-between">
                                    @php
                                        $status = strtolower($class->status);
                                        $statusColor = match ($status) {
                                            'open' => 'bg-green-100 text-green-800',
                                            'closed' => 'bg-red-100 text-red-800',
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'archived' => 'bg-yellow-100 text-yellow-800',
                                            default => 'bg-blue-100 text-blue-800',
                                        };
                                    @endphp

                                    <span class="{{ $statusColor }} rounded-full px-2.5 py-0.5 text-xs font-medium">
                                        {{ ucfirst($class->status) }}
                                    </span>

                                    <span class="inline-flex items-center text-sm font-semibold text-indigo-600 group-hover:text-indigo-800">
                                        Lanjutkan
                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Kondisi jika tidak ada kelas (Optional UX) -->
            @if ($enrolledClasses->isEmpty())
                <div class="mt-10 rounded-2xl border-t-4 border-indigo-500 bg-white p-12 text-center shadow-lg">
                    <p class="mb-4 text-xl font-medium text-gray-800">Ups! Anda belum terdaftar di kelas mana pun.</p>
                    <p class="mb-6 text-gray-600">Segera cari kursus yang menarik untuk memulai perjalanan belajar Anda!</p>
                    <!-- Tombol CTA besar -->
                    <a href="#" class="inline-block transform rounded-xl bg-indigo-600 px-8 py-3 font-semibold text-white shadow-md transition hover:scale-105 hover:bg-indigo-700">
                        Telusuri Semua Kursus
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
