<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SchoolSeeder::class,
            SubjectSeeder::class,
            UserSeeder::class, // Now includes teachers, students, classrooms, and schedules
            TrainingClassSeeder::class,
            StudentTrainingClassSeeder::class,
            StudentApplicationSeeder::class,
            AnnouncementSeeder::class,
            GradeSeeder::class,
            StudentPortfolioSeeder::class,
            StudentSkillSeeder::class,
            TeachingMaterialSeeder::class,
            DocumentSeeder::class,
        ]);
    }
}
