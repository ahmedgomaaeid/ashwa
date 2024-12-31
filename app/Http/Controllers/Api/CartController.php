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
            'delivery'   => number_format($delivery, 2),
            'total'      => number_format($total, 2),
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = auth()->user();
        if(!$user)
        {
            return response()->json(['message' => 'User not found']);
        }
        // Check if the product is already in the cart
        $cart = $user->carts()->where('product_id', $validated['product_id'])->first();
        if($cart)
        {
            $cart->quantity += 1;
            $cart->save();
        }
        else
        {
            $user->carts()->create($validated);
        }

    return response()->json(['message' => 'Item added to cart']);
    }

    public function subtract(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $user = auth()->user();

        $cart = $user->carts()->where('product_id', $validated['product_id'])->first();
        if(!$cart)
        {
            return response()->json(['message' => 'Item not found in cart']);
        }
        if($cart->quantity > 1)
        {
            $cart->quantity -= 1;
            $cart->save();
        }
        else
        {
            $cart->delete();
        }

        return response()->json(['message' => 'Item quantity subtracted']);
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
