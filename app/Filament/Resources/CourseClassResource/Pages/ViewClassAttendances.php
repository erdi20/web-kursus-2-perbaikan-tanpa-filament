<?php

namespace App\Filament\Resources\CourseClassResource\Pages;

use Filament\Tables;
use App\Models\Attendance;
use Filament\Tables\Table;
use App\Models\ClassMaterial;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\CourseClassResource;
use Filament\Tables\Concerns\InteractsWithTable;

class ViewClassAttendances extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = CourseClassResource::class;
    protected static string $view = 'filament.resources.course-class-resource.pages.view-class-attendances';

    public \App\Models\CourseClass $record;

    public function mount(\App\Models\CourseClass $record): void
    {
        $this->record = $record;
    }

    public function getHeading(): string
    {
        return 'Absensi Kelas: ' . $this->record->name;
    }

    public function table(Table $table): Table
    {
        // Ambil semua ClassMaterial untuk kelas ini
        $materialIds = $this->record->classMaterials()->pluck('id');

        return $table
            ->query(Attendance::query()
                ->whereIn('class_material_id', $materialIds)
                ->with(['student', 'classMaterial.material']))
            ->columns([
                TextColumn::make('classMaterial.material.name')
                    ->label('Materi / Pertemuan')
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
                    ->label('Materi')
                    ->options(
                        $this
                            ->record
                            ->classMaterials()
                            ->with('material')
                            ->get()
                            ->pluck('material.name', 'id')
                    )
                    ->multiple(),
            ])
            ->actions([
                // ✅ VIEW: lihat detail + foto besar
                Tables\Actions\ViewAction::make()
                    ->label('Lihat')
                    ->modalHeading(fn(Attendance $record) => 'Detail Absensi: ' . $record->student->name)
                    ->modalWidth('lg')
                    ->modalContent(function (Attendance $record) {
                        $studentName = e($record->student?->name ?? '—');
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
                            $photoHtml = "<img src=\"{$photoUrl}\"
                           alt=\"Foto absen\"
                           class=\"max-h-80 w-auto rounded-lg border shadow-sm\">";
                        } else {
                            $photoHtml = '<span class="text-gray-500 italic">Tidak ada foto</span>';
                        }

                        $html = "
                            <div class=\"space-y-5 text-sm text-gray-800\">

                                <div class=\"grid grid-cols-1 gap-4 sm:grid-cols-2\">
                                    <div>
                                        <dt class=\"font-medium text-gray-600\">Nama Siswa</dt>
                                        <dd class=\"mt-1 font-semibold\">{$studentName}</dd>
                                    </div>
                                    <div>
                                        <dt class=\"font-medium text-gray-600\">Materi / Pertemuan</dt>
                                        <dd class=\"mt-1 font-semibold\">{$materialName}</dd>
                                    </div>
                                    <div>
                                        <dt class=\"font-medium text-gray-600\">Tanggal Pertemuan</dt>
                                        <dd class=\"mt-1 font-semibold\">{$scheduleDate}</dd>
                                    </div>
                                    <div>
                                        <dt class=\"font-medium text-gray-600\">Waktu Absen</dt>
                                        <dd class=\"mt-1 font-semibold\">{$attendedAt}</dd>
                                    </div>
                                </div>

                                <div>
                                    <dt class=\"font-medium text-gray-600\">Foto Bukti Absensi</dt>
                                    <dd class=\"mt-2\">{$photoHtml}</dd>
                                </div>

                            </div>
                            ";
                        return new HtmlString($html);
                    }),
                // ✅ EDIT: hapus foto (atau reset absen)
                Tables\Actions\Action::make('delete_photo')
                    ->label('Hapus Foto')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Foto Absensi?')
                    ->modalDescription('Foto akan dihapus permanen dari storage. Data absen tetap ada, tapi tanpa bukti foto.')
                    ->action(function (Attendance $record) {
                        // Hapus file dari storage
                        if ($record->photo_path && Storage::disk('public')->exists($record->photo_path)) {
                            Storage::disk('public')->delete($record->photo_path);
                        }

                        // Kosongkan kolom photo_path
                        $record->update(['photo_path' => null]);

                        \Filament\Notifications\Notification::make()
                            ->title('Foto absensi berhasil dihapus')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('classMaterial.schedule_date', 'desc');
    }
}
