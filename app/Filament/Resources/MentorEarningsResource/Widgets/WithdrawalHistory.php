<?php

namespace App\Filament\Resources\MentorEarningsResource\Widgets;

use App\Models\Withdrawal;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;

class WithdrawalHistory extends BaseWidget
{
    protected static ?string $heading = 'Riwayat Pencairan';

    // Tampilkan penuh lebar
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Withdrawal::where('mentor_id', auth()->id())
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('account_name')
                    ->label('Pemilik Rekening'),
                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processed' => 'info',
                        'completed' => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y'),
            ])
            ->paginated([5])  // tampilkan 5 riwayat terbaru
            ->defaultPaginationPageOption(5);
    }
}


