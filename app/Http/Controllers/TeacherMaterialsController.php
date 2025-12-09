<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeachingMaterial;
use App\Models\ClassRoom;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
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
        // order materials ascending by title
        $materials = TeachingMaterial::where('teacher_id', $user->id)->orderBy('title', 'asc')->paginate(20);
        return view('teacher.materials.index', compact('materials'));
    }

    public function create()
    {
        $user = Auth::user();
        // collect classes teacher teaches via schedule or homeroom
        $classes = ClassRoom::where('homeroom_teacher_id', $user->id)
            ->orWhereIn('id', function($q) use ($user){
                $q->select('class_id')->from('schedules')->where('teacher_id', $user->id);
            })->get();
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
        return view('teacher.materials.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'required|file|mimes:pdf,doc,docx,pptx,mp4,mov,avi,jpg,jpeg,png,gif|max:20480', // 20MB
            'is_visible' => 'nullable|boolean'
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $file->getClientOriginalName());
        $path = $file->storeAs('teacher-materials', $filename, 'public');
        $fileType = $file->getClientOriginalExtension();

        $material = TeachingMaterial::create([
            'teacher_id' => $user->id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_type' => $fileType,
            'is_visible' => $request->boolean('is_visible', true)
        ]);

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
        return view('teacher.materials.edit', compact('material', 'classes', 'subjects'));
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
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,pptx,mp4,mov,avi,jpg,jpeg,png,gif|max:20480', // 20MB
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

        $material->title = $request->title;
        $material->description = $request->description;
        $material->class_id = $request->class_id;
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
        return redirect()->route('teacher.materials.index')->with('success', 'Materi berhasil dihapus.');
    }
}
