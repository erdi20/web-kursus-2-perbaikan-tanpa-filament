<?php

namespace App\Filament\Resources\MaterialResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CourseResource;
use App\Filament\Resources\MaterialResource;

class EditMaterial extends EditRecord
{
    protected static string $resource = MaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(fn() => CourseResource::getUrl('materials', ['record' => $this->record->course_id])),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ambil data sebelum diupdate
        $originalData = $this->getRecord()->getOriginal();

        // Cek kolom PDF
        if (isset($data['pdf']) && $originalData['pdf'] !== $data['pdf']) {
            if ($originalData['pdf']) {
                Storage::disk('public')->delete($originalData['pdf']);
            }
        }

        // Cek kolom Image
        if (isset($data['image']) && $originalData['image'] !== $data['image']) {
            if ($originalData['image']) {
                Storage::disk('public')->delete($originalData['image']);
            }
        }

        return $data;
    }
}
