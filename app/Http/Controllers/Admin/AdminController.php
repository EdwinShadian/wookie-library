<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeRolesRequest;
use App\Http\Requests\Admin\UserIndexRequest;
use App\Http\Resources\UserResource;
use App\Service\UserService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminController extends Controller
{
    public function userIndex(
        UserIndexRequest $request,
        UserService $userService,
    ): AnonymousResourceCollection {
        $perPage = (int) $request->get('perPage', 10);
        $page = (int) $request->get('page', 1);

        return UserResource::collection($userService->getUserList($perPage, $page));
    }

    public function changeRoles(ChangeRolesRequest $request, UserService $userService): UserResource
    {
        $data = $request->validated();

        return new UserResource($userService->changeRoles($data));
    }

    public function ban(UserService $userService, int $userId): UserResource
    {
        $adminId = auth()->user()->id;

        return new UserResource($userService->banAuthor($adminId, $userId));
    }
}
