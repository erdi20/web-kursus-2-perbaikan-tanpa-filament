<?php

namespace App\Filament\Resources\CommissionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\CommissionResource;
use App\Filament\Resources\CommissionResource\Widgets\RevenueChart;
use App\Filament\Resources\CommissionResource\Widgets\TotalRevenueOverview;

class ManageCommissions extends ManageRecords
{
    protected static string $resource = CommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TotalRevenueOverview::class,
            RevenueChart::class,
        ];
    }
}
