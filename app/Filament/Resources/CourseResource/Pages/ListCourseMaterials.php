<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Filament\Resources\EssayAssignmentResource;
use App\Filament\Resources\QuizAssignmentResource;
use App\Models\Material;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;

class ListCourseMaterials extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CourseResource::class;
    protected static string $view = 'filament.resources.course-resource.pages.list-course-materials';

    public \App\Models\Course $record;

    public function mount(\App\Models\Course $record): void
    {
        $this->record = $record;
    }

    public function getHeading(): string
    {
        return 'Materi Kursus: ' . $this->record->name;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Material::query()
                    ->where('course_id', $this->record->id)  // ✅ Hanya materi dari kursus ini
                    ->withCount('essayAssignments', 'quizAssignments')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Materi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('essay_assignments_count')
                    ->label('Jumlah Essay')
                    ->badge()
                    ->color('info'),
                TextColumn::make('quiz_assignments_count')
                    ->label('Jumlah Quiz')
                    ->badge()
                    ->color('success'),
                TextColumn::make('updated_at')
                    ->label('Terakhir Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable(),
                ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->circular(),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\EditAction::make()->url(fn($record) => \App\Filament\Resources\MaterialResource::getUrl('edit', ['record' => $record->id])),
                    Tables\Actions\Action::make('attendances')
                        ->label('Lihat Absensi')
                        ->icon('heroicon-o-calendar-days')
                        ->url(fn(Material $record) => \App\Filament\Resources\CourseResource::getUrl('attendances', [
                            'record' => $this->record->id,  // ID Course (diambil dari properti page saat ini)
                            'material' => $record->id,  // ID Material (diambil dari baris tabel)
                        ]))
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
                // Tables\Actions\EditAction::make()
                //     ->url(fn($record) => \App\Filament\Resources\MaterialResource::getUrl('edit', ['record' => $record->id])),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Buat Materi Baru')
                    ->url(fn() => \App\Filament\Resources\MaterialResource::getUrl('create') . '?course_id=' . $this->record->id)
                    ->icon('heroicon-o-plus')
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_attendance_required')
                    ->label('Butuh Absensi?')
                    ->boolean(),
            ])
            ->defaultSort('id');
    }
}
