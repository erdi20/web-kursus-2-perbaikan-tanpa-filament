<?php

namespace App\Filament\Widgets;

use App\Models\ClassEnrollment;
use App\Models\Course;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class MentorStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $mentorId = Auth::id();
        $setting = Setting::first();
        $commissionPercent = $setting?->mentor_commission_percent ?? 0;

        // Total Siswa (lewat Class -> Course)
        $totalSiswa = ClassEnrollment::whereHas('courseClass.course', function ($query) use ($mentorId) {
            $query->where('created_by', $mentorId);
        })->count();

        // Hitung Pendapatan
        $enrollments = ClassEnrollment::whereHas('courseClass.course', function ($query) use ($mentorId) {
            $query->where('created_by', $mentorId);
        })->with('courseClass.course')->get();

        $totalGross = $enrollments->sum(fn($e) => $e->courseClass->course->price ?? 0);
        $totalEarnings = ($totalGross * $commissionPercent) / 100;

        return [
            Stat::make('Total Siswa', $totalSiswa)
                ->label('Total Siswa')
                ->description('Siswa terdaftar di semua kelas')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Estimasi Pendapatan', 'IDR ' . number_format($totalEarnings, 0, ',', '.'))
                ->label('Estimasi Pendapatan')
                ->description("Komisi Anda ({$commissionPercent}%)")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
            Stat::make('Rating Rata-rata', function () use ($mentorId) {
                $avgRating = ClassEnrollment::whereHas('courseClass.course', fn($q) => $q->where('created_by', $mentorId))
                    ->whereNotNull('rating')
                    ->avg('rating') ?? 0;

                // Format menjadi 1 angka di belakang koma
                return number_format($avgRating, 1);
            })
                ->label('Rating Rata-rata')
                ->description('Feedback kepuasan siswa')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
}
