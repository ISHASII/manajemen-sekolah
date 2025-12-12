<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TrainingClass;
use App\Models\Student;

class StudentTrainingClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainingClass = TrainingClass::first();
        $students = Student::all();

        if (!$trainingClass || $students->isEmpty()) return;

        foreach ($students as $i => $student) {
            $trainingClass->students()->syncWithoutDetaching([
                $student->id => [
                    'enrolled_at' => now()->subDays($i + 1),
                    'status' => 'enrolled'
                ]
            ]);
        }
    }
}
