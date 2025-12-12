<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentPortfolio;
use App\Models\Student;

class StudentPortfolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::first();
        if (!$student) return;

        StudentPortfolio::create([
            'student_id' => $student->id,
            'title' => 'Portofolio Siti',
            'description' => 'Karya seni dan project sekolah',
            'link' => 'https://example.com/portfolio/siti',
            'file_path' => null
        ]);
    }
}
