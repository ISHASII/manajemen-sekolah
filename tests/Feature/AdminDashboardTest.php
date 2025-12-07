<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\StudentApplication;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_contains_application_detail_link()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // create sample application
        $application = StudentApplication::create([
            'application_number' => 'APP2025' . rand(1000, 9999),
            'student_name' => 'Test Applicant',
            'email' => 'applicant@example.com',
            'phone' => '081234567890',
            'place_of_birth' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'male',
            'religion' => 'islam',
            'address' => 'Jl. Contoh',
            'parent_name' => 'Parent',
            'parent_phone' => '081234567890',
            'parent_address' => 'Jl. Contoh',
            'desired_class' => 'SD',
            'documents' => [],
            'application_date' => now(),
            'status' => 'pending'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee(route('admin.applications.detail', $application->id));
        $response->assertSee('Pendaftar Terbaru');
    }
}
