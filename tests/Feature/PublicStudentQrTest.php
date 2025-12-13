<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Student;
use App\Models\User;

class PublicStudentQrTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_route_returns_png()
    {
        // Create user and student
        $user = User::factory()->create();
        $student = Student::create([
            'user_id' => $user->id,
            'student_id' => '20250001',
            'address' => 'Jl. Siswa',
            'status' => 'active'
        ]);

        $response = $this->get(route('students.public.qrcode', $student->id));
        $response->assertStatus(200);
        $this->assertEquals('image/png', $response->headers->get('Content-Type'));
        $this->assertGreaterThan(0, strlen($response->getContent()));
    }
}
