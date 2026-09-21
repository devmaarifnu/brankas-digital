<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    public function test_admin_can_login_and_access_handover()
    {
        $admin = User::where('username', 'admin')->first();
        $this->assertNotNull($admin, 'Admin user must exist');

        $loginResponse = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $loginResponse->assertRedirect(route('handover.index'));

        // Dashboard redirects to handover
        $dashResponse = $this->actingAs($admin)->get('/a/dashboard');
        $dashResponse->assertRedirect(route('handover.index'));

        $handoverResponse = $this->actingAs($admin)->get('/handover');
        $handoverResponse->assertStatus(200);
    }
}