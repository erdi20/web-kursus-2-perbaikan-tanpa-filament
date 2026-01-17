<?php

namespace App\Filament\Resources\EssayAssignmentResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CourseResource;
use App\Filament\Resources\MaterialResource;
use App\Filament\Resources\EssayAssignmentResource;

class EditEssayAssignment extends EditRecord
{
    protected static string $resource = EssayAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


}
