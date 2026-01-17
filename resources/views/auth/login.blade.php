<x-guest-layout>
    <div class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Selamat Datang Kembali!</h1>
        <p class="text-sm text-gray-500 mt-1">Silakan masuk ke akun Anda</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="font-semibold" />
            <x-text-input id="email" class="mt-1 block w-full border-gray-200 focus:border-[#20C896] focus:ring-[#20C896] rounded-xl"
                type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" class="font-semibold" />
            </div>
            <x-text-input id="password" class="mt-1 block w-full border-gray-200 focus:border-[#20C896] focus:ring-[#20C896] rounded-xl"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#20C896] shadow-sm focus:ring-[#20C896]" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ingat saya') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-[#20C896] hover:text-[#19a37a] transition" href="{{ route('password.request') }}">
                    {{ __('Lupa sandi?') }}
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center rounded-xl bg-[#20C896] px-4 py-3 text-sm font-bold text-white shadow-lg shadow-green-100 transition hover:bg-[#19a37a] focus:outline-none focus:ring-2 focus:ring-[#20C896] focus:ring-offset-2">
                {{ __('Masuk Sekarang') }}
            </button>
        </div>

        <p class="text-center text-sm text-gray-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-[#20C896] hover:underline">Daftar</a>
        </p>
    </form>
</x-guest-layout>
