<x-mentor-layout>
    <div class="py-12">
        <div x-data="{ currentTab: 'profile' }" class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            <div class="mb-6">
                <nav class="flex space-x-2 rounded-lg bg-white p-1 shadow-lg">
                    <button @click="currentTab = 'profile'" :class="{ 'bg-emerald-600 text-white': currentTab === 'profile' }" class="rounded-md px-4 py-2 text-sm font-medium">
                        📝 Profil & Rekening
                    </button>
                    <button @click="currentTab = 'security'" :class="{ 'bg-emerald-600 text-white': currentTab === 'security' }" class="rounded-md px-4 py-2 text-sm font-medium">
                        🔒 Keamanan
                    </button>
                </nav>
            </div>

            <div x-show="currentTab === 'profile'">
                <div class="border-t-4 border-emerald-500 bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    @include('profile.partialsmentor.update-profile-contact-form')
                </div>
            </div>

            <div x-show="currentTab === 'security'" class="space-y-6">
                <div class="border-t-4 border-emerald-500 bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    @include('profile.partialsmentor.update-password-form')
                </div>
                <div class="border-t-4 border-red-500 bg-white p-4 shadow sm:rounded-lg sm:p-8">
                    @include('profile.partialsmentor.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-mentor-layout>
