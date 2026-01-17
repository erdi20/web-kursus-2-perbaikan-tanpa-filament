<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\EssayAssignment;
use App\Models\EssaySubmission;
use App\Models\Material;
use App\Models\QuizAssignment;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $enrolledClasses = Auth::user()
            ->enrollments()
            ->with('courseClass.course', 'courseClass.CreatedBy')  // eager load relasi agar efisien
            ->get()
            ->pluck('courseClass');  // ekstrak hanya CourseClass-nya

        return view('student.class.listclass', compact('enrolledClasses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    // CourseClassController.php
    // public function show(string $id)
    // {
    //     $user = Auth::user();
    //     $class = CourseClass::with(['course', 'materialsFE'])  //  eager load materials
    //         ->where('id', $id)
    //         ->firstOrFail();
    //     $enrollment = ClassEnrollment::where('student_id', $user->id)
    //         ->where('class_id', $class->id)
    //         ->first();
    //     if (!$enrollment) {
    //         abort(403, 'Anda tidak terdaftar di kelas ini.');
    //     }
    //     return view('student.class.class', compact('class', 'enrollment'));
    // }
    // ------------------- refactor 2
    // public function show(string $id)
    // {
    //     $user = Auth::user();
    //     $class = CourseClass::with('course', 'materials')->findOrFail($id);
    //     $enrollment = ClassEnrollment::where('student_id', $user->id)
    //         ->where('class_id', $id)
    //         ->firstOrFail();
    //     // Ambil semua essay assignments untuk kelas ini
    //     $essayAssignments = EssayAssignment::where('course_class_id', $id)
    //         ->where('is_published', true)
    //         ->orderBy('due_date', 'asc')
    //         ->get();
    //     // Ambil submission user (jika ada)
    //     $userSubmissions = EssaySubmission::where('student_id', $user->id)
    //         ->whereIn('essay_assignment_id', $essayAssignments->pluck('id'))
    //         ->pluck('essay_assignment_id')
    //         ->toArray();
    //     return view('student.class.class', compact(
    //         'class', 'enrollment', 'essayAssignments', 'userSubmissions'
    //     ));
    // }
    // ------------------- refactor 3
    // public function show(string $classId)
    // {
    //     $user = Auth::user();
    //     $class = CourseClass::with('course', 'materials')->findOrFail($classId);
    //     $enrollment = ClassEnrollment::where('student_id', $user->id)
    //         ->where('class_id', $classId)
    //         ->firstOrFail();
    //     $progress = ClassEnrollment::where('class_id', $classId)
    //         ->where('student_id', Auth::id())
    //         ->first();
    //     // absen
    //     $today = now()->startOfDay();
    //     $todayMaterial = $class
    //         ->classMaterials()
    //         ->whereDate('schedule_date', $today)
    //         ->first();
    //     return view('student.class.class', compact(
    //         'class',
    //         'enrollment',
    //         'todayMaterial',
    //         'progress'
    //     ));
    // }
    // ------------------------ refactor 4
    public function show(string $classId)
    {
        $user = Auth::user();
        $class = CourseClass::with('course', 'materials')->findOrFail($classId);

        $enrollment = ClassEnrollment::where('student_id', $user->id)
            ->where('class_id', $classId)
            ->firstOrFail();
        $progress = ClassEnrollment::where('class_id', $classId)
            ->where('student_id', Auth::id())
            ->first();
        // absen
        $today = now()->startOfDay();
        $todayMaterial = $class
            ->classMaterials()
            ->whereDate('schedule_date', $today)
            ->first();

        // === TAMBAHAN: Ambil tugas yang belum dikerjakan ===
        $materialIds = $class->materials->pluck('id');

        // Ambil semua tugas yang dipublikasikan
        $essayAssignments = \App\Models\EssayAssignment::whereIn('material_id', $materialIds)
            ->where('is_published', true)
            ->with('material')
            ->get();

        $quizAssignments = \App\Models\QuizAssignment::whereIn('material_id', $materialIds)
            ->where('is_published', true)
            ->with('material')
            ->get();

        // Ambil submission user
        $userEssaySubs = \App\Models\EssaySubmission::where('student_id', $user->id)
            ->whereIn('essay_assignment_id', $essayAssignments->pluck('id'))
            ->pluck('essay_assignment_id')
            ->toArray();

        $userQuizSubs = \App\Models\QuizSubmission::where('student_id', $user->id)
            ->whereIn('quiz_assignment_id', $quizAssignments->pluck('id'))
            ->pluck('quiz_assignment_id')
            ->toArray();

        // Gabungkan dan filter yang belum dikerjakan
        $pendingTasks = [];
        foreach ($essayAssignments as $task) {
            if (!in_array($task->id, $userEssaySubs)) {
                $pendingTasks[] = (object) [
                    'title' => $task->title,
                    'type' => 'essay',
                    'material_name' => $task->material->name ?? 'Materi',
                ];
            }
        }
        foreach ($quizAssignments as $task) {
            if (!in_array($task->id, $userQuizSubs)) {
                $pendingTasks[] = (object) [
                    'title' => $task->title,
                    'type' => 'quiz',
                    'material_name' => $task->material->name ?? 'Materi',
                ];
            }
        }
        // === AKHIR TAMBAHAN ===

        return view('student.class.class', compact(
            'class',
            'enrollment',
            'todayMaterial',
            'progress',
            'pendingTasks'  // ← tambahkan ini
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // -------------------
}
