<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentApplication;
use App\Models\TrainingClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Announcement;
use App\Models\Document;
use App\Models\Grade;
use App\Models\Alumni;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $totalApplications = StudentApplication::count();
        $pendingApplications = StudentApplication::where('status', 'pending')->count();
        $totalStudents = Student::where('status', 'active')->count();
        $totalTeachers = Teacher::where('status', 'active')->count();
        $totalClasses = ClassRoom::where('is_active', true)->count();
        // Avoid query exceptions if the alumni table hasn't been migrated/created yet
        $totalAlumni = Schema::hasTable('alumni') ? Alumni::count() : 0;

        $recentApplications = StudentApplication::query()
            ->latest()
            ->take(5)
            ->get();

        $recentAnnouncements = Announcement::latest()->take(5)->get();

        // Compose recent activities from multiple sources
        $recentActivities = collect();
        foreach ($recentApplications as $app) {
            $recentActivities->push((object)[
                'type' => 'registration',
                'description' => "Pendaftar baru: " . ($app->full_name ?? $app->student_name ?? '—'),
                'created_at' => $app->created_at
            ]);
        }
        foreach ($recentAnnouncements as $ann) {
            $recentActivities->push((object)[
                'type' => 'announcement',
                'description' => $ann->title,
                'created_at' => $ann->created_at
            ]);
        }
        // Optionally add recent user registrations
        $recentUsers = User::latest()->take(5)->get();
        foreach ($recentUsers as $u) {
            $recentActivities->push((object)[
                'type' => 'user_registration',
                'description' => 'User terdaftar: ' . $u->name,
                'created_at' => $u->created_at
            ]);
        }
        $recentActivities = $recentActivities->sortByDesc('created_at')->values();

        $studentStats = [
            'male' => Student::whereHas('user', function($q) { $q->where('gender', 'male'); })->count(),
            'female' => Student::whereHas('user', function($q) { $q->where('gender', 'female'); })->count(),
            'orphan' => Student::where('is_orphan', true)->count(),
        ];

        // Additional overview metrics used by blade
        $newStudentsThisMonth = Student::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $activeTeachers = $totalTeachers; // alias for blade
        $totalSubjects = Subject::count();

        // Registration chart: last 6 months
        $labels = [];
        $regData = [];
        $indMonths = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
        for ($i = 5; $i >= 0; $i--) {
            $dt = now()->subMonths($i);
            $labels[] = $indMonths[(int)$dt->format('n') - 1];
            $regData[] = StudentApplication::whereYear('created_at', $dt->year)
                ->whereMonth('created_at', $dt->month)
                ->count();
        }
        $registrationChartData = ['labels' => $labels, 'data' => $regData];

        // Class distribution (for class chart) - support optional filter by grade level (sd, smp, sma, kejuruan)
        // Default: class rooms only (SD/SMP/SMA)
        $classLabels = [];
        $classData = [];
        $classesForChart = ClassRoom::withCount('students')->orderBy('name')->get();
        foreach ($classesForChart as $c) {
            $classLabels[] = $c->name;
            $classData[] = $c->students_count ?? 0;
        }
        $classDistributionData = ['labels' => $classLabels, 'data' => $classData];

        return view('admin.dashboard', compact(
            'totalApplications', 'pendingApplications', 'totalStudents', 'totalTeachers',
            'totalClasses', 'totalAlumni', 'recentApplications', 'recentAnnouncements', 'studentStats'
        ))->with([
            'recentActivities' => $recentActivities,
            'newStudentsThisMonth' => $newStudentsThisMonth,
            'activeTeachers' => $activeTeachers,
            'totalSubjects' => $totalSubjects,
            'registrationChartData' => $registrationChartData,
            'classDistributionData' => $classDistributionData,
        ]);
    }

    public function applications()
    {
        $applications = StudentApplication::query()
            ->latest()
            ->paginate(20);

        return view('admin.applications.index', compact('applications'));
    }

    public function pendingApplications()
    {
        $applications = StudentApplication::query()
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.applications.index', compact('applications'));
    }

    public function users()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,teacher,student,kejuruan',
            'is_active' => 'nullable|boolean',
        ]);

        // Final verification to prevent case-insensitive 'kejuruan' values
        if (strtolower($request->input('grade_level', '')) === 'kejuruan') {
            return redirect()->back()->withInput()->with('error', 'Pembuatan kelas kejuruan harus dilakukan melalui menu Kelola Pelatihan.');
        }

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'is_active' => $request->boolean('is_active', $user->is_active),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update user', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengguna: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        // Optionally prevent self-delete
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function createAnnouncementForm()
    {
        return view('admin.announcements.create');
    }

    public function applicationDetail($id)
    {
        $application = StudentApplication::findOrFail($id);
        return view('admin.applications.detail', compact('application'));
    }

    public function approveApplication(Request $request, $id)
    {
        $application = StudentApplication::findOrFail($id);

        // Validate class selection - if desired_class is kejuruan, require training_class_id
        if ($application->desired_class === 'kejuruan') {
            $request->validate([
                'training_class_id' => 'required|exists:training_classes,id'
            ]);
        } else {
            $request->validate([
                'class_id' => 'required|exists:classes,id'
            ]);
        }

        // Handle both ClassRoom and TrainingClass selection depending on desired class
        $class = null;
        $trainingClass = null;
        if ($application->desired_class === 'kejuruan') {
            $trainingClass = TrainingClass::findOrFail($request->training_class_id);
            if (!$trainingClass->is_active || !$trainingClass->open_to_kejuruan) {
                return back()->with('error', 'Kelas pelatihan yang dipilih tidak tersedia untuk pendaftar kejuruan.');
            }
        } else {
            $class = ClassRoom::findOrFail($request->class_id);
            if ($application->desired_class && $class->grade_level !== $application->desired_class) {
                return back()->with('error', 'Kelas yang dipilih tidak sesuai dengan tingkat yang diinginkan oleh pendaftar.');
            }
        }

        // Wrap operations in a transaction to avoid partial updates
        // Final verification to disallow updating to 'kejuruan' unless current class is kejuruan
        if ($class->grade_level !== 'kejuruan' && strtolower($request->input('grade_level', '')) === 'kejuruan') {
            return redirect()->back()->withInput()->with('error', 'Perubahan ke tingkat kejuruan harus dilakukan melalui menu Kelola Pelatihan.');
        }

        try {
            DB::transaction(function () use ($application, $request, $class, $trainingClass) {
            // Create or find user account for this applicant
            $targetRole = $application->desired_class === 'kejuruan' ? 'kejuruan' : 'student';
            $user = User::where('email', $application->email)->first();
                if ($user) {
                // If this user already has a student profile, stop
                if ($user->student) {
                    throw new \Exception('User dengan email ini sudah memiliki profil siswa.');
                }

                // Update user's role and contact info
                $user->update([
                    'name' => $application->student_name,
                    'role' => $targetRole,
                    'phone' => $application->phone,
                    'address' => $application->address,
                    'birth_date' => $application->birth_date,
                    'gender' => $application->gender,
                    'is_active' => true,
                ]);
                } else {
                // Use password saved in application (already hashed) if present; otherwise create default password
                $userPassword = $application->password ?? \Illuminate\Support\Facades\Hash::make('password123');
                $user = User::create([
                    'name' => $application->student_name,
                    'email' => $application->email,
                    'password' => $userPassword,
                    'role' => $targetRole,
                    'phone' => $application->phone,
                    'address' => $application->address,
                    'birth_date' => $application->birth_date,
                    'gender' => $application->gender,
                    'is_active' => true,
                ]);
            }

            // Set user's profile photo from application photo document when present
            $photoDoc = collect($application->documents)->firstWhere('type', 'photo');
            if ($photoDoc && isset($photoDoc['path']) && Storage::disk('public')->exists($photoDoc['path'])) {
                // copy to profile photos to keep separation
                $ext = pathinfo($photoDoc['path'], PATHINFO_EXTENSION);
                $destFilename = 'profile_' . $user->id . '_' . time() . '.' . $ext;
                $destPath = 'profile-photos/' . $destFilename;
                Storage::disk('public')->copy($photoDoc['path'], $destPath);
                $user->profile_photo = $destPath;
                $user->save();
            }

            // Create student record for this user
            // Try to create student with a unique student_id; if duplicate key occurs (race), regenerate and retry
            $student = null;
            $attempts = 0;
            while (!$student && $attempts < 5) {
                $attempts++;
                try {
                    $student = Student::create([
                'user_id' => $user->id,
                'student_id' => $this->generateStudentId(),
                'class_id' => $request->class_id,
                'nisn' => $application->nisn,
                'place_of_birth' => $application->place_of_birth,
                'birth_date' => $application->birth_date,
                'religion' => $application->religion,
                'address' => $application->address,
                'parent_name' => $application->parent_name,
                'parent_phone' => $application->parent_phone,
                'parent_address' => $application->parent_address,
                    'parent_email' => $application->parent_email ?? null,
                    'parent_job' => $application->parent_job,
                    'medical_info' => $application->medical_info ?? null,
                    'health_info' => $application->health_info,
                    'disability_info' => $application->disability_info,
                    'is_orphan' => ($application->orphan_status ?? 'none') !== 'none',
                    'orphan_status' => $application->orphan_status ?? 'none',
                    'education_history' => $application->education_history,
                'enrollment_date' => now(),
            ]);
                } catch (\Illuminate\Database\QueryException $qe) {
                    // If duplicate student_id (Unique constraint), try again (generate new)
                    if (str_contains(strtolower($qe->getMessage()), 'duplicate')) {
                        // loop to try again
                        $student = null;
                        continue;
                    }
                    throw $qe; // rethrow other DB exceptions
                }
            }

            // Transfer documents
            foreach ($application->documents as $doc) {
                Document::create([
                    'documentable_type' => Student::class,
                    'documentable_id' => $student->id,
                    'document_type' => $doc['type'],
                    'document_name' => $doc['name'],
                    'file_path' => $doc['path'],
                    'file_size' => $doc['size'] ?? null,
                    'mime_type' => $doc['mime_type'] ?? null,
                ]);
            }

            // increment class's current_students if class exists
            if ($class) {
                $class->increment('current_students');
            }
            // if training class selected, attach student to training_class pivot
            if ($trainingClass) {
                // ensure capacity not exceeded if defined
                if ($trainingClass->capacity && $trainingClass->students()->count() >= $trainingClass->capacity) {
                    throw new \Exception('Kelas pelatihan penuh. Pilih kelas lain atau tingkatkan kapasitas.');
                }
                // Use syncWithoutDetaching to avoid duplicate pivot insertion and preserve existing enrollments
                $student->trainingClasses()->syncWithoutDetaching([
                    $trainingClass->id => ['enrolled_at' => now(), 'status' => 'enrolled']
                ]);
            }

            if (!$student) {
                throw new \Exception('Failed to create student record after multiple attempts.');
            }
            // Mark application as approved
            $application->update([
                'status' => 'approved',
                'notes' => $request->input('notes') ?? null,
            ]);
            });
        } catch (\Exception $e) {
            \Log::error('Approve application failed', ['error' => $e->getMessage(), 'application_id' => $application->id]);
            return back()->with('error', $e->getMessage());
        }

        // approval flow completed inside the DB transaction

        return redirect()->route('admin.applications.index')->with('success', 'Aplikasi berhasil disetujui dan siswa telah terdaftar!');
    }

    public function rejectApplication(Request $request, $id)
    {
        $application = StudentApplication::findOrFail($id);

        $application->update([
            'status' => 'rejected',
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.applications.index')->with('success', 'Aplikasi berhasil ditolak.');
    }

    public function students(Request $request)
    {
        // Show users with role=student and apply optional filters
        $query = \App\Models\User::with(['student.classRoom', 'student.trainingClasses'])
            ->where('role', 'student');

        // Filter: name (search across name)
        if ($request->filled('name')) {
            $q = $request->get('name');
            $query->where('name', 'like', "%{$q}%");
        }

        // Filter: orphan_status (yatim / piatu / yatim_piatu / none)
        if ($request->filled('orphan_status')) {
            $orphan = $request->get('orphan_status');
            $query->whereHas('student', function ($qsub) use ($orphan) {
                $qsub->where('orphan_status', $orphan);
            });
        }

        // Filter: class
        if ($request->filled('class_id')) {
            $classId = $request->get('class_id');
            $query->whereHas('student', function ($qsub) use ($classId) {
                $qsub->where('class_id', $classId);
            });
        }

        // Filter by training class - only applicable for kejuruan listing
        if ($request->filled('training_class_id')) {
            $tid = $request->get('training_class_id');
            $query->whereHas('student.trainingClasses', function ($qsub) use ($tid) {
                $qsub->where('training_classes.id', $tid);
            });
        }

        // Filter: has disability (1 = has, 0 = none)
        if ($request->filled('has_disability')) {
            $has = (int)$request->get('has_disability');
            if ($has === 1) {
                $query->whereHas('student', function ($qsub) {
                    $qsub->whereNotNull('disability_info')
                        ->whereRaw("disability_info <> '[]'");
                });
            } else {
                $query->whereHas('student', function ($qsub) {
                    $qsub->whereNull('disability_info')
                        ->orWhereRaw("disability_info = '[]'");
                });
            }
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(50)->appends($request->query());

        // For the class filter dropdown
        $classes = \App\Models\ClassRoom::orderBy('name')->get();
        $trainingClasses = \App\Models\TrainingClass::orderBy('title')->get();

        // Make the filter form action configurable when rendering the view
        $filterAction = route('admin.students.index');

        return view('admin.students.index', compact('students', 'classes', 'trainingClasses', 'filterAction'));
    }

    /**
     * List only kejuruan users (role = kejuruan) and reuse the students table view
     */
    public function kejuruanStudents(Request $request)
    {
        $query = \App\Models\User::with(['student.classRoom', 'student.trainingClasses'])
            ->where('role', 'kejuruan');

        if ($request->filled('name')) {
            $q = $request->get('name');
            $query->where('name', 'like', "%{$q}%");
        }

        if ($request->filled('orphan_status')) {
            $orphan = $request->get('orphan_status');
            $query->whereHas('student', function ($qsub) use ($orphan) {
                $qsub->where('orphan_status', $orphan);
            });
        }

        // Filter by training class (for kejuruan users)
        if ($request->filled('training_class_id')) {
            $tid = $request->get('training_class_id');
            $query->whereHas('student.trainingClasses', function ($qsub) use ($tid) {
                $qsub->where('training_classes.id', $tid);
            });
        }

        if ($request->filled('has_disability')) {
            $has = (int)$request->get('has_disability');
            if ($has === 1) {
                $query->whereHas('student', function ($qsub) {
                    $qsub->whereNotNull('disability_info')
                        ->whereRaw("disability_info <> '[]'");
                });
            } else {
                $query->whereHas('student', function ($qsub) {
                    $qsub->whereNull('disability_info')
                        ->orWhereRaw("disability_info = '[]'");
                });
            }
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(50)->appends($request->query());
        $classes = \App\Models\ClassRoom::orderBy('name')->get();
        $trainingClasses = \App\Models\TrainingClass::orderBy('title')->get();
        $filterAction = route('admin.students.kejuruan');

        return view('admin.students.index', compact('students', 'classes', 'trainingClasses', 'filterAction'))->with('isKejuruan', true);
    }

    public function createStudentForm(Request $request)
    {
        $classes = ClassRoom::all();
        $prefillUser = null;
        if ($request->has('user_id')) {
            $prefillUser = \App\Models\User::find($request->get('user_id'));
        }
        return view('admin.students.create', compact('classes', 'prefillUser'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'student_id' => 'required|string|unique:students,student_id',
            'class_id' => 'nullable|exists:classes,id',
            'nisn' => 'nullable|string|unique:students,nisn',
            'place_of_birth' => 'required|string',
            'birth_date' => 'required|date',
            'religion' => 'required|in:islam,kristen,katolik,hindu,budha,khonghucu',
            'address' => 'required|string',
            'parent_name' => 'required|string',
            'parent_phone' => 'required|string',
            'parent_address' => 'required|string',
            'parent_email' => 'nullable|email',
            'parent_job' => 'nullable|string|max:255',
            'enrollment_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'medical_info' => 'nullable|string',
            'health_info' => 'nullable|array',
            'health_info.*' => 'nullable|string|max:255',
            'disability_info' => 'nullable|array',
            'disability_info.*' => 'nullable|string|max:255',
            'education_history.previous_school' => 'nullable|string|max:255',
            'education_history.graduation_year' => 'nullable|integer',
            'job_interest' => 'nullable|string|max:255',
            'cv_link' => 'nullable|url|max:2048',
            'portfolio_links' => 'nullable|string',
            'orphan_status' => 'nullable|in:none,yatim,piatu,yatim_piatu',
            'birth_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'last_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'nullable|in:active,inactive,graduated,transferred'
        ]);

        try {
            if ($request->filled('user_id')) {
                $user = \App\Models\User::findOrFail($request->get('user_id'));
                // If this user already has a student record, prevent duplicate
                if ($user->student) {
                    return redirect()->back()->withInput()->with('error', 'User sudah memiliki profil siswa.');
                }
            } else {
                // ensure email is unique before creating user
                if (\App\Models\User::where('email', $request->email)->exists()) {
                    return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar.');
                }

                $user = \App\Models\User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'role' => 'student',
                    'is_active' => true,
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                    'birth_date' => $request->birth_date,
                    'address' => $request->address,
                ]);
            }

            // Merge health/disability 'other' fields
            $healthArr = [];
            if ($request->filled('health_info')) {
                $healthArr = array_map('trim', (array)$request->input('health_info'));
            }
            if ($request->filled('health_info_other')) {
                $healthArr[] = trim($request->input('health_info_other'));
            }
            $healthArr = array_values(array_filter($healthArr, fn($v) => $v !== null && $v !== ''));

            $disabilityArr = [];
            if ($request->filled('disability_info')) {
                $disabilityArr = array_map('trim', (array)$request->input('disability_info'));
            }
            if ($request->filled('disability_info_other')) {
                $disabilityArr[] = trim($request->input('disability_info_other'));
            }
            $disabilityArr = array_values(array_filter($disabilityArr, fn($v) => $v !== null && $v !== ''));

            $student = Student::create([
                'user_id' => $user->id,
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'nisn' => $request->nisn,
                'place_of_birth' => $request->place_of_birth,
                'birth_date' => $request->birth_date,
                'religion' => $request->religion,
                'address' => $request->address,
                'parent_name' => $request->parent_name,
                'parent_phone' => $request->parent_phone,
                'parent_address' => $request->parent_address,
                'parent_email' => $request->parent_email,
                'parent_job' => $request->parent_job,
                'medical_info' => $request->medical_info,
                'health_info' => $healthArr ?: null,
                'disability_info' => $disabilityArr ?: null,
                'education_history' => [
                    'previous_school' => $request->input('education_history.previous_school'),
                    'graduation_year' => $request->input('education_history.graduation_year')
                ],
                'orphan_status' => $request->input('orphan_status') ?? 'none',
                'enrollment_date' => $request->enrollment_date,
                'status' => $request->status ?? 'active',
                'is_orphan' => ($request->input('orphan_status') ?? 'none') !== 'none'
            ]);
                // Add job/cv/portfolio optional fields
                if ($request->filled('job_interest')) {
                    $student->job_interest = $request->job_interest;
                }
                if ($request->filled('cv_link')) {
                    $student->cv_link = $request->cv_link;
                }
                if ($request->filled('portfolio_links')) {
                    $student->portfolio_links = array_map('trim', explode(',', $request->portfolio_links));
                }
                $student->save();

            if ($student->class_id) {
                $class = ClassRoom::find($student->class_id);
                if ($class) $class->increment('current_students');
            }
            // Handle uploaded documents for this student
            foreach (['birth_certificate', 'kk', 'last_certificate', 'photo', 'medical_certificate'] as $key) {
                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    $filename = 'student_' . $student->id . '_' . $key . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('student-documents', $filename, 'public');
                    $doc = Document::create([
                        'documentable_type' => Student::class,
                        'documentable_id' => $student->id,
                        'document_type' => $key,
                        'document_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ]);
                    // If photo, also set the user's profile photo
                    if ($key === 'photo') {
                        $user->profile_photo = $path;
                        $user->save();
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create student', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Gagal membuat siswa: ' . $e->getMessage());
        }

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function editStudent($id)
    {
        $student = Student::with(['gradeHistory', 'classRoom'])->findOrFail($id);
        $classes = ClassRoom::all();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function educationHistory($id)
    {
        $student = Student::with(['gradeHistory', 'user'])->findOrFail($id);

        // Group grade history by education level (SD, SMP, SMA)
        $educationLevels = [
            'SD' => [],
            'SMP' => [],
            'SMA' => []
        ];

        foreach ($student->gradeHistory as $history) {
            $className = $history->class_name;
            if (str_contains($className, 'SD')) {
                $educationLevels['SD'][] = $history;
            } elseif (str_contains($className, 'SMP')) {
                $educationLevels['SMP'][] = $history;
            } elseif (str_contains($className, 'SMA')) {
                $educationLevels['SMA'][] = $history;
            }
        }

        return view('admin.students.education-history', compact('student', 'educationLevels'));
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $user = $student->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'student_id' => 'required|string|unique:students,student_id,' . $student->id,
            'class_id' => 'nullable|exists:classes,id',
            'nisn' => 'nullable|string|unique:students,nisn,' . $student->id,
            'place_of_birth' => 'required|string',
            'birth_date' => 'required|date',
            'religion' => 'required|in:islam,kristen,katolik,hindu,budha,khonghucu',
            'address' => 'required|string',
            'parent_name' => 'required|string',
            'parent_phone' => 'required|string',
            'parent_address' => 'required|string',
            'enrollment_date' => 'required|date',
            'status' => 'nullable|in:active,inactive,graduated,transferred'
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
            ]);

            $oldClass = $student->class_id;
            // Merge 'other' fields into arrays if provided
            $healthArr = [];
            if ($request->filled('health_info')) {
                $healthArr = array_map('trim', (array)$request->input('health_info'));
            }
            if ($request->filled('health_info_other')) {
                $healthArr[] = trim($request->input('health_info_other'));
            }
            $healthArr = array_values(array_filter($healthArr, fn($v) => $v !== null && $v !== ''));

            $disabilityArr = [];
            if ($request->filled('disability_info')) {
                $disabilityArr = array_map('trim', (array)$request->input('disability_info'));
            }
            if ($request->filled('disability_info_other')) {
                $disabilityArr[] = trim($request->input('disability_info_other'));
            }
            $disabilityArr = array_values(array_filter($disabilityArr, fn($v) => $v !== null && $v !== ''));
            $student->update([
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'nisn' => $request->nisn,
                'place_of_birth' => $request->place_of_birth,
                'birth_date' => $request->birth_date,
                'religion' => $request->religion,
                'address' => $request->address,
                'parent_name' => $request->parent_name,
                'parent_phone' => $request->parent_phone,
                'parent_address' => $request->parent_address,
                'parent_email' => $request->parent_email,
                'medical_info' => $request->medical_info,
                'health_info' => $healthArr ?: null,
                'disability_info' => $disabilityArr ?: null,
                'education_history' => [
                    'previous_school' => $request->input('education_history.previous_school'),
                    'graduation_year' => $request->input('education_history.graduation_year')
                ],
                'orphan_status' => $request->input('orphan_status') ?? 'none',
                'parent_job' => $request->parent_job,
                'enrollment_date' => $request->enrollment_date,
                'status' => $request->status ?? $student->status,
                'is_orphan' => ($request->input('orphan_status') ?? $student->orphan_status) !== 'none'
            ]);
            // Update job/cv/portfolio fields
            if ($request->filled('job_interest')) {
                $student->job_interest = $request->job_interest;
            }
            $student->cv_link = $request->cv_link ?? $student->cv_link;
            if ($request->filled('portfolio_links')) {
                $student->portfolio_links = array_map('trim', explode(',', $request->portfolio_links));
            }
            $student->save();

            if ($oldClass != $request->class_id) {
                if ($oldClass) {
                    $old = ClassRoom::find($oldClass);
                    if ($old) $old->decrement('current_students');
                }
                if ($request->class_id) {
                    $new = ClassRoom::find($request->class_id);
                    if ($new) $new->increment('current_students');
                }
            }
            // Process file uploads/update
            foreach (['birth_certificate', 'kk', 'last_certificate', 'photo', 'medical_certificate'] as $key) {
                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    $filename = 'student_' . $student->id . '_' . $key . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('student-documents', $filename, 'public');
                    $existing = $student->documents()->where('document_type', $key)->first();
                    if ($existing) {
                        // Remove old file if it exists
                        if ($existing->file_path && Storage::disk('public')->exists($existing->file_path)) {
                            Storage::disk('public')->delete($existing->file_path);
                        }
                        $existing->update([
                            'document_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ]);
                        if ($key === 'photo') {
                            $user->profile_photo = $path;
                            $user->save();
                        }
                    } else {
                        Document::create([
                            'documentable_type' => Student::class,
                            'documentable_id' => $student->id,
                            'document_type' => $key,
                            'document_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ]);
                        if ($key === 'photo') {
                            $user->profile_photo = $path;
                            $user->save();
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update student', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui siswa: ' . $e->getMessage());
        }

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);
        // Optionally delete the user as well
        $userId = $student->user_id;
        $student->delete();
        // delete the user if desired
        // \App\Models\User::find($userId)?->delete();
        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function teachers()
    {
        // Show all teachers to allow admin reactivation and editing
        $teachers = Teacher::with('user')
            ->paginate(20);
        $subjects = Subject::pluck('name', 'id')->toArray();

        return view('admin.teachers.index', compact('teachers', 'subjects'));
    }

    public function createTeacherForm()
    {
        $subjects = Subject::where('is_active', true)->get();
        return view('admin.teachers.create', compact('subjects'));
    }

    public function storeTeacher(Request $request)
    {
        // convert empty subject selection to null so 'nullable|integer' validation passes
        // Normalize subjects entry: allow array input
        if ($request->filled('subjects') && is_string($request->input('subjects')) && $request->input('subjects') === '') {
            $request->merge(['subjects' => null]);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'teacher_id' => 'required|string|unique:teachers,teacher_id',
            'nip' => 'nullable|string|max:100',
            'subjects' => 'nullable|array',
            'subjects.*' => 'nullable|integer|exists:subjects,id',
            'qualifications' => 'nullable|array',
            'qualifications.*' => 'nullable|string|max:255',
            'certifications' => 'nullable|array',
            'certifications.*' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'status' => 'nullable|in:active,inactive,retired',
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->filled('password') ? $request->password : 'password'),
                'role' => 'teacher',
                'is_active' => true
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'teacher_id' => $request->teacher_id,
                'nip' => $request->nip,
                'subjects' => $request->has('subjects_present') ? ($request->has('subjects') ? array_values(array_filter(array_map('intval', (array)$request->input('subjects')), fn($v) => $v !== 0)) : []) : null,
                'qualifications' => $request->has('qualifications_present') ? ($request->filled('qualifications') ? array_values(array_filter(is_array($request->qualifications) ? array_map('trim', $request->qualifications) : array_map('trim', explode(',', $request->qualifications)), fn($v) => $v !== null && $v !== '')) : []) : null,
                'certifications' => $request->has('certifications_present') ? ($request->filled('certifications') ? array_values(array_filter(is_array($request->certifications) ? array_map('trim', $request->certifications) : array_map('trim', explode(',', $request->certifications)), fn($v) => $v !== null && $v !== '')) : []) : null,
                'hire_date' => $request->hire_date,
                'status' => $request->status ?? 'active'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create teacher', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Gagal membuat guru: ' . $e->getMessage());
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil dibuat.');
    }

    public function editTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $subjects = Subject::where('is_active', true)->get();
        return view('admin.teachers.edit', compact('teacher', 'subjects'));
    }

    public function updateTeacher(Request $request, $id)
    {
        // convert empty subject selection to null so 'nullable|integer' validation passes
        if ($request->filled('subjects') && is_string($request->input('subjects')) && $request->input('subjects') === '') {
            $request->merge(['subjects' => null]);
        }
        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'teacher_id' => 'required|string|unique:teachers,teacher_id,' . $teacher->id,
            'nip' => 'nullable|string|max:100',
            'subjects' => 'nullable|array',
            'subjects.*' => 'nullable|integer|exists:subjects,id',
            'qualifications' => 'nullable|array',
            'qualifications.*' => 'nullable|string|max:255',
            'certifications' => 'nullable|array',
            'certifications.*' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'status' => 'nullable|in:active,inactive,retired',
            'password' => 'nullable|string|min:8|confirmed'
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
                $user->save();
            }

            $teacherData = [
                'teacher_id' => $request->teacher_id,
                'nip' => $request->nip,
                'hire_date' => $request->hire_date,
                'status' => $request->status ?? $teacher->status
            ];
            // Only overwrite array fields when the corresponding presence marker is set
            if ($request->has('subjects_present')) {
                $teacherData['subjects'] = $request->has('subjects') ? array_values(array_filter(array_map('intval', (array)$request->input('subjects')), fn($v) => $v !== 0)) : [];
            }
            if ($request->has('qualifications_present')) {
                $teacherData['qualifications'] = $request->filled('qualifications') ? array_values(array_filter(is_array($request->qualifications) ? array_map('trim', $request->qualifications) : array_map('trim', explode(',', $request->qualifications)), fn($v) => $v !== null && $v !== '')) : [];
            }
            if ($request->has('certifications_present')) {
                $teacherData['certifications'] = $request->filled('certifications') ? array_values(array_filter(is_array($request->certifications) ? array_map('trim', $request->certifications) : array_map('trim', explode(',', $request->certifications)), fn($v) => $v !== null && $v !== '')) : [];
            }
            $teacher->update($teacherData);
        } catch (\Exception $e) {
            \Log::error('Failed to update teacher', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui guru: ' . $e->getMessage());
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroyTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $userId = $teacher->user_id;
        $teacher->delete();
        // optionally delete user
        // \App\Models\User::find($userId)?->delete();
        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function subjects()
    {
        $subjects = Subject::latest()->paginate(20);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function createSubjectForm()
    {
        return view('admin.subjects.create');
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'credit_hours' => 'nullable|numeric|min:0',
            'category' => 'nullable|in:academic,vocational,extracurricular',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        Subject::create([
            'name' => $request->name,
            'code' => $request->code,
            'credit_hours' => $request->credit_hours ?? 0,
            'category' => $request->category,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true)
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil dibuat.');
    }

    public function editSubject($id)
    {
        $subject = Subject::findOrFail($id);
        return view('admin.subjects.edit', compact('subject'));
    }

    public function updateSubject(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $id,
            'credit_hours' => 'nullable|numeric|min:0',
            'category' => 'nullable|in:academic,vocational,extracurricular',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $subject->update([
            'name' => $request->name,
            'code' => $request->code,
            'credit_hours' => $request->credit_hours ?? 0,
            'category' => $request->category,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', false)
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroySubject($id)
    {
        $subject = Subject::findOrFail($id);
        // Prevent deletion if there are schedules or grades referencing this subject
        if ($subject->schedules()->count() > 0 || $subject->grades()->count() > 0) {
            return redirect()->route('admin.subjects.index')->with('error', 'Mata pelajaran tidak dapat dihapus karena memiliki jadwal atau nilai terkait.');
        }
        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function schedules()
    {
        $schedules = Schedule::with(['classRoom', 'subject', 'teacher'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(20);
        return view('admin.schedules.index', compact('schedules'));
    }

    public function createScheduleForm()
    {
        $classes = ClassRoom::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.schedules.create', compact('classes', 'subjects', 'teachers'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'room' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        // Custom validation for time comparison
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);

        if ($endTime->lte($startTime)) {
            return redirect()->back()->withInput()->withErrors(['end_time' => 'The end time field must be a date after start time.']);
        }

        try {
            $schedule = Schedule::create([
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
                'is_active' => $request->boolean('is_active', true),
            ]);

            \Log::info('Schedule created', $schedule->toArray());
        } catch (\Exception $e) {
            \Log::error('Schedule create failed', [
                'error' => $e->getMessage(),
                'input' => $request->all()
            ]);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan jadwal: ' . $e->getMessage());
        }

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dibuat.');
    }

    public function editSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $classes = ClassRoom::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.schedules.edit', compact('schedule', 'classes', 'subjects', 'teachers'));
    }

    public function updateSchedule(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'room' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        // Custom validation for time comparison
        $startTime = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);

        if ($endTime->lte($startTime)) {
            return redirect()->back()->withInput()->withErrors(['end_time' => 'The end time field must be a date after start time.']);
        }

        $schedule->update([
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room' => $request->room,
            'is_active' => $request->boolean('is_active', false),
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroySchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function toggleSchedule($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->is_active = !$schedule->is_active;
        $schedule->save();

        $status = $schedule->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.schedules.index')->with('success', "Jadwal berhasil $status.");
    }

    public function activities()
    {
        $activities = [];
        return view('admin.activities.index', compact('activities'));
    }

    public function alumni()
    {
        // Return alumni list if model exists - only show alumni from students with 'kejuruan' role
        $alumni = [];
        if (Schema::hasTable('alumni')) {
            $alumni = \App\Models\Alumni::with(['student.user'])
                ->whereHas('student.user', function($query) {
                    $query->where('role', 'kejuruan');
                })
                ->latest()
                ->paginate(20);
        }
        return view('admin.alumni.index', compact('alumni'));
    }

    public function alumniCreate()
    {
        // Provide students list for the create form - students with 'student' role (before graduation)
        $students = [];
        if (Schema::hasTable('students')) {
            $students = \App\Models\Student::with('user')
                ->whereHas('user', function($query) {
                    $query->where('role', 'student');
                })
                ->orderBy('id', 'desc')
                ->get();
        }
        return view('admin.alumni.create', compact('students'));
    }

    public function alumniStore(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'graduation_date' => 'required|date',
            'graduation_class' => 'nullable|string|max:32',
            'current_job' => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
            'linkedin_profile' => 'nullable|url',
        ]);

        try {
            // Get student skills automatically
            $student = \App\Models\Student::with('skills')->findOrFail($request->student_id);
            $studentSkills = $student->skills->pluck('skill_name')->toArray();

            // Update student status and user role to kejuruan (alumni)
            $student->update(['status' => 'graduated']);
            $student->user->update(['role' => 'kejuruan']);

            \App\Models\Alumni::create([
                'student_id' => $request->student_id,
                'graduation_date' => $request->graduation_date,
                'graduation_class' => $request->graduation_class,
                'current_job' => $request->current_job,
                'current_company' => $request->current_company,
                'linkedin_profile' => $request->linkedin_profile,
                'skills' => $studentSkills,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create alumni', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return back()->withInput()->with('error', 'Gagal membuat alumni: ' . $e->getMessage());
        }

        return redirect()->route('admin.alumni.index')->with('success', 'Alumni berhasil ditambahkan.');
    }

    public function alumniEdit($id)
    {
        $alumni = \App\Models\Alumni::findOrFail($id);
        $students = [];
        if (Schema::hasTable('students')) {
            $students = \App\Models\Student::with('user')
                ->whereHas('user', function($query) {
                    $query->whereIn('role', ['student', 'kejuruan']);
                })
                ->orderBy('id', 'desc')
                ->get();
        }
        return view('admin.alumni.edit', compact('alumni', 'students'));
    }

    public function alumniUpdate(Request $request, $id)
    {
        $alumni = \App\Models\Alumni::findOrFail($id);
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'graduation_date' => 'required|date',
            'graduation_class' => 'nullable|string|max:32',
            'current_job' => 'nullable|string|max:255',
            'current_company' => 'nullable|string|max:255',
            'linkedin_profile' => 'nullable|url',
        ]);

        try {
            // Get student skills automatically
            $student = \App\Models\Student::with('skills')->findOrFail($request->student_id);
            $studentSkills = $student->skills->pluck('skill_name')->toArray();

            $alumni->update([
                'student_id' => $request->student_id,
                'graduation_date' => $request->graduation_date,
                'graduation_class' => $request->graduation_class,
                'current_job' => $request->current_job,
                'current_company' => $request->current_company,
                'linkedin_profile' => $request->linkedin_profile,
                'skills' => $studentSkills,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update alumni', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return back()->withInput()->with('error', 'Gagal memperbarui alumni: ' . $e->getMessage());
        }

        return redirect()->route('admin.alumni.index')->with('success', 'Alumni berhasil diperbarui.');
    }

    public function getStudentSkills($studentId)
    {
        try {
            $student = \App\Models\Student::with('skills')->findOrFail($studentId);
            $skills = $student->skills->pluck('skill_name')->toArray();

            return response()->json([
                'success' => true,
                'skills' => $skills
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data skills'
            ], 500);
        }
    }

    public function getStudentGradeHistory($historyId)
    {
        try {
            $history = \App\Models\StudentGradeHistory::findOrFail($historyId);
            $subjectsGrades = $history->subjects_grades ?? [];

            return response()->json([
                'success' => true,
                'class_name' => $history->class_name,
                'academic_year' => $history->academic_year,
                'semester' => $history->semester,
                'average_grade' => $history->average_grade,
                'status' => $history->status,
                'subjects_grades' => $subjectsGrades,
                'notes' => $history->notes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data riwayat nilai'
            ], 500);
        }
    }

    public function alumniDestroy($id)
    {
        $alumni = \App\Models\Alumni::findOrFail($id);
        $alumni->delete();
        return redirect()->route('admin.alumni.index')->with('success', 'Alumni berhasil dihapus.');
    }

    public function classes()
    {
        // Show all classes (both active and inactive) so admins can reactivate if needed
        $classes = ClassRoom::with('homeroomTeacher')
            ->get();

        return view('admin.classes.index', compact('classes'));
    }

    public function createClassForm()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'nullable|string|max:50|not_in:kejuruan',
            'capacity' => 'nullable|integer|min:1',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $class = ClassRoom::create([
            'name' => $request->input('name'),
            'grade_level' => $request->input('grade_level'),
            'capacity' => $request->input('capacity') ?? 0,
            'current_students' => 0,
            'description' => $request->input('description'),
            'homeroom_teacher_id' => $request->input('homeroom_teacher_id'),
            'is_active' => $request->boolean('is_active', false),
        ]);

            \Log::info('Class created', $class->toArray());
        } catch (\Exception $e) {
            \Log::error('Class store failed', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Gagal membuat kelas: ' . $e->getMessage());
        }
        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function editClass($id)
    {
        $class = ClassRoom::findOrFail($id);
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.classes.edit', compact('class', 'teachers'));
    }

    public function updateClass(Request $request, $id)
    {
        $class = ClassRoom::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'grade_level' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];

        // If current class is not kejuruan, disallow changing grade_level to 'kejuruan'
        if ($class->grade_level !== 'kejuruan') {
            $rules['grade_level'] .= '|not_in:kejuruan';
        }

        $request->validate($rules);

        try {
            $class->update([
            'name' => $request->input('name'),
            'grade_level' => $request->input('grade_level'),
            'capacity' => $request->input('capacity') ?? 0,
            'description' => $request->input('description'),
            'homeroom_teacher_id' => $request->input('homeroom_teacher_id'),
            'is_active' => $request->boolean('is_active', false),
        ]);

            \Log::info('Class updated', $class->toArray());
        } catch (\Exception $e) {
            \Log::error('Class update failed', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kelas: ' . $e->getMessage());
        }

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroyClass($id)
    {
        $class = ClassRoom::findOrFail($id);
        if ($class->students()->count() > 0) {
            return redirect()->route('admin.classes.index')->with('error', 'Kelas tidak dapat dihapus karena masih memiliki siswa.');
        }

        $class->delete();
        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function toggleClass($id)
    {
        $class = ClassRoom::findOrFail($id);
        $class->is_active = !$class->is_active;
        $class->save();

        $status = $class->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.classes.index')->with('success', "Kelas berhasil $status.");
    }

    public function announcements()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(20);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function editSchool()
    {
        $school = \App\Models\School::first();
        return view('admin.school.edit', compact('school'));
    }

    public function updateSchool(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'facilities' => 'nullable',
            'programs' => 'nullable',
            'social_media' => 'nullable',
        ]);

        $data = $request->only([
            'name', 'description', 'vision', 'mission', 'address', 'phone', 'email', 'website', 'facilities', 'programs', 'social_media'
        ]);

        // Normalize facilities and programs: accept array or CSV string
        if (isset($data['facilities'])) {
            if (is_array($data['facilities'])) {
                $data['facilities'] = array_values(array_filter(array_map('trim', $data['facilities'])));
            } elseif (is_string($data['facilities'])) {
                $data['facilities'] = array_filter(array_map('trim', explode(',', $data['facilities'])));
            }
        }
        if (isset($data['programs'])) {
            if (is_array($data['programs'])) {
                $data['programs'] = array_values(array_filter(array_map('trim', $data['programs'])));
            } elseif (is_string($data['programs'])) {
                $data['programs'] = array_filter(array_map('trim', explode(',', $data['programs'])));
            }
        }
        if (isset($data['social_media'])) {
            // Accept array inputs like social_media[instagram] or CSV/JSON strings
            if (is_array($data['social_media'])) {
                // ensure default keys exist and trim values
                $smDefaults = ['instagram' => '', 'facebook' => '', 'twitter' => ''];
                $clean = [];
                foreach ($data['social_media'] as $k => $v) {
                    $k = trim($k);
                    $clean[$k] = is_null($v) ? '' : trim($v);
                }
                $data['social_media'] = array_merge($smDefaults, array_intersect_key($clean, $smDefaults));
            } elseif (is_string($data['social_media'])) {
            // Accept JSON or CSV
            $maybeJson = json_decode($data['social_media'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($maybeJson)) {
                $data['social_media'] = $maybeJson;
            } else {
                // CSV format like: facebook:url,instagram:url
                $pairs = array_filter(array_map('trim', explode(',', $data['social_media'])));
                $sm = [];
                foreach ($pairs as $p) {
                    [$k, $v] = array_merge(array_filter(array_map('trim', explode(':', $p))), ['']);
                    if ($k) $sm[$k] = $v;
                }
                $data['social_media'] = $sm;
            }
            }
        }

        $school = \App\Models\School::first();
        if ($school) {
            $school->update($data);
        } else {
            $school = \App\Models\School::create($data);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Info sekolah berhasil diperbarui.');
    }

    public function systemSettings()
    {
        return view('admin.system.settings');
    }

    public function createAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,academic,event,urgent',
            'publish_date' => 'required|date',
            'expire_date' => 'nullable|date|after:publish_date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $announcementData = [
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'type' => $request->input('type'),
                'target_audience' => 'all',
                'created_by' => auth()->id(),
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
            \Log::error('Failed to create announcement', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return back()->withInput()->with('error', 'Gagal membuat pengumuman: ' . $e->getMessage());
        }

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dibuat!');
    }

    public function editAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function updateAnnouncement(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,academic,event,urgent',
            'target_audience' => 'required|in:all,students,teachers,parents',
            'publish_date' => 'required|date',
            'expire_date' => 'nullable|date|after:publish_date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $updateData = [
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => $request->type,
            'target_audience' => $request->target_audience,
            'publish_date' => $request->publish_date,
            'expire_date' => $request->expire_date,
        ];

        // handle image update if provided
        if ($request->hasFile('image')) {
            // delete old image file if exists
            if ($announcement->image && Storage::disk('public')->exists($announcement->image)) {
                Storage::disk('public')->delete($announcement->image);
            }
            $file = $request->file('image');
            $filename = 'announcement_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('announcement-images', $filename, 'public');
            $updateData['image'] = $path;
        }

        $announcement->update($updateData);

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroyAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function reports()
    {
        $totalStudents = Student::where('status', 'active')->count();
        $studentsWithDisability = Student::whereJsonLength('disability_info', '>', 0)->count();
        $orphanStudents = Student::where('is_orphan', true)->count();

        $gradeDistribution = Grade::selectRaw('grade, COUNT(*) as count')
            ->groupBy('grade')
            ->pluck('count', 'grade')
            ->toArray();

        $classDistribution = ClassRoom::withCount('students')->get();

        return view('admin.reports', compact(
            'totalStudents', 'studentsWithDisability', 'orphanStudents',
            'gradeDistribution', 'classDistribution'
        ));
    }

    public function dashboardStats()
    {
        $totalApplications = StudentApplication::count();
        $pendingApplications = StudentApplication::where('status', 'pending')->count();
        $totalStudents = Student::where('status', 'active')->count();
        $totalTeachers = Teacher::where('status', 'active')->count();
        $totalClasses = ClassRoom::where('is_active', true)->count();
        $totalAlumni = Schema::hasTable('alumni') ? Alumni::count() : 0;

        // Registration stats per month for a given year (default current year)
        $year = (int) request()->query('year', date('Y'));

        // Get counts grouped by month
        $monthly = StudentApplication::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Build labels for 12 months and data array
        $months = [
            'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'
        ];
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = isset($monthly[$m]) ? (int) $monthly[$m] : 0;
        }

        // Provide a list of available years based on applications (last 5 years)
        $minYear = (int) StudentApplication::selectRaw('MIN(YEAR(created_at)) as min_year')->value('min_year') ?: date('Y');
        $years = range(max($minYear, date('Y') - 5), date('Y'));

        // Build class distribution based on class_level filter
        $classLevel = request()->query('class_level', 'all');
        $classLabels = [];
        $classData = [];
        if (strtolower($classLevel) === 'kejuruan') {
            $classesForChart = TrainingClass::withCount('students')->orderBy('title')->get();
            foreach ($classesForChart as $c) {
                $classLabels[] = $c->title;
                $classData[] = $c->students_count ?? 0;
            }
        } else {
            // Filter by grade_level when provided
            $classQuery = ClassRoom::withCount('students')->orderBy('name');
            if (!in_array(strtolower($classLevel), ['all', ''])) {
                $classQuery->whereRaw('LOWER(grade_level) = ?', [strtolower($classLevel)]);
            }
            $classesForChart = $classQuery->get();
            foreach ($classesForChart as $c) {
                $classLabels[] = $c->name;
                $classData[] = $c->students_count ?? 0;
            }
        }

        $classDistribution = ['labels' => $classLabels, 'data' => $classData, 'class_level' => $classLevel];

        return response()->json([
            'totalApplications' => $totalApplications,
            'pendingApplications' => $pendingApplications,
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalClasses' => $totalClasses,
            'totalAlumni' => $totalAlumni,
            'registration' => [
                'labels' => $months,
                'data' => $data,
                'year' => $year,
                'years' => $years,
            ],
            'classDistribution' => $classDistribution,
        ]);
    }

    private function generateStudentId()
    {
        $year = date('Y');
        // Ensure we generate a unique student id even if concurrent operations occur.
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('student_id', 'desc')
            ->first();
        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Ensure uniqueness: loop until a free student_id is found
        do {
            $candidate = $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $exists = Student::where('student_id', $candidate)->exists();
            if ($exists) {
                $newNumber++;
            }
        } while ($exists);

        return $candidate;
    }
}
