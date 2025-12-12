<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\TeachingMaterial;
use App\Models\StudentSubmission;

class StudentSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // store and update must be student
        $this->middleware('role:student')->only(['store', 'update']);
        // index only for teachers
        $this->middleware('role:teacher')->only(['index']);
    }

    /**
     * Store a student submission for a teaching material
     */
    public function store(Request $request, $materialId)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return back()->with('error', 'Profil siswa tidak ditemukan.');
        }

        $material = TeachingMaterial::findOrFail($materialId);

        // Only allow submission if the material is visible and belongs to student's class or all classes
        if (!$material->is_visible) {
            abort(403, 'Materi tidak tersedia untuk diakses.');
        }
        if ($material->class_id && $material->class_id !== $student->class_id) {
            abort(403, 'Anda tidak berhak menyerahkan tugas untuk materi ini.');
        }
        // If the material is assigned to a training class, ensure the student is enrolled in that training class
        if ($material->training_class_id) {
            $isEnrolled = $student->trainingClasses()->where('training_classes.id', $material->training_class_id)->exists();
            if (!$isEnrolled) {
                abort(403, 'Anda tidak terdaftar pada kelas pelatihan yang terkait materi ini.');
            }
        }

        $request->validate([
            'file' => 'required_without:link|nullable|file|mimes:pdf,doc,docx,ppt,pptx,mp4,mov,avi,jpg,jpeg,png,gif|max:51200',
            'link' => 'nullable|required_without:file|url|max:2048',
            'description' => 'nullable|string|max:2000',
        ]);

        $path = null;
        $fileType = null;
        $link = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('student-submissions', $filename, 'public');
            $fileType = $file->getClientOriginalExtension();
        } elseif ($request->filled('link')) {
            $link = $request->link;
            $fileType = 'link';
        }

        // If a submission exists, update it; otherwise create a new one
        $submission = StudentSubmission::where('student_id', $student->id)->where('material_id', $material->id)->first();
        if ($submission) {
            // if replacing file, delete existing
            if ($path && $submission->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($submission->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($submission->file_path);
            }
            $submission->file_path = $path;
            $submission->file_type = $fileType;
            $submission->link = $link;
            $submission->description = $request->description;
            $submission->save();
        } else {
            $submission = StudentSubmission::create([
                'student_id' => $student->id,
                'material_id' => $material->id,
                'file_path' => $path,
                'file_type' => $fileType,
                'link' => $link,
                'description' => $request->description,
            ]);
        }

        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }

    /**
     * List submissions for a material (teacher view)
     */
    public function index($materialId)
    {
        $user = Auth::user();
        $material = TeachingMaterial::findOrFail($materialId);

        // Only allow teacher who owns the material
        if ($material->teacher_id !== $user->id) {
            abort(403, 'Anda tidak diizinkan melihat pengumpulan tugas untuk materi ini.');
        }

        $submissions = StudentSubmission::where('material_id', $material->id)->with(['student.user'])->orderBy('created_at', 'desc')->paginate(50);

        return view('teacher.materials.submissions', compact('material', 'submissions'));
    }

    /**
     * Update an existing student submission
     */
    public function update(Request $request, $materialId, $submissionId)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return back()->with('error', 'Profil siswa tidak ditemukan.');
        }

        $material = TeachingMaterial::findOrFail($materialId);
        $submission = StudentSubmission::where('id', $submissionId)->where('student_id', $student->id)->firstOrFail();

        // Ownership check: ensure submission belongs to the material
        if ($submission->material_id !== $material->id) {
            abort(403, 'Submission tidak valid untuk materi ini.');
        }

        // Only allow update if the material is visible and accessible
        if (!$material->is_visible) {
            abort(403, 'Materi tidak tersedia untuk diakses.');
        }
        if ($material->class_id && $material->class_id !== $student->class_id) {
            abort(403, 'Anda tidak berhak mengubah tugas untuk materi ini.');
        }
        if ($material->training_class_id) {
            $isEnrolled = $student->trainingClasses()->where('training_classes.id', $material->training_class_id)->exists();
            if (!$isEnrolled) {
                abort(403, 'Anda tidak terdaftar pada kelas pelatihan yang terkait materi ini.');
            }
        }

        $request->validate([
            'file' => 'required_without:link|nullable|file|mimes:pdf,doc,docx,ppt,pptx,mp4,mov,avi,jpg,jpeg,png,gif|max:51200',
            'link' => 'nullable|required_without:file|url|max:2048',
            'description' => 'nullable|string|max:2000',
        ]);

        // Update file/link if provided
        if ($request->hasFile('file')) {
            // delete old file if exists
            if ($submission->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($submission->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($submission->file_path);
            }
            $file = $request->file('file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('student-submissions', $filename, 'public');
            $submission->file_path = $path;
            $submission->file_type = $file->getClientOriginalExtension();
            $submission->link = null;
        } elseif ($request->filled('link')) {
            // If link provided, remove file_path if was present
            if ($submission->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($submission->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($submission->file_path);
            }
            $submission->file_path = null;
            $submission->file_type = 'link';
            $submission->link = $request->link;
        }

        $submission->description = $request->description;
        $submission->save();

        return back()->with('success', 'Tugas berhasil diperbarui.');
    }
}
