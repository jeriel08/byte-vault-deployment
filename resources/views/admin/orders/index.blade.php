@section('title', 'Customer Orders | ByteVault')

<x-app-layout>
    <div class="container-fluid mx-auto px-4 py-6 position-relative">
        <!-- Header with Search and Add Supplier Order-->
        <div class="d-flex justify-content-between align-items-center mx-1 mb-4">
            <div class="input-group w-50">
                <input type="text" class="search-input" id="searchInput" placeholder="Search by order ID" aria-label="Search orderID" value="{{ request('search') }}">
                <button class="search-button d-flex align-items-center justify-content-center" type="button" id="searchButton">
                    <span class="material-icons-outlined">search</span>
                </button>
            </div>
            <x-primary-button href="{{ route('pos.products') }}" class="py-2">
                <span class="material-icons-outlined">add</span>
                Add Customer Order
            </x-primary-button>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Filter Panel -->
            <div class="col-lg-3 col-md-4 col-sm-12">
                <div class="card filter-panel">
                    <div class="card-body p-3">
                        <h5 class="fw-semibold">Filters</h5>

                        <!-- Date Range -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold mb-2">Date Range</label>
                            <small class="text-muted ms-1">From</small>
                            <input type="date" class="form-control mb-2" id="dateFrom" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                            <small class="text-muted ms-1">To</small>
                            <input type="date" class="form-control" id="dateTo" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                        </div>
                        <hr>
                        <!-- Sort By -->
                        <div class="mb-3">
                            <label for="sortBy" class="form-label fw-semibold mb-2">Sort By</label>
                            <select class="form-select" id="sortBy" name="sort_by">
                                <option value="date_desc" {{ request('sort_by') === 'date_desc' ? 'selected' : '' }}>Order Date: Recent First</option>
                                <option value="date_asc" {{ request('sort_by') === 'date_asc' ? 'selected' : '' }}>Order Date: Oldest First</option>
                                <option value="amount_desc" {{ request('sort_by') === 'amount_desc' ? 'selected' : '' }}>Amount: High to Low</option>
                                <option value="amount_asc" {{ request('sort_by') === 'amount_asc' ? 'selected' : '' }}>Amount: Low to High</option>
                            </select>
                        </div>
                        <hr>
                        <div>
                            <button type="button" class="btn btn-outline-danger w-100" id="clearFilters">Clear Filters</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content: Order Cards -->
            <div class="col-lg-9 col-md-8 col-sm-12 product-table" id="orderTable">
                @if ($orders->isEmpty())
                    <div class="col-12 mb-4">
                        <div class="card account-manager-card p-3 py-5 d-flex flex-row align-items-center">
                            <div class="flex-grow-1 text-center text-muted">
                                <span class="fw-semibold">There's no order yet.</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        @foreach ($orders as $order)
                            <div class="col-12 mb-4">
                                <div class="card account-manager-card p-3 d-flex flex-row align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-semibold fs-5 me-4">Order No. {{ $order->orderID }}</p>
                                    </div>
                    
                                    <div class="d-flex align-items-center mx-3 price-section">
                                        <span class="vr me-4"></span>
                                        <div class="d-flex flex-row gap-3 align-items-start">
                                            <div class="text-start me-4" style="width: 7rem;">
                                                <span class="text-muted d-block"><small>Customer</small></span>
                                                <span class="fw-semibold text-truncate d-block">{{ $order->customer ? $order->customer->name : 'N/A' }}</span>
                                            </div>
                                            <div class="text-start me-4" style="width: 7rem;">
                                                <span class="text-muted d-block"><small>Order Date</small></span>
                                                <span class="fw-semibold text-truncate d-block">
                                                    {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y')  }}
                                                </span>
                                            </div>
                                            <div class="text-start me-4" style="width: 7rem;">
                                                <span class="text-muted d-block"><small>Total Amount</small></span>
                                                <div class="d-flex align-items-center">
                                                    <span class="fw-semibold text-truncate d-block total-amount" data-order-id="{{ $order->orderID }}" data-amount="₱{{ number_format($order->total, 2) }}">*****</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="ms-5 d-flex flex-column gap-2">
                                        <x-primary-button class="btn btn-sm toggle-amount" type="button" data-order-id="{{ $order->orderID }}">
                                            <span class="material-icons-outlined">visibility</span>
                                        </x-primary-button>
                                        <x-primary-button class="btn-sm" href="{{ route('orders.show', $order) }}">
                                            <span class="material-icons-outlined">receipt_long</span>
                                        </x-primary-button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        <ul class="pagination">
                            <!-- Previous Page Link -->
                            @if ($orders->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="material-icons-outlined page-link">
                                        navigate_before
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link d-flex justify-content-center align-items-center" href="{{ $orders->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="prev">
                                        <span class="material-icons-outlined">
                                            navigate_before
                                        </span>
                                    </a>
                                </li>
                            @endif
                    
                            <!-- Page Numbers -->
                            @for ($i = 1; $i <= $orders->lastPage(); $i++)
                                <li class="page-item {{ $orders->currentPage() === $i ? 'active' : '' }}">
                                    @if ($orders->currentPage() === $i)
                                        <span class="page-link">{{ $i }}</span>
                                    @else
                                        <a class="page-link" href="{{ $orders->url($i) }}&{{ http_build_query(request()->except('page')) }}">{{ $i }}</a>
                                    @endif
                                </li>
                            @endfor
                    
                            <!-- Next Page Link -->
                            @if ($orders->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link d-flex justify-content-center align-items-center" href="{{ $orders->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="next">
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
                @endif
            </div>
        </div>
    </div>

    <!-- JavaScript for Filter Interactivity -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            const sortBy = document.getElementById('sortBy');
            const clearFiltersBtn = document.getElementById('clearFilters');

            function applyFilters() {
                const params = new URLSearchParams(window.location.search);

                // Search filter
                if (searchInput.value) {
                    params.set('search', searchInput.value);
                } else {
                    params.delete('search');
                }

                // Date range filter
                if (dateFrom.value) {
                    params.set('date_from', dateFrom.value);
                } else {
                    params.delete('date_from');
                }
                if (dateTo.value) {
                    params.set('date_to', dateTo.value);
                } else {
                    params.delete('date_to');
                }

                // Sort by
                if (sortBy.value) {
                    params.set('sort_by', sortBy.value);
                } else {
                    params.delete('sort_by');
                }

                window.location.href = `${window.location.pathname}?${params.toString()}`;
            }

            // Event listeners for filters
            [searchInput, dateFrom, dateTo, sortBy].forEach(element => {
                element.addEventListener('change', applyFilters);
            });

            // Search button click
            searchButton.addEventListener('click', applyFilters);

            // Clear filters
            clearFiltersBtn.addEventListener('click', function() {
                searchInput.value = '';
                dateFrom.value = '';
                dateTo.value = '';
                sortBy.value = 'date_desc';
                window.location.href = window.location.pathname;
            });

            // Toggle amount
            document.querySelectorAll('.toggle-amount').forEach(button => {
                button.addEventListener('click', () => {
                    const orderId = button.dataset.orderId;
                    const totalSpan = document.querySelector(`.total-amount[data-order-id="${orderId}"]`);
                    const actualAmount = totalSpan.dataset.amount;
                    const isMasked = totalSpan.textContent === '*****';
                    totalSpan.textContent = isMasked ? actualAmount : '*****';
                    button.querySelector('span').textContent = isMasked ? 'visibility_off' : 'visibility';
                });
            });
        });
    </script>
</x-app-layout>