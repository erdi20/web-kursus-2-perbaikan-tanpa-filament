<x-app-layout>

    <div class="py-12">
        <div x-data="{ currentTab: 'profile' }" class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="mb-6">
                <nav class="flex space-x-2 rounded-lg bg-white p-1 shadow-lg" aria-label="Tabs">
                    <button @click="currentTab = 'profile'" :class="{ 'bg-indigo-600 text-white shadow-md': currentTab === 'profile', 'text-gray-700 hover:bg-gray-100': currentTab !== 'profile' }" class="rounded-md px-4 py-2 text-sm font-medium transition duration-150 ease-in-out">
                        📝 Info Dasar & Kontak
                    </button>
                    <button @click="currentTab = 'security'" :class="{ 'bg-indigo-600 text-white shadow-md': currentTab === 'security', 'text-gray-700 hover:bg-gray-100': currentTab !== 'security' }" class="rounded-md px-4 py-2 text-sm font-medium transition duration-150 ease-in-out">
                        🔒 Keamanan Akun
                    </button>
                </nav>
            </div>

            <div x-show="currentTab === 'profile'" class="space-y-6">

                <div class="border-t-4 border-indigo-500 bg-white p-4 shadow-xl sm:rounded-lg sm:p-8">
                    @include('profile.partials.update-profile-contact-form')
                </div>

            </div>

            <div x-show="currentTab === 'security'" class="space-y-6">

                <div class="border-t-4 border-indigo-500 bg-white p-4 shadow-xl sm:rounded-lg sm:p-8">
                    @include('profile.partials.update-password-form')
                </div>

                <div class="border-t-4 border-red-500 bg-white p-4 shadow-xl sm:rounded-lg sm:p-8">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
