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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

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
            })->latest()->take(10)->get();
        foreach ($announcements as $ann) {
            $recentActivities->push((object)[
                'type' => 'announcement',
                'description' => $ann->title,
                'created_at' => $ann->created_at,
            ]);
        }
        $recentActivities = $recentActivities->sortByDesc('created_at')->values();

        // Identify students in teacher's classes with low average grades (< 70)
        $classIds = $classes->pluck('id')->filter()->values()->all();
        $lowGradesStudents = collect();
        if (!empty($classIds)) {
            $studentIds = Student::whereIn('class_id', $classIds)->pluck('id');
            if ($studentIds->count() > 0) {
                $avgByStudent = Grade::whereIn('student_id', $studentIds)
                    ->groupBy('student_id')
                    ->selectRaw('student_id, AVG(score) as avg_score')
                    ->pluck('avg_score', 'student_id');

                $threshold = 70;
                $lowIds = $avgByStudent->filter(function ($avg) use ($threshold) {
                    return (float) $avg < $threshold;
                })->keys()->toArray();

                if (!empty($lowIds)) {
                    $lowGradesStudents = Student::with(['user', 'classRoom'])
                        ->whereIn('id', $lowIds)
                        ->get()
                        ->map(function ($student) use ($avgByStudent) {
                            $student->average_grade = round($avgByStudent[$student->id] ?? 0, 1);
                            return $student;
                        });
                }
            }
        }

        return view('teacher.dashboard', compact('teacher', 'schedules', 'classes', 'totalStudents', 'recentGrades', 'todaySchedules', 'todaySchedulesCount', 'lowGradesStudents'))
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

    /**
     * Show teacher profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->with('user')->first();
        $subjectNames = [];
        // Convert teacher subjects to readable names if they are stored as IDs or in different formats
        if ($teacher && !empty($teacher->subjects)) {
            if (is_array($teacher->subjects)) {
                $raw = $teacher->subjects;
            } elseif (is_string($teacher->subjects)) {
                $raw = trim((string)$teacher->subjects);
                $maybeJson = @json_decode($raw, true);
                if (is_array($maybeJson)) {
                    $raw = $maybeJson;
                } else {
                    // comma separated string
                    $raw = array_map('trim', explode(',', $raw));
                }
            } else {
                $raw = [];
            }

            // If the array contains numeric IDs, map them to Subject names; otherwise, treat them as names
            $hasNumeric = false;
            foreach ((array)$raw as $r) {
                if (is_numeric($r)) { $hasNumeric = true; break; }
            }
            if ($hasNumeric) {
                $ids = array_map('intval', array_values((array)$raw));
                $subjectNames = Subject::whereIn('id', $ids)->pluck('name')->toArray();
            } else {
                $subjectNames = array_values(array_filter((array)$raw, fn($v)=>$v !== null && $v !== ''));
            }
        }
        return view('teacher.profile.index', compact('teacher', 'user', 'subjectNames'));
    }

    /**
     * Show edit form for the teacher profile
     */
    public function editProfile()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $subjects = Subject::where('is_active', true)->get();
        return view('teacher.profile.edit', compact('teacher', 'user', 'subjects'));
    }

    /**
     * Update teacher profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) {
            $teacher = Teacher::create(['user_id' => $user->id]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nip' => 'nullable|string|max:255',
            'subjects' => 'nullable|array',
            'subjects.*' => 'nullable|integer|exists:subjects,id',
            'qualifications' => 'nullable|array',
            'qualifications.*' => 'nullable|string|max:255',
            'certifications' => 'nullable|array',
            'certifications.*' => 'nullable|string|max:255',
        ]);

        // Update user fields
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('phone')) $user->phone = $request->phone;
        if ($request->filled('address')) $user->address = $request->address;
        if ($request->filled('birth_date')) $user->birth_date = $request->birth_date;
        if ($request->filled('gender')) $user->gender = $request->gender;

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $file = $request->file('profile_photo');
            $filename = 'teacher_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile-photos', $filename, 'public');
            $user->profile_photo = $path;
        }
        $user->save();

        // Teacher-specific fields
        $teacher->nip = $request->nip ?? $teacher->nip;
        $teacherData = [];
        if ($request->has('subjects_present')) {
            if ($request->has('subjects')) {
                $subjectsInput = $request->input('subjects');
                if (is_array($subjectsInput)) {
                    $teacherData['subjects'] = array_values(array_filter(array_map('trim', $subjectsInput), fn($v) => $v !== null && $v !== ''));
                } elseif (is_string($subjectsInput)) {
                    $teacherData['subjects'] = array_values(array_filter(array_map('trim', explode(',', $subjectsInput)), fn($v) => $v !== null && $v !== ''));
                }
            } else {
                // explicit clear
                $teacherData['subjects'] = [];
            }
        }
        if ($request->has('qualifications_present')) {
            if ($request->has('qualifications')) {
                $qualInput = $request->input('qualifications');
                if (is_array($qualInput)) {
                    $teacherData['qualifications'] = array_values(array_filter(array_map('trim', array_values($qualInput)), fn($v) => $v !== null && $v !== ''));
                } elseif (is_string($qualInput)) {
                    $teacherData['qualifications'] = array_values(array_filter(array_map('trim', explode(',', $qualInput)), fn($v) => $v !== null && $v !== ''));
                }
            } else {
                $teacherData['qualifications'] = [];
            }
        }
        if ($request->has('certifications_present')) {
            if ($request->has('certifications')) {
                $certInput = $request->input('certifications');
                if (is_array($certInput)) {
                    $teacherData['certifications'] = array_values(array_filter(array_map('trim', array_values($certInput)), fn($v) => $v !== null && $v !== ''));
                } elseif (is_string($certInput)) {
                    $teacherData['certifications'] = array_values(array_filter(array_map('trim', explode(',', $certInput)), fn($v) => $v !== null && $v !== ''));
                }
            } else {
                $teacherData['certifications'] = [];
            }
        }

        if (!empty($teacherData)) {
            $teacher->update($teacherData);
        } else {
            // fallback: ensure the properties are saved if only nip changed
            $teacher->save();
        }

        return redirect()->route('teacher.profile')->with('success', 'Profil guru berhasil diperbarui.');
    }

    /**
     * Show grade management UI for the teacher.
     * - select class
     * - select subject
     * - input scores for students in that class
     */
    public function manageGrades(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        // Get classes taught by teacher
        $classes = Schedule::where('teacher_id', $user->id)
            ->with('classRoom')
            ->get()
            ->pluck('classRoom')
            ->unique('id')
            ->values();

        // Get list of subjects that the teacher teaches (by name mapping) or fallback to all active subjects
        $subjectQuery = Subject::where('is_active', true);
        if ($teacher && !empty($teacher->subjects)) {
                if (is_array($teacher->subjects)) {
                    $teacherSubjects = $teacher->subjects;
                } elseif (is_string($teacher->subjects)) {
                    $subjectString = $teacher->subjects;
                    $raw = trim((string)$subjectString);
                    if (str_starts_with($raw, '[') || str_starts_with($raw, '{')) {
                        $teacherSubjects = json_decode($raw, true) ?: [];
                    } else {
                        $teacherSubjects = array_map('trim', explode(',', $raw));
                    }
                } else {
                    $teacherSubjects = [];
                }
            if (!empty($teacherSubjects)) {
                // Determine if IDs were stored or names
                $numericItems = array_filter($teacherSubjects, function ($it) { return is_numeric($it); });
                if (!empty($numericItems)) {
                    $subjectQuery->whereIn('id', array_map('intval', $teacherSubjects));
                } else {
                    $subjectQuery->whereIn('name', $teacherSubjects);
                }
            }
        }
        $subjects = $subjectQuery->pluck('name', 'id')->toArray();

        $selectedClass = $request->query('class_id');
        $selectedSubject = $request->query('subject_id');
        $students = collect();
        if ($selectedClass) {
            $students = Student::where('class_id', $selectedClass)->with('user')->get();
        }

        return view('teacher.grades.manage', compact('teacher', 'classes', 'subjects', 'students', 'selectedClass', 'selectedSubject'));
    }

    /**
     * Store bulk grades from the grade management UI.
     */
    public function storeBulkGrades(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type' => 'required|in:daily,midterm,final,project',
            'semester' => 'required|string',
            'assessment_date' => 'required|date',
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100'
        ]);

        $userId = Auth::id();
        $classId = $request->class_id;
        $subjectId = $request->subject_id;

        // Ensure the teacher actually teaches this class
        $teachesClass = Schedule::where('teacher_id', $userId)->where('class_id', $classId)->exists();
        if (!$teachesClass) {
            return back()->with('error', 'Anda tidak mengajar Kelas yg dipilih.');
        }

        $created = 0;
        foreach ($request->scores as $studentId => $score) {
            if ($score === null || $score === '') continue;
            Grade::create([
                'student_id' => $studentId,
                'subject_id' => $subjectId,
                'teacher_id' => $userId,
                'score' => $score,
                'grade' => $this->calculateGrade($score),
                'assessment_type' => $request->assessment_type,
                'semester' => $request->semester,
                'notes' => $request->notes[$studentId] ?? null,
                'assessment_date' => $request->assessment_date,
            ]);
            $created++;
        }

        return redirect()->route('teacher.grades.manage', ['class_id' => $classId, 'subject_id' => $subjectId])
            ->with('success', "Berhasil menyimpan $created nilai.");
    }

    /**
     * Show teacher announcement create form
     */
    public function createAnnouncementForm()
    {
        return view('teacher.announcements.create');
    }

    /**
     * Store teacher announcement
     */
    public function createAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,academic,event,urgent',
            'target_audience' => 'required|in:all,students,teachers,parents',
            'publish_date' => 'required|date',
            'expire_date' => 'nullable|date|after:publish_date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
                $announcementData = [
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'type' => $request->input('type'),
                'target_audience' => $request->input('target_audience'),
                'created_by' => Auth::id(),
                'publish_date' => $request->input('publish_date'),
                'expire_date' => $request->input('expire_date'),
                ];

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = 'announcement_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('announcement-images', $filename, 'public');
                    $announcementData['image'] = $path;
                }

                Announcement::create($announcementData);
        } catch (\Exception $e) {
                \Log::error('Failed to create teacher announcement', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal membuat pengumuman: ' . $e->getMessage());
        }

        return redirect()->route('teacher.dashboard')->with('success', 'Pengumuman berhasil dibuat!');
    }
}
