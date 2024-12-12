<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $authUser = $request->user();

        // Check if the user is an admin or associated with an admin
        $order = Order::where('admin_id', $authUser->admin_id ?? $authUser->id)->get();

        return response()->json($order, 200);
    }
    public function scanProduct(Request $request)
    {
        $validatedData = $request->validate([
            'product_code' => 'required|string|exists:products,product_code',
        ]);

        $product = Product::where('product_code', $validatedData['product_code'])->first();

        if (!$product || $product->qty <= 0) {
            return response()->json(['response' => 'Product not available or out of stock'], 404);
        }

        $order = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'qty' => 1,
            'subtotal' => $product->price,
        ];

        return response()->json(['order' => $order], 200);
    }

    /**
     * Make a payment for the order.
     */
    public function pay(Request $request)
    {
        $validatedData = $request->validate([
            'cash' => 'required|numeric|min:0',
            'orders' => 'required|array',
            'orders.*.product_id' => 'required|exists:products,id',
            'orders.*.qty' => 'required|numeric|min:1',
        ]);

        $orders = $validatedData['orders'];
        $total = collect($orders)->sum(fn($order) => $order['qty'] * Product::find($order['product_id'])->price);

        if ($validatedData['cash'] < $total) {
            return response()->json(['response' => 'Insufficient cash'], 400);
        }

        foreach ($orders as $orderData) {
            $product = Product::find($orderData['product_id']);

            if ($product->qty < $orderData['qty']) {
                return response()->json(['response' => "Insufficient stock for {$product->name}"], 400);
            }

            Order::create([
                'product_id' => $orderData['product_id'],
                'qty' => $orderData['qty'],
                'cash' => $orderData['qty'] * $product->price,
                'admin_id' => $request->user()->id, // Associate with the logged-in admin
            ]);

            $product->decrement('qty', $orderData['qty']);
        }

        $change = $validatedData['cash'] - $total;

        return response()->json(['response' => 'Payment successful', 'change' => $change], 200);
    }

    /**
     * Update an order.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|numeric|min:1',
        ]);

        $order = Order::where('id', $id)
            ->where('admin_id', $request->user()->id) // Restrict to orders associated with the admin
            ->first();

        if (!$order) {
            return response()->json(['response' => 'Order not found or unauthorized!'], 404);
        }

        $product = Product::find($validatedData['product_id']);

        if ($product->qty < $validatedData['qty']) {
            return response()->json(['response' => "Insufficient stock for {$product->name}"], 400);
        }

        $order->update([
            'product_id' => $validatedData['product_id'],
            'qty' => $validatedData['qty'],
            'cash' => $validatedData['qty'] * $product->price,
        ]);

        // Update product stock
        $product->increment('qty', $order->qty - $validatedData['qty']);

        return response()->json(['response' => 'Order updated successfully', 'order' => $order], 200);
    }

    /**
     * Delete an order.
     */
    public function destroy(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('admin_id', $request->user()->id) // Restrict to orders associated with the admin
            ->first();

        if (!$order) {
            return response()->json(['response' => 'Order not found or unauthorized!'], 404);
        }

        $product = Product::find($order->product_id);
        $product->increment('qty', $order->qty); // Restore the stock

        $order->delete();

        return response()->json(['response' => 'Order deleted successfully'], 200);
    }
}
