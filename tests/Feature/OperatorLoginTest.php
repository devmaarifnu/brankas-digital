<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class OperatorLoginTest extends TestCase
{
    public function test_operator_can_login_and_access_handover()
    {
        $user = User::where('username', '7020001')->first();
        $this->assertNotNull($user, 'Operator user 7020001 must exist');

        $loginResponse = $this->post('/login', [
            'username' => '7020001',
            'password' => '7020001',
        ]);

        $loginResponse->assertRedirect(route('handover.index'));

        // Old dashboard redirects to handover
        $dashResponse = $this->actingAs($user)->get('/dashboard');
        $dashResponse->assertRedirect(route('handover.index'));

        $handoverResponse = $this->actingAs($user)->get('/handover');
        $handoverResponse->assertStatus(200);
    }
}