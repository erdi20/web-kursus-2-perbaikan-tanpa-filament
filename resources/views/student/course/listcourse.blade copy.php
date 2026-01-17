<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {{-- Card --}}
            @foreach ($courses as $item)
                <a href="{{ route('detailkursus', $item->slug) }}">
                    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:shadow-md">
                        <div class="relative">
                            <img src="{{ asset('storage/' . $item->thumbnail) }}" alt="Course Image" class="h-48 w-full object-cover">
                            {{-- <div class="absolute left-3 top-3 rounded-full bg-cyan-400 px-3 py-1 text-xs font-bold text-white">
                        SEMUA TINGKAT
                    </div> --}}
                        </div>

                        <div class="p-5">
                            <h3 class="mb-2 text-lg font-bold text-cyan-500">{{ $item->name }}</h3>

                            {{-- <p class="mb-4 text-sm text-gray-600">21.5 jam | 186 Video</p> --}}

                            <div class="mb-4 flex items-center gap-3">
                                <img src="{{ asset('storage/' . $item->user->avatar_url) }}" alt="Mentor" class="h-10 w-10 rounded-full border border-gray-200">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $item->user->name }}</p>
                                    <p class="text-xs text-gray-500">Mentor</p>
                                </div>
                            </div>

                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    @php
                                        $isDiscountActive = $item->discount_price !== null && ($item->discount_end_date === null || now()->lessThan($item->discount_end_date));
                                    @endphp

                                    @if ($isDiscountActive)
                                        <p class="text-sm text-gray-400" style="text-decoration: line-through;">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </p>
                                        <p class="text-lg font-bold text-gray-900">
                                            Rp {{ number_format($item->discount_price, 0, ',', '.') }}
                                        </p>
                                    @else
                                        {{-- Tampilan Harga Normal --}}
                                        <p class="text-lg font-bold text-gray-900">
                                            Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </div>
                                {{-- <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.917c1.927-1.927 5.07-1.927 6.998 0L22 9a2 2 0 01-2 2h-5l-5 5v-5H4a2 2 0 01-2-2V9zM18 13a2 2 0 01-2 2h-6l-2 2v-2H6a2 2 0 01-2-2v-6a2 2 0 012-2h6l2-2v2h5a2 2 0 012 2v6z" />
                            </svg>
                            <span class="font-medium text-gray-800">4.9 (2136)</span>
                        </div> --}}
                            </div>

                            <!-- Label Terlaris -->
                            <div class="mt-2">
                                {{-- <span class="inline-block rounded-full bg-cyan-100 px-3 py-1 text-xs font-bold text-cyan-700">
                                TERLARIS
                            </span> --}}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach

        </div>


    </div>
</x-app-layout>
