<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\TrainingClass;
use App\Models\Subject;
use App\Models\Grade;

class TeacherCanSaveTrainingGradesTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_and_update_grades_for_training_students()
    {
        // Create teacher
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::create(['user_id' => $user->id, 'nip' => 'T-1']);

        // Create subject
        $subject = Subject::create(['name' => 'Vocational Skill', 'code' => 'VSK1', 'category' => 'vocational']);

        // Create training class
        $training = TrainingClass::create([
            'title' => 'Vocational Training',
            'description' => 'test',
            'start_at' => now(),
            'end_at' => now()->addWeeks(2),
            'capacity' => 10,
            'trainer_id' => $teacher->id,
            'is_active' => true,
        ]);

        // Create students and enroll in training
        $s1user = User::factory()->create(['role' => 'student']);
        $s1 = Student::create(['user_id' => $s1user->id, 'student_id' => 'S1', 'status' => 'active']);
        $s2user = User::factory()->create(['role' => 'student']);
        $s2 = Student::create(['user_id' => $s2user->id, 'student_id' => 'S2', 'status' => 'active']);

        $training->students()->attach([$s1->id => ['enrolled_at' => now(), 'status' => 'enrolled'], $s2->id => ['enrolled_at' => now(), 'status' => 'enrolled']]);

        $this->actingAs($user);

        // Load manage page with training_class selected
        $response = $this->get(route('teacher.grades.manage', ['training_class_id' => $training->id, 'subject_id' => $subject->id, 'assessment_type' => 'daily', 'semester' => 'Ganjil', 'assessment_date' => now()->format('Y-m-d')]));
        $response->assertStatus(200);
        $response->assertSee($s1user->name);

        // Submit grades
        $post = [
            'training_class_id' => $training->id,
            'subject_id' => $subject->id,
            'assessment_type' => 'daily',
            'semester' => 'Ganjil',
            'assessment_date' => now()->format('Y-m-d'),
            'scores' => [$s1->id => 88, $s2->id => 92],
            'notes' => [$s1->id => 'good', $s2->id => 'excellent']
        ];

        $submit = $this->post(route('teacher.grades.manage.store'), $post);
        $submit->assertStatus(302);
        $submit->assertSessionHas('success');

        $this->assertDatabaseHas('grades', ['student_id' => $s1->id, 'subject_id' => $subject->id, 'score' => 88]);
        $this->assertDatabaseHas('grades', ['student_id' => $s2->id, 'subject_id' => $subject->id, 'score' => 92]);

        // Update one grade
        $post['scores'][$s1->id] = 90;
        $submit2 = $this->post(route('teacher.grades.manage.store'), $post);
        $submit2->assertStatus(302);

        $this->assertDatabaseHas('grades', ['student_id' => $s1->id, 'subject_id' => $subject->id, 'score' => 90]);
    }
}
