<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Closure;

class ClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'classes';

    public function form(Form $form): Form
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
                                    ->helperText('Unggah gambar representatif untuk kelas ini (misal: ilustrasi sesi, grup belajar, dll.)'),
                                // Select::make('course_id')
                                //     ->label('Kursus Induk')
                                //     ->relationship('course', 'name')
                                //     ->searchable()
                                //     ->preload()
                                //     ->required()
                                //     ->helperText('Kelas ini akan berada di bawah kursus utama yang dipilih.'),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'open' => 'success',
                        'closed' => 'warning',
                        'archived' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_quota')
                    ->label('Kuota')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->label('Tambah Kelas'),
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
}
