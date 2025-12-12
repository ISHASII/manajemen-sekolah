<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TrainingClass;
use App\Models\User;

class TrainingClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trainer = User::where('role', 'teacher')->first();

        TrainingClass::create([
            'title' => 'Pelatihan Kewirausahaan',
            'slug' => 'pelatihan-kewirausahaan',
            'description' => 'Pelatihan kewirausahaan untuk siswa',
            'start_at' => now()->addDays(5),
            'end_at' => now()->addDays(10),
            'capacity' => 25,
            'trainer_id' => null,
            'is_active' => true,
            'created_by' => $trainer ? $trainer->id : null,
            'open_to_kejuruan' => false,
        ]);

        TrainingClass::create([
            'title' => 'Pelatihan Desain Grafis',
            'slug' => 'pelatihan-desain-grafis',
            'description' => 'Pelatihan desain grafis untuk siswa',
            'start_at' => now()->addDays(14),
            'end_at' => now()->addDays(20),
            'capacity' => 20,
            'trainer_id' => null,
            'is_active' => true,
            'created_by' => $trainer ? $trainer->id : null,
            'open_to_kejuruan' => true,
        ]);
    }
}
