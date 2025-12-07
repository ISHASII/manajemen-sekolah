<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentApplication;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentApplicationController extends Controller
{
    public function showRegistrationForm()
    {
        $classes = ClassRoom::where('is_active', true)
            ->where('current_students', '<', 'capacity')
            ->get();

        return view('student.register', compact('classes'));
    }

    public function submitApplication(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'email' => 'required|email|unique:student_applications,email',
            'phone' => 'required|string|max:20',
            'nisn' => 'nullable|string|unique:student_applications,nisn',
            'place_of_birth' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'religion' => 'required|in:islam,kristen,katolik,hindu,budha,khonghucu',
            'address' => 'required|string',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'parent_address' => 'required|string',
            'parent_job' => 'nullable|string|max:255',
            'desired_class' => 'required|in:SD,SMP,SMA',
            'health_conditions' => 'nullable|array',
            'disabilities' => 'nullable|array',
            'previous_school' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer',
            'birth_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'last_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'photo' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'agreement' => 'accepted',
        ]);

        // Generate application number
        $applicationNumber = 'APP' . date('Y') . str_pad(StudentApplication::count() + 1, 4, '0', STR_PAD_LEFT);

        // Handle file uploads - required documents
        $documents = [];
        foreach (['birth_certificate', 'last_certificate', 'photo'] as $key) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $filename = time() . '_' . $key . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('application-documents', $filename, 'public');

                $documents[] = [
                    'type' => $key,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        // Create application
        $application = StudentApplication::create([
            'application_number' => $applicationNumber,
            'student_name' => $request->student_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'nisn' => $request->nisn,
            'place_of_birth' => $request->place_of_birth,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'religion' => $request->religion,
            'address' => $request->address,
            'parent_name' => $request->parent_name,
            'parent_phone' => $request->parent_phone,
            'parent_address' => $request->parent_address,
            'parent_job' => $request->parent_job,
            'desired_class' => $request->desired_class,
            'health_info' => $request->health_conditions ?? [],
            'disability_info' => $request->disabilities ?? [],
            'education_history' => [
                'previous_school' => $request->previous_school,
                'graduation_year' => $request->graduation_year,
            ],
            'documents' => $documents,
            'application_date' => now(),
        ]);

        return redirect()->route('application.success')
            ->with('application_number', $applicationNumber)
            ->with('success', 'Pendaftaran berhasil disubmit! Nomor aplikasi Anda: ' . $applicationNumber);
    }

    public function applicationSuccess()
    {
        return view('student.application-success');
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'application_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $application = StudentApplication::where('application_number', $request->application_number)
            ->where('email', $request->email)
            ->first();

        if (!$application) {
            return back()->with('error', 'Aplikasi tidak ditemukan. Periksa kembali nomor aplikasi dan email Anda.');
        }

        return view('student.application-status', compact('application'));
    }

    public function showStatusForm()
    {
        return view('student.check-status');
    }
}
