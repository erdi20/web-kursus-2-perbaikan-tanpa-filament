<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($this->getRevenueData() as $item)
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="font-bold text-gray-800">{{ $item->course->name }}</h3>
                <p class="mt-2 text-sm text-gray-600">{{ $item->total_students }} siswa terdaftar</p>
                <p class="mt-3 text-2xl font-extrabold text-green-600">
                    Rp {{ number_format($item->total_revenue, 0, ',', '.') }}
                </p>
            </div>
        @endforeach
    </div>

    @if ($this->getRevenueData()->isEmpty())
        <div class="py-10 text-center text-gray-500">
            Belum ada transaksi yang berhasil.
        </div>
    @endif
</x-filament-panels::page>
