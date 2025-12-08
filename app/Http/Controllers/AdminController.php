<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentApplication;
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

        return view('admin.dashboard', compact(
            'totalApplications', 'pendingApplications', 'totalStudents', 'totalTeachers',
            'totalClasses', 'totalAlumni', 'recentApplications', 'recentAnnouncements', 'studentStats'
        ))->with([
            'recentActivities' => $recentActivities,
            'newStudentsThisMonth' => $newStudentsThisMonth,
            'activeTeachers' => $activeTeachers,
            'totalSubjects' => $totalSubjects,
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

    public function createUserForm()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,teacher,student',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => $request->role,
                'is_active' => $request->boolean('is_active', true),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create user', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Gagal membuat pengguna: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dibuat.');
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
            'role' => 'required|in:admin,teacher,student',
            'is_active' => 'nullable|boolean',
        ]);

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

        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        // Ensure selected class matches the desired grade level
        $class = ClassRoom::findOrFail($request->class_id);
        if ($application->desired_class && $class->grade_level !== $application->desired_class) {
            return back()->with('error', 'Kelas yang dipilih tidak sesuai dengan tingkat yang diinginkan oleh pendaftar.');
        }

        // Wrap operations in a transaction to avoid partial updates
        try {
            DB::transaction(function () use ($application, $request, $class) {
            // Create or find user account for this applicant
            $user = User::where('email', $application->email)->first();
                if ($user) {
                // If this user already has a student profile, stop
                if ($user->student) {
                    throw new \Exception('User dengan email ini sudah memiliki profil siswa.');
                }

                // Update user's role and contact info to ensure they can act as a student
                $user->update([
                    'name' => $application->student_name,
                    'role' => 'student',
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
                    'role' => 'student',
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
                'parent_job' => $application->parent_job,
                'health_info' => $application->health_info,
                'disability_info' => $application->disability_info,
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

    public function students()
    {
        // Show all users with role=student (includes users who do not have a Student record yet)
        $students = \App\Models\User::with(['student.classRoom'])
            ->where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.students.index', compact('students'));
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
            'enrollment_date' => 'required|date',
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
                    'is_active' => true
                ]);
            }

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
                'parent_job' => $request->parent_job,
                'enrollment_date' => $request->enrollment_date,
                'status' => $request->status ?? 'active',
                'is_orphan' => $request->boolean('is_orphan', false)
            ]);

            if ($student->class_id) {
                $class = ClassRoom::find($student->class_id);
                if ($class) $class->increment('current_students');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create student', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Gagal membuat siswa: ' . $e->getMessage());
        }

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function editStudent($id)
    {
        $student = Student::findOrFail($id);
        $classes = ClassRoom::all();
        return view('admin.students.edit', compact('student', 'classes'));
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
                'email' => $request->email
            ]);

            $oldClass = $student->class_id;
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
                'parent_job' => $request->parent_job,
                'enrollment_date' => $request->enrollment_date,
                'status' => $request->status ?? $student->status,
                'is_orphan' => $request->boolean('is_orphan', $student->is_orphan)
            ]);

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
            'status' => 'nullable|in:active,inactive,retired'
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
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
            'status' => 'nullable|in:active,inactive,retired'
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email
            ]);

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
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

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
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

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
        // Return alumni list if model exists
        $alumni = [];
        if (Schema::hasTable('alumni')) {
            $alumni = \App\Models\Alumni::latest()->paginate(20);
        }
        return view('admin.alumni.index', compact('alumni'));
    }

    public function alumniCreate()
    {
        // Provide students list for the create form
        $students = [];
        if (Schema::hasTable('students')) {
            $students = \App\Models\Student::with('user')->orderBy('id', 'desc')->get();
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
            \App\Models\Alumni::create([
                'student_id' => $request->student_id,
                'graduation_date' => $request->graduation_date,
                'graduation_class' => $request->graduation_class,
                'current_job' => $request->current_job,
                'current_company' => $request->current_company,
                'linkedin_profile' => $request->linkedin_profile,
                'skills' => $request->skills ? explode(',', $request->skills) : null,
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
            $students = \App\Models\Student::with('user')->orderBy('id', 'desc')->get();
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
            $alumni->update([
                'student_id' => $request->student_id,
                'graduation_date' => $request->graduation_date,
                'graduation_class' => $request->graduation_class,
                'current_job' => $request->current_job,
                'current_company' => $request->current_company,
                'linkedin_profile' => $request->linkedin_profile,
                'skills' => $request->skills ? explode(',', $request->skills) : null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to update alumni', ['error' => $e->getMessage(), 'input' => $request->all()]);
            return back()->withInput()->with('error', 'Gagal memperbarui alumni: ' . $e->getMessage());
        }

        return redirect()->route('admin.alumni.index')->with('success', 'Alumni berhasil diperbarui.');
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
            'grade_level' => 'nullable|string|max:50',
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

        $request->validate([
            'name' => 'required|string|max:255',
            'grade_level' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

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
            'target_audience' => 'required|in:all,students,teachers,parents',
            'publish_date' => 'required|date',
            'expire_date' => 'nullable|date|after:publish_date',
        ]);

        try {
            Announcement::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => $request->input('type'),
            'target_audience' => $request->input('target_audience'),
            'created_by' => auth()->id(),
            'publish_date' => $request->input('publish_date'),
            'expire_date' => $request->input('expire_date'),
        ]);
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
        ]);

        $announcement->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'type' => $request->type,
            'target_audience' => $request->target_audience,
            'publish_date' => $request->publish_date,
            'expire_date' => $request->expire_date,
        ]);

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

        return response()->json([
            'totalApplications' => $totalApplications,
            'pendingApplications' => $pendingApplications,
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalClasses' => $totalClasses,
            'totalAlumni' => $totalAlumni,
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
