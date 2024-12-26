<?php

namespace App\Services;

use App\Models\Transaction;
use App\Services\Gatewayes\Tap\Pay;

class Payment
{
    public function createPayment($order)
    {
        // Create transaction
        $transaction = new Transaction();
        $transaction->trx = $order->trx;
        $transaction->user_id = $order->user_id;
        $transaction->order_id = $order->id;
        $transaction->payment_method = $order->payment_method;
        $transaction->amount = $order->order_price + $order->shipping_price;
        $transaction->status = 0;
        $transaction->save();

        // make payment API
        $pay = new Pay();
        $url = $pay->pay($transaction, $order);

        return $url;
    }

    public function checkPayment($transaction, $order)
    {
        $result = (new Pay())->IPN($transaction, $order);
        return $result;
    }
}
