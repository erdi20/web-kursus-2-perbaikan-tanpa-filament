<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizAssignmentResource\Pages\CreateQuizAssignment;
use App\Filament\Resources\QuizAssignmentResource\Pages\EditQuizAssignment;
use App\Filament\Resources\QuizAssignmentResource\Pages\ListQuizAssignments;
use App\Filament\Resources\QuizAssignmentResource\RelationManagers\QuestionsRelationManager;
use App\Filament\Resources\QuizAssignmentResource\Pages;
use App\Filament\Resources\QuizAssignmentResource\RelationManagers;
use App\Models\QuizAssignment;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class QuizAssignmentResource extends Resource
{
    protected static ?string $model = QuizAssignment::class;

    // --- Pengaturan Label ---
    protected static ?string $navigationLabel = 'Kuis & Ujian';

    protected static ?string $modelLabel = 'Kuis';

    protected static ?string $pluralModelLabel = 'Daftar Kuis';

    protected static ?string $slug = 'evaluasi-kuis';

    // --- Pengaturan Navigasi & Visual ---
    protected static ?string $navigationGroup = 'Evaluasi & Ujian';  // Masuk grup yang sama dengan Essay

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document-check';

    protected static ?int $navigationSort = 2;  // Urutan kedua setelah Essay (1)

    // --- Pengaturan UX ---
    protected static ?string $navigationBadgeTooltip = 'Jumlah kuis yang aktif saat ini';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        // Izinkan akses jika user adalah admin atau mentor
        return $user->isMentor();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar Kuis')
                    ->description('Tentukan judul, kelas, dan instruksi dasar untuk kuis ini.')
                    ->columns(2)
                    ->schema([
                        Hidden::make('material_id')
                            ->default(function () {
                                $materialId = app('request')->query('material_id');
                                return is_numeric($materialId) ? (int) $materialId : null;
                            }),
                        Select::make('material_id')
                            ->label('Materi')
                            ->relationship(
                                name: 'material',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query
                                    ->whereHas('course', fn($q) => $q->where('created_by', auth()->id()))
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('title')
                            ->label('Judul Kuis')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Kuis Akhir Bab 3 - Struktur Data'),
                    ]),
                Section::make('Instruksi Kuis')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Instruksi Lengkap')
                            ->helperText('Berikan instruksi jelas mengenai materi dan peraturan kuis.')
                            ->columnSpanFull()
                    ]),
                Section::make('Pengaturan Waktu')
                    ->columns(3)
                    ->schema([
                        DateTimePicker::make('due_date')
                            ->label('Batas Waktu Pengumpulan')
                            ->minDate(now())
                            ->required()
                            ->timezone('Asia/Jakarta')
                            ->columnSpan(2),
                        TextInput::make('duration_minutes')
                            ->label('Durasi Kuis (Menit)')
                            ->helperText('Kosongkan jika kuis tidak memiliki batas waktu pengerjaan.')
                            ->numeric()
                            ->minValue(5)
                            ->suffix('Menit'),
                    ]),
                Fieldset::make('Opsi Publikasi')
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Publikasikan Kuis')
                            ->helperText('Jika aktif, kuis akan terlihat oleh siswa di Kelas Tujuan.')
                            ->default(false)
                            ->columnSpan(1),
                        Hidden::make('created_by')
                            ->default(auth()->id()),
                    ]),
            ]);
    }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('courseClass.name')
    //                 ->label('Kelas Tujuan')
    //                 ->searchable()
    //                 ->sortable()
    //                 ->weight('bold')  // Menonjolkan kelas
    //                 ->color('primary'),
    //             Tables\Columns\TextColumn::make('title')
    //                 ->label('Judul Kuis')
    //                 ->searchable()
    //                 ->wrap()
    //                 ->limit(40),
    //             Tables\Columns\TextColumn::make('duration_minutes')
    //                 ->label('Durasi')
    //                 ->formatStateUsing(fn($state) => $state ? "{$state} Menit" : 'Tanpa Batas')
    //                 ->sortable()
    //                 ->description(fn($record) => $record->questions()->count() . ' Soal'),  // Menampilkan jumlah soal
    //             Tables\Columns\TextColumn::make('due_date')
    //                 ->label('Batas Waktu')
    //                 ->dateTime('d M Y H:i')
    //                 ->timezone('Asia/Jakarta')
    //                 ->sortable()
    //                 ->color(fn(string $state): string => match (true) {
    //                     now()->gt($state) => 'danger',  // Merah jika sudah melewati deadline
    //                     now()->diffInHours($state) <= 48 => 'warning',  // Kuning jika mendekati deadline (48 jam)
    //                     default => 'success',
    //                 }),
    //             Tables\Columns\BadgeColumn::make('is_published')
    //                 ->label('Status')
    //                 ->sortable()
    //                 ->getStateUsing(fn($record): string => $record->is_published ? 'Aktif' : 'Draf')
    //                 ->colors([
    //                     'success' => 'Aktif',
    //                     'warning' => 'Draf',
    //                 ]),
    //             // 6. Dibuat Oleh (Menggunakan Relasi Nama)
    //             Tables\Columns\TextColumn::make('creator.name')  // Asumsi relasi 'creator' ada ke Model User
    //                 ->label('Instruktur')
    //                 ->searchable()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //             // 7. Waktu Pembuatan (Hidden by default)
    //             Tables\Columns\TextColumn::make('created_at')
    //                 ->label('Dibuat Tanggal')
    //                 ->dateTime('d/m/Y H:i')
    //                 ->timezone('Asia/Jakarta')
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //         ])
    //         ->filters([
    //             // 1. Filter Publikasi (Ternary Filter)
    //             TernaryFilter::make('is_published')
    //                 ->label('Status Kuis')
    //                 ->trueLabel('Aktif')
    //                 ->falseLabel('Draf')
    //                 ->nullable(),
    //             // 2. Filter Kelas Tujuan (Select Filter Relasi)
    //             SelectFilter::make('course_class_id')
    //                 ->label('Filter Berdasarkan Kelas')
    //                 ->relationship('courseClass', 'name')
    //                 ->searchable()
    //                 ->preload(),
    //             // 3. Filter Deadline (Custom Query Filter)
    //             Tables\Filters\Filter::make('deadline_lewat')
    //                 ->label('Deadline Terlewat')
    //                 ->query(fn(Builder $query): Builder => $query->where('due_date', '<', now()))
    //                 ->toggle(),
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //             Tables\Actions\ViewAction::make(),
    //             Tables\Actions\Action::make('submissions')
    //                 ->label('Lihat Pengumpulan')
    //                 ->url(fn(QuizAssignment $record) => static::getUrl('submissions', ['record' => $record->id]))
    //                 ->button()
    //                 ->color('info'),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('created_by', Auth::id());
            })
            ->columns([
                Tables\Columns\TextColumn::make('courseClass.name')
                    ->label('Kelas Tujuan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Kuis')
                    ->searchable()
                    ->wrap()
                    ->limit(40),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->formatStateUsing(fn($state) => $state ? "{$state} Menit" : 'Tanpa Batas')
                    ->sortable()
                    // Menampilkan jumlah soal (Eager Loading disarankan pada model agar tidak lambat)
                    ->description(fn($record) => $record->questions_count ?? $record->questions()->count() . ' Soal'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Batas Waktu')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta')
                    ->sortable()
                    ->color(fn($state): string => match (true) {
                        now()->gt($state) => 'danger',
                        now()->diffInHours($state) <= 48 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\BadgeColumn::make('is_published')
                    ->label('Status')
                    ->sortable()
                    ->getStateUsing(fn($record): string => $record->is_published ? 'Aktif' : 'Draf')
                    ->colors([
                        'success' => 'Aktif',
                        'warning' => 'Draf',
                    ]),
            ])
            ->filters([
                // 1. Filter Status Publikasi
                TernaryFilter::make('is_published')
                    ->label('Status Kuis')
                    ->placeholder('Semua Status')
                    ->trueLabel('Hanya Kuis Aktif')
                    ->falseLabel('Hanya Draf'),
                // 2. Filter Kelas (Hanya kelas milik mentor login)
                SelectFilter::make('course_class_id')
                    ->label('Filter Kelas')
                    ->relationship('courseClass', 'name', fn(Builder $query) => $query->where('created_by', Auth::id()))
                    ->searchable()
                    ->preload(),
                // 3. Filter Berdasarkan Durasi (Custom)
                Filter::make('duration_type')
                    ->label('Durasi Kuis')
                    ->form([
                        Forms\Components\Select::make('duration')
                            ->options([
                                'short' => 'Kuis Singkat (< 30 Menit)',
                                'long' => 'Kuis Lama (> 30 Menit)',
                                'unlimited' => 'Tanpa Batas Waktu',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['duration'] === 'short', fn($q) => $q->where('duration_minutes', '<', 30))
                            ->when($data['duration'] === 'long', fn($q) => $q->where('duration_minutes', '>=', 30))
                            ->when($data['duration'] === 'unlimited', fn($q) => $q->whereNull('duration_minutes'));
                    }),
                // 4. Filter Deadline Terlewat
                Filter::make('deadline_lewat')
                    ->label('Deadline Terlewat')
                    ->query(fn(Builder $query): Builder => $query->where('due_date', '<', now()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('submissions')
                    ->label('Lihat Pengumpulan')
                    ->url(fn(QuizAssignment $record) => static::getUrl('submissions', ['record' => $record->id]))
                    ->button()
                    ->color('info'),
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
            QuestionsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('created_by', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuizAssignments::route('/'),
            'create' => Pages\CreateQuizAssignment::route('/create'),
            'edit' => Pages\EditQuizAssignment::route('/{record}/edit'),
            'submissions' => Pages\ViewQuizSubmissions::route('/{record}/submissions'),  // ← tambahkan ini
        ];
    }
}
