<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

class RolesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (auth()->user()->hasRole(
                Role::ROLE_AUTHOR,
                Role::ROLE_ADMIN,
                Role::ROLE_PUBLISHER
            )) {
                return $next($request);
            }
        }

        throw new UnauthorizedException('Unauthorized', 403);
    }
}
