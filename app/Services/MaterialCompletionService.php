<?php

namespace App\Services;

use App\Models\ClassMaterial;
use App\Models\EssaySubmission;
use App\Models\Material;
use App\Models\MaterialCompletion;
use App\Models\QuizSubmission;

class MaterialCompletionService
{
    /**
     * Periksa apakah siswa sudah menyelesaikan semua tugas di materi ini,
     * lalu tandai sebagai selesai jika ya.
     */
    public function checkAndMarkAsCompleted(int $studentId, int $classId, int $materialId): void
    {
        $material = Material::with(['essayAssignments', 'quizAssignments'])
            ->findOrFail($materialId);

        $hasTasks = $material->essayAssignments->isNotEmpty() || $material->quizAssignments->isNotEmpty();

        if (!$hasTasks) {
            // Jika tidak ada tugas, selesaikan otomatis (sudah ditangani di markAsAccessed)
            return;
        }

        // Cek essay
        $allEssaysSubmitted = $material->essayAssignments->isEmpty() ||
            $material->essayAssignments->every(fn($essay) =>
                EssaySubmission::where('student_id', $studentId)
                    ->where('essay_assignment_id', $essay->id)
                    ->exists());

        // Cek quiz
        $allQuizzesSubmitted = $material->quizAssignments->isEmpty() ||
            $material->quizAssignments->every(fn($quiz) =>
                QuizSubmission::where('student_id', $studentId)
                    ->where('quiz_assignment_id', $quiz->id)
                    ->exists());

        if ($allEssaysSubmitted && $allQuizzesSubmitted) {
            $this->markAsCompleted($studentId, $classId, $materialId);
        }
    }

    /**
     * Cek apakah siswa sudah menyelesaikan materi ini.
     */
    public function isMaterialCompleted(int $studentId, int $classMaterialId): bool
    {
        return MaterialCompletion::where('student_id', $studentId)
            ->where('class_material_id', $classMaterialId)
            ->exists();
    }

    /**
     * Cek apakah siswa sudah menyelesaikan SEMUA materi sebelum urutan tertentu.
     */
    public function arePreviousMaterialsCompleted(int $studentId, int $classId, int $currentOrder): bool
    {
        if ($currentOrder <= 1) {
            return true;  // materi pertama selalu bisa diakses
        }

        $previousMaterialIds = ClassMaterial::where('course_class_id', $classId)
            ->where('order', '<', $currentOrder)
            ->pluck('id');

        $completedCount = MaterialCompletion::where('student_id', $studentId)
            ->whereIn('class_material_id', $previousMaterialIds)
            ->count();

        return $completedCount === $previousMaterialIds->count();
    }

    public function markAsAccessed(int $studentId, int $classId, int $materialId): void
    {
        $material = Material::findOrFail($materialId);
        $hasTasks = $material->essayAssignments()->exists() || $material->quizAssignments()->exists();

        // Hanya tandai sebagai "diakses" jika materi TIDAK punya tugas
        if (!$hasTasks) {
            $this->markAsCompleted($studentId, $classId, $materialId);
        }
    }

    private function markAsCompleted(int $studentId, int $classId, int $materialId): void
    {
        $classMaterialId = ClassMaterial::where('course_class_id', $classId)
            ->where('material_id', $materialId)
            ->value('id');

        if ($classMaterialId) {
            MaterialCompletion::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_material_id' => $classMaterialId,
                ],
                ['completed_at' => now()]
            );
        }
    }
}
