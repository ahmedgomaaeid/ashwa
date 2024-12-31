<?php

namespace App\Services\Gatewayes\Tap;

use App\Models\OrderProduct;
use App\Models\SellerComeOrder;
use App\Models\SellerTransaction;
use App\Models\User;

class Pay
{
    public function pay($transaction ,$order)
    {
        // Tap Payment Gateway Integration
        $secret_key = env('TAP_SECRET_KEY');
        $amount = $transaction->amount;
        $currency = 'SAR';
        $customer_email = $order->user->email;
        $customer_phone = $order->contact_number;
        $order_id = $transaction->trx;
        $redirect_url = route('api.order.payment.success', $order->id);
        $post_url = 'https://api.tap.company/v2/charges';
        $payment_method = ['src_sa.mada', 'src_apple_pay'];
        $description = 'Payment for order #' . $order->id;

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
                'id' => $payment_method[(int)$order->payment_method - 1],

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
        $transaction->indicator = $result->id;
        $transaction->save();
        return $result->transaction->url;

    }

    public function IPN($transaction, $order)
    {
        $secret_key = env('TAP_SECRET_KEY');
        $post_url = 'https://api.tap.company/v2/charges/' . $transaction->indicator;
        $ch = curl_init($post_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $secret_key,
        ]);

        $result = curl_exec($ch);
        $result = json_decode($result);
        if (!isset($result->status)) {
            return 'Error';
        }
        if ($result->status == 'CAPTURED') {
            $transaction->status = 1;
            $transaction->save();
            $order->status = 1;
            $order->save();

            $sellerComeOrders = SellerComeOrder::where('order_id', $order->id)->get();
            $orderProducts = OrderProduct::where('order_id', $order->id)->get();
            foreach ($orderProducts as $orderProduct) {
                $orderProduct->status = 1;
                $orderProduct->save();
                $sellerTransaction = new SellerTransaction();
                $sellerTransaction->order_id = $orderProduct->id;
                $user_discount = User::find($order->user_id)->where('finished_after', '!=', null)->where('finished_after', '>', now())->first('discount');
                $user_discount = $user_discount? $user_discount->discount : env('DEFAULT_DISCOUNT');
                $product_price = $orderProduct->order_price*$orderProduct->quantity + $orderProduct->delivery_fees;
                $sellerTransaction->amount = $product_price - ($product_price * $user_discount / 100);
                $sellerTransaction->user_id = $order->seller_id;
                $sellerTransaction->sign = '+';
                $sellerTransaction->notes = 'Order payment';
                $sellerTransaction->save();
            }

            foreach ($sellerComeOrders as $sellerComeOrder) {
                $sellerComeOrder->status = 1;
                $sellerComeOrder->save();
            }

            return 'success';
        }
        return 'fail';
    }
}
