<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderProduct;
use App\Models\SellerComeOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function get()
    {
        $user = request()->user();
        $orders = SellerComeOrder::where('seller_id', $user->id)->with('orderProducts')->where('status', '>=', 1)->orderBy('status', 'asc')->get();
        return response()->json(['orders' => $orders]);
    }
    public function updateOrderStatus(Request $request)
    {
        $user = $request->user();
        $orderId = $request->order_id;
        $order = SellerComeOrder::where('seller_id', $user->id)->where('id', $orderId)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->status = $request->input('status');
        $product = OrderProduct::where('order_id', $order->order_id)->first();
        $product->status = $request->input('status');
        $product->save();

        $order->save();


        return response()->json(['message' => 'Order status updated successfully']);
    }
}
