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
use App\Models\StudentApplication;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
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
        }

        return view('student.dashboard', compact('student', 'application', 'announcements', 'schedules', 'grades', 'todaySchedules', 'recentGrades'));
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

        return view('student.profile', compact('student', 'documents', 'application'));
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
            'phone' => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'nisn' => 'nullable|string|max:50|unique:students,nisn,' . $student->id,
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
}
