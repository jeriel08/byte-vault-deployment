<?php

namespace App\Http\Controllers;

use App\Models\Adjustment;
use App\Models\Product;
use App\Models\StockOut;
use App\Models\StockOutDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // [cite: 281]
    {
        // Start query builder, eager load relationships needed for display/sorting
        $query = Adjustment::query()->with(['stockOut', 'createdBy']); // [cite: 281, 261, 268]

        // Search by adjustmentID (case-insensitive example)
        if ($request->filled('search')) { // [cite: 236, 419]
            $query->whereRaw('LOWER(adjustmentID) LIKE ?', ['%' . strtolower($request->input('search')) . '%']); // [cite: 419]
        }

        // Filter by Adjustment Reason
        if ($request->filled('reason')) { //
            $query->where('adjustmentReason', $request->input('reason')); //
        }

        // Filter by Date Range (adjustmentDate)
        if ($request->filled('date_from')) { // [cite: 248, 319-320]
            $query->where('adjustmentDate', '>=', $request->input('date_from')); // [cite: 248, 319-320]
        }
        if ($request->filled('date_to')) { // [cite: 248, 319-320]
            $query->where('adjustmentDate', '<=', $request->input('date_to')); // [cite: 248, 319-320]
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'adjustmentDate_desc'); // Default sort
        switch ($sortBy) {
            case 'adjustmentDate_asc':
                $query->orderBy('adjustmentDate', 'asc');
                break;
            case 'totalQuantity_asc':
            case 'totalQuantity_desc':
                // Sorting by totalQuantity requires joining the stock_outs table
                // Ensure 'stock_outs.stockOutID' is unique per adjustment if using direct join
                // Or use a subquery if needed
                $query->join('stock_outs', function ($join) {
                    $join->on('adjustments.adjustmentID', '=', 'stock_outs.referenceID')
                        ->where('stock_outs.referenceTable', '=', 'adjustments'); // Ensure correct join for adjustments
                })
                    ->orderBy('stock_outs.totalQuantity', $sortBy === 'totalQuantity_asc' ? 'asc' : 'desc')
                    ->select('adjustments.*'); // Select only adjustment columns to avoid conflicts
                break;
            case 'created_by_asc':
            case 'created_by_desc':
                // Sorting by created_by name requires joining the users/employees table
                $query->join('employees', 'adjustments.created_by', '=', 'employees.employeeID') // Adjust table/column names if different
                    ->orderBy('employees.full_name', $sortBy === 'created_by_asc' ? 'asc' : 'desc')
                    ->select('adjustments.*'); // Select only adjustment columns
                break;
            case 'adjustmentDate_desc': // Default case
            default:
                $query->orderBy('adjustmentID', 'desc');
                break;
        }

        // Paginate results and append query parameters to pagination links
        $adjustments = $query->paginate(5)->appends($request->query());

        // Optionally: Calculate counts for filter buttons (like in supplier orders)
        $reasonCounts = [
            'Damaged' => Adjustment::where('adjustmentReason', 'Damaged')->count(),
            'Lost' => Adjustment::where('adjustmentReason', 'Lost')->count(),
            'Other' => Adjustment::where('adjustmentReason', 'Other')->count(),
        ];


        // Pass paginated data and counts to the view
        return view('admin.adjustments.index', compact('adjustments', 'reasonCounts')); // [cite: 282, 431]
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $products = Product::where('productStatus', 'active')->get();
        if ($products->isEmpty()) {
            $products = collect(); // Fallback to empty collection if no data
        }
        return view('admin.adjustments.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'adjustmentDate' => 'required|date',
            'adjustmentReason' => 'required|string|max:255',
            'products' => 'required|array',
            'products.*.productID' => 'required|exists:products,productID',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $adjustment = Adjustment::create([
                'adjustmentDate' => $request->adjustmentDate,
                'adjustmentReason' => $request->adjustmentReason,
                'created_by' => Auth::user()->employeeID,
            ]);

            $totalQuantity = array_sum(array_column($request->products, 'quantity'));
            $stockOut = StockOut::create([
                'reasonType' => 'adjustment',
                'referenceID' => $adjustment->adjustmentID,
                'referenceTable' => 'adjustments',
                'totalQuantity' => $totalQuantity,
                'created_by' => Auth::user()->employeeID,
            ]);
            Log::info('StockOut created', ['id' => $stockOut->stockOutID, 'referenceID' => $stockOut->referenceID]);

            foreach ($request->products as $item) {
                $detail = StockOutDetail::create([
                    'stockOutID' => $stockOut->stockOutID,
                    'productID' => $item['productID'],
                    'quantity' => $item['quantity'],
                ]);

                Log::info('StockOutDetail created', ['id' => $detail->stockOutDetailID]);

                $product = Product::find($item['productID']);
                $newStock = $product->stockQuantity - $item['quantity'];
                if ($newStock < 0) {
                    throw new \Exception("Stock for {$product->name} cannot go below 0.");
                }
                $product->update(['stockQuantity' => $newStock]);
            }
        });

        return redirect()->route('adjustments.index')->with('success', 'Adjustment recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Adjustment $adjustment)
    {
        //
        $adjustment->load('stockOut.details.product');
        return view('admin.adjustments.show', compact('adjustment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Adjustment $adjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Adjustment $adjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Adjustment $adjustment)
    {
        //
    }
}
