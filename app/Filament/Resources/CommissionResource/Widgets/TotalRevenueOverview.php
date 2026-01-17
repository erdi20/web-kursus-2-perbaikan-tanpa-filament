<?php

namespace App\Filament\Resources\CommissionResource\Widgets;

use App\Models\Payment;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

// class TotalRevenueOverview extends BaseWidget
// {
//     protected function getStats(): array
//     {
//         // Ambil total pendapatan dari pembayaran settlement
//         $totalRevenue = Payment::where('transaction_status', 'settlement')
//             ->sum('gross_amount');

//         // Ambil persentase komisi mentor
//         $setting = Setting::first();
//         $mentorPercent = $setting?->mentor_commission_percent ?? 70;
//         $adminPercent = 100 - $mentorPercent;

//         // Hitung komisi
//         $mentorRevenue = ($totalRevenue * $mentorPercent) / 100;
//         $adminRevenue = ($totalRevenue * $adminPercent) / 100;

//         return [
//             Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
//                 ->description('Semua kursus')
//                 ->descriptionIcon('heroicon-m-arrow-trending-up', 'text-green-500')
//                 ->color('primary'),
//             Stat::make('Pendapatan dari semua kursus', 'Rp ' . number_format($mentorRevenue, 0, ',', '.'))
//                 ->description($mentorPercent . '% dari total')
//                 ->descriptionIcon('heroicon-m-user-group', 'text-blue-500')
//                 ->color('success'),
//             Stat::make('Pendapatan Web', 'Rp ' . number_format($adminRevenue, 0, ',', '.'))
//                 ->description($adminPercent . '% dari total')
//                 ->descriptionIcon('heroicon-m-building-office', 'text-purple-500')
//                 ->color('info'),
//         ];
//     }
// }
class TotalRevenueOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue = Payment::where('transaction_status', 'settlement')->sum('gross_amount');
        $setting = Setting::first();
        $mentorPercent = $setting?->mentor_commission_percent ?? 70;

        $mentorRevenue = ($totalRevenue * $mentorPercent) / 100;
        $adminRevenue = $totalRevenue - $mentorRevenue;

        return [
            Stat::make('Gross Revenue', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total perputaran uang')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8])  // Sparkline visual
                ->color('primary'),
            Stat::make('Mentor Share', 'Rp ' . number_format($mentorRevenue, 0, ',', '.'))
                ->description($mentorPercent . '% hak mentor')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Net Profit Web', 'Rp ' . number_format($adminRevenue, 0, ',', '.'))
                ->description((100 - $mentorPercent) . '% keuntungan bersih')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
        ];
    }
}
