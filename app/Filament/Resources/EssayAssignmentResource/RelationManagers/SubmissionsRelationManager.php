<?php

namespace App\Filament\Resources\EssayAssignmentResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('answer_text')
                    ->label('Jawaban Siswa')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('score')
                    ->label('Nilai')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Textarea::make('feedback')
                    ->label('Feedback')
                    ->rows(3),
                Forms\Components\Toggle::make('is_graded')
                    ->label('Sudah Dinilai')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('answer_text')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('score')
                    ->badge()
                    ->color(fn($record) => match (true) {
                        $record->score >= 80 => 'success',
                        $record->score >= 60 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\IconColumn::make('is_graded')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
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
