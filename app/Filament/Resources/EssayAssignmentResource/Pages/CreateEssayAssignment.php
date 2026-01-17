<?php

namespace App\Filament\Resources\EssayAssignmentResource\Pages;

use Filament\Actions;
use App\Models\Material;
use App\Filament\Resources\CourseResource;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MaterialResource;
use App\Filament\Resources\EssayAssignmentResource;

class CreateEssayAssignment extends CreateRecord
{
    protected static string $resource = EssayAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali ke List Materi')
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
                    return EssayAssignmentResource::getUrl('index');
                }),
        ];
    }

    // Agar setelah Save juga kembali ke halaman materi kursus
    protected function getRedirectUrl(): string
    {
        return CourseResource::getUrl('materials', ['record' => $this->record->material->course_id]);
    }
}
