<?php

namespace App\Filament\Resources\CourseClassResource\Pages;

use App\Filament\Resources\CourseClassResource;
use App\Models\ClassEnrollment;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;

class ViewClassStudents extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CourseClassResource::class;
    protected static string $view = 'filament.resources.course-class-resource.pages.view-class-students';

    public \App\Models\CourseClass $record;

    public function mount(\App\Models\CourseClass $record): void
    {
        $this->record = $record;
    }

    public function getHeading(): string
    {
        return 'Daftar Siswa: ' . $this->record->name;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ClassEnrollment::query()
                    ->where('class_id', $this->record->id)
                    ->with('user')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('enrolled_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y')
                    ->sortable(),
                TextColumn::make('progress_percentage')
                    ->label('Progres (%)')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('grade')
                    ->label('Nilai Akhir')
                    ->suffix('/100')
                    ->sortable(),
                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')  // completed = lulus
                    ->falseIcon('heroicon-o-x-circle')  // active/dropped = belum/tidak lulus
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn(ClassEnrollment $record) => match ($record->status) {
                        'completed' => 'Lulus',
                        'active' => 'Belum Lulus',
                        'dropped' => 'Mengundurkan Diri',
                    }),
                TextColumn::make('completed_at')
                    ->label('Tanggal Lulus')
                    ->dateTime('d M Y')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Belum Lulus',
                        'completed' => 'Lulus',
                        'dropped' => 'Mengundurkan Diri',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail')
                    ->modalHeading(fn(ClassEnrollment $record) => 'Detail Siswa: ' . $record->user->name)
                    ->modalWidth('lg')
                    ->modalContent(function (ClassEnrollment $record) {
                        $name = e($record->user->name ?? '—');
                        $email = e($record->user->email ?? '—');
                        $enrolledAt = $record->enrolled_at ? $record->enrolled_at->translatedFormat('d F Y') : '—';
                        $progress = $record->progress_percentage ?? 0;
                        $grade = $record->grade ?? 'Belum dinilai';
                        $status = match ($record->status) {
                            'completed' => 'Lulus',
                            'active' => 'Belum Lulus',
                            'dropped' => 'Mengundurkan Diri',
                            default => 'Tidak Diketahui',
                        };

                        $html = "
                            <div class=\"space-y-5 text-sm text-gray-800\">
                                <div class=\"grid grid-cols-1 gap-4 sm:grid-cols-2\">
                                    <div>
                                        <dt class=\"font-medium text-gray-600\">Nama Lengkap</dt>
                                        <dd class=\"mt-1 font-semibold\">{$name}</dd>
                                    </div>
                                    <div>
                                        <dt class=\"font-medium text-gray-600\">Email</dt>
                                        <dd class=\"mt-1 font-semibold\">{$email}</dd>
                                    </div>
                                    <div>
                                        <dt class=\"font-medium text-gray-600\">Tanggal Daftar</dt>
                                        <dd class=\"mt-1 font-semibold\">{$enrolledAt}</dd>
                                    </div>
                                    <div>
                                        <dt class=\"font-medium text-gray-600\">Status</dt>
                                        <dd class=\"mt-1 font-semibold\">{$status}</dd>
                                    </div>
                                    <div>
                                        <dt class=\"font-medium text-gray-600\">Progres Kelas</dt>
                                        <dd class=\"mt-1 font-semibold\">{$progress}%</dd>
                                    </div>
                                    <div>
                                        <dt class=\"font-medium text-gray-600\">Nilai Akhir</dt>
                                        <dd class=\"mt-1 font-semibold\">{$grade}</dd>
                                    </div>
                                </div>

                                <div>
                                    <dt class=\"font-medium text-gray-600\">Kelas</dt>
                                    <dd class=\"mt-1 font-semibold\">" . ($record->courseClass?->name ?? '—') . '</dd>
                                </div>
                            </div>
                            ';

                        return new \Illuminate\Support\HtmlString($html);
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('refresh_all')
                    ->label('Refresh Semua Siswa')
                    ->color('danger')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->action(function () {
                        $enrollments = ClassEnrollment::where('class_id', $this->record->id)->get();
                        $service = app(\App\Services\GradingService::class);

                        foreach ($enrollments as $enrollment) {
                            $enrollment->updateProgress();
                            $service->updateEnrollmentGrade($enrollment);
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Progres dan nilai ' . $enrollments->count() . ' siswa berhasil diperbarui')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('enrolled_at', 'desc');
    }
}
