<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_password_reset_link()
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertSessionHas('status');

        // Assert the password reset token was stored in DB
        $this->assertDatabaseHas(config('auth.passwords.users.table'), ['email' => $user->email]);

        // Assert ResetPassword notification was dispatched
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_user_can_reset_password_and_is_redirected_to_login_without_auto_login()
    {
        $user = User::factory()->create(['password' => Hash::make('oldpassword')]);

        $token = Str::random(64);

        DB::table(config('auth.passwords.users.table'))->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }
}
