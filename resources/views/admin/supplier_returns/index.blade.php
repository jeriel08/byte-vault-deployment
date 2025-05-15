@section('title', 'Return to Supplier | ByteVault')

<x-app-layout>
    <div class="container-fluid mx-auto px-4 py-6 position-relative">
        <!-- Header with Search and Add Return -->
        <div class="d-flex justify-content-between align-items-center mx-1 mb-4">
            <div class="input-group w-50">
                <input type="text" class="search-input" placeholder="Search by return ID" aria-label="Search returnSupplierID">
                <button class="search-button d-flex align-items-center justify-content-center" type="button">
                    <span class="material-icons-outlined">search</span>
                </button>
            </div>
            <x-primary-button href="{{ route('supplier_returns.create') }}" class="py-2">
                <span class="material-icons-outlined">add</span>
                New Return
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
                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold mb-2">Status</label>
                            <div class="btn-group d-flex flex-wrap gap-2">
                                @foreach(['Pending', 'Completed', 'Rejected'] as $status)
                                    <button type="button" 
                                            class="btn category-filter-button flex-grow-1 {{ request('status') === $status ? 'btn-primary' : 'btn-outline-primary' }}"
                                            data-filter="status" 
                                            data-value="{{ $status }}">
                                        <span class="badge me-2">{{ $statusCounts[$status] }}</span> {{ $status }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <hr>
                        <!-- Supplier -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold mb-2">Supplier</label>
                            <select class="form-select" id="supplierFilter" name="supplierID">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->supplierID }}" {{ request('supplierID') == $supplier->supplierID ? 'selected' : '' }}>
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
                            <input type="date" class="form-control mb-2" id="dateFrom" name="date_from" value="{{ request('date_from') }}">
                            <small class="text-muted ms-1">To</small>
                            <input type="date" class="form-control" id="dateTo" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <hr>
                        <!-- Sort By -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold mb-2">Sort By</label>
                            <select class="form-select" id="sortBy" name="sort_by">
                                <option value="adjustmentDatePlaced" {{ request('sort_by') === 'adjustmentDatePlaced' ? 'selected' : '' }}>Placed Date</option>
                                <option value="completionDate" {{ request('sort_by') === 'completionDate' ? 'selected' : '' }}>Completion Date</option>
                                <option value="cancellationDate" {{ request('sort_by') === 'cancellationDate' ? 'selected' : '' }}>Cancellation Date</option>
                                <option value="total_quantity" {{ request('sort_by') === 'total_quantity' ? 'selected' : '' }}>Total Quantity</option>
                                <option value="created_by" {{ request('sort_by') === 'created_by' ? 'selected' : '' }}>Created By</option>
                            </select>
                            <select class="form-select mt-2" id="sortDirection" name="sort_direction">
                                <option value="asc" {{ request('sort_direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                                <option value="desc" {{ request('sort_direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                            </select>
                        </div>
                        <hr>
                        <button type="button" class="btn btn-outline-danger w-100" id="clearFilters">Clear Filters</button>
                    </div>
                </div>
            </div>

            <!-- Returns List -->
            <div class="col-lg-9 col-md-8 col-sm-12 product-table" id="returnsTable">
                @if ($returns->isEmpty())
                    <div class="card account-manager-card text-center p-5">
                        <h5 class="text-muted d-flex justify-content-center align-items-center gap-3">
                            No returns yet.
                            <span class="material-icons-outlined fs-2">
                                inventory
                            </span>
                        </h5>
                    </div>
                @else
                    <div class="row">
                        @foreach ($returns as $return)
                            <div class="col-12 mb-4">
                                <div class="card account-manager-card p-3 d-flex flex-row align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-semibold fs-5 me-4">Return No. {{ $return->returnSupplierID }}</p>
                                    </div>
                    
                                    <div class="d-flex align-items-center flex-grow-1 pe-0">
                                        <span class="vr me-4"></span>
                                        <div class="d-flex flex-row gap-3 align-items-start ps-4">
                                            <div class="text-start me-4" style="width: 8rem;">
                                                <span class="text-muted d-block"><small>Supplier</small></span>
                                                <span class="fw-semibold text-truncate d-block">{{ $return->supplier->supplierName ?? 'Unknown' }}</span>
                                            </div>
                                            <div class="text-start me-4" style="width: 10rem;">
                                                <span class="text-muted d-block"><small>
                                                    {{ $return->completionDate ? 'Date Returned' : ($return->cancellationDate ? 'Cancellation Date' : 'Return Date') }}
                                                </small></span>
                                                <span class="fw-semibold text-truncate d-block">
                                                    {{ \Carbon\Carbon::parse($return->returnDate)->format('M d, Y') }}
                                                </span>
                                            </div>
                                            <div class="text-start me-4" style="width: 10rem;">
                                                <span class="text-muted d-block"><small>Reason</small></span>
                                                <span class="fw-semibold text-truncate d-block">{{ $return->returnSupplierReason }}</span>
                                            </div>
                                            <div class="text-start" style="width: 8rem;">
                                                <span class="text-muted d-block"><small>Status</small></span>
                                                <span class="badge {{ $return->completionDate ? 'bg-success' : ($return->cancellationDate ? 'bg-danger' : 'bg-warning') }} fixed-badge">
                                                    {{ $return->completionDate ? 'Completed' : ($return->cancellationDate ? 'Rejected' : 'Pending') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Dropdown and Status Actions -->
                                    <div class="ms-5">
                                        <x-primary-button class="btn-sm" href="{{ route('supplier_returns.show', $return->returnSupplierID) }}">
                                            <span class="material-icons-outlined">visibility</span>
                                        </x-primary-button>
                                        @if (!$return->completionDate && !$return->cancellationDate)
                                            <div class="dropdown supplier-order-dropdown">
                                                <x-primary-button class="btn-sm mt-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="material-icons-outlined">more_horiz</span>
                                                </x-primary-button>
                                                <ul class="dropdown-menu">
                                                    @if (!$return->completionDate && !$return->cancellationDate)
                                                        <li>
                                                            <form action="{{ route('supplier_returns.complete', $return->returnSupplierID) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="dropdown-item">
                                                                    <span class="material-icons-outlined align-middle me-2">check_circle</span> Complete
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $return->returnSupplierID }}">
                                                                <span class="material-icons-outlined align-middle me-2">cancel</span> Reject
                                                            </button>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                        
                                    </div>
                                    
                                    <!-- Reject Modal -->
                                    <x-modal name="rejectModal-{{ $return->returnSupplierID }}" maxWidth="md">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rejectModal-{{ $return->returnSupplierID }}-label">Reject Return #{{ $return->returnSupplierID }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('supplier_returns.reject', $return->returnSupplierID) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="rejectionReason-{{ $return->returnSupplierID }}" class="form-label">Reason for Rejection</label>
                                                    <textarea class="form-control" id="rejectionReason-{{ $return->returnSupplierID }}" name="rejectionReason" rows="3" required placeholder="Enter the reason for rejecting this return"></textarea>
                                                    @error('rejectionReason')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <x-danger-button type="submit">Reject Return</x-danger-button>
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
                            @if ($returns->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link d-flex justify-content-center align-items-center" href="{{ $returns->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="prev">
                                        <span class="material-icons-outlined">
                                            navigate_before
                                        </span>
                                    </a>
                                </li>
                            @endif
                    
                            <!-- Page Numbers -->
                            @for ($i = 1; $i <= $returns->lastPage(); $i++)
                                <li class="page-item {{ $returns->currentPage() === $i ? 'active' : '' }}">
                                    @if ($returns->currentPage() === $i)
                                        <span class="page-link">{{ $i }}</span>
                                    @else
                                        <a class="page-link" href="{{ $returns->url($i) }}&{{ http_build_query(request()->except('page')) }}">{{ $i }}</a>
                                    @endif
                                </li>
                            @endfor
                    
                            <!-- Next Page Link -->
                            @if ($returns->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link d-flex justify-content-center align-items-center" href="{{ $returns->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="next">
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const updateFilters = () => {
                const params = new URLSearchParams();
                const status = document.querySelector('.category-filter-button.btn-primary')?.dataset.value || '';
                const supplierId = document.getElementById('supplierFilter').value;
                const dateFrom = document.getElementById('dateFrom').value;
                const dateTo = document.getElementById('dateTo').value;
                const sortBy = document.getElementById('sortBy').value;
                const sortDirection = document.getElementById('sortDirection').value;

                if (status) params.set('status', status);
                if (supplierId) params.set('supplierID', supplierId);
                if (dateFrom) params.set('date_from', dateFrom);
                if (dateTo) params.set('date_to', dateTo);
                if (sortBy) params.set('sort_by', sortBy);
                if (sortDirection) params.set('sort_direction', sortDirection);

                window.location.href = `${window.location.pathname}?${params.toString()}`;
            };

            // Status buttons
            document.querySelectorAll('.category-filter-button').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.category-filter-button').forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
                    btn.classList.replace('btn-outline-primary', 'btn-primary');
                    updateFilters();
                });
            });

            // Other filters
            ['supplierFilter', 'dateFrom', 'dateTo', 'sortBy', 'sortDirection'].forEach(id => {
                document.getElementById(id).addEventListener('change', updateFilters);
            });

            // Clear filters
            document.getElementById('clearFilters').addEventListener('click', () => {
                document.querySelectorAll('.category-filter-button').forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
                document.getElementById('supplierFilter').value = '';
                document.getElementById('dateFrom').value = '';
                document.getElementById('dateTo').value = '';
                document.getElementById('sortBy').value = 'adjustmentDatePlaced';
                document.getElementById('sortDirection').value = 'asc';
                window.location.href = window.location.pathname;
            });
        });
    </script>
</x-app-layout>