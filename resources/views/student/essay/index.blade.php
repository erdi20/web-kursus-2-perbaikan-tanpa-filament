<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('materials.show', ['classId' => $classId, 'materialId' => $materialId]) }}" class="inline-flex items-center text-blue-600 hover:underline">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Materi
            </a>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-lg">
            <h1 class="mb-2 text-2xl font-bold text-gray-900">{{ $assignment->title }}</h1>

            @if ($assignment->due_date)
                <p class="mb-6 text-sm text-gray-600">Batas pengumpulan: {{ \Carbon\Carbon::parse($assignment->due_date)->translatedFormat('d F Y, H:i') }}</p>
            @endif

            <div class="prose prose-slate mb-8 max-w-none">
                {!! $assignment->description !!}
            </div>

            <form method="POST" action="{{ route('essay.submit', ['classId' => $classId, 'assignmentId' => $assignment->id]) }}">
                @csrf
                <div class="mb-6">
                    <label for="essay_answer" class="mb-2 block text-sm font-medium text-gray-700">Jawaban Anda</label>
                    <textarea name="essay_answer" id="essay_answer" required class="min-h-[150px] w-full rounded-lg border border-gray-300 p-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500" placeholder="Tulis jawaban Anda di sini...">{{ old('essay_answer', $submission?->answer_text) }}</textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('materials.show', ['classId' => $classId, 'materialId' => $materialId]) }}" class="rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100">Batal</a>
                    <button type="submit" class="rounded-lg bg-gradient-to-r from-[#20C896] to-[#259D7A] px-6 py-2 font-semibold text-white hover:opacity-90">
                        {{ $submission ? 'Perbarui Jawaban' : 'Kirim Jawaban' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
