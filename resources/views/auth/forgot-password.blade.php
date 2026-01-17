<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-50 dark:bg-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#20C896]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lupa Kata Sandi?</h1>
        <p class="mt-2 text-sm leading-relaxed text-gray-500">
            {{ __('Jangan khawatir! Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.') }}
        </p>
    </div>

    <x-auth-session-status class="mb-6 rounded-xl bg-green-50 p-4 text-sm font-medium text-green-700 shadow-sm" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Alamat Email')" class="font-semibold" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#20C896] focus:ring-[#20C896]" type="email" name="email" :value="old('email')" placeholder="nama@email.com" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="pt-2">
            <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-[#20C896] px-4 py-3 text-sm font-bold text-white shadow-lg shadow-green-100 transition duration-200 hover:bg-[#19a37a] focus:outline-none focus:ring-2 focus:ring-[#20C896] focus:ring-offset-2 active:scale-[0.98]">
                {{ __('Kirim Tautan Atur Ulang') }}
            </button>
        </div>

        <div class="text-center text-sm">
            <a class="flex items-center justify-center gap-1 font-bold text-gray-500 transition duration-200 hover:text-[#20C896]" href="{{ route('login') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('Kembali ke Login') }}
            </a>
        </div>
    </form>
</x-guest-layout>
