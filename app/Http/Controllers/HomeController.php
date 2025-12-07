<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Announcement;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Show the landing page.
     */
    public function welcome()
    {
        $school = School::first();
        $announcements = $this->fetchAnnouncements();

        return view('welcome', compact('school', 'announcements'));
    }

    /**
     * Show the application dashboard based on user role.
     */
    public function index()
    {
        $user = auth()->user();

        // If no authenticated user, show the public landing page
        if (! $user) {
            return $this->welcome();
        }

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            case 'teacher':
                return redirect()->route('teacher.dashboard');
            default:
                // Ensure announcements are passed to home view
                $announcements = $this->fetchAnnouncements();
                $registrationSteps = [
                    ['number' => 1, 'title' => 'Isi Formulir', 'description' => 'Lengkapi formulir pendaftaran online'],
                    ['number' => 2, 'title' => 'Upload Dokumen', 'description' => 'Unggah dokumen persyaratan'],
                    ['number' => 3, 'title' => 'Verifikasi', 'description' => 'Tim kami akan memverifikasi data'],
                    ['number' => 4, 'title' => 'Diterima', 'description' => 'Pengumuman hasil pendaftaran']
                ];

                return view('home', compact('announcements', 'registrationSteps'));
        }
    }

    /**
     * Show registration form page.
     */
    public function showRegistrationForm()
    {
        $school = School::first();
        return view('registration.form', compact('school'));
    }

    /**
     * Show about page.
     */
    public function about()
    {
        $school = School::first();
        return view('about', compact('school'));
    }

    /**
     * Show contact page.
     */
    public function contact()
    {
        $school = School::first();
        return view('contact', compact('school'));
    }

    /**
     * Show programs page.
     */
    public function programs()
    {
        $school = School::first();
        return view('programs', compact('school'));
    }

    /**
     * Show teachers page.
     */
    public function teachers()
    {
        $teachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->with('teacher')
            ->get();

        return view('teachers', compact('teachers'));
    }

    /**
     * Show facilities page.
     */
    public function facilities()
    {
        $school = School::first();
        return view('facilities', compact('school'));
    }

    /**
     * Fetch latest announcements used on several pages
     */
    protected function fetchAnnouncements()
    {
        return Announcement::where('is_active', true)
            ->where('target_audience', 'all')
            ->where('publish_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('expire_date')
                      ->orWhere('expire_date', '>=', now());
            })
            ->orderBy('publish_date', 'desc')
            ->limit(3)
            ->get();
    }
}
