<?php

declare(strict_types=1);

namespace App\Providers;

use App\Service\Book\BookServiceIntegrationInterface;
use App\Service\Book\GutendexService;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Response;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookServiceIntegrationInterface::class, function (Application $app) {
            return new GutendexService(new Client());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('api', function (array|object $data) {
            return Response::make([
                'data' => $data,
            ]);
        });

        Response::macro('error', function (Throwable $e) {
            return Response::make([
                'status' => $e->getCode() ?: 500,
                'message' => $e->getMessage(),
            ]);
        });

        Response::macro('withToken', function (string $token) {
            return Response::make([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ]);
        });
    }
}
