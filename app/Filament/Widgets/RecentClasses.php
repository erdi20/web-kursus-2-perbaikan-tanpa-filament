<?php

namespace App\Filament\Widgets;

use App\Models\CourseClass;
use App\Services\GradingService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\CourseClassResource; // Pastikan import Resource kamu
use Filament\Notifications\Notification;

class RecentClasses extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Kelas Terbaru Saya';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CourseClass::where('created_by', Auth::id())
                    ->with('course')
                    ->latest('id')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelas'),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Kursus'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'open' => 'success',
                        'closed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'open' => 'Terbuka',
                        'closed' => 'Tertutup',
                        default => $state,
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    // Tombol Edit - Diarahkan langsung ke Resource Edit
                    Tables\Actions\EditAction::make()
                        ->url(fn (CourseClass $record): string => CourseClassResource::getUrl('edit', ['record' => $record])),

                    // Tombol Lihat Absensi
                    Action::make('attendances')
                        ->label('Lihat Absensi')
                        ->icon('heroicon-o-calendar-days')
                        ->url(fn (CourseClass $record): string => CourseClassResource::getUrl('attendances', ['record' => $record]))
                        ->color('primary'),

                    // Tombol Lihat Siswa
                    Action::make('students')
                        ->label('Lihat Siswa')
                        ->icon('heroicon-o-users')
                        ->url(fn (CourseClass $record): string => CourseClassResource::getUrl('students', ['record' => $record]))
                        ->color('info'),

                    // Hitung Ulang Nilai
                    Action::make('recalculate_grades')
                        ->label('Hitung Ulang Semua Nilai')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Hitung Ulang Nilai')
                        ->modalDescription('Apakah Anda yakin ingin menghitung ulang nilai untuk semua siswa di kelas ini?')
                        ->action(function (CourseClass $record) {
                            $enrollments = $record->enrollments()->get();

                            if ($enrollments->isEmpty()) {
                                Notification::make()
                                    ->title('Tidak ada siswa terdaftar')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            $service = app(GradingService::class);
                            foreach ($enrollments as $enrollment) {
                                $service->updateEnrollmentGrade($enrollment);
                            }

                            Notification::make()
                                ->title('Nilai berhasil dihitung ulang')
                                ->body('Berhasil memperbarui ' . $enrollments->count() . ' data siswa.')
                                ->success()
                                ->send();
                        }),

                    // Tombol Hapus
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Tindakan Lanjutan'),
            ]);
    }
}
