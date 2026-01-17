<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\QuizAssignment;
use App\Models\EssayAssignment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\RecentClasses;
use App\Filament\Widgets\MentorOverview;
use Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\AdminStatsOverview;
use App\Filament\Widgets\MentorStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\CommissionResource\Widgets\RevenueChart;
use App\Filament\Widgets\RecentReviews;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int
    {
        return Auth::user()->isAdmin() ? 3 : 2;
    }

    public function getWidgets(): array
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return [AdminStatsOverview::class, RevenueChart::class];
        }

        if ($user->isMentor()) {
            return [
                MentorStatsOverview::class,
                RecentClasses::class,
            ];
        }

        return [];
    }
}
