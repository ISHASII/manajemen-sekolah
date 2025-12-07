<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class GuestNavLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_check_status_link()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee(route('application.status'));
        $response->assertSee('Cek Status');
    }

    public function test_authenticated_user_does_not_see_check_status_link()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('home'));
        $response->assertStatus(200);
        $response->assertDontSee('Cek Status');
    }
}
