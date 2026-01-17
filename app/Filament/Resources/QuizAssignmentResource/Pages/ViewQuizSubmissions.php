<?php

namespace App\Filament\Resources\QuizAssignmentResource\Pages;

use App\Filament\Resources\QuizAssignmentResource;
use App\Models\QuizAssignment;
use App\Models\QuizSubmission;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;

class ViewQuizSubmissions extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = QuizAssignmentResource::class;
    protected static string $view = 'filament.resources.quiz-assignment-resource.pages.view-quiz-submissions';

    public QuizAssignment $record;

    public function mount(QuizAssignment $record): void
    {
        if ($record->created_by !== auth()->id()) {
            abort(403);
        }
        $this->record = $record;
    }

    public function getHeading(): string
    {
        return 'Pengumpulan: ' . $this->record->title;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(QuizSubmission::query()
                ->where('quiz_assignment_id', $this->record->id)
                ->with('student'))
            ->columns([
                TextColumn::make('student.name')->label('Siswa'),
                TextColumn::make('submitted_at')->dateTime(),
                TextColumn::make('score')->badge(),
                TextColumn::make('is_graded')
                    ->badge(),
                // Tambahkan status keterlambatan jika diperlukan
                TextColumn::make('submission_status')
                    ->getStateUsing(fn(QuizSubmission $record) => $record->isLate() ? 'Terlambat' : 'Tepat Waktu')
                    ->badge()
                    ->color(fn(QuizSubmission $record) => $record->isLate() ? 'danger' : 'success')
                    ->icon(fn(QuizSubmission $record) => $record->isLate()
                        ? 'heroicon-o-clock'
                        : 'heroicon-o-check-badge')
                    ->iconPosition('before'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        \Filament\Forms\Components\TextInput::make('score')->numeric()->minValue(0),
                        \Filament\Forms\Components\Textarea::make('feedback'),
                        \Filament\Forms\Components\Toggle::make('is_graded'),
                    ]),
                Tables\Actions\ViewAction::make(),
            ]);
    }
}
