<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProductsController extends Controller
{
    private function formatProductImage($product)
    {
        if ($product->image && !str_contains($product->image, 'tmp')) {
            $product->image = asset('storage/' . $product->image);
        } else {
            $product->image = asset('images/img1.jpeg');
        }
        return $product;
    }

    public function index()
    {
        $products = Product::orderBy('name')->get();
        $products->transform(fn($p) => $this->formatProductImage($p));
        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($this->formatProductImage($product));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'stock' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:active,inactive,low stock,out of stock,draft',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Handle image upload separately
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Remove the file from validated data
        unset($data['image']);

        // Add the path to data
        if ($imagePath) {
            $data['image'] = $imagePath;
        }

        $product = Product::create($data);

        return response()->json($this->formatProductImage($product), 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'sku' => 'sometimes|nullable|string|max:100',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|nullable|string|max:100',
            'stock' => 'sometimes|nullable|integer|min:0',
            'status' => 'sometimes|string|in:active,inactive,low stock,out of stock,draft',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Handle image upload separately
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        unset($data['image']);

        if ($imagePath) {
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return response()->json($this->formatProductImage($product));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}