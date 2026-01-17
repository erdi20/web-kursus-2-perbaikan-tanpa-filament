<?php

namespace App\Filament\Resources\MentorEarningsResource\Widgets;

use App\Models\Commission;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Withdrawal;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class MentorTotalEarnings extends BaseWidget
{
    protected function getStats(): array
    {
        // Hitung total pendapatan mentor yang sedang login
        $totalEarned = Commission::where('mentor_id', auth()->id())->sum('amount');
        $totalWithdrawn = Withdrawal::where('mentor_id', auth()->id())
            ->where('status', 'completed')  // ← hanya yang sudah SELESAI
            ->sum('amount');
        $availableBalance = $totalEarned - $totalWithdrawn;

        return [
            Stat::make('Total Komisi', 'Rp ' . number_format($totalEarned, 0, ',', '.'))
                ->description('Seumur hidup')
                ->color('info')
                ->chart([7, 2, 5, 3, 8, 6, 4]),
            Stat::make('Pencairan Selesai', 'Rp ' . number_format($totalWithdrawn, 0, ',', '.'))
                ->description('Sudah diterima')
                ->color('warning')
                ->chart([7, 2, 5, 3, 8, 6, 4]),
            Stat::make('Saldo Tersedia', 'Rp ' . number_format($availableBalance, 0, ',', '.'))
                ->color('success')
                ->description('Siap untuk ditarik')
                ->descriptionIcon('heroicon-m-wallet', 'text-green-500')
                ->chart([7, 2, 5, 3, 8, 6, 4]),  // opsional: mini chart
        ];
    }
}
