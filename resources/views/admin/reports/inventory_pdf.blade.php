<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Inventory Report</title>
    <style>
        /* Basic Styling for PDF */
        body { font-family: sans-serif; font-size: 10px; }
        .container { width: 100%; margin: 0 auto; }
        h2 { text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .summary { margin-bottom: 20px; border: 1px solid #eee; padding: 10px; }
        .summary p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .page-break { page-break-after: always; } /* Optional: for page breaks */
    </style>
</head>
<body>
    <div class="container">
        @if ($dateRangeDisplay)
            <h2>Inventory Report {{ $dateRangeDisplay }}</h2>
        @else
            <h2>Inventory Report as of {{ $reportDate }}</h2>
        @endif
        

        <div class="summary">
            <p><strong>Report Generated:</strong> {{ $reportDate }}</p>
            <p><strong>Date Range:</strong> {{ $dateRangeDisplay }}</p>
            <div>
                <div class="mb-4">
                    <h2>Product Report</h2>
                    <p><strong>Total Products:</strong> {{ $totalProducts }}</p>
                    <p><strong>Total Inventory Value:</strong> ₱{{ number_format($totalValue, 2) }}</p>
                    <p><strong>Low Stock Items (Stock < 5):</strong> {{ $lowStockCount }}</p>
                </div>
                <div>
                    <h2>Sales Report</h2>
                    <p><strong>Orders in Range:</strong> {{ $salesOrdersCount }}</p>
                    <p><strong>Sales Total in Range:</strong> ₱{{ number_format($salesTotalValue, 2) }}</p>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th>Product Name</th>
                    <th class="text-end">Stock</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td class="text-center">{{ $product->productID }}</td>
                        <td>{{ $product->productName }}</td>
                        <td class="text-end">{{ $product->stockQuantity }}</td>
                        <td class="text-end">₱{{ number_format($product->price, 2) }}</td>
                        <td class="text-end">₱{{ number_format($product->stockQuantity * $product->price, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>