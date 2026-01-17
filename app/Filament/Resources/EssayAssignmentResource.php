<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EssayAssignmentResource\RelationManagers\SubmissionsRelationManager;
use App\Filament\Resources\EssayAssignmentResource\Pages;
use App\Filament\Resources\EssayAssignmentResource\RelationManagers;
use App\Models\EssayAssignment;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class EssayAssignmentResource extends Resource
{
    protected static ?string $model = EssayAssignment::class;

    // --- Pengaturan Label ---
    protected static ?string $navigationLabel = 'Penugasan Essay';

    protected static ?string $modelLabel = 'Tugas Essay';

    protected static ?string $pluralModelLabel = 'Tugas Essay';

    protected static ?string $slug = 'evaluasi-essay';

    // --- Pengaturan Navigasi & Visual ---
    protected static ?string $navigationGroup = 'Evaluasi & Ujian';  // Grup terpisah agar rapi

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $activeNavigationIcon = 'heroicon-s-pencil-square';

    protected static ?int $navigationSort = 1;  // Urutan pertama di grup Evaluasi

    // --- Pengaturan UX ---
    protected static ?string $navigationBadgeTooltip = 'Jumlah tugas essay yang perlu dinilai';

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
                Section::make('Informasi Dasar Tugas')
                    ->description('Tentukan kelas tujuan, judul, dan batas waktu pengumpulan.')
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
                            ->label('Judul Tugas')
                            ->required()
                            ->maxLength(255)
                            ->autofocus()
                            ->columnSpanFull()
                            ->placeholder('Contoh: Analisis Kebutuhan Sistem'),
                        DateTimePicker::make('due_date')
                            ->label('Batas Waktu Pengumpulan')
                            ->timezone('Asia/Jakarta')
                            ->required()
                            ->minDate(now()->addHour())
                            ->closeOnDateSelection()
                            ->native(false),
                    ]),
                Section::make('Instruksi dan Lampiran')
                    ->description('Berikan instruksi lengkap dan file pendukung jika diperlukan.')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Instruksi Tugas')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('assignments/attachments')
                            ->placeholder('Jelaskan secara rinci apa yang harus dikerjakan oleh peserta...'),
                    ]),
                Section::make('Pengaturan Pengumpulan')
                    ->description('Atur cara peserta mengumpulkan tugas dan kapan tugas dipublikasikan.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Publikasikan segera')
                            ->default(true)
                            ->helperText('Jika dimatikan, tugas tidak akan terlihat oleh peserta.'),
                    ]),
                Hidden::make('created_by')
                    ->default(fn() => auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 2. Judul Tugas
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Tugas')
                    ->searchable()
                    ->limit(50)  // Batasi panjang teks agar tabel rapi
                    ->wrap(),
                // 3. Status Publikasi (Menggunakan Badge/Icon + Badge jika belum deadline)
                Tables\Columns\BadgeColumn::make('is_published')
                    ->label('Status Publikasi')
                    ->sortable()
                    ->getStateUsing(fn($record): string => $record->is_published ? 'Terpublikasi' : 'Draf')
                    ->colors([
                        'success' => 'Terpublikasi',
                        'warning' => 'Draf',
                    ]),
                // 4. Batas Waktu (Dengan Warna Visual untuk Deadline)
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Batas Waktu')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta')
                    ->sortable()
                    ->color(fn(string $state): string => match (true) {
                        now()->gt($state) => 'danger',  // Merah jika sudah melewati deadline
                        now()->diffInDays($state) <= 2 => 'warning',  // Kuning jika mendekati deadline (2 hari)
                        default => 'success',  // Hijau jika masih lama
                    }),
                // 6. Dibuat Oleh (Menggunakan Relasi Nama)
                Tables\Columns\TextColumn::make('creator.name')  // Asumsi relasi 'creator' ada ke Model User
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),  // Sembunyikan secara default
                // 7. Waktu Pembuatan (Hidden by default)
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->timezone('Asia/Jakarta')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 1. Filter Publikasi (Ternary Filter)
                TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->trueLabel('Terpublikasi')
                    ->falseLabel('Draf')
                    ->nullable(),
                // 2. Filter Kelas Tujuan (Select Filter Relasi)
                SelectFilter::make('course_class_id')
                    ->label('Filter Berdasarkan Kelas')
                    ->relationship('courseClass', 'name')  // Menggunakan relasi
                    ->searchable()
                    ->preload(),
                // 3. Filter Deadline (Custom Query Filter)
                Tables\Filters\Filter::make('deadline_lewat')
                    ->label('Deadline Terlewat')
                    ->query(fn(Builder $query): Builder => $query->where('due_date', '<', now()))
                    ->toggle()  // Tampilkan sebagai toggle button
                    ->default(false),  // Tidak aktif secara default
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('submissions')
                    ->label('Lihat Pengumpulan')
                    ->url(fn(EssayAssignment $record): string => static::getUrl('submissions', ['record' => $record->id]))
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
            SubmissionsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('created_by', auth()->id());  // hanya tugas yang dibuat oleh user ini
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEssayAssignments::route('/'),
            'create' => Pages\CreateEssayAssignment::route('/create'),
            'edit' => Pages\EditEssayAssignment::route('/{record}/edit'),
            'submissions' => Pages\ViewEssaySubmissions::route('/{record}/submissions'),  // ← tambahkan ini
        ];
    }
}
