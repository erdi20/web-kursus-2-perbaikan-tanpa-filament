<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::Open()
            ->with('user')
            ->withAvg('enrollments as avg_rating', 'rating')
            ->withCount('enrollments as review_count')
            ->withCount('enrollments as enrollment_count')
            // Logika Pencarian
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();  // Sangat penting agar filter pencarian tidak hilang saat pindah halaman (pagination)

        return view('student.course.listcourse', compact('courses'));
    }

    // ----------------

    // public function index()
    // {
    //     $courses = Course::Open()
    //         ->with('user')
    //         // Sesuaikan dengan logic dashboard kamu:
    //         ->withAvg('enrollments as avg_rating', 'rating')
    //         ->withCount('enrollments as review_count')
    //         ->withCount('enrollments as enrollment_count')
    //         ->latest()
    //         ->paginate(9);

    //     return view('student.course.listcourse', compact('courses'));
    // }

    // public function show($slug)
    // {
    //     $course = Course::where('slug', $slug)
    //         ->with('user')
    //         ->firstOrFail();

    //     // Cari kelas yang:
    //     // - status = 'open'
    //     // - jumlah peserta < max_quota
    //     $availableClass = ClassEnrollment::select('class_id')
    //         ->selectRaw('COUNT(*) as enrolled_count')
    //         ->groupBy('class_id')
    //         ->havingRaw('enrolled_count < (SELECT max_quota FROM course_classes WHERE id = class_id)')
    //         ->pluck('class_id')
    //         ->toArray();

    //     // Jika tidak ada yang penuh, ambil semua kelas open
    //     $openClasses = CourseClass::where('course_id', $course->id)
    //         ->where('status', 'open')
    //         ->pluck('id')
    //         ->toArray();

    //     // Cari kelas yang tersedia (belum penuh)
    //     $eligibleClassIds = array_intersect($openClasses, $availableClass);

    //     // Jika tidak ada kelas yang belum penuh, cari kelas open yang benar-benar kosong
    //     if (empty($eligibleClassIds)) {
    //         $emptyClasses = CourseClass::where('course_id', $course->id)
    //             ->where('status', 'open')
    //             ->whereNotIn('id', ClassEnrollment::select('class_id')->groupBy('class_id'))
    //             ->pluck('id')
    //             ->toArray();

    //         if (!empty($emptyClasses)) {
    //             $eligibleClassIds = $emptyClasses;
    //         }
    //     }
    //     $topReviews = ClassEnrollment::whereHas('courseClass', function ($query) use ($course) {
    //         $query->where('course_id', $course->id);
    //     })
    //         ->whereNotNull('review')
    //         ->where('review', '<>', '')  // gunakan '<>' bukan '!=' untuk string
    //         ->where('rating', '>=', 3)
    //         ->with([
    //             'user' => fn($q) => $q->select('id', 'name'),
    //             'courseClass' => fn($q) => $q->select('id', 'name')
    //         ])
    //         ->orderBy('rating', 'desc')
    //         ->orderBy('completed_at', 'desc')
    //         ->limit(5)
    //         ->get(['student_id', 'class_id', 'review', 'rating', 'completed_at']);

    //     // Pilih kelas pertama (ID terkecil)
    //     $selectedClassId = !empty($eligibleClassIds) ? min($eligibleClassIds) : null;

    //     // Load ulang kelas dengan relasi (untuk ditampilkan di view)
    //     $course->load([
    //         'classes' => fn($query) => $query->where('status', 'open')->orderBy('id')
    //     ]);
    //     // ✅ Cek apakah user sudah terdaftar di kursus ini
    //     $isAlreadyEnrolled = ClassEnrollment::whereHas('courseClass', function ($query) use ($course) {
    //         $query->where('course_id', $course->id);
    //     })->where('student_id', auth()->id())->exists();

    //     return view('student.course.course', compact('course', 'selectedClassId', 'isAlreadyEnrolled', 'topReviews'));
    // }

    // ---------------------------------------
    public function show($slug)
    {
        $course = Course::where('slug', $slug)
            ->with(['user'])
            ->withAvg('enrollments as avg_rating', 'rating')
            ->withCount('enrollments as enrollment_count')
            ->firstOrFail();

        // Logic pencarian kelas tersedia (Sesuai kode Anda sebelumnya)
        $openClasses = CourseClass::where('course_id', $course->id)->where('status', 'open')->pluck('id')->toArray();
        $availableClass = ClassEnrollment::select('class_id')
            ->selectRaw('COUNT(*) as enrolled_count')
            ->groupBy('class_id')
            ->havingRaw('enrolled_count < (SELECT max_quota FROM course_classes WHERE id = class_id)')
            ->pluck('class_id')
            ->toArray();

        $eligibleClassIds = array_intersect($openClasses, $availableClass);
        if (empty($eligibleClassIds)) {
            $eligibleClassIds = CourseClass::where('course_id', $course->id)
                ->where('status', 'open')
                ->whereNotIn('id', ClassEnrollment::select('class_id')->groupBy('class_id'))
                ->pluck('id')
                ->toArray();
        }

        $selectedClassId = !empty($eligibleClassIds) ? min($eligibleClassIds) : null;
        $isAlreadyEnrolled = ClassEnrollment::whereHas('courseClass', fn($q) => $q->where('course_id', $course->id))
            ->where('student_id', auth()->id())
            ->exists();

        $topReviews = ClassEnrollment::whereHas('courseClass', fn($q) => $q->where('course_id', $course->id))
            ->whereNotNull('review')
            ->where('review', '!=', '')
            ->with(['user:id,name,avatar_url', 'courseClass:id,name'])  // Tambahkan avatar_url jika ada
            ->orderBy('rating', 'desc')  // Utamakan rating bintang 5
            ->latest()
            ->limit(5)
            ->get();

        $course->load(['classes' => function ($q) {
            $q
                ->where('status', 'open')
                ->withCount('enrollments')  // Menghitung siswa yang sudah daftar di kelas tersebut
                ->orderBy('id');
        }]);

        return view('student.course.course', compact('course', 'selectedClassId', 'isAlreadyEnrolled', 'topReviews'));
    }
}
