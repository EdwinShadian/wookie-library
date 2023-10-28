<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'auth', 'middleware' => 'api'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);

    Route::group(['middleware' => 'auth:api'], function ($router) {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::group(['prefix' => 'public', 'middleware'=> 'api'], function ($router) {
    Route::resource('books', \App\Http\Controllers\PublicApi\BookController::class)
        ->only(['index', 'show']);
});

Route::group(['prefix'=> 'internal', 'middleware'=> ['roles', 'api']], function ($router) {
    Route::resource('books', \App\Http\Controllers\Internal\BookController::class)
        ->except(['create', 'edit']);
});

Route::group(['prefix'=> 'admin', 'middleware'=> ['admin', 'api']], function ($router) {
    Route::get('users', [AdminController::class, 'userIndex']);
    Route::post('roles', [AdminController::class, 'changeRoles']);
    Route::post('ban/{userId}', [AdminController::class, 'ban']);
});
