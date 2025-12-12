<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassRoom;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentUsers = User::where('role', 'student')->get();

        foreach ($studentUsers as $user) {
            // Skip if student record already exists
            if (Student::where('user_id', $user->id)->exists()) {
                continue;
            }

            // Parse level and grade from email (e.g., student1@SD.edu -> SD, grade 1-6)
            $level = $this->getLevelFromEmail($user->email);
            $gradeLevel = $this->getGradeLevelFromEmail($user->email, $level);

            // Find appropriate classroom based on level and grade
            $classroom = $this->findAppropriateClassroom($level, $gradeLevel);

            if (!$classroom) {
                echo "No classroom found for {$user->email} (level: {$level}, grade: {$gradeLevel})\n";
                continue;
            }

            echo "Assigning {$user->email} to classroom: {$classroom->name}\n";

            Student::create([
                'user_id' => $user->id,
                'student_id' => $this->generateStudentId($level),
                'class_id' => $classroom ? $classroom->id : null,
                'nisn' => $this->generateNISN(),
                'place_of_birth' => 'Jakarta',
                'birth_date' => $this->getBirthDateForLevel($level),
                'religion' => ['islam', 'kristen', 'katolik', 'hindu', 'budha'][rand(0, 4)],
                'address' => "Jl. Siswa {$level} No. " . rand(1, 100) . ", Jakarta",
                'parent_name' => "Orang Tua " . explode(' ', $user->name)[0],
                'parent_phone' => '08123456' . rand(100, 999),
                'parent_address' => "Jl. Siswa {$level} No. " . rand(1, 100) . ", Jakarta",
                'parent_job' => ['Wiraswasta', 'Pegawai Negeri', 'Guru', 'Dokter', 'Polisi'][rand(0, 4)],
                'health_info' => [
                    'condition' => 'Sehat',
                    'medications' => [],
                    'allergies' => 'Tidak ada',
                    'emergency_contact' => '08123456' . rand(100, 999)
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
            ]);
        }
    }

    private function getLevelFromEmail($email)
    {
        if (strpos($email, '@SD.edu') !== false) return 'SD';
        if (strpos($email, '@SMP.edu') !== false) return 'SMP';
        if (strpos($email, '@SMA.edu') !== false) return 'SMA';
        if (strpos($email, '@Kejuruan.edu') !== false) return 'Kejuruan';
        return 'SMA'; // default
    }

    private function getGradeLevelFromEmail($email, $level)
    {
        // Extract number from email like student1@SD.edu -> 1
        preg_match('/student(\d+)@/', $email, $matches);
        $studentNumber = isset($matches[1]) ? (int)$matches[1] : 1;

        // Distribute students across grades based on their number
        switch ($level) {
            case 'SD':
                return (string)(($studentNumber - 1) % 6 + 1); // Grades 1-6
            case 'SMP':
                return (string)(($studentNumber - 1) % 3 + 7); // Grades 7-9
            case 'SMA':
            case 'Kejuruan':
                return (string)(($studentNumber - 1) % 3 + 10); // Grades 10-12
            default:
                return '1';
        }
    }

    private function findAppropriateClassroom($level, $gradeLevel)
    {
        // Find classrooms that match the level and grade
        $classrooms = ClassRoom::where('grade_level', $gradeLevel)
            ->where('name', 'like', "%{$level}%")
            ->get();

        if ($classrooms->isNotEmpty()) {
            // Return classroom with least students
            return $classrooms->sortBy('current_students')->first();
        }

        // Fallback: any classroom with matching grade level
        return ClassRoom::where('grade_level', $gradeLevel)->first();
    }

    private function generateStudentId($level)
    {
        static $counters = [];
        if (!isset($counters[$level])) {
            $counters[$level] = 1;
        }

        $prefix = substr($level, 0, 2); // SD, SM, SM, Ke
        if ($level === 'Kejuruan') $prefix = 'KJ';

        $id = "STD{$prefix}" . str_pad($counters[$level], 3, '0', STR_PAD_LEFT);
        $counters[$level]++;

        return $id;
    }

    private function generateNISN()
    {
        return '00' . rand(10, 99) . rand(100000, 999999);
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
