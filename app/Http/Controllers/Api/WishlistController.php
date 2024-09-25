<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function get(Request $request)
    {
        $wishlist = $request->user()->wishlists->load('product');
        return response()->json($wishlist);
    }
    public function add(Request $request)
    {
        $product_id = $request->product_id;
        $user_id = $request->user()->id;
        $wishlist = Wishlist::where('user_id', $user_id)->where('product_id', $product_id)->first();
        if ($wishlist) {
            return response()->json(['message' => 'Product already in wishlist']);
        }
        $wishlist = new Wishlist();
        $wishlist->user_id = $user_id;
        $wishlist->product_id = $product_id;
        $wishlist->save();
        return response()->json(['message' => 'Product added to wishlist']);
    }
    public function remove(Request $request)
    {
        $product_id = $request->product_id;
        $user_id = $request->user()->id;
        $wishlist = Wishlist::where('user_id', $user_id)->where('product_id', $product_id)->first();
        if (!$wishlist) {
            return response()->json(['message' => 'Product not in wishlist']);
        }
        $wishlist->delete();
        return response()->json(['message' => 'Product removed from wishlist']);
    }
}
