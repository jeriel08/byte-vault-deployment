<?php

namespace App\Http\Controllers;

use App\Models\PointOfSale;
use App\Models\Orderline;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PointOfSaleController extends Controller
{
    public function products()
    {
        $categories = PointOfSale::getActiveCategories();
        $brands = PointOfSale::getActiveBrands();
        $products = DB::table('products')
            ->join('brands', 'products.brandID', '=', 'brands.brandID')
            ->join('categories', 'products.categoryID', '=', 'categories.categoryID')
            ->where('products.productStatus', 'Active')
            ->where('products.price', '>', 0)
            ->where('products.stockQuantity', '>', 0)
            ->select('products.productID', 'products.productName', 'products.productDescription', 'products.price', 'products.brandID', 'products.categoryID', 'brands.brandName', 'categories.categoryName', 'products.stockQuantity')
            ->get();

        $employee = Auth::user();

        return view('employee.products', compact('categories', 'brands', 'products', 'employee'));
    }

    public function sales()
    {
        // Fetch total sales (sum of all orders' total)
        $totalSales = Order::sum('total');

        // Fetch sales for today (sum of orders' total for current day)
        $todaySales = Order::whereDate('created_at', today())->sum('total') ?? 0;

        // Fetch orders with related customer and orderline data
        $orders = Order::with(['customer', 'orderlines.product'])
            ->latest('created_at')
            ->paginate(10);

        return view('employee.sales', compact('totalSales', 'todaySales', 'orders'));
    }

    public function storeOrder(Request $request)
    {
        Log::info('Received order data:', $request->all()); // Debug log

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'amount_received' => 'required|numeric|min:0',
            'payment_status' => 'required|in:cash,gcash',
            'gcash_number' => 'nullable|regex:/^09[0-9]{9}$/|required_if:payment_status,gcash',
            'reference_number' => 'nullable|numeric|digits_between:4,20|required_if:payment_status,gcash',
            'items' => 'required|array|min:1',
            'items.*.productID' => 'required|exists:products,productID',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->items as $item) {
                $product = Product::find($item['productID']);
                if (!$product || $product->stockQuantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->productName}");
                }
            }

            $customer = Customer::firstOrCreate(['name' => $request->customer_name]);

            $order = PointOfSale::create([
                'customerID' => $customer->customerID,
                'total_items' => count($request->items),
                'payment_status' => $request->payment_status,
                'gcash_number' => $request->gcash_number,
                'reference_number' => $request->reference_number ?? 'REF-' . strtoupper(uniqid()),
                'amount_received' => $request->amount_received,
                'change' => $request->amount_received - $request->grand_total,
                'total' => $request->grand_total,
                'created_by' => Auth::user()->employeeID,
                'created_at' => now(),
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['productID']);
                if ($product->stockQuantity < $item['quantity']) {
                    throw new \Exception('Insufficient stock for product ID: ' . $item['productID']);
                }

                Orderline::create([
                    'productID' => $item['productID'],
                    'orderID' => $order->orderID,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'created_at' => now(),
                ]);

                $product = Product::find($item['productID']);
                $product->stockQuantity -= $item['quantity'];
                $product->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->orderID,
                'reference_number' => $order->reference_number
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS storeOrder error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to place order: ' . $e->getMessage()], 500);
        }
    }
}
