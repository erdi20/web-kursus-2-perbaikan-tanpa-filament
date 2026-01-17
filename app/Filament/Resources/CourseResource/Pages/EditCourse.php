<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Illuminate\Support\Facades\Storage;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ambil path file lama dari database
        $oldThumbnail = $this->getRecord()->thumbnail;

        // Jika ada thumbnail baru yang diupload DAN berbeda dengan yang lama
        if (isset($data['thumbnail']) && $oldThumbnail !== $data['thumbnail']) {
            if ($oldThumbnail && Storage::disk('public')->exists($oldThumbnail)) {
                Storage::disk('public')->delete($oldThumbnail);
            }
        }

        return $data;
    }
}
