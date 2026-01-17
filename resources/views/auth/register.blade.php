<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Akun Baru</h1>
        <p class="mt-1 text-sm text-gray-500">Mulai perjalanan belajar Anda bersama kami</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="font-semibold" />
            <x-text-input id="name" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#20C896] focus:ring-[#20C896]" type="text" name="name" :value="old('name')" placeholder="Masukkan nama lengkap" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="font-semibold" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#20C896] focus:ring-[#20C896]" type="email" name="email" :value="old('email')" placeholder="nama@email.com" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="font-semibold" />
            <x-text-input id="password" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#20C896] focus:ring-[#20C896]" type="password" name="password" placeholder="••••••••" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="font-semibold" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#20C896] focus:ring-[#20C896]" type="password" name="password_confirmation" placeholder="••••••••" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-[#20C896] px-4 py-3 text-sm font-bold text-white shadow-lg shadow-green-100 transition duration-200 hover:bg-[#19a37a] focus:outline-none focus:ring-2 focus:ring-[#20C896] focus:ring-offset-2 active:scale-[0.98]">
                {{ __('Daftar Sekarang') }}
            </button>
        </div>

        <div class="text-center text-sm text-gray-500">
            Sudah punya akun?
            <a class="font-bold text-[#20C896] transition duration-200 hover:text-[#19a37a] hover:underline" href="{{ route('login') }}">
                {{ __('Masuk di sini') }}
            </a>
        </div>
    </form>
</x-guest-layout>
