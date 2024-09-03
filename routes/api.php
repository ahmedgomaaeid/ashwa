<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware(['jwt.verify'])->group(function () {
    // Protected routes
    Route::get('/test', function () {
        return response()->json(['message' => 'This is a protected route']);
    });
    // Add other routes that need protection
});
