<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Role::factory()->create(['name' => Role::ROLE_AUTHOR]);
    }

    /**
     * Check if user can register himself as author
     */
    public function testRegister(): void
    {
        $response = $this->post('api/auth/register', [
            'name' => 'Yoda',
            'author_pseudonym' => 'Grandmaster',
            'password' => 'password',
        ]);
        $response->assertStatus(201);

        $this->assertDatabaseHas(User::class, [
            'name' => 'Yoda',
            'author_pseudonym' => 'Grandmaster',
        ]);
        $this->assertTrue(
            User::where('author_pseudonym', 'Grandmaster')
            ->first()
            ->hasRole(Role::ROLE_AUTHOR)
        );
    }
}
