<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;

class AuthController
{
    public function login(LoginRequest $request): Response
    {
        $credentials = $request->validated();

        $token = auth()->attempt($credentials);

        if (! $token) {
            throw new UnauthorizedException('Unauthorized', 401);
        }

        return response()->withToken($token);
    }

    public function me(): Response
    {
        return response()->api(new UserResource(auth()->user()));
    }

    public function logout(): Response
    {
        auth()->logout();

        return response()->api(['message' => 'Successfully logged out']);
    }

    public function refresh(): Response
    {
        return response()->withToken(auth()->refresh());
    }
}
