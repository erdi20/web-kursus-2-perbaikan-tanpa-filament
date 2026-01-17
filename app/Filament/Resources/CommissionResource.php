<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Setting;
use Filament\Forms\Form;
use App\Models\Commission;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CommissionResource\Pages;
use App\Filament\Resources\CommissionResource\RelationManagers;
use App\Filament\Resources\CommissionResource\Widgets\RevenueChart;
use App\Filament\Resources\CommissionResource\Widgets\TotalRevenueOverview;

class CommissionResource extends Resource
{
    protected static ?string $model = Commission::class;

    // --- Pengaturan Label ---
    protected static ?string $navigationLabel = 'Skema Komisi';

    protected static ?string $pluralModelLabel = 'Skema Komisi';

    protected static ?string $modelLabel = 'Aturan Komisi';

    protected static ?string $slug = 'pengaturan-komisi';

    // --- Pengaturan Navigasi & Visual ---
    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $activeNavigationIcon = 'heroicon-s-receipt-percent';  // Icon solid saat aktif

    protected static ?int $navigationSort = 2;  // Berada di bawah Withdrawal

    // --- Pengaturan UX ---
    protected static ?string $navigationBadgeTooltip = 'Jumlah aturan komisi yang aktif saat ini';

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
                //
            ]);
    }

    // public static function table(Table $table): Table
    // {
    //     $commissionPercent = Setting::first()?->mentor_commission_percent ?? 70;
    //     return $table
    //         ->query(
    //             Course::with('user')
    //                 ->whereHas('payments', function ($q) {
    //                     $q->where('transaction_status', 'settlement');
    //                 })
    //                 ->select('courses.*')
    //                 ->addSelect([
    //                     'total_revenue' => Payment::selectRaw('SUM(gross_amount)')
    //                         ->whereColumn('course_id', 'courses.id')
    //                         ->where('transaction_status', 'settlement'),
    //                     'mentor_commission' => Payment::selectRaw('(SUM(gross_amount) * ?) / 100', [$commissionPercent])
    //                         ->whereColumn('course_id', 'courses.id')
    //                         ->where('transaction_status', 'settlement'),
    //                 ])
    //         )
    //         ->columns([
    //             Tables\Columns\TextColumn::make('name')->label('Kursus'),
    //             Tables\Columns\TextColumn::make('user.name')->label('Mentor'),
    //             Tables\Columns\TextColumn::make('total_revenue')
    //                 ->label('Total Pendapatan')
    //                 ->money('IDR'),
    //             // ->summarize([Sum::make()->money('IDR')]),
    //             Tables\Columns\TextColumn::make('mentor_commission')
    //                 ->label('Komisi Mentor')
    //                 ->money('IDR')
    //             // ->summarize([Sum::make()->money('IDR')]),
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //             Tables\Actions\DeleteAction::make(),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }

    // public static function table(Table $table): Table
    // {
    //     $commissionPercent = Setting::first()?->mentor_commission_percent ?? 70;
    //     $adminPercent = 100 - $commissionPercent;

    //     return $table
    //         ->query(
    //             Course::with('user')
    //                 ->whereHas('payments', fn($q) => $q->where('transaction_status', 'settlement'))
    //                 ->select('courses.*')
    //                 ->addSelect([
    //                     'total_revenue' => Payment::selectRaw('SUM(gross_amount)')
    //                         ->whereColumn('course_id', 'courses.id')
    //                         ->where('transaction_status', 'settlement'),
    //                     'mentor_commission' => Payment::selectRaw("(SUM(gross_amount) * {$commissionPercent}) / 100")
    //                         ->whereColumn('course_id', 'courses.id')
    //                         ->where('transaction_status', 'settlement'),
    //                     // Tambahkan kolom Net Web agar bisa di-summarize
    //                     'admin_revenue' => Payment::selectRaw("(SUM(gross_amount) * {$adminPercent}) / 100")
    //                         ->whereColumn('course_id', 'courses.id')
    //                         ->where('transaction_status', 'settlement'),
    //                 ])
    //         )
    //         ->columns([
    //             Tables\Columns\TextColumn::make('name')
    //                 ->label('Kursus')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('user.name')
    //                 ->label('Mentor')
    //                 ->icon('heroicon-m-user'),
    //             Tables\Columns\TextColumn::make('total_revenue')
    //                 ->label('Gross')
    //                 ->money('IDR')
    //                 ->alignRight()
    //                 ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total')->money('IDR')),
    //             Tables\Columns\TextColumn::make('mentor_commission')
    //                 ->label('Komisi Mentor')
    //                 ->money('IDR')
    //                 ->alignRight()
    //                 ->color('success')
    //                 ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total')->money('IDR')),
    //             Tables\Columns\TextColumn::make('admin_revenue')
    //                 ->label('Net Web')
    //                 ->money('IDR')
    //                 ->alignRight()
    //                 ->color('primary')
    //                 ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total')->money('IDR')),
    //         ])
    //         ->actions([
    //             Tables\Actions\ActionGroup::make([  // Mengelompokkan aksi agar tabel lebih ringkas
    //                 Tables\Actions\EditAction::make(),
    //                 Tables\Actions\DeleteAction::make(),
    //             ]),
    //         ]);
    // }

    public static function table(Table $table): Table
    {
        $commissionPercent = Setting::first()?->mentor_commission_percent ?? 70;
        $adminPercent = 100 - $commissionPercent;

        return $table
            ->query(
                Course::with('user')
                    ->whereHas('payments', fn($q) => $q->where('transaction_status', 'settlement'))
                    ->select('courses.*')
                    ->addSelect([
                        'total_revenue' => Payment::selectRaw('SUM(gross_amount)')
                            ->whereColumn('course_id', 'courses.id')
                            ->where('transaction_status', 'settlement'),
                        'mentor_commission' => Payment::selectRaw("(SUM(gross_amount) * {$commissionPercent}) / 100")
                            ->whereColumn('course_id', 'courses.id')
                            ->where('transaction_status', 'settlement'),
                        'admin_revenue' => Payment::selectRaw("(SUM(gross_amount) * {$adminPercent}) / 100")
                            ->whereColumn('course_id', 'courses.id')
                            ->where('transaction_status', 'settlement'),
                    ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Kursus')
                    ->searchable()  // Pencarian berdasarkan nama kursus
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Mentor')
                    ->searchable()  // Pencarian berdasarkan nama mentor
                    ->sortable()
                    ->icon('heroicon-m-user'),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Gross')
                    ->money('IDR')
                    ->alignRight()
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total')->money('IDR')),
                Tables\Columns\TextColumn::make('mentor_commission')
                    ->label('Komisi Mentor')
                    ->money('IDR')
                    ->alignRight()
                    ->sortable()
                    ->color('success')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total')->money('IDR')),
                Tables\Columns\TextColumn::make('admin_revenue')
                    ->label('Net Web')
                    ->money('IDR')
                    ->alignRight()
                    ->sortable()
                    ->color('primary')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total')->money('IDR')),
            ])
            ->filters([
                // 1. Filter Berdasarkan Mentor
                SelectFilter::make('created_by')
                    ->label('Filter Mentor')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                // 2. Filter Berdasarkan Tanggal Settlement
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereHas('payments', fn($q) => $q->whereDate('created_at', '>=', $date)),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereHas('payments', fn($q) => $q->whereDate('created_at', '<=', $date)),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Mulai: ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Sampai: ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            TotalRevenueOverview::class,
            RevenueChart::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCommissions::route('/'),
        ];
    }
}
