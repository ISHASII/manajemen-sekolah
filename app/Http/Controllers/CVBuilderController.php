<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentSkill;
use App\Models\StudentPortfolio;
use Illuminate\Support\Facades\Auth;

class CVBuilderController extends Controller
{
    /**
     * Show CV Builder form
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->with(['skills', 'trainingClasses'])->first();
        $portfolios = StudentPortfolio::where('student_id', $student?->id)->get();

        // Get training classes as certifications
        $trainings = collect();
        if ($student) {
            $trainings = $student->trainingClasses()
                ->wherePivot('status', 'enrolled')
                ->get();
        }

        return view('kejuruan.cv-builder.index', compact('user', 'student', 'portfolios', 'trainings'));
    }

    /**
     * Generate CV Preview
     */
    public function preview(Request $request)
    {
        $data = $this->validateCVData($request);

        return view('kejuruan.cv-builder.preview', compact('data'));
    }

    /**
     * Generate CV as downloadable HTML (ATS-friendly)
     */
    public function generate(Request $request)
    {
        $data = $this->validateCVData($request);

        $html = view('kejuruan.cv-builder.ats-template', compact('data'))->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="CV_' . str_replace(' ', '_', $data['full_name']) . '.html"');
    }

    /**
     * Generate CV as printable page (for PDF via browser print)
     */
    public function print(Request $request)
    {
        $data = $this->validateCVData($request);

        return view('kejuruan.cv-builder.print', compact('data'));
    }

    /**
     * Validate and prepare CV data
     */
    private function validateCVData(Request $request): array
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'linkedin' => 'nullable|url|max:255',
            'portfolio_link' => 'nullable|url|max:255',

            'profile_summary' => 'nullable|string|max:1000',

            // Education (array)
            'education' => 'nullable|array',
            'education.*.school' => 'nullable|string|max:255',
            'education.*.major' => 'nullable|string|max:255',
            'education.*.year_start' => 'nullable|string|max:10',
            'education.*.year_end' => 'nullable|string|max:10',
            'education.*.gpa' => 'nullable|string|max:10',

            // Work Experience (array)
            'experience' => 'nullable|array',
            'experience.*.company' => 'nullable|string|max:255',
            'experience.*.position' => 'nullable|string|max:255',
            'experience.*.year_start' => 'nullable|string|max:10',
            'experience.*.year_end' => 'nullable|string|max:10',
            'experience.*.description' => 'nullable|string|max:1000',

            // Skills (array of strings)
            'skills' => 'nullable|array',
            'skills.*' => 'nullable|string|max:100',

            // Portfolio items
            'portfolio' => 'nullable|array',
            'portfolio.*.title' => 'nullable|string|max:255',
            'portfolio.*.description' => 'nullable|string|max:500',
            'portfolio.*.link' => 'nullable|url|max:255',

            // Certifications
            'certifications' => 'nullable|array',
            'certifications.*.name' => 'nullable|string|max:255',
            'certifications.*.issuer' => 'nullable|string|max:255',
            'certifications.*.year' => 'nullable|string|max:10',

            // Languages
            'languages' => 'nullable|array',
            'languages.*.name' => 'nullable|string|max:100',
            'languages.*.level' => 'nullable|string|max:50',
        ]);

        // Filter out empty entries
        if (isset($validated['education'])) {
            $validated['education'] = array_filter($validated['education'], fn($e) => !empty($e['school']));
        }
        if (isset($validated['experience'])) {
            $validated['experience'] = array_filter($validated['experience'], fn($e) => !empty($e['company']));
        }
        if (isset($validated['skills'])) {
            $validated['skills'] = array_filter($validated['skills'], fn($s) => !empty($s));
        }
        if (isset($validated['portfolio'])) {
            $validated['portfolio'] = array_filter($validated['portfolio'], fn($p) => !empty($p['title']));
        }
        if (isset($validated['certifications'])) {
            $validated['certifications'] = array_filter($validated['certifications'], fn($c) => !empty($c['name']));
        }
        if (isset($validated['languages'])) {
            $validated['languages'] = array_filter($validated['languages'], fn($l) => !empty($l['name']));
        }

        return $validated;
    }
}
