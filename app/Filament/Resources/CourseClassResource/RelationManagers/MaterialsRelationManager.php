<?php

namespace App\Filament\Resources\CourseClassResource\RelationManagers;

use App\Models\Material;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('material_id')
                //     ->label('Materi')
                //     ->options(Material::pluck('name', 'id'))
                //     ->searchable()
                //     ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // Izinkan reordering menggunakan kolom pivot 'order'
            // ->orderPivotBy('order')
            ->columns([
                Tables\Columns\TextColumn::make('pivot.order')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Materi')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('pivot.schedule_date')
                //     ->label('Jadwal Pertemuan')
                //     ->dateTime()
                //     ->sortable(),
                Tables\Columns\BadgeColumn::make('pivot.visibility')
                    ->label('Status')
                    ->colors([
                        'success' => 'visible',
                        'warning' => 'hidden',
                        'danger' => 'archived',  // Contoh tambahan status
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {  // Menggunakan formatStateUsing untuk tampilan yang lebih baik
                        'visible' => 'Terlihat',
                        'hidden' => 'Tersembunyi',
                        default => 'Arsip',
                    }),
            ])
            ->filters([
                // Filter by visibility
                Tables\Filters\SelectFilter::make('visibility')
                    ->label('Visibilitas')
                    ->options([
                        'visible' => 'Terlihat',
                        'hidden' => 'Tersembunyi',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    // Injeksi $livewire ke closure form
                    ->form(fn(Tables\Actions\AttachAction $action) => [
                        // --- INI YANG DIUBAH ---
                        $action
                            ->getRecordSelect()
                            ->options(function () {
                                // Ambil course_id dari CourseClass yang sedang diedit
                                $courseClassId = $this->getOwnerRecord()->id;  // Owner record adalah CourseClass
                                $courseId = \App\Models\CourseClass::find($courseClassId)->course_id;

                                // Ambil hanya material yang terkait dengan course_id tersebut
                                return Material::where('course_id', $courseId)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            // ->default(1) // Ganti ini
                            ->default(function () {  // Gunakan closure untuk mendapatkan nilai default secara dinamis
                                $courseClassId = $this->getOwnerRecord()->id;
                                // Hitung jumlah materi yang sudah ada di kelas ini
                                $currentCount = DB::table('class_materials')
                                    ->where('course_class_id', $courseClassId)
                                    ->count();
                                // Kembalikan nilai urutan berikutnya
                                return $currentCount + 1;
                            })
                            ->label('Urutan Pertemuan')
                            ->required()
                            ->columnSpan(1),
                        // Field Pivot 2 & 3
                        Forms\Components\DateTimePicker::make('schedule_date')
                            ->label('Jadwal Pertemuan')
                            ->timezone('Asia/Jakarta')
                            ->nullable()
                            ->columnSpan(1),
                        Forms\Components\Select::make('visibility')
                            ->options([
                                'visible' => 'Terlihat',
                                'hidden' => 'Tersembunyi',
                            ])
                            ->default('visible')
                            ->label('Visibilitas')
                            ->required(),
                    ])
            ])
            ->actions([
                // EditAction untuk mengedit data di tabel pivot
                Tables\Actions\EditAction::make()
                    ->form([
                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->label('Urutan Pertemuan')
                            ->required(),
                        Forms\Components\DateTimePicker::make('schedule_date')
                            ->label('Jadwal Pertemuan')
                            ->nullable(),
                        Forms\Components\Select::make('visibility')
                            ->options([
                                'visible' => 'Terlihat',
                                'hidden' => 'Tersembunyi',
                            ])
                            ->label('Visibilitas')
                            ->required(),
                    ]),
                // Ganti DeleteAction dengan DetachAction (melepaskan dari kelas, tapi Materi tetap ada)
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),  // Ganti DeleteBulkAction
                ]),
            ]);
    }
}
