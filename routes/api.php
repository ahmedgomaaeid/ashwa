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
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('phonelogin', [App\Http\Controllers\Api\AuthController::class, 'phoneLogin']);
    Route::post('verify-code', [App\Http\Controllers\Api\AuthController::class, 'verifyCode']);
    Route::post('send-again',[App\Http\Controllers\Api\AuthController::class, 'sendAgain']);
});

Route::group(['prefix' => 'public'], function () {
    Route::get('categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('category/{id}/products', [App\Http\Controllers\Api\CategoryController::class, 'products']);
    Route::get('search', [App\Http\Controllers\Api\CategoryController::class, 'search']);
});


Route::middleware(['jwt.verify', 'check.verified'])->group(function () {
    // Protected routes
    Route::get('/test', function () {
        return response()->json(['message' => 'Now you are authenticated']);
    });
    // Add other routes that need protection
});
