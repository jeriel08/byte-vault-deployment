@section('title', 'Supplier Orders | ByteVault')

<x-app-layout>
    <div class="container-fluid mx-auto px-4 py-6 position-relative">
        <!-- Header with Search and Add Supplier Order -->
        <div class="d-flex justify-content-between align-items-center mx-1 mb-4">
            <form id="searchForm" class="input-group w-50">
                <input type="text" name="search" class="search-input" placeholder="Search by order ID" aria-label="Search orderID" value="{{ request('search') }}">
                <button class="search-button d-flex align-items-center justify-content-center" type="submit">
                    <span class="material-icons-outlined">search</span>
                </button>
            </form>
            <x-primary-button href="{{ route('supplier_orders.create') }}" class="py-2">
                <span class="material-icons-outlined">add</span>
                Add Order
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
            <!-- Supplier Order Filter Panel -->
            <div class="col-lg-3 col-md-4 col-sm-12">
                <div class="card filter-panel">
                    <div class="card-body p-3">
                        <h5 class="fw-semibold">Filters</h5>
                        <!-- Order Status -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold mb-2">Order Status</label>
                            <div class="btn-group d-flex flex-wrap gap-2 mb-3" role="group">
                                <button type="button" class="btn category-filter-button flex-grow-1 {{ request('status') === 'Pending' ? 'btn-primary' : 'btn-outline-primary' }}" data-filter="status" data-value="Pending">
                                    <span class="badge me-2">{{ $pendingCount }}</span> Pending
                                </button>
                                <button type="button" class="btn category-filter-button flex-grow-1 {{ request('status') === 'Cancelled' ? 'btn-primary' : 'btn-outline-primary' }}" data-filter="status" data-value="Cancelled">
                                    <span class="badge me-2">{{ $cancelledCount }}</span> Cancelled
                                </button>
                                <button type="button" class="btn category-filter-button flex-grow-1 {{ request('status') === 'Received' ? 'btn-primary' : 'btn-outline-primary' }}" data-filter="status" data-value="Received">
                                    <span class="badge me-2">{{ $receivedCount }}</span> Received
                                </button>
                            </div>
                        </div>
                        <hr>
                        <!-- Supplier -->
                        <div class="mb-3">
                            <label for="supplierFilter" class="form-label fw-semibold mb-2">Supplier</label>
                            <select class="form-select select2 custom-select2" id="supplierFilter" name="supplier_id">
                                <option value="">All Suppliers</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->supplierID }}" {{ request('supplier_id') == $supplier->supplierID ? 'selected' : '' }}>
                                        {{ $supplier->supplierName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <!-- Date Range -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold mb-2">Date Range</label>
                            <small class="text-muted ms-1">From</small>
                            <input type="date" class="form-control mb-2" id="dateFrom" name="date_from" value="{{ request('date_from') }}" placeholder="From">
                            <small class="text-muted ms-1">To</small>
                            <input type="date" class="form-control" id="dateTo" name="date_to" value="{{ request('date_to') }}" placeholder="To">
                        </div>
                        <hr>
                        <div>
                            <button type="button" class="btn btn-outline-danger w-100" id="clearFilters">Clear Filters</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content: Supplier Order Cards -->
            <div class="col-lg-9 col-md-8 col-sm-12 product-table" id="supplierOrderTable">
                @if ($supplierOrders->isEmpty())
                    <div class="card account-manager-card text-center p-5">
                        <h5 class="text-muted d-flex justify-content-center align-items-center gap-3">
                            No supplier orders yet. 
                            <span class="material-icons-outlined fs-2">local_shipping</span>
                        </h5>
                    </div>
                @else
                    <div class="row">
                        @foreach ($supplierOrders as $supplierOrder)
                            <!-- Your existing card content unchanged -->
                            <div class="col-12 mb-4">
                                <div class="card account-manager-card p-3 d-flex flex-row align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-semibold fs-5 me-4">Order No. {{ $supplierOrder->supplierOrderID }}</p>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="vr me-4"></span>
                                        <div class="d-flex flex-row gap-3 align-items-start ps-4">
                                            <div class="text-start me-4" style="width: 7rem;">
                                                <span class="text-muted d-block"><small>Supplier</small></span>
                                                <span class="fw-semibold text-truncate d-block">{{ $supplierOrder->supplier->supplierName }}</span>
                                            </div>
                                            <div class="text-start me-4" style="width: 7rem;">
                                                <span class="text-muted d-block">
                                                    <small>
                                                        {{ $supplierOrder->receivedDate ? 'Date Received' : ($supplierOrder->cancelledDate ? 'Date Cancelled' : 'Order Placed') }}
                                                    </small>
                                                </span>
                                                <span class="fw-semibold text-truncate d-block">
                                                    {{ \Carbon\Carbon::parse($supplierOrder->receivedDate ?? $supplierOrder->cancelledDate ?? $supplierOrder->orderPlacedDate)->format('M d, Y') }}
                                                </span>
                                            </div>
                                            <div class="text-start me-4" style="width: 5rem;">
                                                <span class="text-muted d-block"><small>Status</small></span>
                                                <span class="badge bg-{{ $supplierOrder->receivedDate ? 'success' : ($supplierOrder->cancelledDate ? 'danger' : 'warning') }} fixed-badge">
                                                    {{ $supplierOrder->receivedDate ? 'Received' : ($supplierOrder->cancelledDate ? 'Cancelled' : 'Pending') }}
                                                </span>
                                            </div>
                                            <div class="text-start" style="width: 8rem;">
                                                <span class="text-muted d-block"><small>Total Cost</small></span>
                                                <span class="fw-semibold text-truncate d-block">₱{{ number_format($supplierOrder->totalCost, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-5">
                                        <x-primary-button class="btn-sm mb-2" href="{{ route('supplier_orders.show', $supplierOrder->supplierOrderID) }}">
                                            <span class="material-icons-outlined">visibility</span>
                                        </x-primary-button>
                                        @if ($supplierOrder->receivedDate || $supplierOrder->cancelledDate)
                                            <x-primary-button class="btn-sm" href="{{ route('supplier_orders.create', ['reorder' => $supplierOrder->supplierOrderID]) }}">
                                                <span class="material-icons-outlined">replay</span>
                                            </x-primary-button>
                                        @else
                                            <div class="dropdown supplier-order-dropdown">
                                                <x-primary-button class="btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="material-icons-outlined">more_horiz</span>
                                                </x-primary-button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('supplier_orders.edit', $supplierOrder->supplierOrderID) }}"><span class="material-icons-outlined align-middle me-2">edit</span> Edit</a></li>
                                                        <li>
                                                            <form action="{{ route('supplier_orders.update', $supplierOrder->supplierOrderID) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="markAsReceived" value="1">
                                                                <button type="submit" class="dropdown-item"><span class="material-icons-outlined align-middle me-2">check_circle</span> Receive</button>
                                                            </form>
                                                        </li>
                                                        <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#cancelModal-{{ $supplierOrder->supplierOrderID }}"><span class="material-icons-outlined align-middle me-2">cancel</span> Cancel</button></li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                    <x-modal name="cancelModal-{{ $supplierOrder->supplierOrderID }}" maxWidth="md">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="cancelModal-{{ $supplierOrder->supplierOrderID }}-label">Cancel Order #{{ $supplierOrder->supplierOrderID }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('supplier_orders.update', $supplierOrder->supplierOrderID) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <input type="hidden" name="markAsCancelled" value="1">
                                                <div class="mb-3">
                                                    <label for="cancellationRemark-{{ $supplierOrder->supplierOrderID }}" class="form-label">Reason for Cancellation</label>
                                                    <textarea class="form-control" id="cancellationRemark-{{ $supplierOrder->supplierOrderID }}" name="cancellationRemark" rows="3" required placeholder="Enter the reason for cancelling this order"></textarea>
                                                    @error('cancellationRemark')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <x-danger-button type="submit">Cancel Order</x-danger-button>
                                                <x-secondary-button type="button" data-bs-dismiss="modal">Close</x-secondary-button>
                                            </div>
                                        </form>
                                    </x-modal>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center">
                        <ul class="pagination">
                            <!-- Previous Page Link -->
                            @if ($supplierOrders->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link d-flex justify-content-center align-items-center" href="{{ $supplierOrders->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="prev">
                                        <span class="material-icons-outlined">
                                            navigate_before
                                        </span>
                                    </a>
                                </li>
                            @endif
                    
                            <!-- Page Numbers -->
                            @for ($i = 1; $i <= $supplierOrders->lastPage(); $i++)
                                <li class="page-item {{ $supplierOrders->currentPage() === $i ? 'active' : '' }}">
                                    @if ($supplierOrders->currentPage() === $i)
                                        <span class="page-link">{{ $i }}</span>
                                    @else
                                        <a class="page-link" href="{{ $supplierOrders->url($i) }}&{{ http_build_query(request()->except('page')) }}">{{ $i }}</a>
                                    @endif
                                </li>
                            @endfor
                    
                            <!-- Next Page Link -->
                            @if ($supplierOrders->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link d-flex justify-content-center align-items-center" href="{{ $supplierOrders->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="next">
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

    <!-- JavaScript for Filter and Search Interactivity -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.category-filter-button');
            const supplierFilter = document.getElementById('supplierFilter');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            const clearFiltersBtn = document.getElementById('clearFilters');
            const searchInput = document.querySelector('.search-input');
            const searchForm = document.getElementById('searchForm');

            let debounceTimer;

            function applyFilters() {
                const params = new URLSearchParams(window.location.search);

                // Status filter
                const activeButton = document.querySelector('.category-filter-button.btn-primary');
                if (activeButton) {
                    params.set('status', activeButton.dataset.value);
                } else {
                    params.delete('status');
                }

                // Supplier filter
                if (supplierFilter.value) {
                    params.set('supplier_id', supplierFilter.value);
                } else {
                    params.delete('supplier_id');
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

                // Search
                if (searchInput.value) {
                    params.set('search', searchInput.value);
                } else {
                    params.delete('search');
                }

                window.location.href = `${window.location.pathname}?${params.toString()}`;
            }

            // Status button toggle
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    filterButtons.forEach(btn => {
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-outline-primary');
                    });
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-primary');
                    applyFilters();
                });
            });

            // Other filters (supplier, date)
            [supplierFilter, dateFrom, dateTo].forEach(element => {
                element.addEventListener('change', applyFilters);
            });

            // Debounced search
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    applyFilters();
                }, 500); // 500ms delay after typing stops
            });

            // Prevent form submission on Enter
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                applyFilters();
            });

            // Clear filters
            clearFiltersBtn.addEventListener('click', function() {
                filterButtons.forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                });
                supplierFilter.value = '';
                dateFrom.value = '';
                dateTo.value = '';
                searchInput.value = '';
                window.location.href = window.location.pathname;

                $('.select2').val('').trigger('change.select2');
            });
        });
    </script>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    placeholder: "Select a supplier",
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    // Get the selected supplier ID
                    var supplierId = $(this).val();
                    
                    // Get current URL and update/add supplier_id query parameter
                    var url = new URL(window.location.href);
                    if (supplierId) {
                        url.searchParams.set('supplier_id', supplierId);
                    } else {
                        url.searchParams.delete('supplier_id');
                    }
                    
                    // Reload the page with the updated URL
                    window.location.href = url.toString();
                });
            });
        </script>
    @endpush

</x-app-layout>