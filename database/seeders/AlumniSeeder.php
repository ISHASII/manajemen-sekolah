<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Alumni;
use App\Models\Student;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::first();
        if (!$student) {
            return;
        }

        Alumni::create([
            'student_id' => $student->id,
            'graduation_date' => now()->subYears(1)->toDateString(),
            'graduation_class' => 'XII-A',
            'skills' => ['Desain Grafis', 'Keterampilan Hidup'],
            'portfolio' => ['portfolio-link-1'],
            'work_interests' => ['IT', 'Design'],
            'current_job' => null,
            'current_company' => null,
            'cv_online' => null,
            'linkedin_profile' => null,
            'achievements' => ['Juara 1 Lomba Matematika'],
            'training_history' => [],
        ]);
    }
}
