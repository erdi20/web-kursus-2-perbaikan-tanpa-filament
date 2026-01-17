<?php

namespace App\Filament\Resources;

use App\Models\Withdrawal;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;
    // --- Pengaturan Label ---
    protected static ?string $navigationLabel = 'Permintaan Pencairan';  // Lebih jelas daripada hanya "Pencairan"
    protected static ?string $modelLabel = 'Data Pencairan';
    protected static ?string $pluralModelLabel = 'Pencairan Dana Mentor';
    protected static ?string $slug = 'pencairan-dana';
    // --- Pengaturan Navigasi & Visual ---
    protected static ?string $navigationGroup = 'Manajemen Keuangan';  // Grup yang sama dengan Skema Komisi
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $activeNavigationIcon = 'heroicon-s-credit-card';
    protected static ?int $navigationSort = 1;  // Prioritas utama di grup Keuangan
    // --- Pengaturan UX (Sangat Penting untuk Admin) ---
    protected static ?string $navigationBadgeTooltip = 'Jumlah permintaan pencairan yang perlu segera diproses';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        // Izinkan akses jika user adalah admin atau mentor
        return $user->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pencairan')
                    ->schema([
                        Forms\Components\TextInput::make('mentor.name')
                            ->label('Mentor')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah')
                            ->prefix('Rp')
                            ->money('IDR')
                            ->dehydrated(),  // ← pastikan tidak diubah
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('account_name')->disabled(),
                                Forms\Components\TextInput::make('bank_name')->disabled(),
                            ]),
                        Forms\Components\TextInput::make('account_number')->disabled(),
                        // ✅ Hanya tampilkan status jika bukan "completed"
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu',
                                'processed' => 'Diproses',
                                'completed' => 'Selesai',
                            ])
                            ->required()
                            ->visible(fn($record) => $record?->status !== 'completed'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mentor.name')
                    ->label('Mentor')
                    ->searchable()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('Diproses')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Selesai')
                    ->dateTime('d M Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'processed' => 'Diproses',
                        'completed' => 'Selesai',
                    ]),
                Tables\Filters\SelectFilter::make('mentor_id')
                    ->label('Mentor')
                    ->relationship('mentor', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('process')
                    ->label('Proses Pencairan')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn(Withdrawal $record): bool => $record->status === 'pending')
                    ->action(function (Withdrawal $record) {
                        $record->update([
                            'status' => 'processed',
                            'processed_at' => now(),
                        ]);
                        Notification::make()
                            ->success()
                            ->title('Berhasil!')
                            ->body("Pencairan untuk {$record->mentor->name} sedang diproses.")
                            ->send();
                    }),
                Tables\Actions\Action::make('complete')
                    ->label('Tandai Selesai')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn(Withdrawal $record): bool => $record->status === 'processed')
                    ->action(function (Withdrawal $record) {
                        $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                        Notification::make()
                            ->success()
                            ->title('Berhasil!')
                            ->body("Pencairan untuk {$record->mentor->name} telah diselesaikan.")
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\WithdrawalResource\Pages\ManageWithdrawals::route('/'),
        ];
    }
}
