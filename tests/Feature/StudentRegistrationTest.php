<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\StudentApplication;

class StudentRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_registration_creates_application_and_stores_files()
    {
        Storage::fake('public');

        $data = [
            'student_name' => 'Test Student',
            'email' => 'teststudent@example.com',
            'phone' => '08123456789',
            'nisn' => '1234567890',
            'place_of_birth' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'gender' => 'male',
            'religion' => 'islam',
            'address' => 'Jl. Contoh No 1',
            'parent_name' => 'Parent Name',
            'parent_phone' => '081987654321',
            'parent_address' => 'Jl. Contoh No 2',
            'parent_job' => 'Teacher',
            'desired_class' => 'SD',
            'agreement' => '1',
        ];

        $files = [
            'birth_certificate' => UploadedFile::fake()->create('birth.pdf', 100, 'application/pdf'),
            'last_certificate' => UploadedFile::fake()->image('last.jpg'),
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ];

        $response = $this->post(route('student.register.submit'), array_merge($data, $files));

        $response->assertRedirect(route('application.success'));

        $this->assertDatabaseHas('student_applications', [
            'email' => 'teststudent@example.com',
            'student_name' => 'Test Student',
            'desired_class' => 'SD'
        ]);

        $application = StudentApplication::where('email', 'teststudent@example.com')->first();
        $this->assertNotNull($application);

        // Check files were stored
        $documents = $application->documents;
        $this->assertIsArray($documents);
        $this->assertCount(3, $documents);

        foreach ($documents as $doc) {
            $this->assertTrue(Storage::disk('public')->exists($doc['path']), "File {$doc['path']} not found in public storage");
        }
    }

    public function test_registration_form_renders_successfully()
    {
        $response = $this->get(route('student.register'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Sekarang');
    }
}
