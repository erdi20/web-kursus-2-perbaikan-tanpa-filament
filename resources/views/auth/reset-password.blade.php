<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 dark:bg-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#20C896]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Atur Ulang Kata Sandi</h1>
        <p class="mt-2 text-sm text-gray-500">
            {{ __('Silakan masukkan kata sandi baru Anda di bawah ini untuk mengamankan akun kembali.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="font-semibold" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 opacity-75 shadow-sm focus:border-[#20C896] focus:ring-[#20C896]" type="email" name="email" :value="old('email', $request->email)" required readonly autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Kata Sandi Baru')" class="font-semibold" />
            <x-text-input id="password" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#20C896] focus:ring-[#20C896]" type="password" name="password" placeholder="••••••••" required autofocus autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi Baru')" class="font-semibold" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#20C896] focus:ring-[#20C896]" type="password" name="password_confirmation" placeholder="••••••••" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-[#20C896] px-4 py-3 text-sm font-bold text-white shadow-lg shadow-green-100 transition duration-200 hover:bg-[#19a37a] focus:outline-none focus:ring-2 focus:ring-[#20C896] focus:ring-offset-2 active:scale-[0.98]">
                {{ __('Perbarui Kata Sandi') }}
            </button>
        </div>
    </form>
</x-guest-layout>
