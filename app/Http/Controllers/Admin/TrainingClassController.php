<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingClass;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TrainingClassController extends Controller
{
    public function __construct()
    {
        // admin middleware assumed registered
        $this->middleware('role:admin');
    }

    public function index()
    {
        $classes = TrainingClass::with('trainer')->latest()->paginate(20);
        return view('admin.training_classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = Teacher::with('user')->get();
        return view('admin.training_classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'capacity' => 'nullable|integer|min:1',
            'trainer_id' => 'nullable|exists:teachers,id',
            'is_active' => 'nullable|boolean',
            'open_to_kejuruan' => 'nullable|boolean'
        ]);

        $data['created_by'] = Auth::id();

        $class = TrainingClass::create($data);

        return redirect()->route('admin.training-classes.index')->with('success', 'Kelas pelatihan berhasil dibuat.');
    }

    public function edit(TrainingClass $trainingClass)
    {
        $teachers = Teacher::with('user')->get();
        return view('admin.training_classes.edit', compact('trainingClass', 'teachers'));
    }

    public function update(Request $request, TrainingClass $trainingClass)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'capacity' => 'nullable|integer|min:1',
            'trainer_id' => 'nullable|exists:teachers,id',
            'is_active' => 'nullable|boolean',
            'open_to_kejuruan' => 'nullable|boolean'
        ]);

        $trainingClass->update($data);

        return redirect()->route('admin.training-classes.index')->with('success', 'Kelas pelatihan berhasil diperbarui.');
    }

    public function destroy(TrainingClass $trainingClass)
    {
        $trainingClass->delete();
        return redirect()->route('admin.training-classes.index')->with('success', 'Kelas pelatihan berhasil dihapus.');
    }

    public function show(TrainingClass $trainingClass)
    {
        $trainingClass->load('students.user', 'trainer');

        // Get available kejuruan students who are not enrolled in any training class
        $availableStudents = Student::whereHas('user', function($query) {
            $query->where('role', 'kejuruan');
        })
        ->whereDoesntHave('trainingClasses')
        ->with('user')
        ->get();

        return view('admin.training_classes.show', compact('trainingClass', 'availableStudents'));
    }

    public function addParticipant(Request $request, TrainingClass $trainingClass)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $student = Student::findOrFail($request->student_id);

        // Check if student is kejuruan role
        if ($student->user->role !== 'kejuruan') {
            return back()->with('error', 'Hanya siswa kejuruan yang dapat ditambahkan ke kelas pelatihan.');
        }

        // Check if already enrolled
        if ($trainingClass->students()->where('students.id', $student->id)->exists()) {
            return back()->with('error', 'Siswa sudah terdaftar di kelas pelatihan ini.');
        }

        // Check capacity
        if ($trainingClass->capacity && $trainingClass->students()->count() >= $trainingClass->capacity) {
            return back()->with('error', 'Kelas pelatihan sudah penuh.');
        }

        // Add student to training class
        $trainingClass->students()->attach($student->id, [
            'enrolled_at' => now(),
            'status' => 'enrolled'
        ]);

        return back()->with('success', 'Peserta berhasil ditambahkan ke kelas pelatihan.');
    }

    /**
     * Handle GET access to add-participant path — redirect back to show with notice
     */
    public function addParticipantRedirect(TrainingClass $trainingClass)
    {
        return redirect()->route('admin.training-classes.show', $trainingClass->id)
            ->with('error', 'Akses tidak diizinkan. Silakan gunakan form untuk menambahkan peserta.');
    }

    public function removeParticipant(Request $request, TrainingClass $trainingClass)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $student = Student::findOrFail($request->student_id);

        // Remove student from training class
        $trainingClass->students()->detach($student->id);

        return back()->with('success', 'Peserta berhasil dihapus dari kelas pelatihan.');
    }
}
