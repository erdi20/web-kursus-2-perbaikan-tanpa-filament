<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CourseClass;
use App\Services\GradingService;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use function Laravel\Prompts\select;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CourseClassResource\Pages;

use App\Filament\Resources\CourseClassResource\RelationManagers;
use App\Filament\Resources\CourseClassResource\RelationManagers\MaterialsRelationManager;

class CourseClassResource extends Resource
{
    protected static ?string $model = CourseClass::class;

    // --- Pengaturan Label ---
    protected static ?string $navigationLabel = 'Batch & Jadwal';  // Lebih spesifik dari sekadar "Kelas"

    protected static ?string $modelLabel = 'Jadwal Kelas';

    protected static ?string $pluralModelLabel = 'Jadwal Kelas';

    protected static ?string $slug = 'batch-kelas';

    // --- Pengaturan Navigasi & Visual ---
    protected static ?string $navigationGroup = 'Manajemen Kursus';  // Disamakan dengan CourseResource agar satu grup

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static ?string $activeNavigationIcon = 'heroicon-s-presentation-chart-bar';

    protected static ?int $navigationSort = 2;  // Berada tepat di bawah Course (1)

    // --- Pengaturan UX ---
    protected static ?string $navigationBadgeTooltip = 'Jumlah batch kelas yang sedang membuka pendaftaran';

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
                // Kolom utama untuk tata letak 2 kolom besar di desktop
                Section::make('Detail Kelas & Kursus Induk')
                    ->description('Pilih kursus induk, nama, dan deskripsi sesi kelas ini.')
                    ->schema([
                        // Grup untuk Kursus Induk dan Nama Kelas (Horizontal)
                        Group::make()
                            ->columns(2)
                            ->schema([
                                Hidden::make('course_id')
                                    ->default(function () {
                                        $courseId = app('request')->query('course_id');
                                        return is_numeric($courseId) ? (int) $courseId : null;
                                    }),
                                FileUpload::make('thumbnail')
                                    ->label('Foto Kelas (Thumbnail)')
                                    ->disk('public')
                                    ->directory('class-thumbnails')
                                    ->image()
                                    ->imageEditor()
                                    ->required(false)
                                    ->deleteUploadedFileUsing(function ($file) {
                                        if ($file) {
                                            Storage::disk('public')->delete($file);
                                        }
                                    })
                                    ->helperText('Unggah gambar representatif untuk kelas ini (misal: ilustrasi sesi, grup belajar, dll.)'),
                                Select::make('course_id')
                                    ->label('Kursus Induk')
                                    ->relationship(
                                        name: 'course',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query, Get $get) => $query->where('created_by', auth()->id())
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Kelas ini akan berada di bawah kursus utama yang dipilih.'),
                                TextInput::make('name')
                                    ->label('Nama Sesi Kelas')
                                    ->placeholder('Contoh: Sesi 1: Pengenalan PHP dan Laravel')
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus(),
                            ]),
                        // Deskripsi Kelas (Full Column Span)
                        RichEditor::make('description')  // Menggunakan RichEditor
                            ->label('Deskripsi Sesi Kelas')
                            ->placeholder('Jelaskan materi yang akan dibahas, tujuan, dan prasyarat pada sesi kelas ini.')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'strike',
                                'heading',
                                'undo',
                                'redo',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->required()
                            ->columnSpanFull(),
                    ]),
                // Bagian Pengaturan Teknis dan Publikasi
                Section::make('Pengaturan Teknis & Jadwal Pendaftaran')
                    ->description('Atur kuota peserta, periode pendaftaran, dan status kelas.')
                    ->columns([
                        'default' => 1,
                        'lg' => 3,  // Menggunakan 3 kolom
                    ])
                    ->schema([
                        // Field 1: Kuota Maksimum
                        TextInput::make('max_quota')
                            ->label('Kuota Maksimum Peserta')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->inputMode('numeric')
                            ->helperText('Jumlah maksimum peserta yang dapat mendaftar.'),
                        // Field 2 & 3: Periode Pendaftaran (Dikelompokkan secara visual)
                        Group::make()
                            ->columnSpan(2)  // Mengambil 2 kolom dari 3 kolom
                            ->columns(2)
                            ->schema([
                                // Pastikan tidak ada konflik seconds()
                                DateTimePicker::make('enrollment_start')
                                    ->label('Mulai Pendaftaran')
                                    // Coba hapus ->seconds(false) atau set ke true
                                    ->seconds(true)  // <-- UBAH KE TRUE ATAU HAPUS SAJA
                                    ->required()
                                    ->timezone('Asia/Jakarta')
                                    ->live(onBlur: true),
                                DateTimePicker::make('enrollment_end')
                                    ->label('Akhir Pendaftaran')
                                    // Coba hapus ->seconds(false) atau set ke true
                                    ->seconds(true)  // <-- UBAH KE TRUE ATAU HAPUS SAJA
                                    ->required()
                                    ->timezone('Asia/Jakarta')
                                    ->minDate(fn(Get $get) => $get('enrollment_start') ?? now())
                                    // Rule validasi tetap dipertahankan
                                    ->rule(fn(Get $get, $state): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                        $start = $get('enrollment_start');
                                        if ($start && $value && $value <= $start) {
                                            $fail('Akhir Pendaftaran harus setelah Mulai Pendaftaran.');
                                        }
                                    }),
                            ]),
                    ]),
                // Bagian Status Publikasi (Dapat dipisah ke Section sendiri jika banyak pengaturan)
                Section::make('Status Kelas')
                    ->columns(1)
                    ->schema([
                        Select::make('status')
                            ->label('Status Kelas')
                            ->options([
                                'draft' => 'Draft (Belum Ditampilkan/Diuji)',
                                'open' => 'Open (Pendaftaran Dibuka)',
                                'closed' => 'Closed (Pendaftaran Ditutup/Selesai)',
                                'archived' => 'Archived (Diarsipkan)',
                            ])
                            ->default('draft')
                            ->required()
                            ->helperText('Atur apakah kelas ini siap untuk didaftarkan oleh peserta.'),
                    ]),
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
                TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable()  // Pencarian berdasarkan nama kelas
                    ->sortable()
                    ->limit(35),
                TextColumn::make('course.name')
                    ->label('Kursus Induk')
                    ->sortable()
                    ->searchable(),  // Pencarian berdasarkan kursus induk
                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'open',
                        'danger' => 'closed',
                        'info' => 'archived',
                    ]),
                TextColumn::make('max_quota')
                    ->label('Kuota Maks.')
                    ->numeric()
                    ->sortable(),
                // Tambahkan kolom jumlah siswa terdaftar (opsional tapi sangat berguna)
                TextColumn::make('enrollments_count')
                    ->label('Terisi')
                    ->counts('enrollments')
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Mentor')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                // 1. Filter berdasarkan Kursus Induk (Hanya menampilkan kursus milik mentor tsb)
                SelectFilter::make('course_id')
                    ->label('Pilih Kursus')
                    ->relationship('course', 'name', fn(Builder $query) => $query->where('created_by', Auth::id()))
                    ->searchable()
                    ->preload(),
                // 2. Filter berdasarkan Status Kelas
                SelectFilter::make('status')
                    ->label('Status Kelas')
                    ->options([
                        'draft' => 'Draft',
                        'open' => 'Buka (Open)',
                        'closed' => 'Tutup (Closed)',
                        'archived' => 'Arsip',
                    ]),
                // 3. Filter Kuota Penuh (Ternary Filter)
                TernaryFilter::make('is_full')
                    ->label('Kuota Penuh')
                    ->placeholder('Semua Kelas')
                    ->trueLabel('Hanya Kelas Penuh')
                    ->falseLabel('Masih Ada Kuota')
                    ->queries(
                        true: fn(Builder $query) => $query->whereColumn('enrollments_count', '>=', 'max_quota'),
                        false: fn(Builder $query) => $query->whereColumn('enrollments_count', '<', 'max_quota'),
                    )
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\Action::make('attendances')
                        ->label('Lihat Absensi')
                        ->icon('heroicon-o-calendar-days')
                        ->url(fn(\App\Models\CourseClass $record) => static::getUrl('attendances', ['record' => $record->id]))
                        ->color('primary'),
                    Tables\Actions\Action::make('students')
                        ->label('Lihat Siswa')
                        ->icon('heroicon-o-users')
                        ->url(fn(\App\Models\CourseClass $record) => static::getUrl('students', ['record' => $record->id]))
                        ->color('info'),
                    Tables\Actions\Action::make('recalculate_grades')
                        ->label('Hitung Ulang Semua Nilai')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (CourseClass $record) {
                            $enrollments = $record->enrollments()->get();

                            if ($enrollments->isEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Tidak ada siswa terdaftar di kelas ini')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $service = app(GradingService::class);

                            foreach ($enrollments as $enrollment) {
                                $service->updateEnrollmentGrade($enrollment);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Nilai berhasil dihitung ulang untuk ' . $enrollments->count() . ' siswa')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Tindakan Lanjutan'),
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
            MaterialsRelationManager::class,
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
            'index' => Pages\ListCourseClasses::route('/'),
            'create' => Pages\CreateCourseClass::route('/create'),
            'edit' => Pages\EditCourseClass::route('/{record}/edit'),
            'attendances' => Pages\ViewClassAttendances::route('/{record}/attendances'),
            'students' => Pages\ViewClassStudents::route('/{record}/students'),
        ];
    }
}
