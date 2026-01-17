<x-filament-panels::page>
    <div class="fi-my-modern-profile grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- Sidebar --}}
        <div class="col-span-1">
            <x-filament::section class="sticky top-6">
                <div class="flex flex-col items-center justify-center p-5 text-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                </div>

                <nav class="mt-6 flex flex-col space-y-1">
                    @php
                        $active = request()->query('section', 'personal-info');
                    @endphp

                    <a href="?section=personal-info" class="{{ $active === 'personal-info' ? 'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400' : '' }} flex items-center gap-3 rounded-lg px-3 py-2 text-sm">
                        <x-filament::icon icon="heroicon-o-user" class="h-5 w-5" /> Informasi Pribadi
                    </a>

                    <a href="?section=password" class="{{ $active === 'password' ? 'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400' : '' }} flex items-center gap-3 rounded-lg px-3 py-2 text-sm">
                        <x-filament::icon icon="heroicon-o-lock-closed" class="h-5 w-5" /> Ubah Kata Sandi
                    </a>

                    <a href="?section=delete" class="{{ $active === 'delete' ? 'bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400' : '' }} flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-danger-600">
                        <x-filament::icon icon="heroicon-o-trash" class="h-5 w-5" /> Hapus Akun
                    </a>
                </nav>
            </x-filament::section>
        </div>

        {{-- Konten --}}
        <div class="col-span-1 md:col-span-2">
            @if ($active === 'personal-info')
                <x-filament::section>
                    <x-slot name="heading">Informasi Pribadi</x-slot>
                    @livewire('my-modern-profile.update-personal-info-form')
                </x-filament::section>
            @elseif ($active === 'password')
                <x-filament::section>
                    <x-slot name="heading">Ubah Kata Sandi</x-slot>
                    @livewire('my-modern-profile.update-password-form')
                </x-filament::section>
            @elseif ($active === 'delete')
                <x-filament::section>
                    <x-slot name="heading" class="text-danger-600">Zona Bahaya</x-slot>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Menghapus akun akan menghilangkan semua data Anda dari sistem kami secara permanen.</p>

                    {{-- TOMBOL PEMICU --}}
                    <x-filament::button
                        color="danger"
                        wire:click="mountAction('deleteAccount')"
                        type="button"
                    >
                        Hapus Akun Saya Sekarang
                    </x-filament::button>
                </x-filament::section>
            @endif
        </div>
    </div>

    {{-- KOMPONEN WAJIB UNTUK MODAL --}}
    <x-filament-actions::modals />
</x-filament-panels::page>
