<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassRoom;

class ClassRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            [
                'name' => 'Kelas X-A',
                'grade_level' => '10',
                'capacity' => 30,
                'current_students' => 0,
                'description' => 'Kelas 10 Program IPA',
                'is_active' => true,
            ],
            [
                'name' => 'Kelas X-B',
                'grade_level' => '10',
                'capacity' => 30,
                'current_students' => 0,
                'description' => 'Kelas 10 Program IPS',
                'is_active' => true,
            ],
            [
                'name' => 'Kelas XI-A',
                'grade_level' => '11',
                'capacity' => 28,
                'current_students' => 0,
                'description' => 'Kelas 11 Program IPA',
                'is_active' => true,
            ],
            [
                'name' => 'Kelas XI-B',
                'grade_level' => '11',
                'capacity' => 28,
                'current_students' => 0,
                'description' => 'Kelas 11 Program IPS',
                'is_active' => true,
            ],
            [
                'name' => 'Kelas XII-A',
                'grade_level' => '12',
                'capacity' => 25,
                'current_students' => 0,
                'description' => 'Kelas 12 Program IPA',
                'is_active' => true,
            ],
            [
                'name' => 'Kelas XII-B',
                'grade_level' => '12',
                'capacity' => 25,
                'current_students' => 0,
                'description' => 'Kelas 12 Program IPS',
                'is_active' => true,
            ],
        ];

        foreach ($classes as $class) {
            ClassRoom::create($class);
        }
    }
}
