<section class="space-y-8">
    <header>
        <h2 class="text-2xl font-bold text-gray-900">Informasi Dasar & Kontak</h2>
        <p class="mt-1 text-sm text-gray-600">Perbarui nama, alamat email, dan detail kontak Anda.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('patch')

        <!-- === Avatar Section === -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Foto Profil</h3>
            <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-center">
                <div class="relative">
                    @if ($user->avatar_url)
                        <img class="h-20 w-20 rounded-full border-4 border-indigo-200 object-cover shadow-md" src="{{ Storage::url($user->avatar_url) }}" alt="Foto Profil">
                    @else
                        <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-indigo-200 bg-indigo-500 shadow-md">
                            <span class="text-3xl font-bold text-white">
                                {{ $user->initials }} </span>
                        </div>
                    @endif
                    <div class="absolute inset-0 flex items-center justify-center rounded-full bg-black bg-opacity-0 transition hover:bg-opacity-10">
                        <span class="text-xs font-medium text-white opacity-0 transition hover:opacity-100">Ubah</span>
                    </div>
                </div>

                <div class="w-full flex-1">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Ganti Foto (Maks 2MB, format: JPG/PNG)</label>
                    <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png" class="block w-full text-sm text-gray-600 transition file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100" />
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

        <!-- === Informasi Dasar === -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Informasi Dasar</h3>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-input-label for="name" :value="__('Nama Lengkap')" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" :value="old('email', $user->email)" required autocomplete="username" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>
            </div>
        </div>

        <!-- === Detail Kontak & Pribadi === -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Detail Kontak & Pribadi</h3>
            <div class="space-y-5">
                <div>
                    <x-input-label for="phone" :value="__('Nomor Telepon')" class="text-sm font-medium text-gray-700" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" :value="old('phone', $user->phone)" autocomplete="tel" placeholder="Contoh: 08123456789" />
                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                </div>

                <div>
                    <x-input-label for="address" :value="__('Alamat Lengkap')" class="text-sm font-medium text-gray-700" />
                    <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="Jalan, RT/RW, Kelurahan, Kota, Provinsi">{{ old('address', $user->address) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="gender" :value="__('Jenis Kelamin')" class="text-sm font-medium text-gray-700" />
                        <select id="gender" name="gender" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="" disabled {{ is_null(old('gender', $user->gender)) ? 'selected' : '' }}>Pilih Jenis Kelamin</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                    </div>

                    <div>
                        <x-input-label for="birth_date" :value="__('Tanggal Lahir')" class="text-sm font-medium text-gray-700" />
                        <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" :value="old('birth_date', $user->birth_date)" autocomplete="bday" />
                        <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="education_level" :value="__('Tingkat Pendidikan')" />

                    <select id="education_level" name="education_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="" disabled {{ is_null(old('education_level', $user->education_level)) ? 'selected' : '' }}>
                            -- Pilih Pendidikan --
                        </option>

                        @php
                            $educationOptions = [
                                'SD' => 'SD',
                                'SMP' => 'SMP',
                                'SMA/SMK' => 'SMA/SMK',
                                'S1' => 'S1',
                                'S2' => 'S2',
                                'S3' => 'S3',
                            ];
                        @endphp

                        @foreach ($educationOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('education_level', $user->education_level) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <x-input-error class="mt-2" :messages="$errors->get('education_level')" />
                </div>
            </div>
        </div>

        <!-- === Submit Button === -->
        <div class="flex flex-wrap items-center gap-4 pt-2">
            <x-primary-button class="rounded-lg px-5 py-2.5 font-medium">
                {{ __('Simpan Perubahan') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="flex items-center text-sm font-medium text-green-600">
                    <svg class="mr-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Berhasil disimpan.
                </p>
            @endif
        </div>
    </form>
</section>
