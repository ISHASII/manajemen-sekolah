<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentSkill;
use App\Models\Student;
use App\Models\User;

class StudentSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::first();
        $assessor = User::where('role', 'teacher')->first();

        if (!$student) return;

        StudentSkill::create([
            'student_id' => $student->id,
            'skill_name' => 'Bermain Piano',
            'skill_category' => 'art',
            'proficiency_level' => 'intermediate',
            'description' => 'Telah mengikuti les piano selama 2 tahun',
            'certificate_file' => null,
            'assessed_date' => now()->subMonths(2)->toDateString(),
            'assessed_by' => $assessor ? $assessor->id : null,
        ]);
    }
}
