<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Service\UserService;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request, UserService $userService): UserResource
    {
        $data = $request->validated();

        return new UserResource($userService->register($data));
    }
}
