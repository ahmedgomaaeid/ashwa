<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\SellerComeOrder;
use App\Models\SellerTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment;
use Illuminate\Http\Request;
use Log;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->get();
        if($cart->count() == 0) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }
        $total = 0;
        $delivery = 0;

        $status_value = ($request->payment_method == 0) ? 1 : 0;

        foreach($cart as $item) {
            $total += $item->product->price * $item->quantity;
            $delivery += $item->product->delivery_fees;
        }

        $order = $user->orders()->create([
            'status' => $status_value,
            'order_price' => $total,
            'shipping_price' => $delivery,
            'shipping_address' => $request->shipping_address,
            'contact_number' => $request->contact_number,
            'note' => $request->note,
            'payment_method' => $request->payment_method,
        ]);

        foreach($cart as $item) {
            $order_product = new OrderProduct();
            $order_product->order_id = $order->id;
            $order_product->product_id = $item->product_id;
            $order_product->quantity = $item->quantity;
            $order_product->price = $item->product->price;
            $order_product->delivery_fees = $item->product->delivery_fees;
            $order_product->status = $status_value;
            $order_product->save();

            $sellerComeOrder = new SellerComeOrder();
            $sellerComeOrder->user_id = $user->id;
            $sellerComeOrder->order_id = $order->id;
            $sellerComeOrder->seller_id = $item->product->user_id;
            $sellerComeOrder->amount = $item->product->price * $item->quantity;
            $sellerComeOrder->shipping_price = $item->product->delivery_fees;
            $sellerComeOrder->payment_method = $request->payment_method;
            $sellerComeOrder->status = $status_value;
            $sellerComeOrder->save();
            if($order->payment_method == 0) {
                $transaction = new SellerTransaction();
                $transaction->order_id = $order->id;
                $transaction->user_id = $item->product->user_id;
                $user_discount = User::find($user->id)->where('finished_after', '!=', null)->where('finished_after', '>', now())->first('discount');
                $user_discount = $user_discount? $user_discount->discount : env('DEFAULT_DISCOUNT');
                $product_price = ($item->product->price * $item->quantity) + $item->product->delivery_fees;
                $transaction->amount = $product_price * $user_discount / 100;
                $transaction->sign = "-";
                $transaction->notes = "order pay when receive fees";
                $transaction->save();
            }
        }

        Cart::where('user_id', $user->id)->delete();
        if($request->payment_method != 0) {
            $payment_url = (new Payment)->createPayment($order);
            return response()->json(['payment_url' => $payment_url], 201);
        }
        return response()->json(['message' => 'Order placed successfully'], 201);
    }

    public function paymentSuccess($order_id)
    {
        $transaction = Transaction::where('indicator', $_GET['tap_id'])->first();
        $order = Order::where('id', $order_id)->first();
        if(!$transaction or !$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        if($transaction->status > 0) {
            return response()->json(['message' => 'Order already paid'], 400);
        }
        if($transaction->order_id != $order->id) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $result = (new Payment)->checkPayment($transaction, $order);

        if($result == 'success')
        {
            return response()->json(['message' => 'Order paid successfully'], 200);
        }else{
            return response()->json(['message' => 'Payment failed'], 400);
        }
    }

    public function get()
    {
        $user = request()->user();
        $orders = Order::where('user_id', $user->id)->where('status','>', 0)->orderBy('id', 'desc')->get();
        return response()->json($orders);
    }

    public function getOrder($order_id)
    {
        $user = request()->user();
        $order = Order::where('user_id', $user->id)->where('id', $order_id)->with('products.product')->first();
        if(!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order);
    }

    public function cancel(Request $request)
    {
        $order_id = $request->order_id;
        $user = request()->user();
        $order = Order::where('user_id', $user->id)->where('id', $order_id)->first();
        if(!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        if($order->status != 1 or $order->created_at->diffInMinutes(now()) > 10) {
            return response()->json(['message' => 'Order can not be cancelled'], 400);
        }
        $order->status = 4;
        $order->save();

        $sellerComeOrders = SellerComeOrder::where('order_id', $order->id)->get();
        $orderProducts = OrderProduct::where('order_id', $order->id)->get();

        // create transaction for each seller
        foreach($orderProducts as $orderProduct) {
            $orderProduct->status = 4;
            $orderProduct->save();
            if($order->payment_method == "0") {
                $sellerTransaction = new SellerTransaction();
                $sellerTransaction->order_id = $orderProduct->id;
                $user_discount = User::find($order->user_id)->where('finished_after', '!=', null)->where('finished_after', '>', now())->first('discount');
                $user_discount = $user_discount? $user_discount->discount : env('DEFAULT_DISCOUNT');
                $product_price = ($orderProduct->price*$orderProduct->quantity) + $orderProduct->delivery_fees;
                $sellerTransaction->amount = $product_price * $user_discount / 100;
                $sellerTransaction->user_id = $order->user_id;
                $sellerTransaction->sign = '+';
                $sellerTransaction->notes = 'Order cancelled';
                $sellerTransaction->save();
            }else
            {
                $sellerTransaction = new SellerTransaction();
                $sellerTransaction->order_id = $orderProduct->id;
                $user_discount = User::find($order->user_id)->where('finished_after', '!=', null)->where('finished_after', '>', now())->first('discount');
                $user_discount = $user_discount? $user_discount->discount : env('DEFAULT_DISCOUNT');
                $product_price = ($orderProduct->price*$orderProduct->quantity) + $orderProduct->delivery_fees;
                $sellerTransaction->amount = $product_price - ($product_price * $user_discount / 100);
                $sellerTransaction->user_id = $order->user_id;
                $sellerTransaction->sign = '-';
                $sellerTransaction->notes = 'Order cancelled';
                $sellerTransaction->save();
            }
        }
        foreach($sellerComeOrders as $sellerComeOrder) {
            $sellerComeOrder->status = 4;
            $sellerComeOrder->save();
        }
        return response()->json(['message' => 'Order cancelled successfully'], 200);
    }

}
