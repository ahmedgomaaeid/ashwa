<?php

namespace App\Services\Gatewayes\Tap;

use App\Models\SellerComeOrder;

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
        dd($result);

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
            foreach ($sellerComeOrders as $sellerComeOrder) {
                $sellerComeOrder->status = 1;
                $sellerComeOrder->save();
            }

            return 'success';
        }
        return 'fail';
    }
}
