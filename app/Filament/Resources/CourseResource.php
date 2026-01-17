<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages\MaterialAttendances;
use App\Filament\Resources\CourseResource\RelationManagers\ClassesRelationManager;
use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Filament\Resources\MaterialResource\Pages\ViewMaterialAttendances;
use App\Models\Course;
use App\Models\CourseClass;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Closure;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    // --- Pengaturan Label ---
    protected static ?string $navigationLabel = 'Katalog Kursus';  // Lebih elegan untuk daftar induk

    protected static ?string $modelLabel = 'Program Kursus';

    protected static ?string $pluralModelLabel = 'Katalog Kursus';

    protected static ?string $slug = 'manajemen-kursus';

    // --- Pengaturan Navigasi & Visual ---
    protected static ?string $navigationGroup = 'Manajemen Kursus';  // Menyatukan dengan Batch & Materi

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $activeNavigationIcon = 'heroicon-s-academic-cap';

    protected static ?int $navigationSort = 1;  // Start dari 1 agar angka urutan lebih natural

    // --- Pengaturan UX ---
    protected static ?string $navigationBadgeTooltip = 'Total kursus yang terpublikasi';

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
                // Kolom Utama (Main Column)
                Group::make()
                    ->schema([
                        Section::make('Detail Dasar & Status Kursus')
                            ->description('Informasi kunci kursus: Nama, Slug, Status, dan Gambar Sampul.')
                            ->columns([
                                'default' => 1,
                                'lg' => 3,  // Menggunakan kolom 3 untuk desktop
                            ])
                            ->schema([
                                Group::make()  // Grup ini mengambil 2 kolom (Nama & Slug)
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 2,
                                    ])
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nama Kursus')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                        TextInput::make('slug')
                                            ->label('URL Slug')
                                            ->helperText('Slug otomatis terisi. Ubah manual hanya jika perlu.')
                                            ->required()
                                            ->disabled()
                                            ->dehydrated()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        Select::make('status')
                                            ->label('Status Publikasi')
                                            ->options([
                                                'draft' => 'Draft (Belum Publik)',
                                                'open' => 'Dibuka (Siap Pendaftaran)',
                                                'closed' => 'Tutup Pendaftaran',
                                                'archived' => 'Diarsipkan',
                                            ])
                                            ->default('draft')
                                            ->required()
                                            ->columnSpanFull(),  // Pastikan Status mengambil satu baris penuh di bawah Nama/Slug jika perlu
                                    ]),
                                FileUpload::make('thumbnail')  // Kolom Gambar Sampul (1 kolom)
                                    ->label('Gambar Sampul (Thumbnail)')
                                    ->helperText('Rekomendasi rasio 16:9 atau 4:3.')
                                    ->disk('public')
                                    ->directory('course-thumbnails')
                                    ->image()
                                    ->imageEditor()
                                    ->deleteUploadedFileUsing(function ($file) {
                                        if ($file) {
                                            Storage::disk('public')->delete($file);
                                        }
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'lg' => 1,
                                    ]),
                            ]),
                        // Menggunakan Tabs untuk memisahkan bagian-bagian form yang lebih berat
                        Tabs::make('Kursus Detail Lanjut')
                            ->tabs([
                                // Tab 1: Deskripsi
                                Tabs\Tab::make('Konten & Deskripsi')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        RichEditor::make('short_description')
                                            ->label('Deskripsi Singkat / Teaser')
                                            ->helperText('Paragraf pembuka yang menarik perhatian pengunjung.')
                                            ->required()
                                            ->columnSpanFull(),
                                        RichEditor::make('description')
                                            ->label('Deskripsi Lengkap Kursus')
                                            ->placeholder('Jelaskan manfaat, kurikulum, dan siapa target kursus ini.')
                                            ->required()
                                            ->columnSpanFull(),
                                    ]),
                                // Tab 2: Harga & Promosi
                                Tabs\Tab::make('Harga & Promosi')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->schema([
                                        TextInput::make('price')
                                            ->label('Harga Dasar')
                                            ->numeric()
                                            ->default(400000)
                                            ->prefix('Rp')
                                            ->required()
                                            ->minValue(0),
                                        // Toggle untuk mengaktifkan Diskon - UX yang lebih baik
                                        Toggle::make('is_on_sale')
                                            ->label('Aktifkan Harga Diskon/Promosi')
                                            ->live()
                                            ->columnSpanFull(),
                                        Grid::make(2)
                                            ->hidden(fn(Get $get): bool => !$get('is_on_sale'))
                                            ->schema([
                                                TextInput::make('discount_price')
                                                    ->label('Harga Diskon')
                                                    ->helperText('Harus lebih rendah dari Harga Dasar.')
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->nullable()
                                                    ->rule(fn(Get $get, $state): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                                        $price = $get('price');
                                                        if ($get('is_on_sale') && $value !== null && $price !== null && $value >= $price) {
                                                            $fail('Harga diskon harus lebih rendah dari Harga Dasar.');
                                                        }
                                                    }),
                                                DateTimePicker::make('discount_end_date')
                                                    ->label('Diskon Berakhir Pada')
                                                    ->helperText('Tanggal dan waktu diskon akan berakhir.')
                                                    ->nullable()
                                                    ->timezone('Asia/Jakarta')
                                                    ->minDate(now())
                                                    ->required(fn(Get $get): bool => $get('is_on_sale') && $get('discount_price') !== null),
                                            ]),
                                    ]),
                                // Tab 3: Penilaian
                                Tabs\Tab::make('Pengaturan Penilaian')
                                    ->icon('heroicon-o-scale')
                                    ->schema([
                                        Section::make('Bobot Penilaian')
                                            ->description('Total bobot (Essay, Quiz, Absensi) harus berjumlah 100%.')
                                            ->columns(4)
                                            ->schema([
                                                TextInput::make('essay_weight')
                                                    ->label('Bobot Essay')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(50)
                                                    ->maxValue(100)
                                                    ->suffix('%')
                                                    ->required()
                                                    ->live(onBlur: true),
                                                TextInput::make('quiz_weight')
                                                    ->label('Bobot Quiz')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(40)
                                                    ->maxValue(100)
                                                    ->suffix('%')
                                                    ->required()
                                                    ->live(onBlur: true),
                                                TextInput::make('attendance_weight')
                                                    ->label('Bobot Absensi')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(10)
                                                    ->maxValue(100)
                                                    ->suffix('%')
                                                    ->required()
                                                    ->live(onBlur: true),
                                                // Placeholder/Info total bobot diletakkan berdampingan dengan bobot
                                                Placeholder::make('total_weight')
                                                    ->label('Total Bobot')
                                                    ->content(function (callable $get) {
                                                        $total = ($get('essay_weight') ?? 0) + ($get('quiz_weight') ?? 0) + ($get('attendance_weight') ?? 0);
                                                        $state = $total . '%';
                                                        if ($total != 100) {
                                                            return new \Illuminate\Support\HtmlString("<span class='fi-badge-danger-text text-lg font-bold'>{$state} (Harus 100%)</span>");
                                                        }
                                                        return new \Illuminate\Support\HtmlString("<span class='fi-badge-success-text text-lg font-bold'>{$state} (OK)</span>");
                                                    }),
                                                // Validasi total bobot 100% pada bobot terakhir (opsional, tapi lebih baik)
                                                TextInput::make('attendance_weight')  // diulang untuk tujuan validasi
                                                    ->hidden(true)
                                                    ->rule(fn(Get $get) => function (string $attribute, $value, Closure $fail) use ($get) {
                                                        $total = ($get('essay_weight') ?? 0) + ($get('quiz_weight') ?? 0) + ($get('attendance_weight') ?? 0);
                                                        if ($total != 100) {
                                                            $fail('Total bobot Penilaian (Essay, Quiz, Absensi) harus 100%. Saat ini: ' . $total . '%');
                                                        }
                                                    }),
                                            ]),
                                        Section::make('Kriteria Kelulusan')
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('min_attendance_percentage')
                                                    ->label('Minimal Kehadiran')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->suffix('%')
                                                    ->default(80)
                                                    ->helperText('Persentase minimal kehadiran yang harus dipenuhi peserta.'),
                                                TextInput::make('min_final_score')
                                                    ->label('Nilai Akhir Minimal untuk Lulus')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->default(70)
                                                    ->helperText('Nilai rata-rata akhir (termasuk bobot) yang harus dicapai peserta.'),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
                Hidden::make('created_by')
                    ->default(auth()->id()),
            ]);
    }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query) {
    //             // Hanya tampilkan kursus yang dibuat oleh user yang sedang login
    //             $query->where('created_by', Auth::id());
    //         })
    //         ->columns([
    //             ImageColumn::make('thumbnail')
    //                 ->label('Sampul')
    //                 ->square()
    //                 ->size(40),
    //             TextColumn::make('name')
    //                 ->label('Nama Kursus')
    //                 ->searchable()
    //                 ->sortable()
    //                 ->limit(35)
    //                 ->weight('bold'),
    //             BadgeColumn::make('status')
    //                 ->label('Status')
    //                 ->sortable()
    //                 ->colors([
    //                     'warning' => 'draft',
    //                     'success' => 'open',
    //                     'danger' => 'closed',
    //                     'secondary' => 'archived',
    //                 ]),
    //             TextColumn::make('price_display')
    //                 ->label('Harga Jual')
    //                 ->getStateUsing(function (Model $record) {
    //                     $isDiscountActive =
    //                         $record->discount_price !== null &&
    //                         ($record->discount_end_date === null || now()->lessThan($record->discount_end_date));

    //                     if ($isDiscountActive) {
    //                         return $record->discount_price;
    //                     }
    //                     return $record->price;
    //                 })
    //                 ->money('IDR')
    //                 ->color(fn(Model $record) =>
    //                     ($record->discount_price !== null && now()->lessThan($record->discount_end_date ?? now()->addDay())) ? 'danger' : 'success')  // Warna Merah jika diskon aktif
    //                 ->description(function (Model $record) {
    //                     $isDiscountActive =
    //                         $record->discount_price !== null &&
    //                         ($record->discount_end_date === null || now()->lessThan($record->discount_end_date));

    //                     if ($isDiscountActive) {
    //                         return 'Harga Normal: Rp' . number_format($record->price, 0, ',', '.');
    //                     }
    //                     return null;
    //                 })
    //                 ->sortable(),
    //             TextColumn::make('classes_count')
    //                 ->label('Jml. Kelas')
    //                 ->counts('classes')
    //                 ->alignCenter()
    //                 ->sortable()
    //                 ->toggleable(),
    //             // TextColumn::make('createdBy.name')
    //             //     ->label('Mentor')
    //             //     ->sortable()
    //             //     ->searchable()
    //             //     ->toggleable(),
    //             TextColumn::make('created_at')
    //                 ->label('Dibuat Pada')
    //                 ->date('d M Y H:i')
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             ActionGroup::make([
    //                 Tables\Actions\EditAction::make(),
    //                 Tables\Actions\DeleteAction::make(),
    //                 Tables\Actions\Action::make('create_class')
    //                     ->label('Buat Kelas')
    //                     ->color('info')
    //                     ->icon('heroicon-o-plus-circle')
    //                     ->url(fn(Course $record): string => CourseClassResource::getUrl('create') . '?course_id=' . $record->id)
    //                     ->openUrlInNewTab(false),
    //                 Tables\Actions\Action::make('view_materials')
    //                     ->label('Lihat Materi')
    //                     ->icon('heroicon-o-book-open')
    //                     ->url(fn(\App\Models\Course $record) => static::getUrl('materials', ['record' => $record->id]))
    //                     ->color('primary'),
    //                 Action::make('create_material')
    //                     ->label('Buat materi')
    //                     ->color('info')
    //                     ->icon('heroicon-o-clipboard-document-check')
    //                     ->url(fn(Course $record): string => MaterialResource::getUrl('create') . '?course_id=' . $record->id)
    //                     ->openUrlInNewTab(false),
    //             ])
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
            ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query) {
                // Hanya tampilkan kursus yang dibuat oleh user yang sedang login
                $query->where('created_by', Auth::id());
            })
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Sampul')
                    ->square()
                    ->size(40),
                TextColumn::make('name')
                    ->label('Nama Kursus')
                    ->searchable()  // Mengaktifkan pencarian nama kursus
                    ->sortable()
                    ->limit(35)
                    ->weight('bold'),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'open',
                        'danger' => 'closed',
                        'secondary' => 'archived',
                    ]),
                TextColumn::make('price_display')
                    ->label('Harga Jual')
                    ->getStateUsing(function (Model $record) {
                        $isDiscountActive =
                            $record->discount_price !== null &&
                            ($record->discount_end_date === null || now()->lessThan($record->discount_end_date));

                        return $isDiscountActive ? $record->discount_price : $record->price;
                    })
                    ->money('IDR')
                    ->color(fn(Model $record) =>
                        ($record->discount_price !== null && now()->lessThan($record->discount_end_date ?? now()->addDay())) ? 'danger' : 'success')
                    ->description(function (Model $record) {
                        $isDiscountActive =
                            $record->discount_price !== null &&
                            ($record->discount_end_date === null || now()->lessThan($record->discount_end_date));

                        return $isDiscountActive ? 'Harga Normal: Rp' . number_format($record->price, 0, ',', '.') : null;
                    })
                    ->sortable(['price']),  // Menyortir berdasarkan harga normal
                TextColumn::make('classes_count')
                    ->label('Jml. Kelas')
                    ->counts('classes')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->date('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 1. Filter Berdasarkan Status
                SelectFilter::make('status')
                    ->label('Status Kursus')
                    ->options([
                        'draft' => 'Draft',
                        'open' => 'Buka',
                        'closed' => 'Tutup',
                        'archived' => 'Arsip',
                    ]),
                // 2. Filter Kursus Berdiskon (Ternary Filter)
                TernaryFilter::make('is_discounted')
                    ->label('Sedang Diskon')
                    ->placeholder('Semua Kursus')
                    ->trueLabel('Sedang Diskon')
                    ->falseLabel('Harga Normal')
                    ->queries(
                        true: fn(Builder $query) => $query
                            ->whereNotNull('discount_price')
                            ->where(fn($q) => $q->whereNull('discount_end_date')->orWhere('discount_end_date', '>', now())),
                        false: fn(Builder $query) => $query
                            ->whereNull('discount_price')
                            ->orWhere('discount_end_date', '<=', now()),
                    ),
                // 3. Filter Berdasarkan Rentang Harga
                Filter::make('price_range')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('min_price')->numeric()->label('Harga Minimum'),
                        \Filament\Forms\Components\TextInput::make('max_price')->numeric()->label('Harga Maksimum'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['min_price'], fn($query, $value) => $query->where('price', '>=', $value))
                            ->when($data['max_price'], fn($query, $value) => $query->where('price', '<=', $value));
                    })
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('create_class')
                        ->label('Buat Kelas')
                        ->color('info')
                        ->icon('heroicon-o-plus-circle')
                        ->url(fn(Course $record): string => CourseClassResource::getUrl('create') . '?course_id=' . $record->id)
                        ->openUrlInNewTab(false),
                    Tables\Actions\Action::make('view_materials')
                        ->label('Lihat Materi')
                        ->icon('heroicon-o-book-open')
                        ->url(fn(\App\Models\Course $record) => static::getUrl('materials', ['record' => $record->id]))
                        ->color('primary'),
                    Action::make('create_material')
                        ->label('Buat materi')
                        ->color('info')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->url(fn(Course $record): string => MaterialResource::getUrl('create') . '?course_id=' . $record->id)
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
            ClassesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
            'materials' => Pages\ListCourseMaterials::route('/{record}/materials'),
            'attendances' => MaterialAttendances::route('/{record}/materials/{material}/attendances'),
        ];
    }
}
