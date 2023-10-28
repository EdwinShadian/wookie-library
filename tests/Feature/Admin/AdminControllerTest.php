<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Auth;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Role::factory()->create(['name' => Role::ROLE_AUTHOR]);
        Role::factory()->create(['name' => Role::ROLE_ADMIN]);
        Role::factory()->create(['name' => Role::ROLE_PUBLISHER]);
    }

    /**
     * Check if we have a paginated list of users in response
     */
    public function testUserIndex(): void
    {
        $users = User::factory()->count(2)->create();

        $users->get(0)->roles()->attach(Role::where('name', Role::ROLE_ADMIN)->get()->first()->id);

        Auth::login($users->get(0));

        $response = $this->get('api/admin/users');
        $response->assertOk();

        $data = json_decode($response->getContent(), true)['data'];

        $this->assertCount(2, $data);
        $this->assertJson($users->get(0)->toJson(), json_encode($data[0]));
        $this->assertJson($users->get(1)->toJson(), json_encode($data[1]));
    }

    /**
     * Check if admin can change roles for some user
     */
    public function testChangeRoles(): void
    {
        $users = User::factory()->count(2)->create();

        $users->get(0)->roles()->attach(Role::where('name', Role::ROLE_ADMIN)->get()->first()->id);

        Auth::login($users->get(0));

        $response = $this->post('api/admin/roles', [
            'user_id' => $users->get(1)->id,
            'roles' => [Role::ROLE_AUTHOR, Role::ROLE_PUBLISHER],
        ]);
        $response->assertOk();

        $data = json_decode($response->getContent(), true)['data'];

        $users->get(1)->refresh();

        $this->assertJson($users->get(1)->toJson(), json_encode($data));
        $this->assertTrue($users->get(1)->hasRole(Role::ROLE_AUTHOR, Role::ROLE_PUBLISHER));
    }

    /**
     * Check if admin can ban some user to use internal api
     */
    public function testBan(): void
    {
        $users = User::factory()->count(2)->create();
        $users->get(0)
            ->roles()
            ->attach(Role::where('name', Role::ROLE_ADMIN)->get()->first()->id);
        $users->get(1)
            ->roles()
            ->attach(Role::whereIn('name', [Role::ROLE_AUTHOR, Role::ROLE_PUBLISHER])->get());

        Auth::login($users->get(0));

        $response = $this->post("api/admin/ban/{$users->get(1)->id}");
        $response->assertOk();

        $data = json_decode($response->getContent(), true)['data'];

        $users->get(1)->refresh();

        $this->assertJson($users->get(1)->toJson(), json_encode($data));
        $this->assertSame([], $users->get(1)->roles()->get()->toArray());
    }
}
