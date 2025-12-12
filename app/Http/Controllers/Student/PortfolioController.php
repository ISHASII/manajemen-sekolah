<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentPortfolio;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:student');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $student = Student::where('user_id', Auth::id())->firstOrFail();

        $data = $request->only(['title', 'description', 'link']);
        $data['student_id'] = $student->id;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = 'portfolio_' . $student->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('student-portfolio', $filename, 'public');
            $data['file_path'] = $path;
        }

        StudentPortfolio::create($data);

        return back()->with('success', 'Portofolio berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $portfolio = StudentPortfolio::where('student_id', $student->id)->where('id', $id)->firstOrFail();
        if ($portfolio->file_path && Storage::disk('public')->exists($portfolio->file_path)) {
            Storage::disk('public')->delete($portfolio->file_path);
        }
        $portfolio->delete();
        return back()->with('success', 'Portofolio dihapus');
    }
}
