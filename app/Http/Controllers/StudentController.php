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
            ->take(5)
            ->get();

        $schedules = [];
        $grades = [];

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
        }

        return view('student.dashboard', compact('student', 'announcements', 'schedules', 'grades'));
    }

    public function profile()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->with('classRoom')->first();
        $documents = Document::where('documentable_type', Student::class)
            ->where('documentable_id', $student->id)
            ->get();

        return view('student.profile', compact('student', 'documents'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'interests_talents' => 'nullable|array',
            'health_info' => 'nullable|array',
            'disability_info' => 'nullable|array',
        ]);

        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        $student->update([
            'interests_talents' => $request->interests_talents,
            'health_info' => $request->health_info,
            'disability_info' => $request->disability_info,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function grades()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

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

        $schedules = [];
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
