<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function get()
    {
        $total = 0;
        $delery = 20;
        $total += $delery;
        $user = auth()->user();
        $carts = $user->carts->load('product')->map(function ($cart) use (&$total) {
            $total += $cart->price * $cart->quantity;
            return [
                'id' => $cart->id,
                'product_id' => $cart->product_id,
                'name' => $cart->product->name,
                'image' => $cart->product->images->first(),
                'price' => $cart->price,
                'quantity' => $cart->quantity
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
                'quantity' => $request->quantity,
                'price' => $request->price
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
