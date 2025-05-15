<?php

namespace App\Http\Controllers;

use App\Models\ReturnToSupplier;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\SupplierOrder;
use App\Models\StockOut;
use App\Models\StockOutDetail;
use Illuminate\Http\Request;

class ReturnToSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ReturnToSupplier::with('supplier', 'stockOut', 'creator');

        // Status Filter
        if ($request->has('status')) {
            switch ($request->input('status')) {
                case 'Pending':
                    $query->whereNotNull('adjustmentDatePlaced')
                        ->whereNull('completionDate')
                        ->whereNull('cancellationDate');
                    break;
                case 'Completed':
                    $query->whereNotNull('completionDate');
                    break;
                case 'Rejected':
                    $query->whereNotNull('cancellationDate');
                    break;
            }
        }

        // Supplier Filter
        if ($request->has('supplierID') && $request->input('supplierID') !== '') {
            $supplierID = $request->input('supplierID');
            if (Supplier::where('supplierID', $supplierID)->exists()) {
                $query->where('supplierID', $supplierID);
            }
        }

        // Date Range Filter
        if ($request->has('date_from')) {
            $query->where('adjustmentDatePlaced', '>=', $request->input('date_from'));
        }
        if ($request->has('date_to')) {
            $query->where('adjustmentDatePlaced', '<=', $request->input('date_to'));
        }

        // Sorting
        if ($request->has('sort_by')) {
            $direction = $request->input('sort_direction', 'asc');
            $query->orderBy($request->input('sort_by'), $direction);
        } else {
            $query->orderBy('returnSupplierID', 'desc');
        }

        // Paginate results
        $returns = $query->paginate(5)->appends($request->query());

        // Status Counts
        $statusCounts = [
            'Pending' => ReturnToSupplier::whereNotNull('adjustmentDatePlaced')
                ->whereNull('completionDate')
                ->whereNull('cancellationDate')
                ->count(),
            'Completed' => ReturnToSupplier::whereNotNull('completionDate')->count(),
            'Rejected' => ReturnToSupplier::whereNotNull('cancellationDate')->count(),
        ];

        $suppliers = Supplier::all();

        return view('admin.supplier_returns.index', compact('returns', 'statusCounts', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $orders = SupplierOrder::with(['supplier', 'details.product'])
            ->whereNotNull('receivedDate')
            ->whereNull('cancelledDate')
            ->get();
        $products = Product::where('productStatus', 'Active')->get();
        $order = $request->has('order') ? SupplierOrder::with('details.product')->findOrFail($request->order) : null;

        if ($order && !$order->receivedDate) {
            return redirect()->route('supplier_returns.index')->with('error', 'Selected order has not been received.');
        }

        // Ensure relationships are included in JSON
        $orders->each(function ($order) {
            $order->setRelation('details', $order->details->load('product'));
        });

        return view('admin.supplier_returns.create', compact('orders', 'products', 'order'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplierOrderID' => 'required|exists:supplier_orders,supplierOrderID',
            'returnDate' => 'required|date',
            'returnSupplierReason' => 'required|string|max:255',
            'details' => 'present|array', // Ensures details array is sent, even if empty
            'details.*.productID' => 'required|exists:products,productID',
            'details.*.quantity' => 'nullable|integer|min:0', // Allows 0 or null
        ]);

        $order = SupplierOrder::findOrFail($request->supplierOrderID);
        $details = array_filter($request->details, fn($detail) => !empty($detail['quantity']) && $detail['quantity'] > 0);
        if (empty($details)) {
            return back()->withErrors(['details' => 'At least one product with a quantity greater than 0 is required.']);
        }

        $totalQuantity = collect($details)->sum('quantity');
        $return = ReturnToSupplier::create([
            'supplierID' => $order->supplierID,
            'supplierOrderID' => $request->supplierOrderID,
            'returnDate' => $request->returnDate,
            'returnSupplierReason' => $request->returnSupplierReason,
            'adjustmentDatePlaced' => now(),
            'created_by' => auth()->id(),
        ]);

        $stockOut = StockOut::create([
            'reasonType' => 'return_to_supplier',
            'referenceID' => $return->returnSupplierID,
            'referenceTable' => 'return_to_suppliers',
            'totalQuantity' => $totalQuantity,
            'created_by' => auth()->id(),
        ]);

        foreach ($details as $detail) {
            StockOutDetail::create([
                'stockOutID' => $stockOut->stockOutID,
                'productID' => $detail['productID'],
                'quantity' => $detail['quantity'],
            ]);
        }

        return redirect()->route('supplier_returns.index')->with('success', 'Return recorded.');
    }

    public function complete(Request $request, $returnSupplierID)
    {
        $return = ReturnToSupplier::findOrFail($returnSupplierID);
        if ($return->completionDate || $return->cancellationDate) {
            return redirect()->route('returns.index')->with('error', 'Return cannot be completed from its current state.');
        }
        $return->update([
            'completionDate' => now(),
            'updated_by' => auth()->id(),
        ]);
        foreach ($return->stockOut->details as $detail) {
            Product::find($detail->productID)->decrement('stockQuantity', $detail->quantity);
        }
        return redirect()->route('supplier_returns.index')->with('success', 'Return completed, stock updated.');
    }

    public function reject(Request $request, $returnSupplierID)
    {
        $return = ReturnToSupplier::findOrFail($returnSupplierID);
        if ($return->completionDate || $return->cancellationDate) {
            return redirect()->route('returns.index')->with('error', 'Return cannot be rejected from its current state.');
        }
        $request->validate(['rejectionReason' => 'required|string|max:255']);
        $return->update([
            'cancellationDate' => now(),
            'cancellationRemark' => $request->rejectionReason,
            'updated_by' => auth()->id(),
        ]);
        return redirect()->route('supplier_returns.index')->with('success', 'Return rejected.');
    }

    /**
     * Display the specified resource.
     */
    public function show($returnSupplierID)
    {
        $return = ReturnToSupplier::with(['creator', 'supplierOrder.supplier', 'stockOut.details.product'])
            ->findOrFail($returnSupplierID);
        return view('admin.supplier_returns.show', compact('return'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReturnToSupplier $returnToSupplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReturnToSupplier $returnToSupplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReturnToSupplier $returnToSupplier)
    {
        //
    }
}
