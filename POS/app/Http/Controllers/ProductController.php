<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of products for the authenticated admin.
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id; // Get the authenticated admin's ID
        $products = Product::with('category')
            ->where('admin_id', $adminId)
            ->get();

        return response()->json($products, 200);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_code' => 'required|string|max:50|unique:products',
            'brand' => 'required|string|max:50',
            'name' => 'required|string|max:50',
            'size' => 'sometimes|required|string|max:50',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::create(array_merge(
            $validatedData,
            ['admin_id' => $request->user()->id] // Associate product with the admin
        ));

        Log::info('Product created:', $product->toArray());

        return response()->json(['response' => 'success', 'product' => $product], 201);
    }

    /**
     * Display a specific product if owned by the authenticated admin.
     */
    public function show(Request $request, $id)
    {
        $product = Product::where('id', $id)
            ->where('admin_id', $request->user()->id) // Restrict to admin's products
            ->with('category')
            ->first();

        if (!$product) {
            return response()->json(['response' => 'No records found or unauthorized!'], 403);
        }

        return response()->json($product, 200);
    }

    /**
     * Update a product if owned by the authenticated admin.
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('id', $id)
            ->where('admin_id', $request->user()->id) // Restrict to admin's products
            ->first();

        if (!$product) {
            return response()->json(['response' => 'No records found or unauthorized!'], 403);
        }

        $validatedData = $request->validate([
            'product_code' => 'required|string|max:50|unique:products,product_code,' . $id,
            'brand' => 'sometimes|required|string|max:50',
            'name' => 'sometimes|required|string|max:50',
            'size' => 'sometimes|required|string|max:50',
            'price' => 'sometimes|required|numeric|min:0',
            'qty' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        $product->update($validatedData);

        return response()->json(['response' => 'success', 'product' => $product], 200);
    }

    /**
     * Delete a product if owned by the authenticated admin.
     */
    public function destroy(Request $request, $id)
    {
        $product = Product::where('id', $id)
            ->where('admin_id', $request->user()->id) // Restrict to admin's products
            ->first();

        if (!$product) {
            return response()->json(['response' => 'No records found or unauthorized!'], 403);
        }

        $product->delete();

        return response()->json(['response' => 'success'], 200);
    }
}
