<?php

declare(strict_types=1);

namespace App\Service;

use App\Models\Role;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\UnauthorizedException;

class UserService
{
    public function register(array $data): User
    {
        $user = User::create($data);
        $user->roles()->attach(Role::where('name', Role::ROLE_AUTHOR)->first()->id);

        return $user;
    }

    public function getUserList(int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return User::paginate($perPage, ['*'], page: $page);
    }

    public function changeRoles(array $data): User
    {
        $user = User::findOrFail($data['user_id']);

        $roleIds = Role::whereIn('name', $data['roles'])->get()->pluck('id')->toArray();

        $user->roles()->sync($roleIds);

        return $user;
    }

    public function banAuthor(int $adminId, int $userId): User
    {
        $admin = User::findOrFail($adminId);

        if ($admin->id === $userId) {
            throw new UnauthorizedException('You cannot ban yourself', 403);
        }

        $this->changeRoles([
            'user_id' => $userId,
            'roles' => [],
        ]);

        return User::findOrFail($userId);
    }
}
