<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Auth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * Check if we can login existing user
     */
    public function testLogin(): void
    {
        $user = User::factory()->create();

        $response = $this->post('api/auth/login', [
            'author_pseudonym' => $user->author_pseudonym,
            'password' => 'password',
        ]);
        $response->assertOk();

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('access_token', $data);
    }

    /**
     * Check if user can get info about himself
     */
    public function testMe(): void
    {
        $user = User::factory()->create();

        Auth::login($user);

        $response = $this->get('api/auth/me');
        $response->assertOk();

        $data = json_decode($response->getContent(), true)['data'];
        $this->assertJson($user->toJson(), json_encode($data));
    }

    /**
     * Check if we can logout
     */
    public function testLogout(): void
    {
        $user = User::factory()->create();

        Auth::login($user);
        $response = $this->post('api/auth/logout');
        $response->assertOk();

        $data = json_decode($response->getContent(), true)['data'];
        $this->assertSame(['message' => 'Successfully logged out'], $data);
        $this->assertNull(Auth::getUser());
    }

    /**
     * Check if user can get new token without logout/login procedure
     */
    public function testRefresh(): void
    {
        $user = User::factory()->create();

        Auth::login($user);
        $response = $this->post('api/auth/refresh');
        $response->assertOk();

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('access_token', $data);
        $this->assertSame($user, Auth::getUser());
    }
}
