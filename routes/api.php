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
    Route::post('password/reset-request', [App\Http\Controllers\Api\AuthController::class, 'requestResetCode']);
    Route::post('password/verify-reset-code', [App\Http\Controllers\Api\AuthController::class, 'verifyResetCode']);
    Route::post('password/reset', [App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
});

Route::group(['prefix' => 'public'], function () {
    Route::get('offers', [App\Http\Controllers\Api\CategoryController::class, 'offers']);
    Route::get('categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('category/{id}/sections', [App\Http\Controllers\Api\CategoryController::class, 'sections']);
    Route::get('section/{s_id}/products', [App\Http\Controllers\Api\CategoryController::class, 'products']);
    Route::post('product/{product_id}',[App\Http\Controllers\Api\CategoryController::class, 'product_detail']);
    Route::post('search', [App\Http\Controllers\Api\CategoryController::class, 'search']);
    Route::post('homepage', [App\Http\Controllers\Api\CategoryController::class, 'homepage']);
});




Route::middleware(['jwt.verify'])->group(function () {
    // Protected routes
    Route::middleware(['check.verified'])->group(function () {
        Route::group(['prefix' => 'seller', 'middleware' => 'sellerCheck'], function () {
            Route::group(['prefix' => 'product'], function () {
                Route::get('get-my-products', [App\Http\Controllers\Api\Seller\ProductController::class, 'get']);
                Route::get('product/{product_id}', [App\Http\Controllers\Api\Seller\ProductController::class, 'getProduct']);
                Route::get('categories', [App\Http\Controllers\Api\Seller\ProductController::class, 'getCategories']);
                Route::get('cayegory/{category_id}/sections', [App\Http\Controllers\Api\Seller\ProductController::class, 'getSections']);
                Route::post('add', [App\Http\Controllers\Api\Seller\ProductController::class, 'add']);
                Route::post('update', [App\Http\Controllers\Api\Seller\ProductController::class, 'update']);
                Route::post('delete', [App\Http\Controllers\Api\Seller\ProductController::class, 'delete']);
                Route::post('upload-images', [App\Http\Controllers\Api\Seller\ProductController::class, 'uploadImages']);
                Route::post('delete-image', [App\Http\Controllers\Api\Seller\ProductController::class, 'deleteImage']);
            });

            Route::group(['prefix' => 'order'], function () {
                Route::get('get', [App\Http\Controllers\Api\Seller\OrderController::class, 'get']);
                Route::post('update-status', [App\Http\Controllers\Api\Seller\OrderController::class, 'updateOrderStatus']);
            });

            Route::group(['prefix' => 'transaction'], function () {
                Route::get('get', [App\Http\Controllers\Api\Seller\TransactionController::class, 'get']);
            });
        });
    });

    Route::group(['prefix'=>'profile'], function(){
        Route::get('me', [App\Http\Controllers\Api\ProfileController::class, 'me']);
        Route::post('update', [App\Http\Controllers\Api\ProfileController::class, 'update']);
        Route::post('update-password', [App\Http\Controllers\Api\ProfileController::class, 'updatePassword']);
        Route::post('update-image', [App\Http\Controllers\Api\ProfileController::class, 'updateImage']);
    });

    Route::group(['prefix'=>'wishlist'], function(){
        Route::get('get', [App\Http\Controllers\Api\WishlistController::class, 'get']);
        Route::post('add', [App\Http\Controllers\Api\WishlistController::class, 'add']);
        Route::post('remove', [App\Http\Controllers\Api\WishlistController::class, 'remove']);
    });

    Route::group(['prefix'=>'cart'], function(){
        Route::get('get', [App\Http\Controllers\Api\CartController::class, 'get']);
        Route::post('add', [App\Http\Controllers\Api\CartController::class, 'add']);
        Route::post('subtract', [App\Http\Controllers\Api\CartController::class, 'subtract']);
        Route::post('remove', [App\Http\Controllers\Api\CartController::class, 'remove']);
    });

    Route::group(['prefix'=>'order'], function(){
        Route::post('place-order', [App\Http\Controllers\Api\OrderController::class, 'placeOrder']);
        Route::get('get', [App\Http\Controllers\Api\OrderController::class, 'get']);
        Route::get('/{order_id}', [App\Http\Controllers\Api\OrderController::class, 'getOrder']);
        Route::post('cancel', [App\Http\Controllers\Api\OrderController::class, 'cancel']);
    });
});

Route::get('payment/success/{order_id}', [App\Http\Controllers\Api\OrderController::class, 'paymentSuccess'])->name('api.order.payment.success');
