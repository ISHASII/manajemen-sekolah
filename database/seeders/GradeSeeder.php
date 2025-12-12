<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $subjects = Subject::all();
        $teachers = User::where('role', 'teacher')->get();

        if ($students->isEmpty() || $subjects->isEmpty() || $teachers->isEmpty()) {
            return; // nothing to seed yet
        }

        foreach ($students as $student) {
            foreach ($subjects->take(5) as $subject) {
                Grade::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teachers->random()->id,
                    'semester' => '1',
                    'score' => rand(60, 100),
                    'grade' => ['A','B','C'][rand(0,2)],
                    'notes' => 'Nilai contoh',
                    'assessment_type' => 'daily',
                    'assessment_date' => now()->subMonths(rand(1,6))->toDateString(),
                ]);
            }
        }
    }
}
