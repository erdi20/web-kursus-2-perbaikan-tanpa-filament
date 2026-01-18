<?php

use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\FaqAdminController;
use App\Http\Controllers\Admin\SettingAdminController;
use App\Http\Controllers\Admin\SliderAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\WithdrawalAdminController;
use App\Http\Controllers\Mentor\AbsensiMentorController;
use App\Http\Controllers\Mentor\EssayMentorController;
use App\Http\Controllers\Mentor\HomeMentorController;
use App\Http\Controllers\Mentor\KelasMentorController;
use App\Http\Controllers\Mentor\KeuanganMentorController;
use App\Http\Controllers\Mentor\KursusMentorController;
use App\Http\Controllers\Mentor\MateriMentorController;
use App\Http\Controllers\Mentor\QuizMentorController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseClassController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EssayController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/contact-us', [HomeController::class, 'contact'])->name('contact.us');
// web.php
Route::get('/listkursus', [CourseController::class, 'index'])->name('listkursus');
Route::middleware('auth')->group(function () {
    Route::get('/payment', function () {
        return view('student.payment');
    });
    // ---------------------
    Route::get('/detailkursus/{slug}', [CourseController::class, 'show'])->name('detailkursus');
    // ---------------------
    Route::get('listkelas', [CourseClassController::class, 'index'])->name('listkelas');  // [Route::get('/listkelas', [CourseClassController::class, 'index'])->name('listkelas'] )
    Route::get('kelas/{id}', [CourseClassController::class, 'show'])->name('kelas');  // [Route::get('/listkelas', [CourseClassController::class, 'index'])->name('listkelas'] )
    // ---------------------
    Route::get('/kelas/{classId}/materi/{materialId}', [MaterialController::class, 'show'])->name('materials.show');
    // --------------------- tugas essay
    Route::get('/kelas/{classId}/essay/{assignmentId}', [EssayController::class, 'show'])->name('essay.show');
    Route::post('/kelas/{classId}/essay/{assignmentId}/submit', [EssayController::class, 'submit'])->name('essay.submit');
    // ---------------------
    // Quiz
    Route::get('/kelas/{classId}/quiz/{assignmentId}', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/kelas/{classId}/quiz/{assignmentId}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/kelas/{classId}/quiz/{assignmentId}/result', [QuizController::class, 'result'])
        ->name('quiz.result');
    // ---------------------
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    // ---------------------
    // Absensi
    Route::post('/kelas/{classId}/absen', [AttendanceController::class, 'store'])->name('attendance.store');

    // sertifikat
    Route::get('/kelas/{classId}/sertifikat', [CertificateController::class, 'download'])->name('certificates.download');
    // ---------------------

    // review
    Route::post('/kelas/{classId}/review', [ReviewController::class, 'store'])->name('reviews.store');
    // ---------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'mentor'])->prefix('mentor')->name('mentor.')->group(function () {
    Route::get('/dashboard', [HomeMentorController::class, 'index'])->name('dashboardmentor');
    Route::get('/mentor/revenue-chart', [HomeMentorController::class, 'getChartData'])->name('chart-data');
    // kursus
    Route::get('/kursus', [KursusMentorController::class, 'index'])->name('kursus');
    Route::get('/buatkursus', [KursusMentorController::class, 'create'])->name('buatkursus');
    Route::post('/tambahkursus', [KursusMentorController::class, 'store'])->name('tambahkursus');
    Route::get('/kursus/{id}/kelola', [KursusMentorController::class, 'manage'])->name('kelolakursus');
    Route::get('/kursus/{id}/edit', [KursusMentorController::class, 'edit'])->name('editkursus');
    Route::put('/kursus/{id}/update', [KursusMentorController::class, 'update'])->name('updatekursus');
    Route::delete('/mentor/kursus/{id}', [KursusMentorController::class, 'destroy'])->name('hapuskursus');
    // kelas
    Route::get('/kursus/{id}/kelola/kelas', [KelasMentorController::class, 'index'])->name('kelolakursuskelas');
    Route::post('/kursus/{id}/kelola/kelas', [KelasMentorController::class, 'store'])->name('tambahkelas');
    Route::put('/kursus/{course_id}/kelas/{class_id}', [KelasMentorController::class, 'update'])->name('updatekelas');
    Route::delete('/kursus/{course_id}/kelas/{class_id}', [KelasMentorController::class, 'destroy'])->name('hapuskelas');
    Route::get('/kursus/{course_id}/kelas/{class_id}/materi', [KelasMentorController::class, 'kelolaKelas'])->name('kelolakelas');
    Route::post('/kursus/{course_id}/kelas/{class_id}/materi/sync', [KelasMentorController::class, 'syncMaterials'])->name('kelasmaterisync');
    Route::delete('/kursus/{course_id}/kelas/{class_id}/materi/{material_id}', [KelasMentorController::class, 'detachMaterial'])->name('hapusmaterikelas');
    // Cukup tulis /enrollment saja karena sudah otomatis ditambah /mentor oleh prefix group
    Route::get('/enrollment/{id}/detail', [KelasMentorController::class, 'getEnrollmentDetail'])
        ->name('enrollment.detail');
    // Di dalam group Route::middleware(['auth', 'mentor'])
    Route::get('/kursus/{id}/kelola/materi', [MateriMentorController::class, 'index'])->name('kelolakursusmateri');
    Route::post('/kursus/{id}/kelola/materi', [MateriMentorController::class, 'store'])->name('tambahmateri');
    Route::put('/kursus/{course_id}/kelola/materi/{id}', [MateriMentorController::class, 'update'])->name('updatemateri');
    Route::delete('/kursus/{course_id}/kelola/materi/{id}', [MateriMentorController::class, 'destroy'])->name('hapusmateri');
    // Mengelola spesifik materi (Tugas & Absen)
    Route::get('/materi/{material_id}/manage', [MateriMentorController::class, 'manageContent'])->name('materi.manage');
    // CRUD Essay di dalam Materi
    Route::post('/materi/{material_id}/essay', [EssayMentorController::class, 'storeEssay'])->name('materi.essay.store');
    Route::put('/essay/{id}', [EssayMentorController::class, 'updateEssay'])->name('materi.essay.update');
    Route::delete('/essay/{id}', [EssayMentorController::class, 'destroyEssay'])->name('materi.essay.destroy');
    // List semua jawaban siswa untuk satu tugas essay tertentu
    Route::get('/essay/{essay_id}/submissions', [EssayMentorController::class, 'submissions'])->name('materi.essay.submissions');

    // Memberi nilai (Grade) - Kita siapkan untuk langkah berikutnya
    Route::post('/submission/{id}/grade', [EssayMentorController::class, 'gradeSubmission'])->name('materi.essay.grade');

    // Quiz Management
    Route::post('/quiz/store', [QuizMentorController::class, 'storeQuiz'])->name('materi.quiz.store');
    Route::get('/quiz/{id}/questions', [QuizMentorController::class, 'manageQuestions'])->name('materi.quiz.questions');
    Route::delete('/quiz/{id}', [QuizMentorController::class, 'destroy'])->name('hapusquiz');
    Route::post('/quiz/question/store', [QuizMentorController::class, 'storeQuestion'])->name('materi.quiz.question.store');
    Route::get('/quiz/{id}/submissions', [QuizMentorController::class, 'quizSubmissions'])->name('materi.quiz.submissions');
    Route::delete('/quiz/question/{id}', [QuizMentorController::class, 'destroyQuestion'])->name('materi.quiz.question.destroy');
    Route::put('/quiz/question/{id}', [QuizMentorController::class, 'updateQuestion'])->name('materi.quiz.question.update');
    // absen
    Route::patch('/mentor/attendance/{id}/delete-photo', [AbsensiMentorController::class, 'deletePhoto'])
        ->name('attendance.delete-photo');
    // leuangan
    Route::get('/mentor/laporan-keuangan', [KeuanganMentorController::class, 'index'])->name('laporan-keuangan');
    Route::post('/mentor/laporan-keuangan/withdraw', [KeuanganMentorController::class, 'storeWithdrawal'])->name('withdraw');

    // profil
    Route::get('mentor/profile', [ProfileController::class, 'editMentor'])->name('mentoredit');
    Route::patch('mentor/profile', [ProfileController::class, 'updateMentor'])->name('mentorupdate');
    Route::delete('mentor/profile', [ProfileController::class, 'destroy'])->name('mentordestroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminHomeController::class, 'index'])->name('dashboard');
    // slider
    Route::get('/slider', [SliderAdminController::class, 'index'])->name('slider');
    Route::get('/slider/create', [SliderAdminController::class, 'create'])->name('slider.create');
    Route::post('/slider', [SliderAdminController::class, 'store'])->name('slider.store');
    Route::get('/slider/{slider}/edit', [SliderAdminController::class, 'edit'])->name('slider.edit');
    Route::put('/slider/{slider}', [SliderAdminController::class, 'update'])->name('slider.update');
    Route::delete('/slider/{slider}', [SliderAdminController::class, 'destroy'])->name('slider.destroy');
    Route::patch('/slider/{slider}/toggle-status', [SliderAdminController::class, 'toggleStatus'])->name('slider.toggle');

    // faq
    Route::get('/faq', [FaqAdminController::class, 'index'])->name('faq');
    Route::get('/faq/create', [FaqAdminController::class, 'create'])->name('faq.create');
    Route::post('/faq', [FaqAdminController::class, 'store'])->name('faq.store');
    Route::get('/faq/{faq}/edit', [FaqAdminController::class, 'edit'])->name('faq.edit');
    Route::put('/faq/{faq}', [FaqAdminController::class, 'update'])->name('faq.update');
    Route::delete('/faq/{faq}', [FaqAdminController::class, 'destroy'])->name('faq.destroy');
    Route::patch('/faq/{faq}/toggle', [FaqAdminController::class, 'toggle'])->name('faq.toggle');

    // setting
    Route::get('/settings', [SettingAdminController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingAdminController::class, 'update'])->name('settings.update');

    //
    Route::get('/withdrawals', [WithdrawalAdminController::class, 'index'])->name('withdrawal.index');
    Route::patch('/withdrawals/{withdrawal}/process', [WithdrawalAdminController::class, 'process'])->name('withdrawal.process');
    Route::patch('/withdrawals/{withdrawal}/complete', [WithdrawalAdminController::class, 'complete'])->name('withdrawal.complete');

    // user
    Route::resource('users', UserAdminController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);
});
require __DIR__ . '/auth.php';
