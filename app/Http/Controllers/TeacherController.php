<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Announcement;
use App\Models\Schedule;
use App\Models\StudentSkill;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $schedules = Schedule::where('teacher_id', $user->id)
            ->with(['classRoom', 'subject'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Calculate today's schedules based on day name
        $today = strtolower(\Carbon\Carbon::now()->format('l'));
        $todaySchedules = $schedules->where('day_of_week', $today);
        $todaySchedulesCount = $todaySchedules->count();

        $classes = Schedule::where('teacher_id', $user->id)
            ->with('classRoom')
            ->get()
            ->pluck('classRoom')
            ->unique('id');

        $totalStudents = 0;
        foreach ($classes as $class) {
            $totalStudents += $class->current_students;
        }

        $recentGrades = Grade::where('teacher_id', $user->id)
            ->with(['student.user', 'subject'])
            ->latest()
            ->take(10)
            ->get();

        // Compose recent activities for teacher dashboard
        $recentActivities = collect();
        foreach ($recentGrades as $g) {
            $recentActivities->push((object)[
                'type' => 'grade',
                'description' => 'Menambahkan nilai untuk ' . ($g->student->user->name ?? '—'),
                'created_at' => $g->created_at
            ]);
        }
        $announcements = Announcement::where('is_active', true)
            ->where(function($q) use ($user) {
                $q->where('target_audience', 'all')
                    ->orWhere('target_audience', 'teachers');
            })->latest()->take(5)->get();
        foreach ($announcements as $ann) {
            $recentActivities->push((object)[
                'type' => 'announcement',
                'description' => $ann->title,
                'created_at' => $ann->created_at,
            ]);
        }
        $recentActivities = $recentActivities->sortByDesc('created_at')->values();

        return view('teacher.dashboard', compact('teacher', 'schedules', 'classes', 'totalStudents', 'recentGrades', 'todaySchedules', 'todaySchedulesCount'))
            ->with('recentActivities', $recentActivities)
            ->with('announcements', $announcements);
    }

    public function students()
    {
        $user = Auth::user();

        $classes = Schedule::where('teacher_id', $user->id)
            ->with(['classRoom.students.user'])
            ->get()
            ->pluck('classRoom')
            ->unique('id');

        return view('teacher.students', compact('classes'));
    }

    public function studentDetail($id)
    {
        $student = Student::with(['user', 'classRoom'])->findOrFail($id);

        // Check if teacher teaches this student
        $hasAccess = Schedule::where('teacher_id', Auth::id())
            ->where('class_id', $student->class_id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke data siswa ini.');
        }

        $grades = Grade::where('student_id', $id)
            ->where('teacher_id', Auth::id())
            ->with('subject')
            ->get();

        $skills = StudentSkill::where('student_id', $id)
            ->where('assessed_by', Auth::id())
            ->get();

        return view('teacher.student-detail', compact('student', 'grades', 'skills'));
    }

    public function addGrade(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'score' => 'required|numeric|min:0|max:100',
            'assessment_type' => 'required|in:daily,midterm,final,project',
            'semester' => 'required|string',
            'notes' => 'nullable|string',
            'assessment_date' => 'required|date',
        ]);

        $grade = Grade::create([
            'student_id' => $request->student_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => Auth::id(),
            'score' => $request->score,
            'grade' => $this->calculateGrade($request->score),
            'assessment_type' => $request->assessment_type,
            'semester' => $request->semester,
            'notes' => $request->notes,
            'assessment_date' => $request->assessment_date,
        ]);

        return back()->with('success', 'Nilai berhasil ditambahkan!');
    }

    public function addSkill(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'skill_name' => 'required|string|max:255',
            'skill_category' => 'required|in:academic,technical,soft_skill,language,art,sport',
            'proficiency_level' => 'required|in:beginner,intermediate,advanced,expert',
            'description' => 'nullable|string',
        ]);

        StudentSkill::create([
            'student_id' => $request->student_id,
            'skill_name' => $request->skill_name,
            'skill_category' => $request->skill_category,
            'proficiency_level' => $request->proficiency_level,
            'description' => $request->description,
            'assessed_date' => now(),
            'assessed_by' => Auth::id(),
        ]);

        return back()->with('success', 'Keterampilan berhasil ditambahkan!');
    }

    private function calculateGrade($score)
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'E';
    }

    public function schedules()
    {
        $schedules = Schedule::where('teacher_id', Auth::id())
            ->with(['classRoom', 'subject'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        return view('teacher.schedules', compact('schedules'));
    }
}
