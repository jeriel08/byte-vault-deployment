@section('title', 'Adjustments | ByteVault')

<x-app-layout>
    <div class="container-fluid mx-auto px-4 py-6 position-relative">
        <!-- Header with Search and Add Adjustment -->
        <div class="d-flex justify-content-between align-items-center mx-1 mb-4">
            <div class="input-group w-50">
                <input type="text" name="search" id="searchInput" class="search-input" placeholder="Search by adjustment ID" aria-label="Search adjustmentID" value="{{ request('search') }}">
                <button class="btn btn-outline-secondary search-button d-flex align-items-center justify-content-center" type="button" id="searchButton">
                    <span class="material-icons-outlined">search</span>
                </button>
            </div>
            <x-primary-button href="{{ route('adjustments.create') }}" class="py-2">
                <span class="material-icons-outlined">add</span>
                New Adjustment
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
                {{-- Wrap content in a form for easier JS handling --}}
                <form id="filterForm" method="GET" action="{{ route('adjustments.index') }}">
                    {{-- Hidden input for the reason filter, controlled by JS --}}
                    <input type="hidden" name="reason" id="reasonInput" value="{{ request('reason') }}">
            
                    <div class="card filter-panel">
                        <div class="card-body p-3">
                            <h5 class="fw-semibold">Filters</h5>
                            <hr>
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">Date Range</label>
                                <div>
                                    <label for="date_from" class="form-label fs-6 text-muted"><small>From</small></label>
                                    <input type="date" name="date_from" id="date_from" class="form-control form-control-sm mb-1" value="{{ request('date_from') }}">
                                </div>
                                <div>
                                    <label for="date_to" class="form-label fs-6 text-muted"><small>To</small></label>
                                    <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <hr>
                            <div class="mb-4">
                                <label for="sortBy" class="form-label fw-semibold mb-2">Sort By</label>
                                {{-- Update option values and add selected logic --}}
                                <select class="form-select form-select-sm" id="sortBy" name="sort_by"> {{-- Use form-select-sm for consistency --}}
                                     <option value="adjustmentDate_desc" {{ request('sort_by', 'adjustmentDate_desc') == 'adjustmentDate_desc' ? 'selected' : '' }}>Adj. Date (Newest)</option>
                                     <option value="adjustmentDate_asc" {{ request('sort_by') == 'adjustmentDate_asc' ? 'selected' : '' }}>Adj. Date (Oldest)</option>
                                     <option value="totalQuantity_desc" {{ request('sort_by') == 'totalQuantity_desc' ? 'selected' : '' }}>Total Qty (High-Low)</option>
                                     <option value="totalQuantity_asc" {{ request('sort_by') == 'totalQuantity_asc' ? 'selected' : '' }}>Total Qty (Low-High)</option>
                                     <option value="created_by_desc" {{ request('sort_by') == 'created_by_desc' ? 'selected' : '' }}>Created By (Z-A)</option>
                                     <option value="created_by_asc" {{ request('sort_by') == 'created_by_asc' ? 'selected' : '' }}>Created By (A-Z)</option>
                                </select>
                            </div>
                            <hr>
                            <div>
                                <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">Clear Filters</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Adjustment List -->
            <div class="col-lg-9 col-md-8 col-sm-12 product-table" id="adjustmentTable">
                @if ($adjustments->isEmpty())
                    <div class="card account-manager-card text-center p-5">
                        <h5 class="text-muted d-flex justify-content-center align-items-center gap-3">
                            No adjustments yet. 
                            <span class="material-icons-outlined fs-2">
                                inventory
                            </span>
                        </h5>
                    </div>
                @else
                    <div class="row">
                        @foreach ($adjustments as $adjustment)
                            <div class="col-12 mb-4">
                                <div class="card account-manager-card p-3 d-flex flex-row align-items-center">
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-semibold fs-5 me-4">Adjustment No. {{ $adjustment->adjustmentID }}</p>
                                    </div>
                    
                                    <div class="d-flex align-items-center flex-grow-1 pe-0">
                                        <span class="vr me-4"></span>
                                        <div class="d-flex flex-row gap-3 align-items-start ps-4">
                                            <div class="text-start me-4" style="width: 8rem;">
                                                <span class="text-muted d-block"><small>Created By</small></span>
                                                <span class="fw-semibold text-truncate d-block">{{ $adjustment->createdBy->full_name ?? 'Unknown' }}</span>                                            </div>
                                            <div class="text-start me-4" style="width: 10rem;">
                                                <span class="text-muted d-block"><small>Adjustment Date</small></span>
                                                <span class="fw-semibold text-truncate d-block">
                                                    {{ \Carbon\Carbon::parse($adjustment->adjustmentDate)->format('M d, Y') }}
                                                </span>
                                            </div>
                                            <div class="text-start me-4" style="width: 10rem;">
                                                <span class="text-muted d-block"><small>Reason</small></span>
                                                <span class="fw-semibold text-truncate d-block">{{ $adjustment->adjustmentReason }}</span>
                                            </div>
                                            <div class="text-start" style="width: 8rem;">
                                                <span class="text-muted d-block"><small>Quantity Adjusted</small></span>
                                                <span class="fw-semibold text-truncate d-block">{{ $adjustment->stockOut->totalQuantity }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Dropdown for options -->
                                    <div class="ms-5">
                                        <x-primary-button class="btn-sm" href="{{ route('adjustments.show', $adjustment->adjustmentID) }}">
                                            <span class="material-icons-outlined">visibility</span>
                                        </x-primary-button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center">
                        <ul class="pagination">
                            <!-- Previous Page Link -->
                            @if ($adjustments->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Previous</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link d-flex justify-content-center align-items-center" href="{{ $adjustments->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="prev">
                                        <span class="material-icons-outlined">
                                            navigate_before
                                        </span>
                                    </a>
                                </li>
                            @endif
                    
                            <!-- Page Numbers -->
                            @for ($i = 1; $i <= $adjustments->lastPage(); $i++)
                                <li class="page-item {{ $adjustments->currentPage() === $i ? 'active' : '' }}">
                                    @if ($adjustments->currentPage() === $i)
                                        <span class="page-link">{{ $i }}</span>
                                    @else
                                        <a class="page-link" href="{{ $adjustments->url($i) }}&{{ http_build_query(request()->except('page')) }}">{{ $i }}</a>
                                    @endif
                                </li>
                            @endfor
                    
                            <!-- Next Page Link -->
                            @if ($adjustments->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link d-flex justify-content-center align-items-center" href="{{ $adjustments->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="next">
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
        document.addEventListener('DOMContentLoaded', function() {
            // Elements from the Filter Panel Form
            const filterForm = document.getElementById('filterForm');
            const reasonButtons = filterForm.querySelectorAll('.category-filter-button[data-filter="reason"]');
            const reasonInput = filterForm.querySelector('#reasonInput'); // Hidden input for reason
            const dateFromInput = filterForm.querySelector('#date_from');
            const dateToInput = filterForm.querySelector('#date_to');
            const sortBySelect = filterForm.querySelector('#sortBy');
            const clearFiltersBtn = filterForm.querySelector('#clearFilters');

            // Separate Search Bar Elements (use the IDs added above)
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');

            let debounceTimer;

            // --- CORE FUNCTION ---
            // Reads values from BOTH search bar and filter form, then navigates
            function applyFilters() {
                const params = new URLSearchParams();

                // 1. Read Search Input value
                if (searchInput && searchInput.value) {
                    params.set('search', searchInput.value);
                }

                // 2. Read Filter Form values
                if (reasonInput && reasonInput.value) { // Use hidden input for reason
                    params.set('reason', reasonInput.value);
                }
                if (dateFromInput && dateFromInput.value) {
                    params.set('date_from', dateFromInput.value);
                }
                if (dateToInput && dateToInput.value) {
                    params.set('date_to', dateToInput.value);
                }
                if (sortBySelect && sortBySelect.value) {
                    params.set('sort_by', sortBySelect.value);
                }

                // 3. Navigate
                // Use filterForm.action as the base URL (adjust if needed)
                window.location.href = `${filterForm.action}?${params.toString()}`;
            }

            // --- EVENT LISTENERS ---

            // Filter Panel Listeners (Reason, Date, Sort)
            reasonButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const currentValue = this.dataset.value;
                    if (this.classList.contains('btn-primary')) {
                        this.classList.remove('btn-primary');
                        this.classList.add('btn-outline-primary');
                        if(reasonInput) reasonInput.value = '';
                    } else {
                        reasonButtons.forEach(btn => {
                            btn.classList.remove('btn-primary');
                            btn.classList.add('btn-outline-primary');
                        });
                        this.classList.remove('btn-outline-primary');
                        this.classList.add('btn-primary');
                        if(reasonInput) reasonInput.value = currentValue;
                    }
                    applyFilters(); // Apply combined filters
                });
            });

            [dateFromInput, dateToInput, sortBySelect].forEach(element => {
                if (element) {
                    element.addEventListener('change', applyFilters); // Apply combined filters
                }
            });

            // Separate Search Bar Listeners
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        applyFilters(); // Apply combined filters
                    }, 500);
                });

                // Optional: Allow Enter key in search input to trigger filtering
                searchInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault(); // Prevent potential form submission if it were in one
                        clearTimeout(debounceTimer); // Clear pending debounce
                        applyFilters(); // Apply combined filters immediately
                    }
                });
            }

            if (searchButton) {
                searchButton.addEventListener('click', function() {
                    clearTimeout(debounceTimer); // Clear pending debounce if user clicks button
                    applyFilters(); // Apply combined filters immediately
                });
            }


            // Clear Filters Button Listener (within Filter Panel)
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    // Clear Filter Panel inputs
                    reasonButtons.forEach(btn => {
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-outline-primary');
                    });
                    if(reasonInput) reasonInput.value = '';
                    if(dateFromInput) dateFromInput.value = '';
                    if(dateToInput) dateToInput.value = '';
                    if(sortBySelect) sortBySelect.value = 'adjustmentDate_desc'; // Reset sort

                    // ALSO Clear the separate Search Input
                    if (searchInput) {
                        searchInput.value = '';
                    }

                    // Navigate to base URL (without any parameters)
                    window.location.href = filterForm.action;
                });
            }

            // Prevent accidental submission of filterForm via Enter key in date/select fields
            filterForm.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA' && event.target.tagName !== 'BUTTON') {
                    event.preventDefault();
                }
            });

        });
    </script>
</x-app-layout>