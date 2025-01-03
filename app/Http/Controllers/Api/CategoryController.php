<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Wishlist;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function sections($id)
    {
        $category = Category::find($id);
        $sections = $category->sections;
        return response()->json([
            'category_name' => $category->name,
            'sections' => $sections
        ]);
    }

    public function products($s_id)
    {
        $products = Product::where('status', 1)->where('section_id', $s_id)->with('images')->get();
        return response()->json($products);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        if ($search == '' || $search == " ") {
            return response()->json([]);
        }
        //search in products name and description but name appears first
        $products = Product::where('status', 1)->where('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->with('images')
            ->get();
        return response()->json($products);
    }
    public function product_detail(Request $request, $product_id)
    {
        // check if bearer token is sent in header and user is authenticated
        // if ($request->bearerToken() == null ) {
        //     $product = Product::where('status', 1)->find($product_id)->load('images');
        // } else {
        //     // check if product is in user's wishlist
        //     $product = Product::where('status', 1)->find($product_id)->load('images');
        //     Wishlist::where('user_id', $request->user()->id)->where('product_id', $product_id)->exists() ? $product->is_wishlist = true : $product->is_wishlist = false;

        // }
        if($request->bearerToken() != null)
        {
            // check middle ware jwt.verify
            try{
                $user = JWTAuth::parseToken()->authenticate();
                $product = Product::where('status', 1)->find($product_id)->load('images');
            Wishlist::where('user_id', $user->id)->where('product_id', $product_id)->exists() ? $product->is_wishlist = true : $product->is_wishlist = false;
            }catch(Exception $e){
                $product = Product::where('status', 1)->find($product_id)->load('images');
            }
        }else
        {
            $product = Product::where('status', 1)->find($product_id)->load('images');
        }

        return response()->json($product);
    }
    public function offers()
    {
        $offers = Offer::all();
        return response()->json($offers);
    }

    public function homepage(Request $request)
    {
        $num_of_categories = $request->num_of_categories ?? 1;
        $num_of_category_products = $request->num_of_category_products ?? 1;

        $categories = Category::get();
        $categories = $categories->map(function ($category) use ($num_of_category_products) {
            $category->products = $category->products()
                ->with('firstImage')
                ->inRandomOrder()
                ->take($num_of_category_products)
                ->get();
            return $category;
        })->take($num_of_categories);

        return response()->json($categories);
    }
}
