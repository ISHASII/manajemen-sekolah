<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = \App\Models\User::create([
            'name' => 'Administrator',
            'email' => 'admin@slbsharingschool.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1, Jakarta',
            'birth_date' => '1980-01-01',
            'gender' => 'male',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Teacher User
        $teacher = \App\Models\User::create([
            'name' => 'Budi Santoso',
            'email' => 'teacher@slbsharingschool.edu',
            'password' => bcrypt('password'),
            'role' => 'teacher',
            'phone' => '081234567891',
            'address' => 'Jl. Guru No. 2, Jakarta',
            'birth_date' => '1985-05-15',
            'gender' => 'male',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Teacher record
        \App\Models\Teacher::create([
            'user_id' => $teacher->id,
            'teacher_id' => 'TCH001',
            'nip' => '198505152010011001',
            'subjects' => ['Pendidikan Khusus', 'Terapi Okupasi', 'Keterampilan Hidup'],
            'qualifications' => [
                ['degree' => 'S1 Pendidikan Luar Biasa', 'institution' => 'UPI Bandung', 'year' => 2007],
                ['degree' => 'S2 Pendidikan Khusus', 'institution' => 'UNJ Jakarta', 'year' => 2012]
            ],
            'certifications' => [
                ['name' => 'Sertifikat Terapi Okupasi', 'issuer' => 'IOTA', 'year' => 2015],
                ['name' => 'Sertifikat Bahasa Isyarat', 'issuer' => 'Gerkatin', 'year' => 2018]
            ],
            'hire_date' => '2010-07-01',
            'status' => 'active',
        ]);

        // Create Student User
        $student = \App\Models\User::create([
            'name' => 'Siti Aminah',
            'email' => 'student@slbsharingschool.edu',
            'password' => bcrypt('password'),
            'role' => 'student',
            'phone' => '081234567892',
            'address' => 'Jl. Siswa No. 3, Jakarta',
            'birth_date' => '2010-03-20',
            'gender' => 'female',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Student record
        \App\Models\Student::create([
            'user_id' => $student->id,
            'student_id' => 'STD001',
            'nisn' => '0010032010001',
            'place_of_birth' => 'Jakarta',
            'birth_date' => '2010-03-20',
            'religion' => 'islam',
            'address' => 'Jl. Siswa No. 3, Jakarta',
            'parent_name' => 'Ahmad Syahrir',
            'parent_phone' => '081234567893',
            'parent_address' => 'Jl. Siswa No. 3, Jakarta',
            'parent_job' => 'Wiraswasta',
            'health_info' => [
                'condition' => 'Tunanetra ringan',
                'medications' => [],
                'allergies' => 'Tidak ada',
                'emergency_contact' => '081234567893'
            ],
            'disability_info' => [
                'type' => 'Tunanetra',
                'level' => 'Ringan',
                'assistance_needed' => 'Bantuan orientasi mobilitas'
            ],
            'education_history' => [
                [
                    'level' => 'TK',
                    'school' => 'TK Inklusi Harapan',
                    'year_start' => 2015,
                    'year_end' => 2017
                ]
            ],
            'interests_talents' => [
                'music' => 'Bermain piano',
                'sport' => 'Goalball',
                'academic' => 'Matematika'
            ],
            'status' => 'active',
            'is_orphan' => false,
            'enrollment_date' => '2024-07-15',
        ]);
    }
}
