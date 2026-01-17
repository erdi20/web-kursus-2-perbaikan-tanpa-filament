<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Models\Attendance;
use App\Models\ClassMaterial;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class MaterialAttendances extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CourseResource::class;
    protected static string $view = 'filament.resources.course-resource.pages.material-attendances';
    public $record;  // Akan berisi Model Course
    public $material;  // Akan berisi Model Material

    public function mount($record, $material): void
    {
        // Mencari data Course berdasarkan ID di URL
        $this->record = \App\Models\Course::findOrFail($record);

        // Mencari data Material berdasarkan ID di URL
        $this->material = \App\Models\Material::findOrFail($material);
    }

    public function getHeading(): string
    {
        return 'Absensi Materi: ' . $this->record->name;
    }

    public function table(Table $table): Table
    {
        // Ambil semua ClassMaterial yang menggunakan materi ini
        $classMaterialIds = ClassMaterial::where('material_id', $this->material->id)
            ->pluck('id');
        return $table
            ->query(
                Attendance::query()
                    ->whereIn('class_material_id', $classMaterialIds)
                    ->with([
                        'student',
                        'classMaterial.courseClass.course',
                        'classMaterial'
                    ])
            )
            ->columns([
                TextColumn::make('classMaterial.courseClass.name')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('classMaterial.courseClass.course.name')
                    ->label('Kursus')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('student.name')
                    ->label('Siswa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('classMaterial.schedule_date')
                    ->label('Tanggal Pertemuan')
                    ->dateTime('d M Y')
                    ->sortable(),
                ImageColumn::make('photo_path')
                    ->label('Foto Absen')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('attended_at')
                    ->label('Waktu Absen')
                    ->timezone('Asia/Jakarta')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_material_id')
                    ->label('Kelas')
                    ->options(
                        // FIX: Gunakan $this->material->id
                        ClassMaterial::where('material_id', $this->material->id)
                            ->with('courseClass')
                            ->get()
                            ->pluck('courseClass.name', 'id')
                    )
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat')
                    ->modalHeading(fn(Attendance $record) => 'Detail Absensi: ' . $record->student->name)
                    ->modalWidth('lg')
                    ->modalContent(function (Attendance $record) {
                        $studentName = e($record->student?->name ?? '—');
                        $courseName = e($record->classMaterial?->courseClass?->course?->name ?? '—');
                        $className = e($record->classMaterial?->courseClass?->name ?? '—');
                        $materialName = e($record->classMaterial?->material?->name ?? '—');
                        $scheduleDate = $record->classMaterial?->schedule_date
                            ? \Carbon\Carbon::parse($record->classMaterial->schedule_date)->translatedFormat('d F Y')
                            : '—';
                        $attendedAt = $record->attended_at
                            ? $record->attended_at->translatedFormat('d F Y, H:i')
                            : '—';

                        $photoHtml = '';
                        if ($record->photo_path && Storage::disk('public')->exists($record->photo_path)) {
                            $photoUrl = Storage::url($record->photo_path);
                            $photoHtml = "<img src=\"{$photoUrl}\" class=\"max-h-80 w-auto rounded-lg border shadow-sm\">";
                        } else {
                            $photoHtml = '<span class="text-gray-500 italic">Tidak ada foto</span>';
                        }

                        $html = "
                            <div class=\"space-y-5 text-sm text-gray-800\">
                                <div class=\"grid grid-cols-1 gap-4 sm:grid-cols-2\">
                                    <div><dt class=\"font-medium text-gray-600\">Nama Siswa</dt><dd class=\"mt-1 font-semibold\">{$studentName}</dd></div>
                                    <div><dt class=\"font-medium text-gray-600\">Kursus</dt><dd class=\"mt-1 font-semibold\">{$courseName}</dd></div>
                                    <div><dt class=\"font-medium text-gray-600\">Kelas</dt><dd class=\"mt-1 font-semibold\">{$className}</dd></div>
                                    <div><dt class=\"font-medium text-gray-600\">Materi</dt><dd class=\"mt-1 font-semibold\">{$materialName}</dd></div>
                                    <div><dt class=\"font-medium text-gray-600\">Tanggal Pertemuan</dt><dd class=\"mt-1 font-semibold\">{$scheduleDate}</dd></div>
                                    <div><dt class=\"font-medium text-gray-600\">Waktu Absen</dt><dd class=\"mt-1 font-semibold\">{$attendedAt}</dd></div>
                                </div>
                                <div>
                                    <dt class=\"font-medium text-gray-600\">Foto Bukti Absensi</dt>
                                    <dd class=\"mt-2\">{$photoHtml}</dd>
                                </div>
                            </div>
                        ";
                        return new HtmlString($html);
                    }),
                Tables\Actions\Action::make('delete_photo')
                    ->label('Hapus Foto')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Foto Absensi?')
                    ->modalDescription('Foto akan dihapus permanen. Data absen tetap ada.')
                    ->action(function (Attendance $record) {
                        if ($record->photo_path && Storage::disk('public')->exists($record->photo_path)) {
                            Storage::disk('public')->delete($record->photo_path);
                        }
                        $record->update(['photo_path' => null]);
                        \Filament\Notifications\Notification::make()
                            ->title('Foto absensi dihapus')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('attended_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            // Gunakan \Filament\Actions\Action bukan \Filament\Tables\Actions\Action
            \Filament\Actions\Action::make('back')
                ->label('Kembali ke List Materi')
                ->color('gray')
                ->icon('heroicon-o-arrow-left')
                ->url(fn() => CourseResource::getUrl('materials', ['record' => $this->record->id])),
        ];
    }
}
