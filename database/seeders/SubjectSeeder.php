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
            ['name' => 'Matematika', 'code' => 'MTK', 'category' => 'academic'],
            ['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'category' => 'academic'],
            ['name' => 'Bahasa Inggris', 'code' => 'BIG', 'category' => 'academic'],
            ['name' => 'Fisika', 'code' => 'FIS', 'category' => 'academic'],
            ['name' => 'Kimia', 'code' => 'KIM', 'category' => 'academic'],
            ['name' => 'Biologi', 'code' => 'BIO', 'category' => 'academic'],
            ['name' => 'Sejarah', 'code' => 'SEJ', 'category' => 'academic'],
            ['name' => 'Geografi', 'code' => 'GEO', 'category' => 'academic'],
            ['name' => 'Ekonomi', 'code' => 'EKO', 'category' => 'academic'],
            ['name' => 'Sosiologi', 'code' => 'SOS', 'category' => 'academic'],
            ['name' => 'Pendidikan Agama', 'code' => 'PAI', 'category' => 'academic'],
            ['name' => 'PKn', 'code' => 'PKN', 'category' => 'academic'],
            ['name' => 'Seni Budaya', 'code' => 'SBK', 'category' => 'academic'],
            ['name' => 'Pendidikan Jasmani', 'code' => 'PJK', 'category' => 'academic'],
            ['name' => 'TIK', 'code' => 'TIK', 'category' => 'vocational'],
            ['name' => 'Keterampilan Hidup', 'code' => 'KH', 'category' => 'vocational'],
        ];

        foreach ($subjects as $subject) {
            \App\Models\Subject::firstOrCreate(
                ['code' => $subject['code']],
                $subject
            );
        }
    }
}
