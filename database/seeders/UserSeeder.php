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
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Jl. Admin No. 1, Jakarta',
                'birth_date' => '1980-01-01',
                'gender' => 'male',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Education levels
        $levels = ['SD', 'SMP', 'SMA', 'Kejuruan'];

        // Store teachers by level for later use
        $teachersByLevel = [];

        // Create Teachers (10 per level = 40 total)
        $teacherNames = [
            'Ahmad Rahman', 'Siti Nurhaliza', 'Budi Santoso', 'Maya Sari', 'Dedi Kurniawan',
            'Rina Amelia', 'Hendra Gunawan', 'Lina Marlina', 'Fajar Setiawan', 'Dewi Anggraini'
        ];

        $teacherCounter = 1;
        foreach ($levels as $level) {
            $levelTeachers = [];
            foreach ($teacherNames as $index => $name) {
                $email = "teacher{$teacherCounter}@{$level}.edu";

                $teacher = \App\Models\User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => bcrypt('password'),
                        'role' => 'teacher',
                        'phone' => '08123456' . str_pad($teacherCounter, 3, '0', STR_PAD_LEFT),
                        'address' => "Jl. Guru {$level} No. {$teacherCounter}, Jakarta",
                        'birth_date' => now()->subYears(rand(25, 45))->format('Y-m-d'),
                        'gender' => $index % 2 == 0 ? 'male' : 'female',
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );

                // Create Teacher record
                $teacherRecord = \App\Models\Teacher::firstOrCreate(
                    ['user_id' => $teacher->id],
                    [
                        'teacher_id' => "TCH{$level}" . str_pad($teacherCounter, 3, '0', STR_PAD_LEFT),
                        'nip' => '19' . rand(80, 95) . rand(10, 12) . rand(10, 31) . '00100' . rand(1, 9),
                        'subjects' => $this->getSubjectsForLevel($level),
                        'qualifications' => [
                            ['degree' => 'S1 Pendidikan', 'institution' => 'Universitas Indonesia', 'year' => rand(2005, 2015)],
                        ],
                        'certifications' => [
                            ['name' => 'Sertifikat Guru', 'issuer' => 'Kemendikbud', 'year' => rand(2010, 2020)],
                        ],
                        'hire_date' => now()->subYears(rand(5, 20))->format('Y-m-d'),
                        'status' => 'active',
                    ]
                );

                $levelTeachers[] = $teacher;
                $teacherCounter++;
            }
            $teachersByLevel[$level] = $levelTeachers;
        }

        // Create Classrooms with homeroom teachers
        $this->createClassrooms($teachersByLevel);

        // Store students by level for classroom assignment
        $studentsByLevel = [];

        // Create Students (10 per level = 40 total)
        $studentNames = [
            'Ahmad Putra', 'Sari Dewi', 'Budi Rahman', 'Maya Sari', 'Dedi Kurnia',
            'Rina Amelia', 'Hendra Gunawan', 'Lina Marlina', 'Fajar Setiawan', 'Dewi Anggraini'
        ];

        $studentCounter = 1;
        foreach ($levels as $level) {
            $levelStudents = [];
            foreach ($studentNames as $index => $name) {
                $email = "student{$studentCounter}@{$level}.edu";

                $student = \App\Models\User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => bcrypt('password'),
                        'role' => 'student',
                        'phone' => '08123456' . str_pad($studentCounter + 100, 3, '0', STR_PAD_LEFT),
                        'address' => "Jl. Siswa {$level} No. {$studentCounter}, Jakarta",
                        'birth_date' => $this->getBirthDateForLevel($level),
                        'gender' => $index % 2 == 0 ? 'male' : 'female',
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]
                );

                // Create Student record (will assign classroom later)
                $studentRecord = \App\Models\Student::firstOrCreate(
                    ['user_id' => $student->id],
                    [
                        'student_id' => "STD{$level}" . str_pad($studentCounter, 3, '0', STR_PAD_LEFT),
                        'nisn' => '00' . rand(10, 99) . rand(100000, 999999),
                        'place_of_birth' => 'Jakarta',
                        'birth_date' => $this->getBirthDateForLevel($level),
                        'religion' => ['islam', 'kristen', 'katolik', 'hindu', 'budha'][rand(0, 4)],
                        'address' => "Jl. Siswa {$level} No. {$studentCounter}, Jakarta",
                        'parent_name' => "Orang Tua {$name}",
                        'parent_phone' => '08123456' . str_pad($studentCounter + 200, 3, '0', STR_PAD_LEFT),
                        'parent_address' => "Jl. Siswa {$level} No. {$studentCounter}, Jakarta",
                        'parent_job' => ['Wiraswasta', 'Pegawai Negeri', 'Guru', 'Dokter', 'Polisi'][rand(0, 4)],
                        'health_info' => [
                            'condition' => 'Sehat',
                            'medications' => [],
                            'allergies' => 'Tidak ada',
                            'emergency_contact' => '08123456' . str_pad($studentCounter + 200, 3, '0', STR_PAD_LEFT)
                        ],
                        'disability_info' => [
                            'type' => 'Tidak ada',
                            'level' => 'Tidak ada',
                            'assistance_needed' => 'Tidak ada'
                        ],
                        'education_history' => [
                            [
                                'level' => $this->getPreviousLevel($level),
                                'school' => "Sekolah {$this->getPreviousLevel($level)} Jakarta",
                                'year_start' => date('Y') - rand(1, 3),
                                'year_end' => date('Y') - rand(0, 2)
                            ]
                        ],
                        'interests_talents' => [
                            'academic' => 'Matematika',
                            'sport' => 'Sepak Bola',
                            'art' => 'Menggambar'
                        ],
                        'status' => 'active',
                        'is_orphan' => false,
                        'enrollment_date' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                    ]
                );

                $levelStudents[] = $studentRecord;
                $studentCounter++;
            }
            $studentsByLevel[$level] = $levelStudents;
        }

        // Assign students to classrooms
        $this->assignStudentsToClassrooms($studentsByLevel);

        // Create teaching schedules
        $this->createSchedules($teachersByLevel);
    }

    private function createClassrooms($teachersByLevel)
    {
        $classes = [];

        // SD Classes (Grades 1-6)
        for ($grade = 1; $grade <= 6; $grade++) {
            for ($class = 'A'; $class <= 'B'; $class++) {
                $homeroomTeacher = $teachersByLevel['SD'][($grade - 1) % count($teachersByLevel['SD'])];
                $classes[] = [
                    'name' => "Kelas {$grade}-{$class} SD",
                    'grade_level' => (string)$grade,
                    'capacity' => 25,
                    'current_students' => 0,
                    'description' => "Kelas {$grade} Sekolah Dasar",
                    'homeroom_teacher_id' => $homeroomTeacher->id,
                    'is_active' => true,
                ];
            }
        }

        // SMP Classes (Grades 7-9)
        for ($grade = 7; $grade <= 9; $grade++) {
            for ($class = 'A'; $class <= 'C'; $class++) {
                $homeroomTeacher = $teachersByLevel['SMP'][($grade - 7) % count($teachersByLevel['SMP'])];
                $classes[] = [
                    'name' => "Kelas {$grade}-{$class} SMP",
                    'grade_level' => (string)$grade,
                    'capacity' => 30,
                    'current_students' => 0,
                    'description' => "Kelas {$grade} Sekolah Menengah Pertama",
                    'homeroom_teacher_id' => $homeroomTeacher->id,
                    'is_active' => true,
                ];
            }
        }

        // SMA Classes (Grades 10-12)
        $programs = ['IPA', 'IPS', 'Bahasa'];
        $programIndex = 0;
        for ($grade = 10; $grade <= 12; $grade++) {
            foreach ($programs as $program) {
                $homeroomTeacher = $teachersByLevel['SMA'][$programIndex % count($teachersByLevel['SMA'])];
                $classes[] = [
                    'name' => "Kelas {$grade} {$program} SMA",
                    'grade_level' => (string)$grade,
                    'capacity' => 30,
                    'current_students' => 0,
                    'description' => "Kelas {$grade} Program {$program} Sekolah Menengah Atas",
                    'homeroom_teacher_id' => $homeroomTeacher->id,
                    'is_active' => true,
                ];
                $programIndex++;
            }
        }

        // Kejuruan Classes (Grades 10-12)
        $kejuruanPrograms = ['TKJ', 'RPL', 'Multimedia', 'Akuntansi'];
        $kejuruanIndex = 0;
        for ($grade = 10; $grade <= 12; $grade++) {
            foreach ($kejuruanPrograms as $program) {
                $homeroomTeacher = $teachersByLevel['Kejuruan'][$kejuruanIndex % count($teachersByLevel['Kejuruan'])];
                $classes[] = [
                    'name' => "Kelas {$grade} {$program} Kejuruan",
                    'grade_level' => (string)$grade,
                    'capacity' => 25,
                    'current_students' => 0,
                    'description' => "Kelas {$grade} Program {$program} Sekolah Menengah Kejuruan",
                    'homeroom_teacher_id' => $homeroomTeacher->id,
                    'is_active' => true,
                ];
                $kejuruanIndex++;
            }
        }

        foreach ($classes as $class) {
            \App\Models\ClassRoom::firstOrCreate(
                ['name' => $class['name']],
                $class
            );
        }
    }

    private function assignStudentsToClassrooms($studentsByLevel)
    {
        foreach ($studentsByLevel as $level => $students) {
            $classrooms = \App\Models\ClassRoom::where('name', 'like', "%{$level}%")
                ->orderBy('current_students')
                ->get();

            $classIndex = 0;
            foreach ($students as $student) {
                $classroom = $classrooms[$classIndex % $classrooms->count()];

                $student->update(['class_id' => $classroom->id]);
                $classroom->increment('current_students');

                $classIndex++;
            }
        }
    }

    private function createSchedules($teachersByLevel)
    {
        echo "Creating schedules...\n";
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $subjects = \App\Models\Subject::all();

        echo "Found " . $subjects->count() . " subjects\n";

        if ($subjects->isEmpty()) {
            echo "No subjects found, skipping schedule creation\n";
            return;
        }

        // Group subjects by level
        $subjectsByLevel = [
            'SD' => $subjects->filter(function($subject) { return $subject->level === 'SD'; }),
            'SMP' => $subjects->filter(function($subject) { return $subject->level === 'SMP'; }),
            'SMA' => $subjects->filter(function($subject) { return $subject->level === 'SMA'; }),
            'Kejuruan' => $subjects->filter(function($subject) { return $subject->level === 'Kejuruan'; }),
        ];

        $classrooms = \App\Models\ClassRoom::all();

        foreach ($classrooms as $classroom) {
            $level = $this->getLevelFromClassroomName($classroom->name);
            $levelTeachers = collect($teachersByLevel[$level] ?? []);
            $levelSubjects = $subjectsByLevel[$level] ?? collect();

            if ($levelTeachers->isEmpty() || $levelSubjects->isEmpty()) {
                continue;
            }

            // Create 4-6 subjects per day
            $subjectsPerDay = rand(4, 6);

            foreach ($days as $day) {
                $dailySubjects = $levelSubjects->random(min($subjectsPerDay, $levelSubjects->count()));
                $startHour = 7;

                foreach ($dailySubjects as $subject) {
                    $teacher = $levelTeachers->random();

                    \App\Models\Schedule::firstOrCreate(
                        [
                            'class_id' => $classroom->id,
                            'subject_id' => $subject->id,
                            'day_of_week' => $day,
                            'start_time' => sprintf('%02d:00', $startHour),
                        ],
                        [
                            'teacher_id' => $teacher->id,
                            'end_time' => sprintf('%02d:00', $startHour + 1),
                            'room' => 'R-' . rand(1, 20),
                            'is_active' => true,
                        ]
                    );

                    $startHour++;
                }
            }
        }
    }

    private function getLevelFromClassroomName($className)
    {
        if (str_contains($className, 'SD')) {
            return 'SD';
        } elseif (str_contains($className, 'SMP')) {
            return 'SMP';
        } elseif (str_contains($className, 'SMA')) {
            return 'SMA';
        } elseif (str_contains($className, 'Kejuruan')) {
            return 'Kejuruan';
        }
        return 'SD';
    }

    private function getSubjectsForLevel($level)
    {
        $subjects = [
            'SD' => ['Pendidikan Agama', 'Bahasa Indonesia', 'Matematika', 'IPA', 'IPS'],
            'SMP' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Kimia', 'Biologi'],
            'SMA' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Kimia', 'Biologi', 'Sejarah'],
            'Kejuruan' => ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Teknik Komputer', 'Keterampilan']
        ];

        return $subjects[$level] ?? ['Umum'];
    }

    private function getBirthDateForLevel($level)
    {
        $year = date('Y');
        $ages = [
            'SD' => rand(6, 12),
            'SMP' => rand(12, 15),
            'SMA' => rand(15, 18),
            'Kejuruan' => rand(15, 20)
        ];

        return date('Y-m-d', strtotime("-{$ages[$level]} years", strtotime("{$year}-01-01")));
    }

    private function getPreviousLevel($level)
    {
        $previous = [
            'SD' => 'TK',
            'SMP' => 'SD',
            'SMA' => 'SMP',
            'Kejuruan' => 'SMP'
        ];

        return $previous[$level] ?? 'TK';
    }
}
