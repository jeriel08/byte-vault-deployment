<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer; // Add this line
use Illuminate\Http\Request;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $productId = $request->input('product_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $sortBy = $request->input('sort_by', 'date_desc');

        $orders = Order::with('product', 'customer')
            ->when($search, function ($query, $search) {
                return $query->where('orderID', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($productId, function ($query, $productId) {
                return $query->where('product_id', $productId);
            })
            ->when($dateFrom, function ($query, $dateFrom) {
                return $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, $dateTo) {
                return $query->whereDate('created_at', '<=', $dateTo);
            })
            ->when($sortBy, function ($query, $sortBy) {
                if ($sortBy === 'date_asc') {
                    return $query->orderBy('created_at', 'asc');
                } elseif ($sortBy === 'amount_desc') {
                    return $query->orderBy('amount', 'desc');
                } elseif ($sortBy === 'amount_asc') {
                    return $query->orderBy('amount', 'asc');
                } else {
                    return $query->orderBy('created_at', 'desc');
                }
            })
            ->paginate(5);

        return view('admin.orders.index', compact('orders', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Placeholder for POS system redirect or logic
        return view('admin.orders.create');
    }

    public function store(Request $request)
    {
        // Comment out until POS system is ready
        /*
    $validated = $request->validate([
        'customerID' => 'required|exists:customers,customerID',
        'product_id' => 'required|exists:products,productID',
        'quantity' => 'required|integer|min:1',
        'amount' => 'required|numeric|min:0',
        'payment_status' => 'required|in:Pending,Paid',
        'status' => 'required|in:Pending,Delivered,Cancelled',
    ]);

    $validated['order_id'] = '#' . str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
    Order::create($validated);

    return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    */
        return redirect()->route('orders.index')->with('message', 'POS system not yet implemented.');
    }

    /**
     * Display the specified resource.
     */
    // OrderController.txt
    public function show(Order $order)
    {
        $order->load('customer', 'orderlines.product'); // Eager-load customer and orderlines with products
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $products = Product::all();
        return view('admin.orders.edit', compact('order', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'product_id' => 'required|exists:products,productID', // Changed 'id' to 'productID'
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'payment_status' => 'required|string',
            'status' => 'required|string',
        ]);

        $order->update($validated);

        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
