<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            ['name' => 'Matematika', 'code' => 'MTK', 'category' => 'academic', 'credit_hours' => 4],
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'category' => 'academic', 'credit_hours' => 4],
            ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'category' => 'academic', 'credit_hours' => 3],
            ['name' => 'Fisika', 'code' => 'FIS', 'category' => 'academic', 'credit_hours' => 3],
            ['name' => 'Kimia', 'code' => 'KIM', 'category' => 'academic', 'credit_hours' => 3],
            ['name' => 'Biologi', 'code' => 'BIO', 'category' => 'academic', 'credit_hours' => 3],
            ['name' => 'Sejarah', 'code' => 'SEJ', 'category' => 'academic', 'credit_hours' => 2],
            ['name' => 'Geografi', 'code' => 'GEO', 'category' => 'academic', 'credit_hours' => 2],
            ['name' => 'Ekonomi', 'code' => 'EKO', 'category' => 'academic', 'credit_hours' => 3],
            ['name' => 'Sosiologi', 'code' => 'SOS', 'category' => 'academic', 'credit_hours' => 2],
            ['name' => 'Pendidikan Agama', 'code' => 'PAI', 'category' => 'academic', 'credit_hours' => 2],
            ['name' => 'PKn', 'code' => 'PKN', 'category' => 'academic', 'credit_hours' => 2],
            ['name' => 'Seni Budaya', 'code' => 'SBK', 'category' => 'academic', 'credit_hours' => 2],
            ['name' => 'Pendidikan Jasmani', 'code' => 'PJK', 'category' => 'academic', 'credit_hours' => 2],
            ['name' => 'TIK', 'code' => 'TIK', 'category' => 'vocational', 'credit_hours' => 2],
            ['name' => 'Keterampilan Hidup', 'code' => 'KH', 'category' => 'vocational', 'credit_hours' => 2],
        ];

        foreach ($subjects as $subject) {
            \App\Models\Subject::firstOrCreate(
                ['code' => $subject['code']],
                $subject
            );
        }
    }
}
