<form wire:submit.prevent="updateProfile" class="space-y-6">
    {{ $this->form }}

    <div class="text-right">
        <x-filament::button type="submit">
            Simpan Perubahan
        </x-filament::button>
    </div>
</form>
