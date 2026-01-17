<?php

namespace App\Filament\Resources\EssayAssignmentResource\Pages;

use App\Filament\Resources\EssayAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEssayAssignments extends ListRecords
{
    protected static string $resource = EssayAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
