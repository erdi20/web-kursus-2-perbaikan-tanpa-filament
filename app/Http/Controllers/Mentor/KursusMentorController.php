<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KursusMentorController extends Controller
{
    public function index()
    {
        $mentorId = auth()->id();

        // 1. Ambil semua kursus milik mentor
        $courses = Course::where('created_by', $mentorId)
            ->latest()
            ->get();

        $courseIds = $courses->pluck('id');

        // 2. Hitung Total Siswa Unik (Menghindari double count jika siswa ikut > 1 kelas)
        $totalStudentsCount = ClassEnrollment::whereIn('class_id', function ($query) use ($courseIds) {
            $query
                ->select('id')
                ->from('course_classes')
                ->whereIn('course_id', $courseIds);
        })->distinct('student_id')->count();

        // 3. Ambil pendaftaran terbaru (Jika ingin ditampilkan di halaman ini)
        $recentStudents = ClassEnrollment::with(['user', 'courseClass'])
            ->whereIn('class_id', function ($query) use ($courseIds) {
                $query
                    ->select('id')
                    ->from('course_classes')
                    ->whereIn('course_id', $courseIds);
            })
            ->latest('enrolled_at')
            ->take(5)
            ->get();

        // Kirim semua variabel ke view
        return view('mentor.kursus.index', compact('courses', 'totalStudentsCount', 'recentStudents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return \view('mentor.kursus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'required' => ':attribute wajib diisi, Bro.',
            'lt' => 'Harga diskon harus lebih rendah dari harga normal.',
            'after' => 'Tanggal berakhir diskon nggak boleh masa lalu.',
        ];

        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'short_description' => 'required',
            'thumbnail' => 'required|image|mimes:jpg,png,jpeg|max:2048',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|lt:price',
            'discount_end_date' => 'nullable|date|after:today',  // Gunakan after:today agar lebih aman
            'status' => 'required|in:draft,open,closed',
            'essay_weight' => 'required|numeric|min:0|max:100',  // WAJIB ADA DI SINI
            'quiz_weight' => 'required|numeric|min:0|max:100',
            'attendance_weight' => 'required|numeric|min:0|max:100',
            'min_attendance_percentage' => 'required|numeric|min:0|max:100',
            'min_final_score' => 'required|numeric|min:0|max:100',
        ], $messages);

        // Perbaikan perhitungan total bobot
        $totalWeight = (int) $validated['essay_weight']
            + (int) $validated['quiz_weight']
            + (int) $validated['attendance_weight'];

        if ($totalWeight != 100) {
            return back()->withErrors([
                'essay_weight' => 'Total bobot nilai harus 100%. Saat ini: ' . $totalWeight . '%'
            ])->withInput();
        }

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $validated['created_by'] = auth()->id();

        \App\Models\Course::create($validated);

        return redirect()->route('mentor.kursus')->with('success', 'Kursus berhasil diterbitkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('mentor.kursus.edit', compact('course'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $messages = [
            'required' => ':attribute wajib diisi, Bro.',
            'lt' => 'Harga diskon harus lebih rendah dari harga normal.',
            'after' => 'Tanggal berakhir diskon nggak boleh masa lalu.',
        ];

        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'short_description' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',  // Ganti jadi nullable
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|lt:price',
            'discount_end_date' => 'nullable|date|after:today',
            'status' => 'required|in:draft,open,closed',
            'essay_weight' => 'required|numeric|min:0|max:100',  // Tambahkan ini
            'quiz_weight' => 'required|numeric|min:0|max:100',
            'attendance_weight' => 'required|numeric|min:0|max:100',
            'min_attendance_percentage' => 'required|numeric|min:0|max:100',
            'min_final_score' => 'required|numeric|min:0|max:100',
        ], $messages);

        // Cek total bobot
        $totalWeight = (int) $validated['essay_weight'] + (int) $validated['quiz_weight'] + (int) $validated['attendance_weight'];
        if ($totalWeight != 100) {
            return back()->withErrors(['essay_weight' => 'Total bobot harus 100%. Sekarang: ' . $totalWeight . '%'])->withInput();
        }

        // Handle thumbnail (Hanya update jika ada file baru)
        if ($request->hasFile('thumbnail')) {
            // Opsional: Hapus file lama dari storage agar tidak menumpuk
            if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        } else {
            // Jika tidak upload baru, tetap pakai thumbnail yang lama
            unset($validated['thumbnail']);
        }

        // Update data
        $course->update($validated);

        return redirect()->route('mentor.kursus')->with('success', 'Kursus berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        // 1. Hapus file thumbnail dari storage
        if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        // 2. Hapus data dari database
        $course->delete();

        return redirect()->route('mentor.kursus')->with('success', 'Kursus berhasil dihapus selamanya!');
    }

    public function manage($id)
    {
        // Ambil kursus beserta kelas dan enrollments-nya
        $course = Course::with(['classes.enrollments'])
            ->where('created_by', auth()->id())
            ->findOrFail($id);

        // Kumpulkan semua enrollment dari semua kelas di kursus ini
        $allEnrollments = $course->classes->flatMap->enrollments;

        $stats = [
            'total_students' => $allEnrollments->count(),
            'active_students' => $allEnrollments->where('status', 'active')->count(),
            'completed_students' => $allEnrollments->where('status', 'completed')->count(),
            'avg_progress' => round($allEnrollments->avg('progress_percentage') ?? 0, 1),
            'total_classes' => $course->classes->count(),
            'total_materials' => $course->materials()->count(),
        ];

        // Perbaikan: definisikan $courseIds dari kursus yang sedang di-manage
        $courseId = $course->id;

        $recentStudents = ClassEnrollment::with(['user', 'courseClass'])
            ->whereIn('class_id', function ($query) use ($courseId) {
                $query->select('id')->from('course_classes')->where('course_id', $courseId);
            })
            ->latest('enrolled_at')
            ->take(5)
            ->get();

        // Pastikan stats dikirim ke view
        return view('mentor.kursus.manage', compact('course', 'stats', 'recentStudents'));
    }

    public function classes($id)
    {
        $course = Course::findOrFail($id);

        // Asumsi nanti relasinya namanya 'classes' atau 'batches'
        // $classes = $course->classes()->latest()->get();

        return view('mentor.kelas.index', compact('course'));
    }
}
