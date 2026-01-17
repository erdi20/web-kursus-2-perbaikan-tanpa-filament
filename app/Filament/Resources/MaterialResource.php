<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages\ListCourseMaterials;
use App\Filament\Resources\MaterialResource\Pages;
use App\Filament\Resources\MaterialResource\RelationManagers;
use App\Models\Material;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    // --- Pengaturan Label ---
    protected static ?string $navigationLabel = 'Modul & Materi';  // Mencakup bab dan isi materi

    protected static ?string $modelLabel = 'Materi';

    protected static ?string $pluralModelLabel = 'Konten Pembelajaran';

    protected static ?string $slug = 'materi-pembelajaran';

    // --- Pengaturan Navigasi & Visual ---
    protected static ?string $navigationGroup = 'Manajemen Kursus';  // Disatukan dengan Katalog Kursus & Batch

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $activeNavigationIcon = 'heroicon-s-book-open';

    protected static ?int $navigationSort = 3;  // Urutan: 1. Kursus -> 2. Batch -> 3. Materi

    // --- Pengaturan UX ---
    protected static ?string $navigationBadgeTooltip = 'Total materi pembelajaran yang tersedia';

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
                // 1. SECTION: Informasi Dasar & Relasi
                Section::make('Identifikasi dan Penempatan Materi')
                    ->description('Tentukan nama materi, urutan, dan kursus induknya.')
                    ->columns(2)
                    ->schema([
                        Hidden::make('course_id')
                            ->default(function () {
                                $courseId = app('request')->query('course_id');
                                return is_numeric($courseId) ? (int) $courseId : null;
                            }),
                        // Kolom Kiri
                        Select::make('course_id')
                            ->label('Kursus Induk')
                            ->relationship(
                                name: 'course',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query, Get $get) => $query->where('created_by', auth()->id())
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('Judul Materi')
                            ->placeholder('Contoh: Struktur Data dan Array')
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),
                        Toggle::make('is_attendance_required')
                            ->label('Aktifkan Absensi untuk Materi Ini')
                            ->helperText('Jika diaktifkan, siswa harus absen saat mengakses materi ini.'),
                        // Di dalam form Attach/Edit action
                        Forms\Components\DateTimePicker::make('attendance_start')
                            ->label('Waktu Mulai Absen')
                            ->timezone('Asia/Jakarta')
                            ->seconds(false)
                            ->required(fn($get) => $get('is_attendance_required'))
                            ->columnSpan(1),
                        Forms\Components\DateTimePicker::make('attendance_end')
                            ->label('Waktu Selesai Absen')
                            ->timezone('Asia/Jakarta')
                            ->seconds(false)
                            ->required(fn($get) => $get('is_attendance_required'))
                            ->columnSpan(1),
                    ]),
                // ---
                // 2. SECTION: Konten Utama (Menggunakan Rich Editor)
                Section::make('Konten Teks Materi')
                    ->description('Tuliskan isi utama materi menggunakan editor kaya.')
                    ->schema([
                        // Mengganti Textarea biasa dengan RichEditor (Tiptap)
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Materi')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                // ---
                // 3. SECTION: Sumber Daya Tambahan (Grid 3 Kolom)
                Section::make('Lampiran dan Sumber Daya')
                    ->description('Unggah atau tautkan file tambahan yang relevan dengan materi ini.')
                    ->columns(3)
                    ->schema([
                        // Video Link
                        TextInput::make('link_video')
                            ->label('Tautan Video (YouTube/Vimeo)')
                            ->placeholder('Masukkan URL video'),
                        FileUpload::make('pdf')
                            ->label('File PDF / Dokumen')
                            ->directory('material-docs')
                            ->disk('public')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120)
                            // Otomatis hapus file lama saat file baru diupload atau dihapus dari form
                            ->deleteUploadedFileUsing(function ($file, $record) {
                                Storage::disk('public')->delete($file);
                            })
                            ->downloadable()
                            ->openable(),
                        FileUpload::make('image')
                            ->label('Gambar Ilustrasi')
                            ->directory('material-images')
                            ->disk('public')
                            ->image()
                            ->imageEditor()
                            ->maxSize(1024)
                            ->deleteUploadedFileUsing(function ($file, $record) {
                                Storage::disk('public')->delete($file);
                            })
                            ->nullable(),
                    ]),
                // 4. FIELD TERSEMBUNYI (Sistem)
                Hidden::make('created_by')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query) {
                // Hanya tampilkan kursus yang dibuat oleh user yang sedang login
                $query->where('created_by', Auth::id());
            })
            ->columns([
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Kursus')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Materi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->circular(),
                Tables\Columns\IconColumn::make('link_video')
                    ->label('Video')
                    ->options([
                        'heroicon-o-video-camera' => fn($state): bool => filled($state),
                        'heroicon-o-x-circle' => fn($state): bool => blank($state),
                    ])
                    ->colors([
                        'success' => fn($state): bool => filled($state),
                        'gray' => fn($state): bool => blank($state),
                    ]),
                Tables\Columns\IconColumn::make('pdf')
                    ->label('PDF')
                    ->options([
                        'heroicon-o-document-arrow-down' => fn($state): bool => filled($state),
                    ])
                    ->color('danger')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl. Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 1. Filter Berdasarkan Kursus (Hanya kursus milik user login)
                SelectFilter::make('course_id')
                    ->label('Filter Kursus')
                    ->relationship('course', 'name', fn(Builder $query) => $query->where('created_by', Auth::id()))
                    ->searchable()
                    ->preload(),
                // 2. Filter Berdasarkan Ketersediaan Video
                TernaryFilter::make('has_video')
                    ->label('Memiliki Video')
                    ->placeholder('Semua Materi')
                    ->trueLabel('Hanya Materi Video')
                    ->falseLabel('Tanpa Video')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('link_video'),
                        false: fn(Builder $query) => $query->whereNull('link_video'),
                    ),
                // 3. Filter Berdasarkan Ketersediaan PDF
                TernaryFilter::make('has_pdf')
                    ->label('Memiliki PDF')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNotNull('pdf'),
                        false: fn(Builder $query) => $query->whereNull('pdf'),
                    ),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('attendances')
                        ->label('Lihat Absensi')
                        ->icon('heroicon-o-calendar-days')
                        ->url(fn(Material $record) => static::getUrl('attendances', ['record' => $record->id]))
                        ->color('primary'),
                    Action::make('create_essay')
                        ->label('Buat Tugas Essay')
                        ->color('success')
                        ->icon('heroicon-o-document-text')
                        ->url(fn(Material $record): string => EssayAssignmentResource::getUrl('create') . '?material_id=' . $record->id)
                        ->openUrlInNewTab(false),  // atau true jika ingin di tab baru
                    Action::make('create_quiz')
                        ->label('Buat Tugas Quiz')
                        ->color('info')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->url(fn(Material $record): string => QuizAssignmentResource::getUrl('create') . '?material_id=' . $record->id)
                        ->openUrlInNewTab(false),
                ])
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
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
            'attendances' => Pages\ViewMaterialAttendances::route('/{record}/attendances'),
        ];
    }
}
