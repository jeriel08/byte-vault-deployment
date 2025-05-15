@section('title', 'Inventory Report | ByteVault')

<x-app-layout>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Inventory report as of {{ \Carbon\Carbon::now()->format('F j, Y') }}</h2>
            <div class="d-flex gap-2">
                <x-primary-button href="{{ route('reports.inventory.download.excel', ['filter_start_date' => $filter_start_date, 'filter_end_date' => $filter_end_date]) }}" class="me-2">
                    <i class="fa-solid fa-file-excel"></i> Download Excel
                </x-primary-button> 
                <x-primary-button href="{{ route('reports.inventory.download.pdf', ['filter_start_date' => $filter_start_date, 'filter_end_date' => $filter_end_date]) }}" class="me-2">
                    <i class="fa-solid fa-file-pdf"></i> Download PDF
                </x-primary-button> 
                <x-secondary-button href="{{ route('dashboard') }}">
                    <span class="material-icons-outlined">
                        arrow_back
                    </span>Go back
                </x-secondary-button>
            </div>
        </div>

        <div class="mb-4">
            <!-- Date Range Filter -->
            <form action="{{ route('reports.inventory') }}" method="GET" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="dateRangePickerInput" class="form-label fw-semibold">Select Date Range (Sales)</label>
                        <input type="text" class="form-control form-control-sm" id="dateRangePickerInput" placeholder="Select date range...">
                        {{-- Hidden fields to store and submit the actual dates --}}
                        <input type="hidden" name="filter_start_date" id="filter_start_date" value="{{ request('filter_start_date') }}">
                        <input type="hidden" name="filter_end_date" id="filter_end_date" value="{{ request('filter_end_date') }}">
                    </div>
                    {{-- Removed the Apply Filters button --}}
                    <div class="col-md-auto d-flex align-items-center">
                        <x-secondary-button href="{{ route('reports.inventory') }}" class="btn btn-secondary btn-sm">
                        <span class="material-icons-outlined" style="font-size: 1.1em; vertical-align: middle;">clear</span> Clear Date Filter
                        </x-secondary-button>
                    </div>
                </div>
            </form>
        </div>

        <hr class="mb-4">

        <div class="row mb-4">
            <p><strong>Report Generated:</strong> {{ $reportDate }}</p>
            <p><strong>Date Range:</strong> {{ $dateRangeDisplay }}</p>
            <div class="row mt-3">
                <!-- Products Card -->
                <div class="col-6">
                    <div class="card shadow-sm rounded-4 p-3 d-flex flex-column inventory-report-card" style="height: 100%;">
                        <h2 class="fw-bold mb-3">Products</h2>
                        <p><strong>Total Products:</strong> {{ $totalProducts }}</p>
                        <p><strong>Total Inventory Value:</strong> ${{ number_format($totalValue, 2) }}</p>
                        <p><strong>Low Stock Items (Stock < 5):</strong> {{ $lowStockCount }}</p>
                    </div>
                </div>
                <!-- Sales Card -->
                <div class="col-6">
                    <div class="card shadow-sm rounded-4 p-3 d-flex flex-column inventory-report-card" style="height: 100%;">
                        <h2 class="fw-bold mb-3">Sales</h2>
                        <p><strong>Orders in Range:</strong> {{ $salesOrdersCount }}</p>
                        <p><strong>Sales Total in Range:</strong> ₱{{ number_format($salesTotalValue, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    
        <hr class="mb-4">
            
        <!-- Products Table -->
        <table class="table table-bordered inventory-table">
            <thead class="inventory-table-header">
                <tr>
                    <th class="text-center">ID</th>
                    <th>Product Name</th>
                    <th>Stock</th>
                    <th>Unit Price</th>
                    <th>Total Value</th>
                </tr>
            </thead>
            <tbody class="inventory-table-body table-group-divider">
                @foreach ($products as $product)
                    <tr class="inventory-table-row">
                        <td class="text-center">{{ $product->productID }}</td>
                        <td>{{ $product->productName }}</td>
                        <td class="text-end">{{ $product->stockQuantity }}</td>
                        <td class="text-end">₱{{ number_format($product->price, 2) }}</td>
                        <td class="text-end">₱{{ number_format($product->stockQuantity * $product->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            <ul class="pagination">
                <!-- Previous Page Link -->
                @if ($products->onFirstPage())
                    <li class="page-item disabled">
                        <span class="material-icons-outlined page-link">
                            navigate_before
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link d-flex justify-content-center align-items-center" href="{{ $products->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="prev">
                            <span class="material-icons-outlined">
                                navigate_before
                            </span>
                        </a>
                    </li>
                @endif
        
                <!-- Page Numbers -->
                @for ($i = 1; $i <= $products->lastPage(); $i++)
                    <li class="page-item {{ $products->currentPage() === $i ? 'active' : '' }}">
                        @if ($products->currentPage() === $i)
                            <span class="page-link">{{ $i }}</span>
                        @else
                            <a class="page-link" href="{{ $products->url($i) }}&{{ http_build_query(request()->except('page')) }}">{{ $i }}</a>
                        @endif
                    </li>
                @endfor
        
                <!-- Next Page Link -->
                @if ($products->hasMorePages())
                    <li class="page-item">
                        <a class="page-link d-flex justify-content-center align-items-center" href="{{ $products->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="next">
                            <span class="material-icons-outlined">
                                navigate_next
                            </span>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="material-icons-outlined page-link">
                            navigate_next
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the input element and the hidden fields
            const dateInput = document.getElementById('dateRangePickerInput');
            const startDateInput = document.getElementById('filter_start_date');
            const endDateInput = document.getElementById('filter_end_date');
            const filterForm = document.getElementById('filterForm'); // Get the form
    
            // Initialize Litepicker
            const picker = new Litepicker({
                element: dateInput,
                singleMode: false, // Enable range selection
                format: 'YYYY-MM-DD', // Format for display and hidden inputs
                tooltipText: {
                  one: 'day',
                  other: 'days'
                },
                tooltipNumber: (totalDays) => {
                  return totalDays; // Show total days selected
                },
                setup: (picker) => {
                    // Event triggered when a date range is selected
                    picker.on('selected', (date1, date2) => {
                        // Update hidden inputs with formatted dates
                        startDateInput.value = date1.format('YYYY-MM-DD');
                        endDateInput.value = date2.format('YYYY-MM-DD');
    
                        // Automatically submit the form
                        if (filterForm) {
                            filterForm.submit();
                        } else {
                            console.error('Filter form not found.');
                        }
                    });
                },
            });
    
            // --- Optional: Display initial dates if they exist (from backend) ---
            const initialStartDate = startDateInput.value;
            const initialEndDate = endDateInput.value;
            if (initialStartDate && initialEndDate) {
                // Set the visible input's value (display only)
                dateInput.value = `${initialStartDate} - ${initialEndDate}`;
                // Optionally, set the picker's dates if needed (might depend on library version)
                // picker.setDateRange(initialStartDate, initialEndDate);
            } else if (initialStartDate) { // Handle case where only start date might be set (unlikely for range)
                 dateInput.value = initialStartDate;
            }
            // --- End Optional Initial Display ---
    
        });
    </script>
</x-app-layout>