<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // app/Http/Controllers/HomeController.php

    // public function index()
    // {
    //     $sliders = \App\Models\Slider::where('is_active', true)
    //         ->orderBy('order')
    //         ->take(5)
    //         ->get();

    //     $topRatedCourses = Course::query()
    //         // Kita gunakan relasi 'enrollments' (HasManyThrough)
    //         ->withAvg('enrollments as avg_rating', 'rating')
    //         ->withCount('enrollments as review_count')
    //         ->whereHas('enrollments')
    //         ->orderBy('avg_rating', 'desc')
    //         ->orderBy('review_count', 'desc')
    //         ->limit(6)
    //         ->get();  // Jangan batasi kolom dengan get(['id',...]) dulu untuk testing

    //     return view('dashboard', compact('sliders', 'topRatedCourses'));
    // }
    // ---------------------------------

    // public function index()
    // {
    //     $sliders = \App\Models\Slider::where('is_active', true)
    //         ->orderBy('order')
    //         ->take(5)
    //         ->get();

    //     //  Kursus TERBAIK: berdasarkan rating
    //     $topRatedCourses = Course::query()
    //         ->withAvg('enrollments as avg_rating', 'rating')
    //         ->withCount('enrollments as review_count')
    //         ->whereHas('enrollments', fn($q) => $q->whereNotNull('rating'))
    //         ->orderBy('avg_rating', 'desc')
    //         ->orderBy('review_count', 'desc')
    //         ->limit(6)
    //         ->get();

    //     //  Kursus POPULER: berdasarkan jumlah pendaftar
    //     $popularCourses = Course::query()
    //         ->withCount('enrollments as enrollment_count')  // hitung total pendaftar
    //         ->with('user')  // untuk nama mentor
    //         ->orderBy('enrollment_count', 'desc')
    //         ->orderBy('created_at', 'desc')  // jika jumlah sama, pilih yang terbaru
    //         ->limit(3)  // sesuai tampilan Anda (3 kursus)
    //         ->get();
    //     //  Kursus Acak (10 data, kecuali yang sudah di "Terbaik" & "Populer")
    //     $excludeIds = $topRatedCourses->pluck('id')->merge($popularCourses->pluck('id'))->unique()->toArray();

    //     $randomCourses = Course::query()
    //         ->with('user')
    //         ->whereNotIn('id', $excludeIds)
    //         ->inRandomOrder()
    //         ->limit(10)
    //         ->get();
    //     //  Testimoni Global: ambil dari semua class_enrollments
    //     $testimonials = \App\Models\ClassEnrollment::query()
    //         ->whereNotNull('review')
    //         ->where('review', '<>', '')
    //         ->where('rating', '>=', 4)  // hanya rating bagus (4-5)
    //         ->with([
    //             'user' => fn($q) => $q->select('id', 'name'),
    //             'courseClass.course' => fn($q) => $q->select('id', 'name')
    //         ])
    //         ->orderBy('rating', 'desc')
    //         ->orderBy('completed_at', 'desc')
    //         ->limit(4)  // sesuaikan dengan grid-cols-2 → 4 item
    //         ->get(['student_id', 'class_id', 'review', 'rating', 'completed_at']);

    //     $faqs = \App\Models\Faq::where('is_active', true)
    //         ->orderBy('order')
    //         ->get();
    //     return view('dashboard', compact('sliders', 'topRatedCourses', 'popularCourses', 'randomCourses', 'testimonials', 'faqs'));
    // }

    // -----------------------------
    public function index()
    {
        $setting = \App\Models\Setting::first();
        $sliders = \App\Models\Slider::where('is_active', true)
            ->orderBy('order')
            ->take(5)
            ->get();

        // Query Dasar agar DRY (Don't Repeat Yourself)
        $baseQuery = Course::query()
            ->with('user')
            ->where('status', 'open')
            ->withAvg('enrollments as avg_rating', 'rating')
            ->withCount('enrollments as review_count')
            ->withCount('enrollments as enrollment_count');

        //  Kursus TERBAIK
        $topRatedCourses = (clone $baseQuery)
            ->whereHas('enrollments', fn($q) => $q->whereNotNull('rating'))
            ->orderBy('avg_rating', 'desc')
            ->orderBy('review_count', 'desc')
            ->limit(6)
            ->get();

        //  Kursus POPULER
        $popularCourses = (clone $baseQuery)
            ->orderBy('enrollment_count', 'desc')
            ->limit(3)
            ->get();

        //  Kursus Acak
        $excludeIds = $topRatedCourses->pluck('id')->merge($popularCourses->pluck('id'))->unique()->toArray();
        $randomCourses = (clone $baseQuery)
            ->whereNotIn('id', $excludeIds)
            ->inRandomOrder()
            ->limit(9)
            ->get();

        //  Testimoni & FAQ (Tetap sama)
        $testimonials = \App\Models\ClassEnrollment::whereNotNull('review')
            ->where('rating', '>=', 4)
            ->with(['user:id,name,avatar_url', 'courseClass.course:id,name'])
            ->latest()
            ->limit(6)
            ->get();

        $faqs = \App\Models\Faq::where('is_active', true)->orderBy('order')->get();

        return view('dashboard', compact('setting','sliders', 'topRatedCourses', 'popularCourses', 'randomCourses', 'testimonials', 'faqs'));
    }

    public function privacyPolicy()
    {
        $setting = Setting::first();
        return view('privacy', compact('setting'));
    }

    // Di WebController atau SettingController

    public function terms()
    {
        $setting = Setting::first();
        return view('terms', compact('setting'));
    }

    public function contact()
    {
        $setting = Setting::first();
        return view('contact', compact('setting'));
    }
}
