<x-admin-layout>
    <div class="py-12">
        <div x-data="{ currentTab: 'profile' }" class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="mb-6">
                <nav class="flex space-x-2 rounded-lg bg-white p-1 shadow-lg">
                    <button @click="currentTab = 'profile'" :class="{ 'bg-indigo-600 text-white': currentTab === 'profile' }" class="rounded-md px-4 py-2 text-sm font-medium transition">
                        👤 Informasi Profil
                    </button>
                    <button @click="currentTab = 'security'" :class="{ 'bg-indigo-600 text-white': currentTab === 'security' }" class="rounded-md px-4 py-2 text-sm font-medium transition">
                        🔒 Keamanan Akun
                    </button>
                </nav>
            </div>

            <div x-show="currentTab === 'profile'">
                <div class="border-t-4 border-indigo-500 bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    @include('profile.partialsadmin.update-profile-contact-form')
                </div>
            </div>

            <div x-show="currentTab === 'security'" class="space-y-6">
                <div class="border-t-4 border-indigo-500 bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    @include('profile.partialsadmin.update-password-form')
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
