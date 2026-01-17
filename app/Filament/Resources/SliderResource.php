<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    // --- Pengaturan Label ---
    protected static ?string $navigationLabel = 'Banner Promosi';  // Lebih menjual daripada sekadar "Slider"

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $pluralModelLabel = 'Banner Beranda (Slider)';

    protected static ?string $slug = 'manajemen-banner';

    // --- Pengaturan Navigasi & Visual ---
    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $navigationIcon = 'heroicon-o-photo';  // Ikon foto/gambar

    protected static ?string $activeNavigationIcon = 'heroicon-s-photo';

    protected static ?int $navigationSort = 1;  // Berada di urutan pertama dalam grupnya

    // --- Pengaturan UX ---
    protected static ?string $navigationBadgeTooltip = 'Jumlah banner yang aktif di halaman depan';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        // Izinkan akses jika user adalah admin atau mentor
        return $user->isAdmin();
    }

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\FileUpload::make('image')
    //                 ->label('Foto Slider')
    //                 ->image()
    //                 ->directory('sliders')
    //                 ->required(),
    //             Forms\Components\TextInput::make('title')
    //                 ->label('Judul (Opsional)')
    //                 ->maxLength(255),
    //             Forms\Components\Textarea::make('description')
    //                 ->label('Deskripsi (Opsional)')
    //                 ->maxLength(500),
    //             Forms\Components\Toggle::make('is_active')
    //                 ->label('Aktif')
    //                 ->default(true),
    //             Forms\Components\TextInput::make('order')
    //                 ->label('Urutan')
    //                 ->numeric()
    //                 ->default(1),
    //         ]);
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->defaultSort('order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Preview')
                    ->disk('public')  // Pastikan disk sesuai config Anda
                    ->width(120)  // Memberikan preview lebih lebar untuk slider
                    ->height(60)
                    ->rounded(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Slider')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Tanpa Judul'),
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                // Menggunakan ToggleColumn agar admin bisa mengaktifkan/matikan slider
                // tanpa harus masuk ke halaman edit (Fast Action)
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 1. Filter Status Aktif
                TernaryFilter::make('is_active')
                    ->label('Visibilitas')
                    ->placeholder('Semua Slider')
                    ->trueLabel('Hanya Slider Aktif')
                    ->falseLabel('Hanya Slider Non-aktif'),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 1. Filter Status Aktif
                TernaryFilter::make('is_active')
                    ->label('Visibilitas')
                    ->placeholder('Semua Slider')
                    ->trueLabel('Hanya Slider Aktif')
                    ->falseLabel('Hanya Slider Non-aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
