<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentApplication;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Update the desired class (SD/SMP/SMA) for the user's student application.
     * If an application already exists for this user's email, update it; otherwise create a minimal application.
     */
    public function updatePreference(Request $request)
    {
        $request->validate([
            'desired_class' => 'required|in:SD,SMP,SMA,kejuruan'
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('home')->with('error', __('User tidak ditemukan.'));
        }

        $application = StudentApplication::firstWhere('email', $user->email);
        if ($application) {
            $application->update(['desired_class' => $request->desired_class]);
        } else {
            // If no application exists yet for the user, redirect them to the registration page
            return redirect()->route('student.register')
                ->with('error', __('Silakan lengkapi form pendaftaran terlebih dahulu sebelum memilih kelas.'));
        }

        return back()->with('success', __('Pilihan kelas berhasil disimpan. Admin akan menempatkan siswa ke kelas yang sesuai.'));
    }
}
