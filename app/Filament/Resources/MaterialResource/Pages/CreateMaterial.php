<?php

namespace App\Filament\Resources\MaterialResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Filament\Resources\MaterialResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;

class CreateMaterial extends CreateRecord
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(function () {
                    // Ambil course_id dari parameter di URL (?course_id=...)
                    /** @var \Illuminate\Http\Request $request */
                    $request = request();
                    $courseId = $request->query('course_id');

                    if ($courseId) {
                        return CourseResource::getUrl('materials', ['record' => $courseId]);
                    }

                    // Jika tidak ada course_id di URL (akses manual), balik ke index material
                    return MaterialResource::getUrl('index');
                }),
        ];
    }
}
