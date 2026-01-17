<?php

namespace App\Filament\Resources\CommissionResource\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendapatan Bulanan';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Set locale MySQL ke Bahasa Indonesia
        \Illuminate\Support\Facades\DB::statement("SET lc_time_names = 'id_ID'");

        // Menggunakan MIN(created_at) agar valid dalam GROUP BY dan SQL Mode strict
        $data = Payment::selectRaw('SUM(gross_amount) as sum, MONTHNAME(created_at) as month')
            ->where('transaction_status', 'settlement')
            ->groupBy('month')
            ->orderByRaw('MIN(created_at) ASC')  // Memperbaiki error sort
            ->limit(6)
            ->get()  // Menggunakan get() untuk kemudahan mapping
            ->pluck('sum', 'month');

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (IDR)',
                    'data' => $data->values(),
                    'fill' => 'start',
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.3,  // Membuat garis sedikit melengkung (lebih estetik)
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'line';  // Chart garis lebih cocok untuk tren keuangan
    }
}
