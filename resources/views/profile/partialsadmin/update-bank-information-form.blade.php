<section class="space-y-6">
    <header>
        <h2 class="text-2xl font-bold text-gray-900">Informasi Pembayaran</h2>
        <p class="mt-1 text-sm text-gray-600">Digunakan untuk pencairan komisi pengajaran.</p>
    </header>

    <form method="post" action="{{ route('mentor.mentorupdate') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <x-input-label for="bank_name" :value="__('Nama Bank / E-Wallet')" />
                <select id="bank_name" name="bank_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="" disabled {{ is_null($user->bank_name) ? 'selected' : '' }}>-- Pilih Bank --</option>
                    <optgroup label="Bank Umum">
                        <option value="BCA" {{ $user->bank_name == 'BCA' ? 'selected' : '' }}>Bank Central Asia (BCA)</option>
                        <option value="BNI" {{ $user->bank_name == 'BNI' ? 'selected' : '' }}>Bank Negara Indonesia (BNI)</option>
                        <option value="BRI" {{ $user->bank_name == 'BRI' ? 'selected' : '' }}>Bank Rakyat Indonesia (BRI)</option>
                        <option value="Mandiri" {{ $user->bank_name == 'Mandiri' ? 'selected' : '' }}>Bank Mandiri</option>
                    </optgroup>
                    <optgroup label="E-Wallet">
                        <option value="GoPay" {{ $user->bank_name == 'GoPay' ? 'selected' : '' }}>GoPay</option>
                        <option value="OVO" {{ $user->bank_name == 'OVO' ? 'selected' : '' }}>OVO</option>
                        <option value="Dana" {{ $user->bank_name == 'Dana' ? 'selected' : '' }}>DANA</option>
                    </optgroup>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
            </div>

            <div>
                <x-input-label for="account_number" :value="__('Nomor Rekening / HP')" />
                <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" :value="old('account_number', $user->account_number)" required />
                <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="account_name" :value="__('Nama Pemilik Rekening')" />
                <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full" :value="old('account_name', $user->account_name)" required />
                <p class="mt-1 text-xs text-gray-500">Pastikan nama sesuai dengan yang tertera di buku tabungan atau aplikasi.</p>
                <x-input-error class="mt-2" :messages="$errors->get('account_name')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan Informasi Pembayaran') }}</x-primary-button>
        </div>
    </form>
</section>
