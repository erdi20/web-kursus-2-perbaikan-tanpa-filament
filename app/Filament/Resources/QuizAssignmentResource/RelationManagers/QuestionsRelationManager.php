<?php

namespace App\Filament\Resources\QuizAssignmentResource\RelationManagers;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Soal Kuis')
                    ->description('Tulis pertanyaan dan atur bobot nilainya.')
                    ->icon('heroicon-o-question-mark-circle')
                    ->schema([
                        Textarea::make('question_text')
                            ->label('Pertanyaan')
                            ->required()
                            ->columnSpanFull(),
                            // ->toolbarButtons([
                            //     'bold', 'italic', 'link', 'bulletList', 'orderedList', 'uploadImage'
                            // ]),
                        TextInput::make('points')
                            ->label('Bobot Nilai (Poin)')
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(100)
                            ->required()
                            ->helperText('Berapa poin yang diberikan jika jawaban benar?')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                Section::make('Opsi Jawaban')
                    ->description('Isi minimal 3 opsi. Opsi D bersifat opsional. Anda bisa menyisipkan gambar atau format teks kaya.')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                RichEditor::make('option_a')
                                    ->label('A')
                                    ->required()
                                    ->placeholder('Isi opsi jawaban A...')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'uploadImage']),
                                RichEditor::make('option_b')
                                    ->label('B')
                                    ->required()
                                    ->placeholder('Isi opsi jawaban B...')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'uploadImage']),
                                RichEditor::make('option_c')
                                    ->label('C')
                                    ->required()
                                    ->placeholder('Isi opsi jawaban C...')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'uploadImage']),
                                RichEditor::make('option_d')
                                    ->label('D (Opsional)')
                                    ->nullable()
                                    ->placeholder('Isi opsi jawaban D (jika ada)...')
                                    ->toolbarButtons(['bold', 'italic', 'link', 'uploadImage']),
                            ])
                            ->columns(2),
                    ]),
                Section::make('Kunci Jawaban')
                    ->description('Pilih satu opsi yang merupakan jawaban benar.')
                    ->icon('heroicon-o-key')
                    ->schema([
                        Select::make('correct_option')
                            ->label('Jawaban Benar')
                            ->options([
                                'A' => 'A',
                                'B' => 'B',
                                'C' => 'C',
                                'D' => 'D',
                            ])
                            ->native(false)
                            ->searchable(false)
                            ->required()
                            ->helperText('Pastikan opsi yang dipilih telah diisi di atas.'),
                    ])
                    ->columns(1),
            ]);
    }

    // protected function getRecordTitle(Model $record): string
    // {
    //     // Ambil teks pertanyaan, lalu hapus semua tag HTML
    //     return strip_tags($record->question_text);
    // }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question_text')
            ->columns([
                Tables\Columns\TextColumn::make('question_text')
                    ->label('Soal')
                    ->formatStateUsing(fn($state) => strip_tags($state))
                    ->limit(50),
                Tables\Columns\TextColumn::make('correct_option')
                    ->label('Jawaban Benar')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('points')
                    ->label('Poin')
                    ->alignCenter(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Soal'),
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
