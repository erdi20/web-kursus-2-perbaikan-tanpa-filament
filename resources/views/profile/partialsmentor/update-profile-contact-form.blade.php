<section class="space-y-8">
    <header>
        <h2 class="text-2xl font-bold text-gray-900">Informasi Profil & Perbankan</h2>
        <p class="mt-1 text-sm text-gray-600">Perbarui foto, data diri, kontak, dan informasi rekening bank Anda dalam satu tempat.</p>
    </header>

    {{-- Form verifikasi email jika dibutuhkan --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('mentor.mentorupdate') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('patch')

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Foto Profil</h3>
            <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center">
                <div class="relative">
                    @if ($user->avatar_url)
                        <img class="h-20 w-20 rounded-full border-4 border-emerald-200 object-cover shadow-md" src="{{ Storage::url($user->avatar_url) }}" alt="Foto Profil">
                    @else
                        <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-emerald-200 bg-emerald-500 shadow-md">
                            <span class="text-3xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <div class="w-full flex-1">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Ganti Foto (Maks 2MB, format: JPG/PNG)</label>
                    <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png" class="block w-full text-sm text-gray-600 transition file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-emerald-700 hover:file:bg-emerald-100" />
                    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />

                    @if ($user->avatar_url)
                        <div class="mt-3 flex items-center">
                            <input type="hidden" name="remove_avatar" value="0">
                            <input type="checkbox" name="remove_avatar" id="remove_avatar" value="1" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <label for="remove_avatar" class="ml-2 text-sm font-medium text-red-600">Hapus avatar saat ini</label>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Informasi Dasar</h3>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Detail Kontak & Pribadi</h3>
            <div class="space-y-5">
                <div>
                    <x-input-label for="phone" :value="__('Nomor Telepon')" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" placeholder="Contoh: 08123456789" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div>
                    <x-input-label for="address" :value="__('Alamat Lengkap')" />
                    <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Jalan, RT/RW, Kelurahan, Kota, Provinsi">{{ old('address', $user->address) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="gender" :value="__('Jenis Kelamin')" />
                        <select id="gender" name="gender" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="" disabled {{ is_null(old('gender', $user->gender)) ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                    </div>

                    <div>
                        <x-input-label for="birth_date" :value="__('Tanggal Lahir')" />
                        <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date', $user->birth_date)" />
                        <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                    </div>
                </div>
                <div class="mt-5">
                    <x-input-label for="bio" :value="__('Bio / Deskripsi Singkat')" class="text-sm font-medium text-gray-700" />
                    <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="Ceritakan sedikit tentang keahlian dan pengalaman mengajar Anda...">{{ old('bio', $user->bio) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Bio ini akan muncul di profil publik Anda agar calon murid bisa mengenal Anda.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                </div>
                <div>
                    <x-input-label for="education_level" :value="__('Tingkat Pendidikan')" />
                    <select id="education_level" name="education_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="" disabled {{ is_null(old('education_level', $user->education_level)) ? 'selected' : '' }}>-- Pilih Pendidikan --</option>
                        @foreach (['SD', 'SMP', 'SMA/SMK', 'S1', 'S2', 'S3'] as $edu)
                            <option value="{{ $edu }}" {{ old('education_level', $user->education_level) == $edu ? 'selected' : '' }}>{{ $edu }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('education_level')" />
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-emerald-100 bg-emerald-50/30 p-6 shadow-sm">
            <h3 class="mb-4 flex items-center gap-2 text-lg font-semibold text-emerald-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Informasi Pembayaran (Komisi)
            </h3>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="bank_name" :value="__('Nama Bank / E-Wallet')" />
                    <select id="bank_name" name="bank_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="" disabled {{ is_null($user->bank_name) ? 'selected' : '' }}>-- Pilih Bank --</option>
                        <optgroup label="Bank Umum">
                            <option value="BCA" {{ old('bank_name', $user->bank_name) == 'BCA' ? 'selected' : '' }}>BCA</option>
                            <option value="BNI" {{ old('bank_name', $user->bank_name) == 'BNI' ? 'selected' : '' }}>BNI</option>
                            <option value="BRI" {{ old('bank_name', $user->bank_name) == 'BRI' ? 'selected' : '' }}>BRI</option>
                            <option value="Mandiri" {{ old('bank_name', $user->bank_name) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        </optgroup>
                        <optgroup label="E-Wallet">
                            <option value="GoPay" {{ old('bank_name', $user->bank_name) == 'GoPay' ? 'selected' : '' }}>GoPay</option>
                            <option value="OVO" {{ old('bank_name', $user->bank_name) == 'OVO' ? 'selected' : '' }}>OVO</option>
                            <option value="Dana" {{ old('bank_name', $user->bank_name) == 'Dana' ? 'selected' : '' }}>DANA</option>
                        </optgroup>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
                </div>

                <div>
                    <x-input-label for="account_number" :value="__('Nomor Rekening / HP')" />
                    <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" :value="old('account_number', $user->account_number)" />
                    <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="account_name" :value="__('Nama Pemilik Rekening')" />
                    <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full" :value="old('account_name', $user->account_name)" />
                    <p class="mt-1 text-xs text-gray-500">Pastikan nama sesuai dengan rekening agar pencairan lancar.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('account_name')" />
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button class="bg-emerald-600 px-6 py-3 hover:bg-emerald-700">
                {{ __('Simpan Semua Perubahan') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="flex items-center text-sm font-medium text-emerald-600">
                    <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Data berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>
