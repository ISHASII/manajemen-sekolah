<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\Announcement;
use App\Models\StudentSkill;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\StudentApplication;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Allow both student and kejuruan roles
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isStudent()) {
                abort(403, 'Access denied.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->with('classRoom')->first();

        $announcements = Announcement::where('target_audience', 'all')
            ->orWhere('target_audience', 'students')
            ->where('is_active', true)
            ->where('publish_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('expire_date')
                      ->orWhere('expire_date', '>=', now());
            })
            ->latest()
            ->take(10)
            ->get();

        // If no Student record yet (i.e., newly registered user pending approval)
        // attempt to find a StudentApplication for the current user's email so we can
        // show NISN or other basic info in dashboard while pending.
        // Load any related application in case the Student record hasn't been populated or nisn wasn't set
        $application = StudentApplication::where('email', $user->email)->latest()->first();
        // If not found by email, try some fallbacks: phone, name, birth_date
        if (!$application) {
            $applicationQuery = StudentApplication::query();
            $applicationQuery->where(function($q) use ($user, $student) {
                $q->where('email', $user->email);
                if ($user->phone) {
                    $q->orWhere('phone', $user->phone);
                }
                if ($student && $student->birth_date) {
                    $q->orWhere('birth_date', $student->birth_date);
                }
                if ($user->name) {
                    $q->orWhere('student_name', $user->name);
                }
            });
            $application = $applicationQuery->latest()->first();
        }

        $schedules = collect();
        $grades = collect();
        $todaySchedules = collect();
        $recentGrades = collect();
        $attendancePercent = 0;
        $averageGrade = 0;
        $pendingAssignments = 0;

        // Indonesian day names
        $daysInIndonesian = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];

        if ($student && $student->classRoom) {
            $schedules = Schedule::where('class_id', $student->classRoom->id)
                ->with(['subject', 'teacher'])
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get();

            $grades = Grade::where('student_id', $student->id)
                ->with(['subject', 'teacher'])
                ->latest()
                ->take(10)
                ->get();

            // todays schedules (e.g., monday, tuesday) filter
            $today = strtolower(\Carbon\Carbon::now()->format('l'));
            $todaySchedules = $schedules->where('day_of_week', $today);

            // latest grades limited for dashboard
            $recentGrades = Grade::where('student_id', $student->id)
                ->with(['subject', 'teacher'])
                ->latest()
                ->take(5)
                ->get();

            // Calculate average grade
            if ($grades->count() > 0) {
                $averageGrade = $grades->avg('score');
            }

            // Calculate pending assignments (materials without submissions)
            $materials = \App\Models\TeachingMaterial::where('class_id', $student->classRoom->id)
                ->where('is_visible', true)
                ->get();
            $submittedMaterialIds = \App\Models\StudentSubmission::where('student_id', $student->id)
                ->pluck('material_id')
                ->toArray();
            $pendingAssignments = $materials->whereNotIn('id', $submittedMaterialIds)->count();

            // Mock attendance (you can implement real attendance tracking later)
            $attendancePercent = 85;
        }

        return view('student.dashboard', compact('student', 'application', 'announcements', 'schedules', 'grades', 'todaySchedules', 'recentGrades', 'attendancePercent', 'averageGrade', 'pendingAssignments', 'daysInIndonesian'));
    }

    public function profile()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->with('classRoom')->first();
        $application = StudentApplication::where('email', $user->email)->latest()->first();
        // If student doesn't exist, redirect to the internal profile creation page
        if (!$student) {
            return redirect()->route('student.profile.create')->with('info', 'Silakan lengkapi profil siswa Anda.');
        }

        $documents = Document::where('documentable_type', Student::class)
            ->where('documentable_id', $student->id)
            ->get();
        $portfolios = \App\Models\StudentPortfolio::where('student_id', $student->id)->get();

        return view('student.profile', compact('student', 'documents', 'application', 'portfolios'));
    }

    /**
     * Show the create form for logged-in student to create internal profile.
     */
    public function create()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        if ($student) {
            return redirect()->route('student.profile')->with('info', 'Profil siswa sudah tersedia.');
        }

        // No classes passed here; class assignment is handled by admin.
        return view('student.profile.create');
    }

    /**
     * Store the newly created student profile for the logged-in user.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $existing = Student::where('user_id', $user->id)->first();
        if ($existing) {
            return redirect()->route('student.profile')->with('info', 'Profil siswa sudah tersedia.');
        }

        $validated = $request->validate([
            'student_id' => 'required|string|max:255|unique:students,student_id',
            'class_id' => 'nullable|exists:classes,id',
            'nisn' => 'nullable|string|max:50|unique:students,nisn',
            'place_of_birth' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'religion' => 'required|in:islam,kristen,katolik,hindu,budha,khonghucu',
            'address' => 'required|string',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:50',
            'parent_address' => 'required|string',
            'parent_job' => 'nullable|string|max:255',
            'health_info' => 'nullable|array',
            'disability_info' => 'nullable|array',
            'education_history' => 'nullable|array',
            'interests_talents' => 'nullable|array',
            'is_orphan' => 'nullable|boolean',
            'enrollment_date' => 'required|date',
        ]);

        $student = Student::create(array_merge($validated, [
            'user_id' => $user->id
        ]));

        // If the user had previously submitted an application with a profile photo, set the profile photo
        $application = StudentApplication::where('email', $user->email)->latest()->first();
        if ($application && isset($application->documents) && is_array($application->documents)) {
            $photoDoc = collect($application->documents)->firstWhere('type', 'photo');
            if ($photoDoc && isset($photoDoc['path']) && Storage::disk('public')->exists($photoDoc['path'])) {
                $ext = pathinfo($photoDoc['path'], PATHINFO_EXTENSION);
                $destFilename = 'profile_' . $user->id . '_' . time() . '.' . $ext;
                $destPath = 'profile-photos/' . $destFilename;
                Storage::disk('public')->copy($photoDoc['path'], $destPath);
                $user->profile_photo = $destPath;
                $user->save();
            }
        }

        return redirect()->route('student.profile')->with('success', 'Profil siswa berhasil dibuat.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return back()->with('error', 'Profil siswa tidak ditemukan.');
        }

        // Accept a broader set of editable fields from students
        $request->validate([
            'interests_talents' => 'nullable|array',
            'health_info' => 'nullable|array',
            'disability_info' => 'nullable|array',
            'place_of_birth' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|in:islam,kristen,katolik,hindu,budha,khonghucu',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:50',
            'parent_address' => 'nullable|string',
            'parent_job' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nisn' => 'nullable|string|max:50|unique:students,nisn,' . $student->id,
            'job_interest' => 'nullable|string|max:255',
            'cv_link' => 'nullable|url|max:2048',
            'portfolio_links' => 'nullable|string',
        ]);

        // $user and $student are already loaded

        // Update user-level fields if present (e.g., birth_date)
        $userUpdated = [];
        if ($request->filled('birth_date')) {
            $userUpdated['birth_date'] = $request->birth_date;
        }
        if ($request->filled('gender')) {
            $userUpdated['gender'] = $request->gender;
        }
        // update email and phone directly
        if ($request->filled('email')) {
            $userUpdated['email'] = $request->email;
        }
        // Password is optional; if provided, hash it and update the user's password
        if ($request->filled('password')) {
            $userUpdated['password'] = Hash::make($request->password);
        }
        if ($request->filled('phone')) {
            $userUpdated['phone'] = $request->phone;
        }
        if ($request->filled('address')) {
            // update user address as well
            $userUpdated['address'] = $request->address;
        }
        // handle profile photo upload if present
        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $file = $request->file('profile_photo');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile-photos', $filename, 'public');
            $userUpdated['profile_photo'] = $path;
        }
        if (!empty($userUpdated)) {
            $user->update($userUpdated);
        }

        // Update student-level fields
        $studentFields = array_filter([
            'interests_talents' => $request->interests_talents ?? $student->interests_talents,
            'health_info' => $request->health_info ?? $student->health_info,
            'disability_info' => $request->disability_info ?? $student->disability_info,
            'place_of_birth' => $request->place_of_birth ?? $student->place_of_birth,
            'birth_date' => $request->birth_date ?? $student->birth_date,
            'religion' => $request->religion ?? $student->religion,
            'address' => $request->address ?? $student->address,
            'parent_name' => $request->parent_name ?? $student->parent_name,
            'parent_phone' => $request->parent_phone ?? $student->parent_phone,
            'parent_address' => $request->parent_address ?? $student->parent_address,
            'parent_job' => $request->parent_job ?? $student->parent_job,
            'nisn' => $request->nisn ?? $student->nisn,
            'job_interest' => ($student->classRoom && $student->classRoom->grade_level === 'kejuruan') ? ($request->job_interest ?? $student->job_interest) : $student->job_interest,
            'cv_link' => ($student->classRoom && $student->classRoom->grade_level === 'kejuruan') ? ($request->cv_link ?? $student->cv_link) : $student->cv_link,
            'portfolio_links' => ($student->classRoom && $student->classRoom->grade_level === 'kejuruan') ? ($request->portfolio_links ? array_map('trim', explode(',', $request->portfolio_links)) : $student->portfolio_links) : $student->portfolio_links,
        ], function ($v) {
            return $v !== null;
        });

        $student->update($studentFields);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Display a list of announcements for the student.
     */
    public function announcements(Request $request)
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

    public function grades()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            $grades = collect();
            $skills = collect();
            return view('student.grades', compact('grades', 'skills'));
        }

        $grades = Grade::where('student_id', $student->id)
            ->with(['subject', 'teacher'])
            ->orderBy('semester')
            ->orderBy('assessment_date')
            ->get();

        $skills = StudentSkill::where('student_id', $student->id)
            ->with('assessedBy')
            ->orderBy('assessed_date', 'desc')
            ->get();

        return view('student.grades', compact('grades', 'skills'));
    }

    public function schedules()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->with('classRoom')->first();
        $schedules = collect();
        if ($student && $student->classRoom) {
            $schedules = Schedule::where('class_id', $student->classRoom->id)
                ->with(['subject', 'teacher'])
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_of_week');
        }

        return view('student.schedules', compact('schedules'));
    }

    /**
     * List training classes available for kejuruan students
     */
    public function trainingIndex()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        $classes = collect();
        if ($student && $student->classRoom && $student->classRoom->grade_level === 'kejuruan') {
            $classes = \App\Models\TrainingClass::where('open_to_kejuruan', true)->withCount('students')->orderBy('start_at', 'desc')->get();
        }
        // Determine if the student currently has an active training that hasn't ended (enrolled)
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

    public function trainingShow($id)
    {
        $training = \App\Models\TrainingClass::withCount('students')->findOrFail($id);
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
        return view('student.training.show', compact('training', 'hasActiveTraining', 'student'));
    }

    public function enrollTraining($id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return back()->with('error', 'Profil siswa tidak ditemukan');
        }
        $training = \App\Models\TrainingClass::findOrFail($id);
        // Only allow if open_to_kejuruan and student's class is kejuruan
        if (!$training->open_to_kejuruan || !($student->classRoom && $student->classRoom->grade_level === 'kejuruan')) {
            return back()->with('error', 'Anda tidak berhak mengikuti pelatihan ini');
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
            return back()->with('error', 'Anda sedang mengikuti pelatihan lain. Selesaikan dulu pelatihan tersebut sebelum mendaftar yang baru.');
        }
        // Enroll (attach) if not already
        if (!$student->trainingClasses()->where('training_classes.id', $training->id)->exists()) {
            $student->trainingClasses()->attach($training->id, ['enrolled_at' => now(), 'status' => 'enrolled']);
        }
        return back()->with('success', 'Terdaftar pada pelatihan');
    }

    public function unenrollTraining($id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return back()->with('error', 'Profil siswa tidak ditemukan');
        }
        $training = \App\Models\TrainingClass::findOrFail($id);
        $student->trainingClasses()->detach($training->id);
        return back()->with('success', 'Anda telah dibatalkan pendaftarannya');
    }

    public function materials(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->with('classRoom')->first();
        $materials = collect();
        if ($student) {
            $trainingClassIds = $student->trainingClasses()->pluck('training_classes.id')->toArray();
            $filterTrainingClass = (int) $request->query('training_class_id', 0);

            $isKejuruan = ($user->role === 'kejuruan' || ($student->classRoom && ($student->classRoom->grade_level === 'kejuruan')));

            if ($isKejuruan) {
                // kejuruan students should only see materials from training classes they belong to
                $query = \App\Models\TeachingMaterial::whereIn('training_class_id', $trainingClassIds ?: [0]);
                if ($filterTrainingClass) {
                    $query->where('training_class_id', $filterTrainingClass);
                }
                $materials = $query
                    ->where('is_visible', true)
                    ->with(['trainingClass', 'submissions' => function($q) use ($student) { $q->where('student_id', $student->id)->orderBy('created_at', 'desc'); }])
                    ->orderBy('title', 'asc')
                    ->paginate(20);
            } else {
                $query = \App\Models\TeachingMaterial::where(function($q) use ($student, $trainingClassIds) {
                    $q->whereNull('class_id')
                      ->orWhere('class_id', $student->classRoom?->id ?? 0)
                      ->orWhereIn('training_class_id', $trainingClassIds ?: [0]);
                });
                if ($filterTrainingClass) {
                    $query->where('training_class_id', $filterTrainingClass);
                }
                $materials = $query->where('is_visible', true)->with(['classRoom', 'subject', 'trainingClass', 'submissions' => function($q) use ($student) { $q->where('student_id', $student->id)->orderBy('created_at', 'desc'); }])->orderBy('title', 'asc')->paginate(20);
            }
        }
        return view('student.materials.index', compact('materials', 'isKejuruan'));
    }

    public function gradeHistory()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get complete grade history with all details
        $gradeHistory = \App\Models\StudentGradeHistory::where('student_id', $student->id)
            ->with('classRoom')
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Get current grades
        $currentGrades = Grade::where('student_id', $student->id)
            ->with('subject')
            ->orderBy('assessment_date', 'desc')
            ->get();

        // Get training classes history (for kejuruan students)
        $trainingHistory = [];
        if ($user->role === 'kejuruan') {
            $trainingHistory = $student->trainingClasses()
                ->withPivot(['enrolled_at', 'status'])
                ->get();
        }

        return view('student.grade-history', compact('gradeHistory', 'currentGrades', 'trainingHistory', 'student'));
    }
}
