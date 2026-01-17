<div class="flex items-center justify-center">
    @if($logoUrl)
        {{-- Jika ada logo, tampilkan gambar dengan ukuran yang lebih fleksibel --}}
        <img src="{{ $logoUrl }}"
             alt="{{ $siteName }}"
             class="h-11 w-auto max-w-[200px] object-contain md:h-16">
    @else
        {{-- Jika tidak ada logo, tampilkan Nama Brand sebagai teks --}}
        <span class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
            {{ $siteName }}
        </span>
    @endif
</div>
