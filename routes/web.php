<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/purchase/{id}', function ($id) {
    if($id == 1){
        $price = 7000;
    }elseif($id == 2){
        $price = 10000;
    }elseif($id == 3){
        $price = 7000;
    }else{
        return 'Invalid Product';
    }

    $secret_key = env('TAP_SECRET_KEY');
        $amount = $price;
        $currency = 'SAR';
        $customer_email = "ahmed@gmail.com";
        $customer_phone = "01001659186";
        $order_id = $id;
        $redirect_url = route('api.order.payment.success', $id);
        $post_url = 'https://api.tap.company/v2/charges';
        $payment_method = ['src_sa.mada', 'src_apple_pay'];
        $description = 'Payment for order #' . $id;

        $data = [
            'amount' => $amount,
            'currency' => $currency,
            'customer' => [
                'email' => $customer_email,
                'phone' => [
                    'country_code' => '966',
                    'number' => $customer_phone,
                ],
            ],
            'source' => [
                'id' => $payment_method[0],

            ],
            'redirect' => [
                'url' => $redirect_url,
            ],
            'reference' => [
                'order' => $order_id,
            ],
            'description' => $description,
        ];

        $data_string = json_encode($data);

        $ch = curl_init($post_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $secret_key,
        ]);

        $result = curl_exec($ch);
        $result = json_decode($result);

        if (!isset($result->status) or $result->status != "INITIATED") {
            return $result->message ?? 'Error';
        }
        return redirect($result->transaction->url);
});
Route::group(['prefix' => 'admin', 'middleware'=> [''], 'as'=> 'amdin', 'namespace'=> 'Admin'], function () {
    Route::group(['prefix' => 'category'], function () {
        Route::get('/', 'CategoryController@index')->name('category.index');
        Route::get('/create', 'CategoryController@create')->name('category.create');
        Route::post('/store', 'CategoryController@store')->name('category.store');
        Route::get('/edit/{id}', 'CategoryController@edit')->name('category.edit');
        Route::post('/update/{id}', 'CategoryController@update')->name('category.update');
        Route::get('/delete/{id}', 'CategoryController@delete')->name('category.delete');
    });
});
// Route::middleware(['auth:admin', 'admin'])->group(function () {
//     Route::get('/admin/dashboard', function () {
//         return view('admin.dashboard');
//     });
// });
