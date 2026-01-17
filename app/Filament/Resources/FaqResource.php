<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Resources\FaqResource\RelationManagers;
use App\Models\Faq;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    // --- Pengaturan Label ---
    protected static ?string $navigationLabel = 'Tanya Jawab (FAQ)';

    protected static ?string $modelLabel = 'Pertanyaan';

    protected static ?string $pluralModelLabel = 'Pusat Bantuan (FAQ)';

    protected static ?string $slug = 'pusat-bantuan-faq';

    // --- Pengaturan Navigasi & Visual ---
    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $activeNavigationIcon = 'heroicon-s-question-mark-circle';

    protected static ?int $navigationSort = 2;  // Biasanya Slider di urutan 1

    // --- Pengaturan UX ---
    protected static ?string $navigationBadgeTooltip = 'Total pertanyaan yang terdaftar';

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
                // Section Utama untuk Konten FAQ
                Section::make('Konten FAQ')
                    ->description('Masukkan detail pertanyaan dan jawaban yang sering diajukan.')
                    ->schema([
                        Forms\Components\TextInput::make('question')
                            ->label('Pertanyaan')
                            ->placeholder('Contoh: Bagaimana cara mendaftar kursus?')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('answer')
                            ->label('Jawaban')
                            ->placeholder('Tuliskan jawaban lengkap di sini...')
                            ->required()
                            ->toolbarButtons([
                                'blockquote',
                                'bold',
                                'bulletList',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 2]),  // Mengambil 2 kolom di layar lebar
                // Section Samping untuk Pengaturan (Status & Urutan)
                Section::make('Pengaturan')
                    ->description('Kontrol visibilitas FAQ')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Jika nonaktif, FAQ tidak muncul di website.')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                        Forms\Components\TextInput::make('order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(fn() => \App\Models\Faq::max('order') + 1)
                            ->helperText('Angka otomatis dibuat dari urutan terakhir.')
                            ->prefix('No.'),
                    ])
                    ->columnSpan(['lg' => 1]),  // Mengambil 1 kolom di layar lebar (Sidebar style)
            ])
            ->columns(3);  // Membagi layout menjadi 3 kolom grid
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFaqs::route('/'),
        ];
    }
}
