<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array  // ✅ protected, bukan public
    {
        if (!auth()->user()->isAdmin()) {
            return [];  // ← jangan tampilkan apa-apa jika bukan admin
        }
        return [
            Stat::make('Total Mentor', User::where('role', 'mentor')->count())
                ->icon('heroicon-o-user-group')
                ->color('info'),
            Stat::make('Total Kursus', Course::count())
                ->icon('heroicon-o-academic-cap')
                ->color('success'),
            Stat::make('Total Kelas', CourseClass::count())
                ->icon('heroicon-o-book-open')
                ->color('primary'),
        ];
    }
}
