<?php

namespace App\Filament\Resources\MentorEarningsResource\Pages;

use App\Filament\Resources\MentorEarningsResource\Widgets\MentorTotalEarnings;
use App\Filament\Resources\MentorEarningsResource\Widgets\WithdrawalHistory;
use App\Filament\Resources\MentorEarningsResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions;

class ManageMentorEarnings extends ManageRecords
{
    protected static string $resource = MentorEarningsResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            MentorTotalEarnings::class,
            WithdrawalHistory::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
