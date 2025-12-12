<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeachingMaterial;
use App\Models\ClassRoom;
use App\Models\TrainingClass;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Schedule;
use App\Models\Teacher;
use Illuminate\Support\Facades\Storage;

class TeacherMaterialsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:teacher');
    }

    public function index()
    {
        $user = Auth::user();
        // If student_submissions table exists, eager-load submissions count to reduce queries
        $hasSubmissionsTable = Schema::hasTable('student_submissions');
        $query = TeachingMaterial::where('teacher_id', $user->id)->orderBy('title', 'asc');
        if ($hasSubmissionsTable) {
            $query = $query->withCount('submissions');
        }
        $materials = $query->paginate(20);
        return view('teacher.materials.index', compact('materials', 'hasSubmissionsTable'));
    }

    public function create()
    {
        $user = Auth::user();
        // collect classes teacher teaches via schedule or homeroom
        $classes = ClassRoom::where('homeroom_teacher_id', $user->id)
            ->orWhereIn('id', function($q) use ($user){
                $q->select('class_id')->from('schedules')->where('teacher_id', $user->id);
            })->get();
        // training classes where this teacher is assigned as trainer
        $teacherModel = Teacher::where('user_id', $user->id)->first();
        $trainingClasses = TrainingClass::where('is_active', true);
        if ($teacherModel) {
            $trainingClasses = $trainingClasses->where('trainer_id', $teacherModel->id);
        } else {
            $trainingClasses = $trainingClasses->whereNull('trainer_id');
        }
        $trainingClasses = $trainingClasses->withCount('students')->get();
        // Restrict options if query context specified
        $ctxTraining = request()->query('training_class_id');
        $ctxClass = request()->query('class_id');
        if ($ctxTraining) {
            $trainingClasses = $trainingClasses->where('id', $ctxTraining)->values();
            $classes = collect();
        }
        if ($ctxClass) {
            $classes = $classes->where('id', $ctxClass)->values();
            $trainingClasses = collect();
        }
        // Subjects taught by this teacher based on schedule entries
        $subjectIds = Schedule::where('teacher_id', $user->id)->pluck('subject_id')->unique()->filter()->values()->all();
        // also include teacher->subjects set on profile (if numeric IDs are used)
        $teacherModel = Teacher::where('user_id', $user->id)->first();
        if ($teacherModel && !empty($teacherModel->subjects)) {
            $teacherSubjects = [];
            if (is_array($teacherModel->subjects)) {
                $teacherSubjects = array_map('intval', array_filter($teacherModel->subjects));
            } elseif (!is_array($teacherModel->subjects)) {
                $subjectsStr = (string)$teacherModel->subjects;
                $maybeJson = @json_decode($subjectsStr, true);
                if (is_array($maybeJson)) {
                    $teacherSubjects = array_map('intval', array_filter($maybeJson));
                } elseif (strpos($subjectsStr, ',') !== false) {
                    $teacherSubjects = array_map('intval', array_filter(array_map('trim', explode(',', $subjectsStr))));
                }
            }
            // If teacherSubjects didn't parse numeric IDs, try mapping by name or code
            if (count($teacherSubjects) === 0) {
                $potential = [];
                if (is_array($teacherModel->subjects)) {
                    $potential = array_filter($teacherModel->subjects);
                } else {
                    $potential = array_filter(array_map('trim', preg_split('/[,\r\n]+/', (string)$teacherModel->subjects)));
                }
                // Try mapping to Subject IDs by name or code
                $mappedIds = Subject::whereIn('name', $potential)->orWhereIn('code', $potential)->pluck('id')->toArray();
                if (!empty($mappedIds)) {
                    $teacherSubjects = array_map('intval', $mappedIds);
                }
            }
            $subjectIds = array_values(array_unique(array_merge($subjectIds, $teacherSubjects)));
        }
        $subjects = Subject::whereIn('id', $subjectIds)->where('is_active', true)->get();
        return view('teacher.materials.create', compact('classes', 'subjects', 'trainingClasses'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $teacherModel = Teacher::where('user_id', $user->id)->first();
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id|required_without:training_class_id',
            'training_class_id' => 'nullable|exists:training_classes,id|required_without:class_id',
            'subject_id' => 'nullable|exists:subjects,id|required_without:training_class_id',
            // either file or link is required. file max 50MB.
            'file' => 'required_without:link|file|mimes:pdf,doc,docx,pptx,mp4,mov,avi,jpg,jpeg,png,gif|max:51200', // 50MB
            'link' => 'nullable|required_without:file|url|max:2048',
            'is_visible' => 'nullable|boolean'
        ]);

        $path = null;
        $fileType = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('teacher-materials', $filename, 'public');
            $fileType = $file->getClientOriginalExtension();
        } elseif ($request->filled('link')) {
            // store link in file_path and mark file_type as 'link'
            $path = $request->link;
            $fileType = 'link';
        }

        // verify teacher can upload to the provided class or training class
        $canManageClass = false;
        if (!empty($request->class_id)) {
            $canManageClass = \App\Models\Schedule::where('teacher_id', $user->id)->where('class_id', $request->class_id)->exists();
            if (!$canManageClass) {
                $canManageClass = \App\Models\ClassRoom::where('id', $request->class_id)->where('homeroom_teacher_id', $user->id)->exists();
            }
        }
        if ($request->filled('class_id') && !$canManageClass) {
            abort(403, 'Anda tidak dapat menambahkan materi untuk kelas ini.');
        }

        // If training class is provided, ensure teacher is trainer
        if ($request->filled('training_class_id')) {
            $canManageTraining = false;
            if ($teacherModel) {
                $canManageTraining = TrainingClass::where('id', $request->training_class_id)->where('trainer_id', $teacherModel->id)->exists();
            }
            if (!$canManageTraining) {
                abort(403, 'Anda tidak dapat menambahkan materi untuk kelas pelatihan ini.');
            }
        }

        $material = TeachingMaterial::create([
            'teacher_id' => $user->id,
            'class_id' => $request->class_id,
            'training_class_id' => $request->training_class_id,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_type' => $fileType,
            'is_visible' => $request->boolean('is_visible', true)
        ]);

        // redirect to a context-specific listing
        if ($request->filled('training_class_id')) {
            return redirect()->route('teacher.training-class.materials', $request->training_class_id)->with('success', 'Materi berhasil diunggah.');
        }
        return redirect()->route('teacher.materials.index')->with('success', 'Materi berhasil diunggah.');
    }

    public function edit($id)
    {
        $material = TeachingMaterial::findOrFail($id);
        // Only allow the owner teacher to edit
        if ($material->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $user = Auth::user();
        $classes = ClassRoom::where('homeroom_teacher_id', $user->id)
            ->orWhereIn('id', function($q) use ($user){
                $q->select('class_id')->from('schedules')->where('teacher_id', $user->id);
            })->get();
        $teacherModel = Teacher::where('user_id', $user->id)->first();
        $trainingClasses = TrainingClass::where('is_active', true);
        if ($teacherModel) {
            $trainingClasses = $trainingClasses->where('trainer_id', $teacherModel->id);
        } else {
            $trainingClasses = $trainingClasses->whereNull('trainer_id');
        }
        $trainingClasses = $trainingClasses->get();
        // Restrict options if query context specified
        $ctxTraining = request()->query('training_class_id');
        $ctxClass = request()->query('class_id');
        if ($ctxTraining) {
            $trainingClasses = $trainingClasses->where('id', $ctxTraining)->values();
            $classes = collect();
        }
        if ($ctxClass) {
            $classes = $classes->where('id', $ctxClass)->values();
            $trainingClasses = collect();
        }
        $subjectIds = Schedule::where('teacher_id', $user->id)->pluck('subject_id')->unique()->filter()->values()->all();
        $teacherModel = Teacher::where('user_id', $user->id)->first();
        if ($teacherModel && !empty($teacherModel->subjects)) {
            $teacherSubjects = [];
            if (is_array($teacherModel->subjects)) {
                $teacherSubjects = array_map('intval', array_filter($teacherModel->subjects));
            } elseif (!is_array($teacherModel->subjects)) {
                $subjectsStr = (string)$teacherModel->subjects;
                $maybeJson = @json_decode($subjectsStr, true);
                if (is_array($maybeJson)) {
                    $teacherSubjects = array_map('intval', array_filter($maybeJson));
                } elseif (strpos($subjectsStr, ',') !== false) {
                    $teacherSubjects = array_map('intval', array_filter(array_map('trim', explode(',', $subjectsStr))));
                }
            }
            // If teacherSubjects didn't parse numeric IDs, try mapping by name or code
            if (count($teacherSubjects) === 0) {
                $potential = [];
                if (is_array($teacherModel->subjects)) {
                    $potential = array_filter($teacherModel->subjects);
                } else {
                    $potential = array_filter(array_map('trim', preg_split('/[,\r\n]+/', (string)$teacherModel->subjects)));
                }
                // Try mapping to Subject IDs by name or code
                $mappedIds = Subject::whereIn('name', $potential)->orWhereIn('code', $potential)->pluck('id')->toArray();
                if (!empty($mappedIds)) {
                    $teacherSubjects = array_map('intval', $mappedIds);
                }
            }
            $subjectIds = array_values(array_unique(array_merge($subjectIds, $teacherSubjects)));
        }
        $subjects = Subject::whereIn('id', $subjectIds)->where('is_active', true)->get();
        return view('teacher.materials.edit', compact('material', 'classes', 'subjects', 'trainingClasses'));
    }

    public function update(Request $request, $id)
    {
        $material = TeachingMaterial::findOrFail($id);
        if ($material->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'training_class_id' => 'nullable|exists:training_classes,id',
            'subject_id' => 'nullable|exists:subjects,id|required_without:training_class_id',
            // either file or link is allowed; file max 50MB
            'file' => 'nullable|file|mimes:pdf,doc,docx,pptx,mp4,mov,avi,jpg,jpeg,png,gif|max:51200', // 50MB
            'link' => 'nullable|url|max:2048',
            'is_visible' => 'nullable|boolean'
        ]);

        if ($request->hasFile('file')) {
            // delete old file
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }
            $file = $request->file('file');
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('teacher-materials', $filename, 'public');
            $material->file_path = $path;
            $material->file_type = $file->getClientOriginalExtension();
        }
        // If teacher provides a link instead of a file, replace file_path with link and delete previous uploaded file if any
        if (!$request->hasFile('file') && $request->filled('link')) {
            // if previous file_path was an uploaded file, remove it
            if ($material->file_path && Storage::disk('public')->exists($material->file_path) && $material->file_type !== 'link') {
                Storage::disk('public')->delete($material->file_path);
            }
            $material->file_path = $request->link;
            $material->file_type = 'link';
        }

        // Verify teacher can manage the requested class or training class if changed
        if ($request->filled('class_id')) {
            $canManageClass = \App\Models\Schedule::where('teacher_id', Auth::id())->where('class_id', $request->class_id)->exists();
            if (!$canManageClass) {
                $canManageClass = \App\Models\ClassRoom::where('id', $request->class_id)->where('homeroom_teacher_id', Auth::id())->exists();
            }
            if (!$canManageClass) {
                abort(403, 'Anda tidak dapat menempatkan materi ini pada kelas tersebut.');
            }
        }

        if ($request->filled('training_class_id')) {
            $teacherModel = Teacher::where('user_id', Auth::id())->first();
            $canManageTraining = false;
            if ($teacherModel) {
                $canManageTraining = TrainingClass::where('id', $request->training_class_id)->where('trainer_id', $teacherModel->id)->exists();
            }
            if (!$canManageTraining) {
                abort(403, 'Anda tidak dapat menempatkan materi ini pada kelas pelatihan tersebut.');
            }
        }

        $material->title = $request->title;
        $material->description = $request->description;
        $material->class_id = $request->class_id;
        $material->training_class_id = $request->training_class_id;
        $material->subject_id = $request->subject_id;
        $material->is_visible = $request->boolean('is_visible', true);
        $material->save();

        return redirect()->route('teacher.materials.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $material = TeachingMaterial::findOrFail($id);
        if ($material->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        // remove file
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }
        $material->delete();
        return back()->with('success', 'Materi berhasil dihapus.');
    }

    public function classMaterials($classId)
    {
        $user = Auth::user();
        $classRoom = ClassRoom::findOrFail($classId);

        // Verify teacher can access this class
        $canManageClass = Schedule::where('teacher_id', $user->id)->where('class_id', $classId)->exists();
        if (!$canManageClass) {
            $canManageClass = ClassRoom::where('id', $classId)->where('homeroom_teacher_id', $user->id)->exists();
        }
        if (!$canManageClass) {
            abort(403, 'Anda tidak dapat mengakses materi kelas ini.');
        }

        // Get all materials for this class
        $hasSubmissionsTable = Schema::hasTable('student_submissions');
        $query = TeachingMaterial::where('teacher_id', $user->id)
            ->where('class_id', $classId)
            ->with(['subject', 'classRoom'])
            ->orderBy('created_at', 'desc');
        if ($hasSubmissionsTable) {
            $query = $query->withCount('submissions');
        }
        $materials = $query->paginate(20);

        // Get subjects taught by this teacher
        $subjectIds = Schedule::where('teacher_id', $user->id)->where('class_id', $classId)->pluck('subject_id')->unique()->filter()->values()->all();
        $teacherModel = Teacher::where('user_id', $user->id)->first();
        if ($teacherModel && !empty($teacherModel->subjects)) {
            $teacherSubjects = [];
            if (is_array($teacherModel->subjects)) {
                $teacherSubjects = array_map('intval', array_filter($teacherModel->subjects));
            } elseif (!is_array($teacherModel->subjects)) {
                $subjectsStr = (string)$teacherModel->subjects;
                $maybeJson = @json_decode($subjectsStr, true);
                if (is_array($maybeJson)) {
                    $teacherSubjects = array_map('intval', array_filter($maybeJson));
                } elseif (strpos($subjectsStr, ',') !== false) {
                    $teacherSubjects = array_map('intval', array_filter(array_map('trim', explode(',', $subjectsStr))));
                }
            }
            if (count($teacherSubjects) === 0) {
                $potential = [];
                if (is_array($teacherModel->subjects)) {
                    $potential = array_filter($teacherModel->subjects);
                } else {
                    $potential = array_filter(array_map('trim', preg_split('/[,\r\n]+/', (string)$teacherModel->subjects)));
                }
                $mappedIds = Subject::whereIn('name', $potential)->orWhereIn('code', $potential)->pluck('id')->toArray();
                if (!empty($mappedIds)) {
                    $teacherSubjects = array_map('intval', $mappedIds);
                }
            }
            $subjectIds = array_values(array_unique(array_merge($subjectIds, $teacherSubjects)));
        }
        $subjects = Subject::whereIn('id', $subjectIds)->where('is_active', true)->get();

        return view('teacher.materials.class', compact('materials', 'classRoom', 'subjects', 'hasSubmissionsTable'));
    }

    public function trainingMaterials($trainingClassId)
    {
        $user = Auth::user();
        $teacherModel = Teacher::where('user_id', $user->id)->first();
        $trainingClass = TrainingClass::findOrFail($trainingClassId);

        // Verify teacher is trainer for this training class
        $canManageTraining = false;
        if ($teacherModel) {
            $canManageTraining = TrainingClass::where('id', $trainingClassId)->where('trainer_id', $teacherModel->id)->exists();
        }
        if (!$canManageTraining) {
            abort(403, 'Anda tidak dapat mengakses materi pelatihan ini.');
        }

        // Get all materials for this training class
        $hasSubmissionsTable = Schema::hasTable('student_submissions');
        $query = TeachingMaterial::where('teacher_id', $user->id)
            ->where('training_class_id', $trainingClassId)
            ->with(['subject'])
            ->orderBy('created_at', 'desc');
        if ($hasSubmissionsTable) {
            $query = $query->withCount('submissions');
        }
        $materials = $query->paginate(20);

        // Subjects for training classes may be defined by trainer or via other means; use teacher->subjects fallback
        $subjectIds = Schedule::where('teacher_id', $user->id)->pluck('subject_id')->unique()->filter()->values()->all();
        $teacherModel = Teacher::where('user_id', $user->id)->first();
        if ($teacherModel && !empty($teacherModel->subjects)) {
            $teacherSubjects = [];
            if (is_array($teacherModel->subjects)) {
                $teacherSubjects = array_map('intval', array_filter($teacherModel->subjects));
            } else {
                $subjectsStr = (string)$teacherModel->subjects;
                $maybeJson = @json_decode($subjectsStr, true);
                if (is_array($maybeJson)) {
                    $teacherSubjects = array_map('intval', array_filter($maybeJson));
                } elseif (strpos($subjectsStr, ',') !== false) {
                    $teacherSubjects = array_map('intval', array_filter(array_map('trim', explode(',', $subjectsStr))));
                }
            }
            if (count($teacherSubjects) === 0) {
                $potential = [];
                if (is_array($teacherModel->subjects)) {
                    $potential = array_filter($teacherModel->subjects);
                } else {
                    $potential = array_filter(array_map('trim', preg_split('/[,\r\n]+/', (string)$teacherModel->subjects)));
                }
                $mappedIds = Subject::whereIn('name', $potential)->orWhereIn('code', $potential)->pluck('id')->toArray();
                if (!empty($mappedIds)) {
                    $teacherSubjects = array_map('intval', $mappedIds);
                }
            }
            $subjectIds = array_values(array_unique(array_merge($subjectIds, $teacherSubjects)));
        }
        $subjects = Subject::whereIn('id', $subjectIds)->where('is_active', true)->get();

        return view('teacher.materials.training', compact('materials', 'trainingClass', 'subjects', 'hasSubmissionsTable'));
    }
}
