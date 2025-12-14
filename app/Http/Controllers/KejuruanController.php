<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Announcement;
use App\Models\StudentSkill;
use App\Models\TeachingMaterial;
use App\Models\TrainingClass;
use App\Models\StudentApplication;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class KejuruanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('kejuruan');
    }

    /**
     * Dashboard untuk kejuruan
     */
    public function dashboard()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->with(['classRoom', 'skills'])->first();



        // Get training classes that the student is enrolled in
        $trainingClasses = collect();
        if ($student) {
            $trainingClasses = $student->trainingClasses()
                ->withCount('students')
                ->withPivot(['enrolled_at', 'status'])
                ->orderBy('start_at', 'desc')
                ->get();
        }

        // Get training class IDs for materials count
        $trainingClassIds = $trainingClasses->pluck('id')->toArray();

        // Count materials from enrolled training classes
        $materialsCount = TeachingMaterial::whereIn('training_class_id', $trainingClassIds ?: [0])
            ->where('is_visible', true)
            ->count();

        // Get announcements
        $announcements = Announcement::where(function($q) {
                $q->where('target_audience', 'all')
                  ->orWhere('target_audience', 'students');
            })
            ->where('is_active', true)
            ->where('publish_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('expire_date')
                      ->orWhere('expire_date', '>=', now());
            })
            ->latest()
            ->take(10)
            ->get();

        // Get recent grades
        $recentGrades = collect();
        $averageGrade = 0;
        if ($student) {
            $recentGrades = Grade::where('student_id', $student->id)
                ->with(['subject', 'trainingClass'])
                ->latest()
                ->take(5)
                ->get();

            $allGrades = Grade::where('student_id', $student->id)->get();
            if ($allGrades->count() > 0) {
                $averageGrade = $allGrades->avg('score');
            }
        }

        return view('kejuruan.dashboard', compact(
            'student',
            'trainingClasses',
            'materialsCount',
            'announcements',
            'recentGrades',
            'averageGrade'
        ));
    }

    /**
     * Profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->with('classRoom')->first();
        $application = StudentApplication::where('email', $user->email)->latest()->first();
        $documents = Document::where('documentable_type', Student::class)
            ->where('documentable_id', $student?->id)
            ->get();
        $portfolios = \App\Models\StudentPortfolio::where('student_id', $student?->id)->get();

        // if student doesn't exist, redirect to create profile
        if (!$student) {
            return redirect()->route('student.profile.create')->with('info', 'Silakan lengkapi profil siswa Anda.');
        }

        return view('student.profile', compact('student', 'documents', 'application', 'portfolios'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        // Reuse StudentController's updateProfile logic
        return app(StudentController::class)->updateProfile($request);
    }

    /**
     * List training classes
     */
    public function trainingIndex()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        // Show all training classes open to kejuruan
        $classes = TrainingClass::where('open_to_kejuruan', true)
            ->orderBy('start_at', 'desc')
            ->get();

        $hasActiveTraining = false;
        if ($student) {
            $hasActiveTraining = $student->trainingClasses()
                ->wherePivot('status', 'enrolled')
                ->where(function($q) {
                    $q->whereNull('end_at')
                      ->orWhere('end_at', '>=', now());
                })->exists();
        }
        return view('student.training.index', compact('classes', 'student', 'hasActiveTraining'));
    }

    /**
     * Show training class detail
     */
    public function trainingShow($id)
    {
        $training = TrainingClass::withCount('students')->findOrFail($id);
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        $hasActiveTraining = false;
        if ($student) {
            $hasActiveTraining = $student->trainingClasses()
                ->wherePivot('status', 'enrolled')
                ->where(function($q) {
                    $q->whereNull('end_at')
                      ->orWhere('end_at', '>=', now());
                })->exists();
        }
        return view('student.training.show', compact('training', 'student', 'hasActiveTraining'));
    }
    /**
     * Attendance recap for kejuruan (reuse StudentController logic)
     */
    public function attendance(Request $request)
    {
        return app(\App\Http\Controllers\StudentController::class)->attendance($request);
    }
    /**
     * Enroll in training class
     */
    public function enrollTraining($id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        $training = TrainingClass::findOrFail($id);

        if (!$training->open_to_kejuruan) {
            return back()->with('error', 'Pelatihan ini tidak tersedia untuk kejuruan');
        }

        // Check capacity
        $enrolledCount = $training->students()->count();
        if ($training->capacity && $enrolledCount >= $training->capacity) {
            return back()->with('error', 'Pelatihan sudah penuh');
        }

        // If the student is currently enrolled in another active training, disallow new enrollments
        $hasActiveOtherTraining = $student->trainingClasses()
            ->wherePivot('status', 'enrolled')
            ->where(function($q) {
                $q->whereNull('end_at')
                  ->orWhere('end_at', '>=', now());
            })
            ->where('training_classes.id', '!=', $training->id)
            ->exists();
        if ($hasActiveOtherTraining) {
            return back()->with('error', 'Anda sedang mengikuti pelatihan lain. Selesaikan dulu sebelum mendaftar pelatihan baru.');
        }

        // Enroll if not already
        if (!$student->trainingClasses()->where('training_classes.id', $training->id)->exists()) {
            $student->trainingClasses()->attach($training->id, [
                'enrolled_at' => now(),
                'status' => 'enrolled'
            ]);
        }

        return back()->with('success', 'Berhasil mendaftar pelatihan');
    }

    /**
     * Unenroll from training class
     */
    public function unenrollTraining($id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return back()->with('error', 'Profil siswa tidak ditemukan');
        }

        $training = TrainingClass::findOrFail($id);
        $student->trainingClasses()->detach($training->id);

        return back()->with('success', 'Berhasil membatalkan pendaftaran');
    }

    /**
     * List materials from enrolled training classes
     */
    public function materials(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        $materials = collect();
        $isKejuruan = true;

        if ($student) {
            $trainingClassIds = $student->trainingClasses()->pluck('training_classes.id')->toArray();
            $filterTrainingClass = (int) $request->query('training_class_id', 0);
            // Only show published materials for kejuruan students
            $query = TeachingMaterial::whereIn('training_class_id', $trainingClassIds ?: [0]);
            if ($filterTrainingClass) {
                $query->where('training_class_id', $filterTrainingClass);
            }
            $materials = $query->where('is_visible', true)
                ->with(['trainingClass', 'teacher', 'submissions' => function($q) use ($student) { $q->where('student_id', $student->id)->orderBy('created_at', 'desc'); }])
                ->orderBy('title', 'asc')
                ->paginate(20);
        }

        return view('student.materials.index', compact('materials', 'isKejuruan'));
    }

    /**
     * Grades page
     */
    public function grades()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        $grades = collect();
        $skills = collect();

        if ($student) {
            $grades = Grade::where('student_id', $student->id)
                ->with(['subject', 'teacher', 'trainingClass'])
                ->orderBy('semester')
                ->orderBy('assessment_date')
                ->get();

            $skills = StudentSkill::where('student_id', $student->id)
                ->with('assessedBy')
                ->orderBy('assessed_date', 'desc')
                ->get();
        }

        return view('student.grades', compact('grades', 'skills'));
    }

    /**
     * Grade history
     */
    public function gradeHistory()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('kejuruan.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $gradeHistory = \App\Models\StudentGradeHistory::where('student_id', $student->id)
            ->with('classRoom')
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $currentGrades = Grade::where('student_id', $student->id)
            ->with('subject')
            ->orderBy('assessment_date', 'desc')
            ->get();

        $trainingHistory = $student->trainingClasses()
            ->withPivot(['enrolled_at', 'status'])
            ->get();

        return view('student.grade-history', compact('gradeHistory', 'currentGrades', 'trainingHistory', 'student'));
    }

    /**
     * Announcements page
     */
    public function announcements()
    {
        $announcements = Announcement::where(function($q) {
                $q->where('target_audience', 'all')
                  ->orWhere('target_audience', 'students');
            })
            ->where('is_active', true)
            ->where('publish_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('expire_date')
                      ->orWhere('expire_date', '>=', now());
            })
            ->latest()
            ->paginate(10);

        return view('student.announcements.index', compact('announcements'));
    }

    /**
     * Schedules (for kejuruan, might be empty or show training schedules)
     */
    public function schedules()
    {
        $schedules = collect()->groupBy('day_of_week');
        return view('student.schedules', compact('schedules'));
    }
}
