<?php

namespace App\Http\Controllers;

use App\Models\SupplierOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\SupplierOrderDetail;
use Illuminate\Http\Request;

class SupplierOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SupplierOrder::query();

        // Search by supplierOrderID (case-insensitive)
        if ($request->has('search') && !empty($request->input('search'))) {
            $query->whereRaw('LOWER(supplierOrderID) LIKE ?', ['%' . strtolower($request->input('search')) . '%']);
        }

        // Filter by status (using date fields)
        if ($request->has('status')) {
            switch ($request->status) {
                case 'Pending':
                    $query->whereNotNull('orderPlacedDate')
                        ->whereNull('receivedDate')
                        ->whereNull('cancelledDate');
                    break;
                case 'Received':
                    $query->whereNotNull('receivedDate');
                    break;
                case 'Cancelled':
                    $query->whereNotNull('cancelledDate')
                        ->whereNull('receivedDate');
                    break;
            }
        }

        // Filter by supplier
        if ($request->has('supplier_id')) {
            $query->where('supplierID', $request->supplier_id);
        }

        // Filter by date range (using orderDate)
        if ($request->has('date_from')) {
            $query->where('orderDate', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('orderDate', '<=', $request->date_to);
        }

        // Sort by supplierOrderID (ascending)
        $query->orderBy('supplierOrderID', 'desc');

        // Paginate results (5 per page) and append query params
        $supplierOrders = $query->paginate(5)->appends($request->query());

        // Calculate total counts for each status (unfiltered)
        $pendingCount = SupplierOrder::whereNotNull('orderPlacedDate')
            ->whereNull('receivedDate')
            ->whereNull('cancelledDate')
            ->count();
        $receivedCount = SupplierOrder::whereNotNull('receivedDate')->count();
        $cancelledCount = SupplierOrder::whereNotNull('cancelledDate')
            ->whereNull('receivedDate')
            ->count();

        $suppliers = Supplier::all();

        return view('admin.supplier_orders.index', compact('supplierOrders', 'suppliers', 'pendingCount', 'receivedCount', 'cancelledCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $suppliers = Supplier::where('supplierStatus', 'Active')->get();
        $products = Product::where('productStatus', 'Active')->get();
        $reorderOrder = null;

        if ($request->has('reorder')) {
            $reorderOrder = SupplierOrder::with('details.product')->findOrFail($request->reorder);
        }

        return view('admin.supplier_orders.create', compact('suppliers', 'products', 'reorderOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplierID' => 'required|exists:suppliers,supplierID',
            'orderDate' => 'required|date',
            'expectedDeliveryDate' => 'nullable|date|after_or_equal:orderDate',
            'details' => 'required|array',
            'details.*.productID' => 'required|exists:products,productID',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.unitCost' => 'required|numeric|min:0',
        ]);

        $totalCost = collect($request->details)->sum(function ($detail) {
            return $detail['quantity'] * $detail['unitCost'];
        });

        $supplierOrder = SupplierOrder::create([
            'supplierID' => $request->supplierID,
            'orderDate' => $request->orderDate,
            'expectedDeliveryDate' => $request->expectedDeliveryDate,
            'totalCost' => $totalCost,
            'orderPlacedDate' => now(), // Set when order is placed
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        foreach ($request->details as $detail) {
            SupplierOrderDetail::create([
                'supplierOrderID' => $supplierOrder->supplierOrderID,
                'productID' => $detail['productID'],
                'quantity' => $detail['quantity'],
                'unitCost' => $detail['unitCost'],
                'receivedQuantity' => 0, // Initially 0, updated when received
            ]);
        }

        return redirect()->route('supplier_orders.index')->with('success', 'Supplier order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierOrder $supplierOrder)
    {
        //
        $supplierOrder->load('supplier', 'details.product');
        return view('admin.supplier_orders.show', compact('supplierOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierOrder $supplierOrder)
    {
        //
        $suppliers = Supplier::where('supplierStatus', 'Active')->get();
        $products = Product::where('productStatus', 'Active')->get();
        $supplierOrder->load('details.product', 'supplier');
        return view('admin.supplier_orders.edit', compact('supplierOrder', 'suppliers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $supplierOrderID)
    {
        $supplierOrder = SupplierOrder::findOrFail($supplierOrderID);

        // Handle "Receive" action
        if ($request->has('markAsReceived')) {
            if ($supplierOrder->receivedDate || $supplierOrder->cancelledDate) {
                return redirect()->route('supplier_orders.index')->with('error', 'This order has already been received or cancelled.');
            }

            $supplierOrder->update([
                'receivedDate' => now(),
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            // Update product stock and price (no partial receiving)
            foreach ($supplierOrder->details as $detail) {
                $product = Product::find($detail->productID);
                if ($product) {
                    $product->update([
                        'stockQuantity' => $product->stockQuantity + $detail->quantity,
                        'price' => $detail->unitCost,
                    ]);
                }
            }

            return redirect()->route('supplier_orders.index')->with('success', 'Supplier order marked as received, stock and prices updated.');
        }

        // Handle "Cancel" action with remark
        if ($request->has('markAsCancelled')) {
            if ($supplierOrder->receivedDate || $supplierOrder->cancelledDate) {
                return redirect()->route('supplier_orders.index')->with('error', 'This order has already been received or cancelled.');
            }

            $request->validate([
                'cancellationRemark' => 'required|string|max:255',
            ]);

            $supplierOrder->update([
                'cancelledDate' => now(),
                'cancellationRemark' => $request->cancellationRemark,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            return redirect()->route('supplier_orders.index')->with('success', 'Supplier order marked as cancelled with remark.');
        }

        // Handle regular updates from edit.blade.php
        $validated = $request->validate([
            'supplierID' => 'required|exists:suppliers,supplierID',
            'orderDate' => 'required|date',
            'expectedDeliveryDate' => 'nullable|date|after_or_equal:orderDate',
            'details' => 'required|array',
            'details.*.supplierOrderDetailID' => 'sometimes|exists:supplier_order_details,supplierOrderDetailID',
            'details.*.productID' => 'required|exists:products,productID',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.unitCost' => 'required|numeric|min:0',
        ]);

        $totalCost = collect($request->details)->sum(function ($detail) {
            return $detail['quantity'] * $detail['unitCost'];
        });

        $supplierOrder->update([
            'supplierID' => $request->supplierID,
            'orderDate' => $request->orderDate,
            'expectedDeliveryDate' => $request->expectedDeliveryDate,
            'totalCost' => $totalCost,
            'updated_by' => auth()->id(),
            'updated_at' => now(),
        ]);

        // Sync order details
        $existingDetailIds = $supplierOrder->details->pluck('supplierOrderDetailID')->toArray();
        $submittedDetailIds = collect($request->details)->pluck('supplierOrderDetailID')->filter()->toArray();

        SupplierOrderDetail::where('supplierOrderID', $supplierOrder->supplierOrderID)
            ->whereNotIn('supplierOrderDetailID', $submittedDetailIds)
            ->delete();

        foreach ($request->details as $detail) {
            SupplierOrderDetail::updateOrCreate(
                ['supplierOrderDetailID' => $detail['supplierOrderDetailID'] ?? null],
                [
                    'supplierOrderID' => $supplierOrder->supplierOrderID,
                    'productID' => $detail['productID'],
                    'quantity' => $detail['quantity'],
                    'unitCost' => $detail['unitCost'],
                ]
            );
        }

        return redirect()->route('supplier_orders.index')->with('success', 'Supplier order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierOrder $supplierOrder)
    {
        //
    }
}
