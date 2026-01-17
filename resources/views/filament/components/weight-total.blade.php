<span class="{{ $color == 'success' ? 'text-green-600' : 'text-red-600' }} font-bold">
    {{ $total }}%
    @if ($total !== 100)
        <span class="text-red-600"> (harus 100%)</span>
    @endif
</span>
