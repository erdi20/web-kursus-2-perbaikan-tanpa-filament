<x-app-layout>
    <div class="mx-auto max-w-3xl px-4 py-8">
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900">Hasil Quiz</h1>
            <p class="mt-2 text-gray-600">{{ $assignment->title }}</p>
        </div>

        <div class="mb-8 rounded-xl bg-white p-6 shadow-sm">
            <div class="flex justify-between text-lg font-bold">
                <span>Skor Anda:</span>
                <span class="text-indigo-600">{{ $submission->score }} poin</span>
            </div>
            <p class="mt-2 text-sm text-gray-500">
                Jawaban Anda telah dinilai. Di bawah ini ditampilkan status setiap jawaban.
            </p>
        </div>

     <div class="space-y-8">
    @foreach($answers as $index => $answer)
        {{-- Pastikan soal ada sebelum ditampilkan untuk menghindari error --}}
        @if($answer->question)
            <div class="overflow-hidden rounded-xl border bg-white shadow-sm {{ $answer->is_correct ? 'border-green-200' : 'border-red-200' }}">

                <div class="{{ $answer->is_correct ? 'bg-green-50' : 'bg-red-50' }} flex items-center justify-between px-6 py-4 border-b">
                    <span class="text-sm font-bold uppercase tracking-wider {{ $answer->is_correct ? 'text-green-700' : 'text-red-700' }}">
                        Soal {{ $loop->iteration }}
                    </span>

                    @if($answer->is_correct)
                        <span class="flex items-center gap-1.5 rounded-full bg-green-200 px-3 py-1 text-xs font-bold text-green-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                            BENAR
                        </span>
                    @else
                        <span class="flex items-center gap-1.5 rounded-full bg-red-200 px-3 py-1 text-xs font-bold text-red-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            SALAH
                        </span>
                    @endif
                </div>

                <div class="p-6">
                    <div class="prose prose-indigo max-w-none text-lg font-medium text-gray-900 mb-6">
                        {!! $answer->question->question_text !!}
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="relative rounded-lg border p-4 {{ $answer->is_correct ? 'border-green-300 bg-green-50/50' : 'border-red-300 bg-red-50/50' }}">
                            <span class="absolute -top-3 left-3 bg-white px-2 text-xs font-bold uppercase {{ $answer->is_correct ? 'text-green-600' : 'text-red-600' }}">
                                Jawaban Anda
                            </span>
                            @php
                                // Mengambil teks jawaban berdasarkan kolom option_a, option_b, dll
                                $userCol = 'option_' . strtolower($answer->selected_option);
                                $userText = $answer->question->{$userCol} ?? 'Jawaban tidak ditemukan';
                            @endphp
                            <div class="text-gray-800 font-semibold italic mt-1">
                                {!! $userText !!}
                            </div>
                        </div>

                        @if(!$answer->is_correct)
                            <div class="relative rounded-lg border border-blue-300 bg-blue-50/50 p-4">
                                <span class="absolute -top-3 left-3 bg-white px-2 text-xs font-bold uppercase text-blue-600">
                                    Jawaban Yang Benar
                                </span>
                                @php
                                    $correctCol = 'option_' . strtolower($answer->question->correct_option);
                                    $correctText = $answer->question->{$correctCol} ?? 'Kunci tidak ditemukan';
                                @endphp
                                <div class="text-blue-900 font-bold mt-1">
                                    {!! $correctText !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 border-t text-right">
                    <p class="text-xs text-gray-500 font-medium">
                        Skor: {{ $answer->is_correct ? $answer->question->points : 0 }} / {{ $answer->question->points }} Poin
                    </p>
                </div>
            </div>
        @endif
    @endforeach
</div>

        <div class="mt-8 text-center">
            <a href="{{ route('materials.show', ['classId' => $classId, 'materialId' => $materialId]) }}" class="inline-block rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700">
                Kembali ke materi
            </a>
        </div>
    </div>
</x-app-layout>
