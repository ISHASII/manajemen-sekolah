<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\StudentApplication;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Append is_active requirement to credentials so only active users can login
     */
    protected function credentials(Request $request)
    {
        $creds = $request->only($this->username(), 'password');
        $creds['is_active'] = true;
        return $creds;
    }

    /**
     * Show a helpful message if login failed and an application exists for this email that is pending
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $email = $request->input($this->username());
        $app = StudentApplication::where('email', $email)->where('status', 'pending')->first();
        if ($app) {
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([$this->username() => __('Akun Anda belum aktif. Pendaftaran sedang menunggu persetujuan admin.')]);
        }
        return parent::sendFailedLoginResponse($request);
    }
}
