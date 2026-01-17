<form wire:submit.prevent="confirmDelete">
    <div class="mb-4">
        <label for="password" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
        <input type="password" wire:model="password" id="password" class="focus:border-danger-500 focus:ring-danger-500 mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" placeholder="Masukkan kata sandi Anda" />
        @error('password')
            <p class="text-danger-600 mt-1 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-danger-600 hover:bg-danger-700 focus:ring-danger-500 inline-flex items-center rounded-md border border-transparent px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2">
            Hapus Akun Saya
        </button>
    </div>
</form>
