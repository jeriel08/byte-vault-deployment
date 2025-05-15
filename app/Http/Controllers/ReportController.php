<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryReportExport;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ReportController extends Controller
{
    //

    /**
     * Fetch data for the inventory/sales report, considering date filters.
     */
    private function getReportData(Request $request) // <-- Inject Request
    {
        \Illuminate\Support\Facades\Log::info('Request Inputs:', $request->all());
        // --- Date Range Handling ---
        $defaultStartDate = Carbon::today()->startOfDay(); // Default start: Today
        $defaultEndDate = Carbon::today()->endOfDay();   // Default end: Today

        // Get dates from request, use defaults if not present or invalid
        try {
            $startDate = Carbon::parse($request->input('filter_start_date', $defaultStartDate))->startOfDay();
        } catch (\Exception $e) {
            $startDate = $defaultStartDate;
        }
        try {
            $endDate = Carbon::parse($request->input('filter_end_date', $defaultEndDate))->endOfDay();
        } catch (\Exception $e) {
            $endDate = $defaultEndDate;
        }

        // Ensure end date is not before start date
        if ($endDate->isBefore($startDate)) {
            $endDate = $startDate->copy()->endOfDay();
        }
        // --- End Date Range Handling ---


        // --- Inventory Data (Remains the same - current snapshot) ---
        $products = Product::paginate(10);
        $totalProducts = $products->count();
        $totalValue = $products->sum(function ($product) {
            return $product->stockQuantity * $product->price;
        });
        $lowStockCount = $products->where('stockQuantity', '<', 5)->count();
        // --- End Inventory Data ---


        // --- Fetch Sales Data For Date Range ---
        $salesQuery = Order::whereBetween('created_at', [$startDate, $endDate]);

        $salesOrdersCount = $salesQuery->count();
        $salesTotalValue = $salesQuery->sum('total'); // Use 'total' column from orders table
        // --- End Sales Data Fetch ---

        // Format dates for display and potentially for the view/export
        $reportDate = Carbon::now()->format('F j, Y'); // Date the report was generated
        $filterStartDateString = $startDate->format('Y-m-d');
        $filterEndDateString = $endDate->format('Y-m-d');
        $dateRangeDisplay = $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y');
        // Handle if start and end date are the same day
        if ($startDate->isSameDay($endDate)) {
            $dateRangeDisplay = $startDate->format('M d, Y');
        }


        return [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalValue' => $totalValue,
            'lowStockCount' => $lowStockCount,
            'salesOrdersCount' => $salesOrdersCount, // Use new variable name
            'salesTotalValue' => $salesTotalValue,   // Use new variable name
            'reportDate' => $reportDate,
            'filter_start_date' => $filterStartDateString, // Pass back for repopulation
            'filter_end_date' => $filterEndDateString,   // Pass back for repopulation
            'dateRangeDisplay' => $dateRangeDisplay,     // Pass back for display
        ];
    }

    public function inventoryReport(Request $request)
    {
        // Pass data to the view
        $data = $this->getReportData($request);
        return view('admin.reports.inventory', $data);
    }

    public function downloadInventoryPdf(Request $request)
    {
        // Fetch all products
        $data = $this->getReportData($request);

        \Illuminate\Support\Facades\Log::info('PDF Data:', $data);

        // Load the dedicated PDF view and pass data
        $pdf = PDF::loadView('admin.reports.inventory_pdf', $data);

        $pdfName = 'inventory-sales-report-' . $data['filter_start_date'] . '_to_' . $data['filter_end_date'] . '.pdf';
        return $pdf->download($pdfName);
    }

    public function downloadInventoryExcel(Request $request)
    {
        $data = $this->getReportData($request);

        // Define a filename for the downloaded file
        $fileName = 'inventory-sales-report-' . $data['filter_start_date'] . '_to_' . $data['filter_end_date'] . '.xlsx';

        // Trigger the download using the Excel facade and your export class
        return Excel::download(
            new InventoryReportExport(
                $data['salesOrdersCount'],
                $data['salesTotalValue'],
                $data['dateRangeDisplay']
            ),
            $fileName
        );
    }
}
