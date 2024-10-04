<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function get()
    {
        $user = auth()->user();
        $total = 0;
        $delery = 20;
        $cart_data = $user->carts->load('product');
        if($cart_data->count() == 0){
            return response()->json(['cart_items' => [], 'delevery' => 0, 'total' => 0]);
        }
        $total += $delery;

        $carts = $cart_data->map(function ($cart) use (&$total) {
            $total += 30 * $cart->quantity;
            return [
                'id' => $cart->id,
                'product_id' => $cart->product_id,
                'name' => $cart->product->name,
                'image' => $cart->product->images->first(),
                'quantity' => $cart->quantity,
                'unit_price' => 30
            ];
        });
        return response()->json(['cart_items' => $carts, 'delevery' => $delery, 'total' => $total]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = auth()->user();
        $cart = $user->carts()->where('product_id', $request->product_id)->first();
        if ($cart) {
            $cart->quantity = $request->quantity;
            $cart->save();
        } else {
            $cart = $user->carts()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['cart' => $cart]);
    }
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = auth()->user();
        $cart = $user->carts()->where('product_id', $request->product_id)->first();
        if ($cart) {
            $cart->delete();
        }

        return response()->json(['message' => 'Item removed from cart']);
    }
}
