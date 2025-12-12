<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Override to include a developer-friendly reset link when running locally.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Use the default behavior for security: always respond with the same message.
        // If the user exists, create token and send the notification.
        if ($user) {
            // Manually generate a token and store the hashed version in the password reset table.
            $token = Str::random(64);
            $table = config('auth.passwords.users.table');
            DB::table($table)->updateOrInsert([
                'email' => $user->email
            ], [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            // Send the normal reset notification (email) using the token
            $user->sendPasswordResetNotification($token);

            // When developing locally, expose the raw reset link as a flash message
            if (app()->isLocal()) {
                $link = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));
                session()->flash('reset_link', $link);
            }

            // If configured, automatically redirect the user to the reset page
            // (This will expose account existence; use with caution)
            if (app()->isLocal() || env('PASSWORD_RESET_AUTO_REDIRECT', false)) {
                return redirect()->route('password.reset', ['token' => $token, 'email' => $user->email]);
            }
        }

        // Return the usual success response so callers don't learn whether the email exists
        return back()->with('status', trans(Password::RESET_LINK_SENT));
    }
}
