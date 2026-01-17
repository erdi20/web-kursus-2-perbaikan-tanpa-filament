<?php

namespace App\Filament\Resources\QuizAssignmentResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Filament\Resources\QuizAssignmentResource;
use App\Models\Material;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;

class CreateQuizAssignment extends CreateRecord
{
    protected static string $resource = QuizAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(function () {
                    // 1. Ambil material_id dari URL

                    /** @var \Illuminate\Http\Request $request */
                    $request = request();
                    $materialId = $request->query('material_id');

                    if ($materialId) {
                        // 2. Cari material tersebut untuk mendapatkan course_id
                        $material = Material::find($materialId);

                        if ($material) {
                            return CourseResource::getUrl('materials', ['record' => $material->course_id]);
                        }
                    }

                    // Fallback jika tidak ada konteks, kembali ke index quiz
                    return QuizAssignmentResource::getUrl('index');
                }),
        ];
    }

    // Agar setelah Save juga kembali ke halaman materi kursus
    protected function getRedirectUrl(): string
    {
        return CourseResource::getUrl('materials', ['record' => $this->record->material->course_id]);
    }
}
