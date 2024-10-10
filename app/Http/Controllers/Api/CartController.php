<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function get()
    {
        $user = auth()->user();

        // Eager load 'product' and 'product.images' to prevent N+1 query problem
        $cartItems = $user->carts()->with('product.images')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['cart_items' => [], 'delivery' => 0, 'total' => 0]);
        }

        $total = 0;
        $delivery = 0;

        // Use collection methods for cleaner code
        $cartData = $cartItems->map(function ($cart) use (&$total, &$delivery) {
            $product = $cart->product;
            $quantity = $cart->quantity;
            $unitPrice = $product->price;
            $productDeliveryFee = $product->delivery_fees;

            $total += $unitPrice * $quantity;
            $delivery += $productDeliveryFee;

            return [
                'id'          => $cart->id,
                'product_id'  => $product->id,
                'name'        => $product->name,
                'image'       => $product->images->first(),
                'quantity'    => $quantity,
                'unit_price'  => $unitPrice,
            ];
        });

        $total += $delivery;

        return response()->json([
            'cart_items' => $cartData,
            'delivery'   => $delivery,
            'total'      => $total,
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        if(!$user)
        {
            return response()->json(['message' => 'User not found']);
        }
        // Use 'updateOrCreate' for cleaner logic
        $cart = $user->carts()->updateOrCreate(
            ['product_id' => $validated['product_id']],
            ['quantity'   => $validated['quantity']]
        );

        return response()->json(['cart' => $cart]);
    }

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = auth()->user();

        // Use 'delete' directly on the query
        $user->carts()->where('product_id', $validated['product_id'])->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}
