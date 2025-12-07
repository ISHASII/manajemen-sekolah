<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Alumni;

class AdminAlumniCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_alumni()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $studentUser = User::factory()->create();
        $class = ClassRoom::create(['name' => 'Test Class', 'grade_level' => 'X']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_id' => '20250001',
            'class_id' => $class->id,
            'nisn' => '123456',
            'place_of_birth' => 'Jakarta',
            'birth_date' => now()->subYears(16)->toDateString(),
            'religion' => 'islam',
            'address' => 'Test address',
            'parent_name' => 'Parent',
            'enrollment_date' => now()->subYears(4)->toDateString(),
        ]);

        $payload = [
            'student_id' => $student->id,
            'graduation_date' => now()->toDateString(),
            'graduation_class' => 'X-A',
            'current_job' => 'Developer',
            'current_company' => 'Test Company',
            'linkedin_profile' => 'https://linkedin.com/test',
            'skills' => 'php,laravel'
        ];

        $response = $this->post(route('admin.alumni.store'), $payload);
        $response->assertRedirect(route('admin.alumni.index'));

        $this->assertDatabaseHas('alumni', [
            'student_id' => $student->id,
            'current_job' => 'Developer'
        ]);
    }

    public function test_admin_can_update_alumni()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $studentUser = User::factory()->create();
        $class = ClassRoom::create(['name' => 'Test Class', 'grade_level' => 'X']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_id' => '20250002',
            'class_id' => $class->id,
            'nisn' => '654321',
            'place_of_birth' => 'Bandung',
            'birth_date' => now()->subYears(16)->toDateString(),
            'religion' => 'islam',
            'address' => 'Test address 2',
            'parent_name' => 'Parent 2',
            'enrollment_date' => now()->subYears(4)->toDateString(),
        ]);

        $alumni = Alumni::create([
            'student_id' => $student->id,
            'graduation_date' => now()->toDateString(),
        ]);

        $payload = [
            'student_id' => $student->id,
            'graduation_date' => now()->addDays(1)->toDateString(),
            'graduation_class' => 'X-B',
            'current_job' => 'Engineer',
        ];

        $response = $this->put(route('admin.alumni.update', $alumni->id), $payload);
        $response->assertRedirect(route('admin.alumni.index'));

        $this->assertDatabaseHas('alumni', [
            'id' => $alumni->id,
            'current_job' => 'Engineer'
        ]);
    }

    public function test_admin_can_delete_alumni()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $studentUser = User::factory()->create();
        $class = ClassRoom::create(['name' => 'Test Class', 'grade_level' => 'X']);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_id' => '20250003',
            'class_id' => $class->id,
            'nisn' => '987654',
            'place_of_birth' => 'Surabaya',
            'birth_date' => now()->subYears(16)->toDateString(),
            'religion' => 'islam',
            'address' => 'Test address 3',
            'parent_name' => 'Parent 3',
            'enrollment_date' => now()->subYears(4)->toDateString(),
        ]);

        $alumni = Alumni::create([
            'student_id' => $student->id,
            'graduation_date' => now()->toDateString(),
        ]);

        $response = $this->delete(route('admin.alumni.destroy', $alumni->id));
        $response->assertRedirect(route('admin.alumni.index'));

        $this->assertDatabaseMissing('alumni', ['id' => $alumni->id]);
    }
}
