<?php

namespace App\Filament\Resources\CourseClassResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CourseClassResource;

class EditCourseClass extends EditRecord
{
    protected static string $resource = CourseClassResource::class;

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

        // Cek jika ada thumbnail baru dan berbeda dengan yang lama
        if (isset($data['thumbnail']) && $oldThumbnail !== $data['thumbnail']) {
            if ($oldThumbnail && Storage::disk('public')->exists($oldThumbnail)) {
                Storage::disk('public')->delete($oldThumbnail);
            }
        }

        return $data;
    }
}
