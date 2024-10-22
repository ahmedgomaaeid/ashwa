<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function get(Request $request)
    {
        $products = Product::where('user_id', auth()->id())->get();
        return response()->json($products);
    }

    public function getProduct(Request $request, $product_id)
    {
        $product = Product::where('id', $product_id)
            ->where('user_id', auth()->id())
            ->with('images')
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function getCategories(Request $request)
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function getSections(Request $request, $category_id)
    {
        $sections = Section::where('category_id', $category_id)->get();
        return response()->json($sections);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'delivery_fees' => 'nullable|numeric',
            'category_id' => 'required|exists:categories,id',
            'section_id' => 'required|exists:sections,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->user_id = auth()->id();
        $product->quantity = $request->quantity;
        $product->delivery_fees = $request->delivery_fees ?? 0;
        $product->category_id = $request->category_id;
        $product->section_id = $request->section_id;
        $product->save();

        // Save product images
        $this->uploadImagesFunction($request, $product->id);

        return response()->json(['message' => 'Product added successfully']);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'delivery_fees' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::where('id', $request->product_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found or unauthorized'], 404);
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->delivery_fees = $request->delivery_fees ?? 0;

        if ($request->category_id) {
            $product->category_id = $request->category_id;
        }
        if ($request->section_id) {
            $product->section_id = $request->section_id;
        }

        $product->save();
        return response()->json(['message' => 'Product updated successfully']);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::where('id', $request->product_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found or unauthorized'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function deleteImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'image_id' => 'required|exists:product_images,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::where('id', $request->product_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found or unauthorized'], 404);
        }

        $product->images()->where('id', $request->image_id)->delete();
        return response()->json(['message' => 'Image deleted successfully']);
    }

    public function uploadImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::where('id', $request->product_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found or unauthorized'], 404);
        }

        $this->uploadImagesFunction($request, $product->id);

        return response()->json(['message' => 'Images uploaded successfully']);
    }

    private function uploadImagesFunction(Request $request, $product_id)
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Generate a unique name for each image
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('products', $imageName, 'public');

                // Create a new ProductImage instance and save the image path
                $productImage = new ProductImage();
                $productImage->product_id = $product_id;
                $productImage->image = $path;
                $productImage->save();
            }
        }
    }
}
