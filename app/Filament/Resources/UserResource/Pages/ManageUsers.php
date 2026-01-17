<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pengguna Baru'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pengguna'),
            'admin' => Tab::make('Administrator')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'admin'))
                ->icon('heroicon-m-shield-check'),
            'mentor' => Tab::make('Mentor')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'mentor'))
                ->icon('heroicon-m-academic-cap'),
            'student' => Tab::make('Siswa')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'student'))
                ->icon('heroicon-m-user-group'),
        ];
    }
}
