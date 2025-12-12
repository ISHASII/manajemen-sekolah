<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TeachingMaterial;
use App\Models\User;
use App\Models\ClassRoom;
use App\Models\Subject;

class TeachingMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher = User::where('role', 'teacher')->first();
        $class = ClassRoom::first();
        $subject = Subject::first();

        if (!$teacher || !$class || !$subject) return;

        TeachingMaterial::create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'title' => 'Materi Percobaan Sains',
            'description' => 'Bahan ajar percobaan sains dasar',
            'file_path' => 'materials/experiment.pdf',
            'file_type' => 'application/pdf',
            'is_visible' => true,
        ]);
    }
}
