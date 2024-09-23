<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\Request;

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

    public function products($id, $s_id)
    {
        $products = Product::where('category_id', $id)->where('section_id', $s_id)->with('images')->get();
        return response()->json($products);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        //search in products name and description but name appears first
        $products = Product::where('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->with('images')
            ->get();
        return response()->json($products);
    }
    public function product_detail($id, $s_id, $product_id)
    {
        $product = Product::find($product_id)->load('images');
        return response()->json($product);
    }
    public function offers()
    {
        $offers = Offer::all();
        return response()->json($offers);
    }
}
