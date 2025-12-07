<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class StudentProfileCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_create_profile_flow()
    {
        // No class selection on create form - class is assigned by admin, keep create data minimal.
        $user = User::factory()->create([ 'role' => 'student' ]);

        $this->actingAs($user);

        $response = $this->get(route('student.profile.create'));
        $response->assertStatus(200);
        $response->assertSee('Buat Profil Siswa');

        $postData = [
            'student_id' => 'STU-001',
            // class_id removed from student create form (set by admin).
            'nisn' => '9876543210',
            'place_of_birth' => 'Bandung',
            'birth_date' => '2010-05-05',
            'religion' => 'islam',
            'address' => 'Jl. Uji',
            'parent_name' => 'Parent',
            'parent_phone' => '081234567',
            'parent_job' => 'Farmer',
            'parent_address' => 'Jl. Parent',
            'interests_talents' => ['Singing', 'Football'],
            'enrollment_date' => now()->format('Y-m-d'),
        ];

        $response = $this->post(route('student.profile.store'), $postData);
        $response->assertRedirect(route('student.profile'));

        $this->assertDatabaseHas('students', [
            'student_id' => 'STU-001',
            'nisn' => '9876543210',
            'class_id' => null,
        ]);
    }
}
