<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StudentApplication;

class StudentApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        StudentApplication::truncate();
        // Application 1
        StudentApplication::create([
                'application_number' => 'APP001',
                'student_name' => 'Andi Wijaya',
                'email' => 'andi@gmail.com',
                'phone' => '081234567899',
                'nisn' => 'NISN111111',
                'place_of_birth' => 'Bandung',
                'birth_date' => now()->subYears(14)->toDateString(),
                'gender' => 'male',
                'religion' => 'islam',
                'address' => 'Jl. Contoh No. 1',
                'parent_name' => 'Bapak Andi',
                'parent_phone' => '081234567898',
                'parent_address' => 'Jl. Orang Tua No. 1',
                'desired_class' => 'SD',
                'status' => 'pending',
                'application_date' => now()->toDateString(),
        ]);

        // Application 2
        StudentApplication::create([
                'application_number' => 'APP002',
                'student_name' => 'Dewi Lestari',
                'email' => 'dewi@gmail.com',
                'phone' => '081234567897',
                'nisn' => 'NISN222222',
                'place_of_birth' => 'Surabaya',
                'birth_date' => now()->subYears(15)->toDateString(),
                'gender' => 'female',
                'religion' => 'islam',
                'address' => 'Jl. Contoh No. 2',
                'parent_name' => 'Ibu Dewi',
                'parent_phone' => '081234567896',
                'parent_address' => 'Jl. Orang Tua No. 2',
                'desired_class' => 'SMP',
                'status' => 'pending',
                'application_date' => now()->toDateString(),
        ]);
    }
}
