<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ReviewController extends Controller
{
    // public function store(Request $request, string $classId)
    // {
    //     $request->validate([
    //         'review' => 'required|string|max:1000',
    //     ]);

    //     $enrollment = ClassEnrollment::where('class_id', $classId)
    //         ->where('student_id', Auth::id())
    //         ->firstOrFail();

    //     $enrollment->update(['review' => $request->review]);
    //     return redirect()
    //         ->route('kelas', $classId)
    //         ->with('success', 'Terima kasih atas ulasan Anda!');
    // }
    // app/Http/Controllers/ReviewController.php

    public function store(Request $request, string $classId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
        ]);

        $enrollment = ClassEnrollment::where('class_id', $classId)
            ->where('student_id', Auth::id())
            ->firstOrFail();

        $enrollment->update([
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return redirect()
            ->route('kelas', $classId)
            ->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
