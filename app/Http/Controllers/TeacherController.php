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
use App\Models\TrainingClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        // Get all schedules for the teacher (no day filter)
        $allSchedules = $schedules;
        $allSchedulesCount = $allSchedules->count();

        $scheduleClassIds = Schedule::where('teacher_id', $user->id)
            ->pluck('class_id')
            ->unique()
            ->filter()
            ->values()
            ->all();
        $homeroomClassIds = ClassRoom::where('homeroom_teacher_id', $user->id)->pluck('id')->unique()->filter()->values()->all();
        $classIds = array_values(array_unique(array_merge($scheduleClassIds, $homeroomClassIds)));
        $classes = collect();
        if (!empty($classIds)) {
            $classes = ClassRoom::whereIn('id', $classIds)
                ->with('homeroomTeacher')
                ->withCount('students')
                ->get();
        }
        if (!is_object($classes) || !method_exists($classes, 'count')) {
            $classes = collect();
        }

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

        // Fetch training classes where this teacher is trainer (support legacy where trainer_id might be set to user id)
        $trainingClasses = collect();
        if ($teacher) {
            $trainingClasses = TrainingClass::where('is_active', true)
                ->where(function ($q) use ($teacher, $user) {
                    $q->where('trainer_id', $teacher->id)->orWhere('trainer_id', $user->id);
                })
                ->withCount('students')
                ->get();
        }

        return view('teacher.dashboard', compact('teacher', 'schedules', 'classes', 'totalStudents', 'recentGrades', 'allSchedules', 'allSchedulesCount', 'lowGradesStudents', 'trainingClasses'))
            ->with('recentActivities', $recentActivities)
            ->with('announcements', $announcements);
    }

    public function students()
    {
        $user = Auth::user();
        // Collect class IDs from schedules and fetch ClassRoom with students to ensure proper relationships
        $scheduleClassIds = Schedule::where('teacher_id', $user->id)
            ->pluck('class_id')
            ->unique()
            ->filter()
            ->values()
            ->all();
        $homeroomClassIds = ClassRoom::where('homeroom_teacher_id', $user->id)->pluck('id')->unique()->filter()->values()->all();
        $classIds = array_values(array_unique(array_merge($scheduleClassIds, $homeroomClassIds)));

        $classes = collect();
        if (!empty($classIds)) {
            $classes = ClassRoom::whereIn('id', $classIds)
                ->with(['students.user'])
                ->withCount('students')
                ->get();
        }

        // Defensive: ensure $classes is a Collection (avoid accidental string values in views)
        $classes = collect($classes);

        return view('teacher.students', compact('classes'));
    }

    public function studentDetail($id)
    {
        $student = Student::with(['user', 'classRoom'])->findOrFail($id);

        // Check if teacher teaches this student
        $hasAccess = Schedule::where('teacher_id', Auth::id())
            ->where('class_id', $student->class_id)
            ->exists();

        // Also allow access if the teacher is the homeroom teacher for the student's class
        $isHomeroom = ClassRoom::where('id', $student->class_id)
            ->where('homeroom_teacher_id', Auth::id())
            ->exists();

        // As a final fallback, allow access if the teacher is explicitly assigned via the teacher profile (teacher->user_id),
        // or the teacher is a trainer for any training class that the student is enrolled in.
        $teacherModel = Teacher::where('user_id', Auth::id())->first();
        $isAssignedViaTeacherModel = false;
        if ($teacherModel) {
            // No direct mapping required; leave default false
            $isAssignedViaTeacherModel = false;
        }

        // Also allow trainer access: if the teacher is trainer for any of the training classes the student is enrolled in
        $isTrainerOfStudent = false;
        if ($teacherModel) {
            $trainingIds = $student->trainingClasses()->pluck('training_classes.id');
            if ($trainingIds && $trainingIds->count() > 0) {
                $isTrainerOfStudent = TrainingClass::whereIn('id', $trainingIds)
                    ->where(function ($q) use ($teacherModel) {
                        $q->where('trainer_id', $teacherModel->id);
                    })->exists();
            }
        }
        // Support legacy trainer id being user id
        if (!$isTrainerOfStudent) {
            $trainingIds = $student->trainingClasses()->pluck('training_classes.id');
            if ($trainingIds && $trainingIds->count() > 0) {
                $isTrainerOfStudent = TrainingClass::whereIn('id', $trainingIds)
                    ->where(function ($q) {
                        $q->where('trainer_id', Auth::id());
                    })->exists();
            }
        }

        if (!$hasAccess && !$isHomeroom && !$isAssignedViaTeacherModel && !$isTrainerOfStudent) {
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

    public function classDetail($id)
    {
        $user = Auth::user();
        $classRoom = ClassRoom::with(['students.user', 'schedules.subject', 'homeroomTeacher'])->findOrFail($id);

        // Determine whether the teacher is allowed to view this class (teaches on schedule or is homeroom)
        $teachesClass = Schedule::where('teacher_id', $user->id)->where('class_id', $id)->exists();
        $isHomeroom = ($classRoom->homeroom_teacher_id ?? null) === $user->id;

        if (!$teachesClass && !$isHomeroom) {
            abort(403, 'Anda tidak memiliki akses ke data kelas ini.');
        }

        $students = $classRoom->students()->with('user')->get();
        $schedules = Schedule::where('class_id', $id)->with(['subject', 'teacher'])->get();

        return view('teacher.classes.detail', compact('classRoom', 'students', 'schedules'));
    }

    /**
     * Show a training-class detail page for trainers.
     */
    public function trainingClassDetail($id)
    {
        $user = Auth::user();
        $teacherModel = Teacher::where('user_id', $user->id)->first();

        $trainingClass = TrainingClass::with(['students.user', 'trainer'])->findOrFail($id);

        // check if the current user is the trainer for this training class
        $isTrainer = false;
        if ($trainingClass->trainer_id && $teacherModel && $trainingClass->trainer_id === $teacherModel->id) {
            $isTrainer = true;
        }
        // Support legacy case where trainer_id might reference user id directly
        if (!$isTrainer && $trainingClass->trainer_id === $user->id) {
            $isTrainer = true;
        }

        if (!$isTrainer) {
            abort(403, 'Anda tidak memiliki akses ke data pelatihan ini.');
        }

        $students = $trainingClass->students()->with('user')->get();

        // Get subjects for this training class from teaching materials
        $trainingSubjects = \App\Models\Subject::whereHas('teachingMaterials', function($query) use ($id) {
            $query->where('training_class_id', $id);
        })->where('is_active', true)->get();

        // If no specific training subjects, suggest a relevant subject based on training class title
        $suggestedSubject = null;
        if ($trainingSubjects->isEmpty()) {
            $title = strtolower($trainingClass->title);
            if (str_contains($title, 'komputer') || str_contains($title, 'tik') || str_contains($title, 'teknologi')) {
                $suggestedSubject = \App\Models\Subject::where('is_active', true)
                    ->where(function($query) {
                        $query->where('name', 'like', '%TIK%')
                              ->orWhere('name', 'like', '%Komputer%')
                              ->orWhere('name', 'like', '%Teknologi%');
                    })->first();
            }
        }

        return view('teacher.training-classes.detail', compact('trainingClass', 'students', 'trainingSubjects', 'suggestedSubject'));
    }

    public function addGrade(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'training_class_id' => 'nullable|exists:training_classes,id',
            'score' => 'required|numeric|min:0|max:100',
            'assessment_type' => 'required|in:daily,midterm,final,project',
            'semester' => 'required|string',
            'notes' => 'nullable|string',
            'assessment_date' => 'required|date',
        ]);

        $grade = Grade::create([
            'student_id' => $request->student_id,
            'subject_id' => $request->subject_id,
            'training_class_id' => $request->training_class_id,
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

    public function updateGrade(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);
        if ($grade->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // prevent editing grades for kejuruan students in teacher UI
        $student = $grade->student;
        if ($student && optional($student->classRoom)->grade_level === 'kejuruan') {
            return back()->with('error', 'Tidak dapat mengubah nilai siswa kejuruan melalui halaman ini.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'score' => 'required|numeric|min:0|max:100',
            'assessment_type' => 'required|in:daily,midterm,final,project',
            'semester' => 'required|string',
            'assessment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $grade->update([
            'subject_id' => $request->subject_id,
            'score' => $request->score,
            'grade' => $this->calculateGrade($request->score),
            'assessment_type' => $request->assessment_type,
            'semester' => $request->semester,
            'notes' => $request->notes,
            'assessment_date' => $request->assessment_date,
        ]);

        return back()->with('success', 'Nilai berhasil diperbarui!');
    }

    public function destroyGrade($id)
    {
        $grade = Grade::findOrFail($id);
        if ($grade->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $student = $grade->student;
        if ($student && optional($student->classRoom)->grade_level === 'kejuruan') {
            return back()->with('error', 'Tidak dapat menghapus nilai siswa kejuruan melalui halaman ini.');
        }
        $grade->delete();
        return back()->with('success', 'Nilai berhasil dihapus!');
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

    public function updateSkill(Request $request, $id)
    {
        $skill = StudentSkill::findOrFail($id);
        if ($skill->assessed_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'skill_name' => 'required|string|max:255',
            'skill_category' => 'required|in:academic,technical,soft_skill,language,art,sport',
            'proficiency_level' => 'required|in:beginner,intermediate,advanced,expert',
            'description' => 'nullable|string'
        ]);

        $skill->update([
            'skill_name' => $request->skill_name,
            'skill_category' => $request->skill_category,
            'proficiency_level' => $request->proficiency_level,
            'description' => $request->description,
            'assessed_date' => now()
        ]);
        return back()->with('success', 'Keterampilan berhasil diperbarui!');
    }

    public function destroySkill($id)
    {
        $skill = StudentSkill::findOrFail($id);
        if ($skill->assessed_by !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $skill->delete();
        return back()->with('success', 'Keterampilan berhasil dihapus!');
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
        // load materials for classes the teacher manages, grouped by class_id
        $classIds = $schedules->flatten()->pluck('class_id')->unique()->filter()->values()->all();
        $materialsByClass = [];
        if (!empty($classIds)) {
            $materials = \App\Models\TeachingMaterial::where('teacher_id', Auth::id())
                ->where(function($q) use ($classIds){
                    $q->whereNull('class_id')
                      ->orWhereIn('class_id', $classIds);
                })
                ->where('is_visible', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('class_id');
            $materialsByClass = $materials;
        }

        return view('teacher.schedules', compact('schedules', 'materialsByClass'));
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
            'password' => 'nullable|string|min:8|confirmed',
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
        // Update password if supplied
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
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

        // Get classes taught by teacher (from schedule) and classes where the teacher is homeroom
        $scheduleClassIds = Schedule::where('teacher_id', $user->id)
            ->pluck('class_id')
            ->unique()
            ->filter()
            ->values()
            ->all();
        $homeroomClassIds = ClassRoom::where('homeroom_teacher_id', $user->id)->pluck('id')->unique()->filter()->values()->all();
        $classIds = array_values(array_unique(array_merge($scheduleClassIds, $homeroomClassIds)));
        $classes = collect();
        if (!empty($classIds)) {
            $classes = ClassRoom::whereIn('id', $classIds)
                ->with('homeroomTeacher')
                ->withCount('students')
                ->get();
        }

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

        // Load training classes where teacher acts as trainer
        $trainingClasses = collect();
        if ($teacher) {
            $trainingClasses = TrainingClass::where('is_active', true)
                ->where(function ($q) use ($teacher, $user) {
                    $q->where('trainer_id', $teacher->id)->orWhere('trainer_id', $user->id);
                })
                ->withCount('students')
                ->get();
        }

        $selectedClass = $request->query('class_id');
        $selectedTraining = $request->query('training_class_id');
        $selectedSubject = $request->query('subject_id');
        $students = collect();
        $existingGrades = collect();
        if ($selectedClass) {
            $students = Student::where('class_id', $selectedClass)->with('user')->get();
            if ($selectedSubject && $request->get('assessment_type') && $request->get('semester') && $request->get('assessment_date')) {
                $gradeQuery = Grade::whereIn('student_id', $students->pluck('id')->toArray())
                    ->where('subject_id', $selectedSubject)
                    ->where('assessment_type', $request->get('assessment_type'))
                    ->where('semester', $request->get('semester'))
                    ->where('assessment_date', $request->get('assessment_date'));
                $existingGrades = $gradeQuery->get()->keyBy('student_id');
            }
        } elseif ($selectedTraining) {
            // Load students from training class if provided and ensure teacher is trainer
            $training = TrainingClass::with('students.user')->find($selectedTraining);
            if ($training) {
                $teacherModel = Teacher::where('user_id', $user->id)->first();
                $isTrainer = false;
                if ($training->trainer_id && $teacherModel && $training->trainer_id === $teacherModel->id) {
                    $isTrainer = true;
                }
                if (!$isTrainer && $training->trainer_id === $user->id) {
                    $isTrainer = true;
                }
                if ($isTrainer) {
                    $students = $training->students()->with('user')->get();
                    // preload existing grades for the selected subject/assessment to show in inputs
                    if ($selectedSubject && $request->get('assessment_type') && $request->get('semester') && $request->get('assessment_date')) {
                        $gradeQuery = Grade::whereIn('student_id', $students->pluck('id')->toArray())
                            ->where('subject_id', $selectedSubject)
                            ->where('assessment_type', $request->get('assessment_type'))
                            ->where('semester', $request->get('semester'))
                            ->where('assessment_date', $request->get('assessment_date'));
                        $existingGrades = $gradeQuery->get()->keyBy('student_id');
                    } else {
                        $existingGrades = collect();
                    }
                } else {
                    return back()->with('error', 'Anda tidak memiliki akses untuk mengelola nilai pelatihan ini.');
                }
            }
        }

        return view('teacher.grades.manage', compact('teacher', 'classes', 'subjects', 'students', 'selectedClass', 'selectedSubject', 'trainingClasses', 'selectedTraining', 'existingGrades'));
    }

    /**
     * Store bulk grades from the grade management UI.
     */
    public function storeBulkGrades(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:classes,id|required_without:training_class_id',
            'training_class_id' => 'nullable|exists:training_classes,id|required_without:class_id',
            'subject_id' => 'required|exists:subjects,id',
            'assessment_type' => 'required|in:daily,midterm,final,project',
            'semester' => 'required|string',
            'assessment_date' => 'required|date',
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:100'
        ]);

        $userId = Auth::id();
        $classId = $request->class_id;
        $trainingClassId = $request->training_class_id;
        $subjectId = $request->subject_id;

        // Ensure teacher has rights: for class check schedule or homeroom; for training, teacher must be trainer
        if ($trainingClassId) {
            $training = TrainingClass::find($trainingClassId);
            if (!$training) return back()->with('error', 'Pelatihan tidak ditemukan.');
            $teacherModel = Teacher::where('user_id', $userId)->first();
            $isTrainer = false;
            if ($training->trainer_id && $teacherModel && $training->trainer_id === $teacherModel->id) {
                $isTrainer = true;
            }
            if (!$isTrainer && $training->trainer_id === $userId) {
                $isTrainer = true;
            }
            if (!$isTrainer) {
                return back()->with('error', 'Anda tidak memiliki akses untuk mengelola nilai pelatihan ini.');
            }
        } else {
            // Ensure the teacher actually teaches this class
            $teachesClass = Schedule::where('teacher_id', $userId)->where('class_id', $classId)->exists();
            if (!$teachesClass) {
                return back()->with('error', 'Anda tidak mengajar Kelas yg dipilih.');
            }
        }

        $created = 0;
        $updated = 0;
        DB::beginTransaction();
        try {
            foreach ($request->scores as $studentId => $score) {
                if ($score === null || $score === '') continue;
                // Prevent grading kejuruan students via teacher manage UI (training/class)
                $student = Student::find($studentId);
                if ($student && optional($student->classRoom)->grade_level === 'kejuruan') {
                    // skip silently; or you may choose to collect and return messages
                    continue;
                }

                $criteria = [
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'assessment_type' => $request->assessment_type,
                    'semester' => $request->semester,
                    'assessment_date' => $request->assessment_date,
                ];
                if ($trainingClassId) {
                    $criteria['training_class_id'] = $trainingClassId;
                }

                $values = [
                    'teacher_id' => $userId,
                    'score' => $score,
                    'grade' => $this->calculateGrade($score),
                    'notes' => $request->notes[$studentId] ?? null,
                ];
                if ($trainingClassId) {
                    $values['training_class_id'] = $trainingClassId;
                }

                $gradeModel = Grade::where($criteria)->first();
                if ($gradeModel) {
                    $gradeModel->update($values);
                    $updated++;
                } else {
                    Grade::create(array_merge($criteria, $values));
                    $created++;
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }

        $routeParams = ['subject_id' => $subjectId];
        if ($trainingClassId) $routeParams['training_class_id'] = $trainingClassId; else $routeParams['class_id'] = $classId;
        $msg = "Berhasil menyimpan $created nilai.";
        if ($updated) $msg .= ", $updated nilai diperbarui.";
        return redirect()->route('teacher.grades.manage', $routeParams)
            ->with('success', $msg);
    }

    public function graduationManagement()
    {
        $user = Auth::user();

        // Get all classes where teacher is homeroom teacher
        $classes = ClassRoom::where('homeroom_teacher_id', $user->id)
            ->with(['students.user', 'students.grades'])
            ->get();

        // Also include training classes where teacher is trainer, with students
        $teacherModel = Teacher::where('user_id', $user->id)->first();
        $trainingClasses = collect();
        if ($teacherModel) {
            $trainingClasses = TrainingClass::where('is_active', true)
                ->where(function ($q) use ($teacherModel, $user) {
                    $q->where('trainer_id', $teacherModel->id)->orWhere('trainer_id', $user->id);
                })
                ->with(['students.user', 'students.grades'])
                ->get();
        }

        return view('teacher.graduation', compact('classes', 'trainingClasses'));
    }

    public function processGraduation(Request $request, $studentId)
    {
        $request->validate([
            'status' => 'required|in:passed,failed',
            'academic_year' => 'required|numeric',
            'semester' => 'required|in:1,2',
            'notes' => 'nullable|string'
        ]);

        $student = Student::with(['classRoom', 'grades', 'user', 'trainingClasses'])->findOrFail($studentId);

        // Check for training class context from request
        $trainingClassId = $request->input('training_class_id');
        $currentClass = null;
        $currentClassName = null;
        $historyClassId = null;
        if ($trainingClassId) {
            $trainingClass = TrainingClass::find($trainingClassId);
            if ($trainingClass) {
                $currentClass = $trainingClass; // keep model for checking
                $currentClassName = $trainingClass->title;
                $historyClassId = null; // no class_id for training class in history
            } else {
                return back()->with('error', 'Kelas pelatihan tidak ditemukan.');
            }
        } else {
            $currentClass = $student->classRoom;
            $currentClassName = optional($currentClass)->name ?? null;
            $historyClassId = optional($currentClass)->id ?? null;
        }

        if (!$currentClass) {
            return back()->with('error', 'Siswa tidak memiliki kelas.');
        }

        // Calculate average grade
        $averageGrade = $student->grades()
            ->where('academic_year', $request->academic_year)
            ->where('semester', $request->semester)
            ->avg('score');

        // Get all grades for this period
        $subjectsGrades = $student->grades()
            ->where('academic_year', $request->academic_year)
            ->where('semester', $request->semester)
            ->with('subject')
            ->get()
            ->map(function ($grade) {
                return [
                    'subject' => $grade->subject->name,
                    'score' => $grade->score,
                    'notes' => $grade->notes
                ];
            });

        DB::beginTransaction();
        try {
            // Save grade history
            \App\Models\StudentGradeHistory::create([
                'student_id' => $student->id,
                'class_id' => $historyClassId,
                'class_name' => $currentClassName,
                'academic_year' => $request->academic_year,
                'semester' => $request->semester,
                'average_grade' => $averageGrade,
                'subjects_grades' => $subjectsGrades->toArray(),
                'status' => $request->status,
                'notes' => $request->notes,
                'completed_at' => now()
            ]);

            if ($request->status == 'passed') {
                // For training class students, treat graduation as final (become Kejuruan/Alumni)
                if (isset($trainingClassId) && $trainingClassId) {
                    $nextClass = 'graduated';
                } else {
                    $nextClass = $this->determineNextClass(optional($currentClass)->name);
                }

                if ($nextClass === 'graduated') {
                    // Student becomes Kejuruan (alumni)
                    $student->user->update(['role' => 'kejuruan']);
                    $student->update(['status' => 'graduated']);

                    // Create alumni record
                    \App\Models\Alumni::create([
                        'student_id' => $student->id,
                        'graduation_year' => $request->academic_year,
                        'final_grade' => $averageGrade,
                        'notes' => 'Lulus dari ' . $currentClass->name
                    ]);
                } else {
                    // Find or create next class
                    $newClass = ClassRoom::firstOrCreate(
                        ['name' => $nextClass],
                        ['capacity' => 30]
                    );

                    $student->update(['class_id' => $newClass->id]);
                }
            } else {
                // Failed - stays in same class
                $student->update(['status' => 'active']);
            }

            DB::commit();
            return back()->with('success', 'Status kelulusan berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function determineNextClass($currentClassName)
    {
        // Parse class name (e.g., "1 SD", "2 SMP", "3 SMA")
        preg_match('/(\d+)\s+(SD|SMP|SMA)/', $currentClassName, $matches);

        if (empty($matches)) {
            return $currentClassName; // If can't parse, stay in same class
        }

        $grade = (int)$matches[1];
        $level = $matches[2];

        // Graduation rules
        if ($level === 'SMA' && $grade === 3) {
            return 'graduated'; // 3 SMA graduates
        } elseif ($level === 'SMA') {
            return ($grade + 1) . ' SMA';
        } elseif ($level === 'SMP' && $grade === 3) {
            return '1 SMA'; // 3 SMP -> 1 SMA
        } elseif ($level === 'SMP') {
            return ($grade + 1) . ' SMP';
        } elseif ($level === 'SD' && $grade === 6) {
            return '1 SMP'; // 6 SD -> 1 SMP
        } elseif ($level === 'SD') {
            return ($grade + 1) . ' SD';
        }

        return $currentClassName;
    }
}
