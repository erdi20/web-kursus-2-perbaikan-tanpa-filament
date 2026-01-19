<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class AdminHomeController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Logika AJAX untuk Grafik
    //     if ($request->ajax()) {
    //         $range = $request->get('range', '7days');
    //         $labels = [];
    //         $data = [];

    //         if ($range === '1year') {
    //             // JANGAN pakai startOfYear() karena bakal kepotong di Januari.
    //             // Pakai subMonths(11) biar narik data dari 12 bulan ke belakang.
    //             $revenueData = Payment::where('transaction_status', 'settlement')
    //                 ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
    //                 ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_year, SUM(gross_amount) as total')
    //                 ->groupBy('month_year')
    //                 ->pluck('total', 'month_year');

    //             // Looping 12 bulan ke belakang
    //             for ($i = 11; $i >= 0; $i--) {
    //                 $monthObj = now()->subMonths($i);
    //                 $monthKey = $monthObj->format('Y-m');  // Untuk nyocokkin sama data DB
    //                 $label = $monthObj->format('M Y');  // Label grafik (Des 2025, Jan 2026)

    //                 $labels[] = $label;
    //                 $data[] = $revenueData[$monthKey] ?? 0;
    //             }
    //         } else {
    //             // Logika harian tetap sama
    //             $days = ($range === '30days') ? 30 : 7;
    //             $revenueData = Payment::where('transaction_status', 'settlement')
    //                 ->where('created_at', '>=', now()->subDays($days))
    //                 ->selectRaw('DATE(created_at) as date, SUM(gross_amount) as total')
    //                 ->groupBy('date')
    //                 ->pluck('total', 'date');

    //             for ($i = $days; $i >= 0; $i--) {
    //                 $date = now()->subDays($i)->format('Y-m-d');
    //                 $labels[] = now()->subDays($i)->format('d M');
    //                 $data[] = $revenueData[$date] ?? 0;
    //             }
    //         }

    //         return response()->json(['labels' => $labels, 'data' => $data]);
    //     }

    //     // Data Statistik Utama (Tetap Simple)
    //     $stats = [
    //         'total_students' => User::where('role', 'student')->count(),
    //         'total_revenue' => Payment::where('transaction_status', 'settlement')->sum('gross_amount'),
    //         'total_courses' => Course::count(),
    //         'pending_wd' => Withdrawal::where('status', 'pending')->count(),
    //         'total_mentors' => User::where('role', 'mentor')->count(),
    //     ];

    //     $recentTransactions = Payment::with(['course', 'user'])->latest()->take(5)->get();

    //     return view('admin.index', compact('stats', 'recentTransactions'));
    // }

    public function index(Request $request)
    {
        // Logika AJAX untuk Grafik
        if ($request->ajax()) {
            $range = $request->get('range', '7days');
            $labels = [];
            $data = [];

            if ($range === '1year') {
                // Ambil total komisi per bulan (bukan gross_amount!)
                $commissionData = Commission::whereHas('payment', fn($q) =>
                    $q
                        ->where('transaction_status', 'settlement')
                        ->where('created_at', '>=', now()->subMonths(11)->startOfMonth()))
                    ->selectRaw('DATE_FORMAT(payments.created_at, "%Y-%m") as month_year, SUM(commissions.amount) as total')
                    ->join('payments', 'commissions.payment_id', '=', 'payments.id')
                    ->groupBy('month_year')
                    ->pluck('total', 'month_year');

                for ($i = 11; $i >= 0; $i--) {
                    $monthObj = now()->subMonths($i);
                    $monthKey = $monthObj->format('Y-m');
                    $label = $monthObj->format('M Y');

                    $labels[] = $label;
                    $data[] = $commissionData[$monthKey] ?? 0;
                }
            } else {
                $days = ($range === '30days') ? 30 : 7;
                $commissionData = Commission::whereHas('payment', fn($q) =>
                    $q
                        ->where('transaction_status', 'settlement')
                        ->whereDate('created_at', '>=', now()->subDays($days)))
                    ->selectRaw('DATE(payments.created_at) as date, SUM(commissions.amount) as total')
                    ->join('payments', 'commissions.payment_id', '=', 'payments.id')
                    ->groupBy('date')
                    ->pluck('total', 'date');

                for ($i = $days; $i >= 0; $i--) {
                    $date = now()->subDays($i)->format('Y-m-d');
                    $labels[] = now()->subDays($i)->format('d M');
                    $data[] = $commissionData[$date] ?? 0;
                }
            }

            return response()->json(['labels' => $labels, 'data' => $data]);
        }

        // Data Statistik Utama — gunakan total komisi, bukan gross_amount
        // === HITUNG PENDAPATAN BERDASARKAN PERSENTASE SAAT INI ===
        $totalGross = Payment::where('transaction_status', 'settlement')->sum('gross_amount');

        $setting = Setting::first();
        $mentorPercent = $setting?->mentor_commission_percent ?? 70;
        $adminPercent = 100 - $mentorPercent;

        $mentorShare = ($totalGross * $mentorPercent) / 100;
        $netProfit = $totalGross - $mentorShare;

        $stats = [
            'total_gross' => $totalGross,
            'mentor_share' => $mentorShare,
            'net_profit' => $netProfit,
            'mentor_percent' => $mentorPercent,
            'admin_percent' => $adminPercent,
            // Data lain tetap
            'total_students' => User::where('role', 'student')->count(),
            'total_courses' => Course::count(),
            'pending_wd' => Withdrawal::where('status', 'pending')->count(),
            'total_mentors' => User::where('role', 'mentor')->count(),
        ];

        return view('admin.index', compact('stats', 'topCourses'));
    }
}
