<form wire:submit.prevent="updatePassword" class="space-y-6">
    {{ $this->form }}

    <div class="text-right">
        <x-filament::button type="submit">
            Ubah Kata Sandi
        </x-filament::button>
    </div>
</form>
