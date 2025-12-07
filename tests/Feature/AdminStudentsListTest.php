<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;

class AdminStudentsListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_all_users_with_student_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        // Create users with student role
        $u1 = User::factory()->create(['role' => 'student', 'name' => 'HasProfile']);
        $s1 = Student::create([
            'user_id' => $u1->id,
            'student_id' => 'S-1',
            'place_of_birth' => 'City',
            'birth_date' => now()->subYears(10)->toDateString(),
            'religion' => 'islam',
            'address' => 'Test Address',
            'parent_name' => 'Parent',
            'parent_phone' => '08123456789',
            'parent_address' => 'Parent Address',
            'enrollment_date' => now()->toDateString(),
            'status' => 'active'
        ]);

        $u2 = User::factory()->create(['role' => 'student', 'name' => 'NoProfile']);

        $response = $this->get(route('admin.students.index'));
        $response->assertStatus(200);
        $response->assertSee('HasProfile');
        $response->assertSee('NoProfile');
    }
}
