{{-- resources/views/vendor/pagination/tailwind.blade.php --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center">
        <div class="flex items-center gap-2">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex cursor-default items-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-300">
                    {!! __('&laquo; Sebelum') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-500 transition hover:border-[#20C896] hover:text-[#20C896]">
                    {!! __('&laquo; Sebelum') !!}
                </a>
            @endif

            {{-- Angka Halaman --}}
            <div class="flex gap-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="cursor-default px-4 py-2 italic text-gray-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="relative inline-flex cursor-default items-center rounded-xl border border-[#20C896] bg-[#20C896] px-4 py-2 text-sm font-bold text-white">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="relative inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-[#20C896] hover:bg-green-50 hover:text-[#20C896]" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Tombol Selanjutnya --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-500 transition hover:border-[#20C896] hover:text-[#20C896]">
                    {!! __('Lanjut &raquo;') !!}
                </a>
            @else
                <span class="relative inline-flex cursor-default items-center rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-300">
                    {!! __('Lanjut &raquo;') !!}
                </span>
            @endif
        </div>
    </nav>
@endif
