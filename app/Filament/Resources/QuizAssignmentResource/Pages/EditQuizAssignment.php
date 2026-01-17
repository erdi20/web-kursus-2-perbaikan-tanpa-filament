<?php

namespace App\Filament\Resources\QuizAssignmentResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Filament\Resources\QuizAssignmentResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditQuizAssignment extends EditRecord
{
    protected static string $resource = QuizAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
