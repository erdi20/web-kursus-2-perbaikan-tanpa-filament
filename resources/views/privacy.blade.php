<x-guest-layout>
    <div class="mx-auto max-w-4xl px-4 py-10">
        <div class="rounded-3xl border border-green-100 bg-white p-8 shadow-sm">
            <h1 class="mb-6 text-3xl font-bold text-gray-900">Kebijakan Privasi</h1>

            <div class="prose prose-green max-w-none leading-relaxed text-gray-600">
                {{-- Gunakan {!! !!} jika input menggunakan text editor (Rich Text) --}}
                {!! $setting->privacy_policy ?? 'Kebijakan privasi belum diatur oleh admin.' !!}
            </div>

            <div class="mt-10 border-t border-gray-100 pt-6">
                <a href="{{ url()->previous() }}" class="font-bold text-[#20C896] hover:underline">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
