<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }
    public function products($id)
    {
        $category = Category::find($id);
        // get all products of this category with their images
        $products = $category->products()->with('images')->get();
        
        return response()->json([
            'category_name' => $category->name,
            'products' => $products
        ]);
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
}
