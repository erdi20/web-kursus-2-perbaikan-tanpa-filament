<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\Commission;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;

class HomeMentorController extends Controller
{
    // public function index()
    // {
    //     $mentorId = auth()->id();
    //     $courses = Course::where('created_by', $mentorId)->get();
    //     $courseIds = $courses->pluck('id');

    //     $successfulPayments = Payment::whereIn('course_id', $courseIds)
    //         ->whereIn('transaction_status', ['settlement', 'capture'])
    //         ->get();

    //     $stats = [
    //         'total_students' => ClassEnrollment::whereIn('class_id', function ($query) use ($courseIds) {
    //             $query->select('id')->from('course_classes')->whereIn('course_id', $courseIds);
    //         })->count(),
    //         'total_revenue' => $successfulPayments->sum('gross_amount'),
    //         'active_courses' => $courses->where('status', 'open')->count(),
    //         'avg_rating' => round(ClassEnrollment::whereIn('class_id', function ($query) use ($courseIds) {
    //             $query->select('id')->from('course_classes')->whereIn('course_id', $courseIds);
    //         })->whereNotNull('rating')->avg('rating') ?? 0, 1),
    //     ];

    //     $popularCourses = $courses->map(function ($course) use ($successfulPayments) {
    //         $coursePayments = $successfulPayments->where('course_id', $course->id);
    //         $course->students_count = $coursePayments->unique('student_id')->count();
    //         $course->revenue = $coursePayments->sum('gross_amount');
    //         return $course;
    //     })->sortByDesc('revenue')->take(5);

    //     $recentStudents = ClassEnrollment::with(['user', 'courseClass'])
    //         ->whereIn('class_id', function ($query) use ($courseIds) {
    //             $query->select('id')->from('course_classes')->whereIn('course_id', $courseIds);
    //         })
    //         ->latest('enrolled_at')
    //         ->take(5)
    //         ->get();

    //     return view('mentor.index', compact('stats', 'popularCourses', 'recentStudents'));
    // }

    // Fungsi API untuk Data Grafik Dinamis
    // public function getChartData(Request $request)
    // {
    //     $range = $request->get('range', '7days');  // default 7 hari
    //     $mentorId = auth()->id();
    //     $courseIds = Course::where('created_by', $mentorId)->pluck('id');

    //     $labels = [];
    //     $data = [];

    //     if ($range == '1year') {
    //         // Per Bulan selama setahun
    //         for ($i = 11; $i >= 0; $i--) {
    //             $month = now()->subMonths($i);
    //             $labels[] = $month->translatedFormat('M Y');
    //             $data[] = Payment::whereIn('course_id', $courseIds)
    //                 ->whereIn('transaction_status', ['settlement', 'capture'])
    //                 ->whereMonth('settlement_at', $month->month)
    //                 ->whereYear('settlement_at', $month->year)
    //                 ->sum('gross_amount');
    //         }
    //     } else {
    //         // Per Hari (7 hari atau 30 hari)
    //         $days = ($range == '30days') ? 29 : 6;
    //         for ($i = $days; $i >= 0; $i--) {
    //             $date = now()->subDays($i);
    //             $labels[] = $date->translatedFormat('d M');
    //             $data[] = Payment::whereIn('course_id', $courseIds)
    //                 ->whereIn('transaction_status', ['settlement', 'capture'])
    //                 ->whereDate('settlement_at', $date->format('Y-m-d'))
    //                 ->sum('gross_amount');
    //         }
    //     }

    //     return response()->json(['labels' => $labels, 'data' => $data]);
    // }

    public function index()
    {
        $mentorId = auth()->id();
        $courses = Course::where('created_by', $mentorId)->get();
        $courseIds = $courses->pluck('id');

        // ✅ Ambil komisi yang sukses (bukan payment gross_amount)
        $commissions = Commission::with('payment')
            ->where('mentor_id', $mentorId)
            ->whereHas('payment', fn($q) => $q->whereIn('transaction_status', ['settlement', 'capture']))
            ->get();

        $stats = [
            'total_students' => ClassEnrollment::whereIn('class_id', function ($query) use ($courseIds) {
                $query->select('id')->from('course_classes')->whereIn('course_id', $courseIds);
            })->count(),
            // ✅ Ganti: total_revenue = jumlah komisi
            'total_revenue' => $commissions->sum('amount'),
            'active_courses' => $courses->where('status', 'open')->count(),
            'avg_rating' => round(ClassEnrollment::whereIn('class_id', function ($query) use ($courseIds) {
                $query->select('id')->from('course_classes')->whereIn('course_id', $courseIds);
            })->whereNotNull('rating')->avg('rating') ?? 0, 1),
        ];

        // ✅ Hitung revenue per kursus berdasarkan komisi
        $popularCourses = $courses->map(function ($course) use ($commissions) {
            $courseCommissions = $commissions->filter(fn($c) => $c->payment->course_id == $course->id);
            $course->students_count = $courseCommissions->unique('payment.student_id')->count();
            $course->revenue = $courseCommissions->sum('amount');  // ✅ gunakan amount dari commissions
            return $course;
        })->sortByDesc('revenue')->take(5);

        $recentStudents = ClassEnrollment::with(['user', 'courseClass'])
            ->whereIn('class_id', function ($query) use ($courseIds) {
                $query->select('id')->from('course_classes')->whereIn('course_id', $courseIds);
            })
            ->latest('enrolled_at')
            ->take(5)
            ->get();

        return view('mentor.index', compact('stats', 'popularCourses', 'recentStudents'));
    }

    public function getChartData(Request $request)
    {
        $range = $request->get('range', '7days');
        $mentorId = auth()->id();

        $labels = [];
        $data = [];

        if ($range == '1year') {
            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $labels[] = $month->translatedFormat('M Y');
                $data[] = Commission::where('mentor_id', $mentorId)
                    ->whereHas('payment', fn($q) =>
                        $q
                            ->whereIn('transaction_status', ['settlement', 'capture'])
                            ->whereMonth('settlement_at', $month->month)
                            ->whereYear('settlement_at', $month->year))
                    ->sum('amount');  // ✅ komisi, bukan gross_amount
            }
        } else {
            $days = ($range == '30days') ? 29 : 6;
            for ($i = $days; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->translatedFormat('d M');
                $data[] = Commission::where('mentor_id', $mentorId)
                    ->whereHas('payment', fn($q) =>
                        $q
                            ->whereIn('transaction_status', ['settlement', 'capture'])
                            ->whereDate('settlement_at', $date->format('Y-m-d')))
                    ->sum('amount');  // ✅ komisi
            }
        }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }
}
