<x-filament-panels::page>
    <div class="mb-6">
        <?php
        $studentCount = \App\Models\ClassEnrollment::where('class_id', $this->record->id)->count();
        ?>
        <p class="text-gray-600">Total: {{ $studentCount }} siswa</p>
    </div>
    {{ $this->table }}
</x-filament-panels::page>
