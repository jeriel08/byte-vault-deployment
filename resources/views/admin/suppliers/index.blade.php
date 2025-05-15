@section('title', 'Suppliers | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="row d-flex justify-content-between align-items-center mb-4">
            <!-- Search Form -->
            <div class="col-5">
                <form action="{{ route('suppliers.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search suppliers..." value="{{ request('search') }}">
                    <x-primary-button type="submit" class="py-2">
                        <span class="material-icons-outlined">search</span>
                        Search
                    </x-primary-button>
                </form>
            </div>
            <div class="col-auto d-flex justify-content-center">
                <x-primary-button href="{{ route('suppliers.create') }}">
                    <span class="material-icons-outlined">add</span>
                    Add New Supplier
                </x-primary-button>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="row">
            @if ($suppliers->isEmpty())
                <!-- No Suppliers Card -->
                <div class="col-12">
                    <div class="card account-manager-card text-center p-5">
                        <h5 class="text-muted d-flex justify-content-center align-items-center gap-3">
                            There's no supplier yet. 
                            <span class="material-icons-outlined fs-2">
                                inventory_2
                            </span>
                        </h5>
                    </div>
                </div>
            @else
                <!-- Supplier Cards -->
                @foreach ($suppliers as $supplier)
                    <div class="col-12 mb-3">
                        <div class="card account-manager-card p-3 d-flex flex-row align-items-center">
                            <img src="{{ asset('storage/' . $supplier->supplierProfileImage) }}" 
                                alt="{{ $supplier->supplierName }}" 
                                class="supplier-image rounded-circle me-3" 
                                style="width: 150px; height: 150px; object-fit: cover;" />
    
                            <!-- Supplier Details -->
                            <div class="flex-grow-1 ms-2">
                                <h5 class="mb-1">{{ $supplier->supplierName }}</h5>
                                <p class="mb-1">
                                    <strong>Address:</strong> {{ $supplier->supplierAddress }}<br />
                                    <strong>Phone:</strong> {{ $supplier->supplierPhoneNumber }}
                                </p>
                                <span class="badge {{ $supplier->supplierStatus == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $supplier->supplierStatus }}
                                </span>
                            </div>
    
                            <!-- Buttons -->
                            <div class="d-flex flex-column gap-2">
                                <x-primary-button href="tel:{{ $supplier->supplierPhoneNumber }}"
                                    class="btn-sm"> 
                                    <span class="material-icons-outlined">phone</span>
                                </x-primary-button>
    
                                <x-primary-button href="{{ route('suppliers.edit', $supplier->supplierID) }}" >
                                    <span class="material-icons-outlined">edit</span>
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-center">
                    <ul class="pagination">
                        <!-- Previous Page Link -->
                        @if ($suppliers->onFirstPage())
                            <li class="page-item disabled">
                                <span class="material-icons-outlined page-link">
                                    navigate_before
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link d-flex justify-content-center align-items-center" href="{{ $suppliers->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="prev">
                                    <span class="material-icons-outlined">
                                        navigate_before
                                    </span>
                                </a>
                            </li>
                        @endif
                
                        <!-- Page Numbers -->
                        @for ($i = 1; $i <= $suppliers->lastPage(); $i++)
                            <li class="page-item {{ $suppliers->currentPage() === $i ? 'active' : '' }}">
                                @if ($suppliers->currentPage() === $i)
                                    <span class="page-link">{{ $i }}</span>
                                @else
                                    <a class="page-link" href="{{ $suppliers->url($i) }}&{{ http_build_query(request()->except('page')) }}">{{ $i }}</a>
                                @endif
                            </li>
                        @endfor
                
                        <!-- Next Page Link -->
                        @if ($suppliers->hasMorePages())
                            <li class="page-item">
                                <a class="page-link d-flex justify-content-center align-items-center" href="{{ $suppliers->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="next">
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

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $suppliers->links() }}
        </div>
    </div>
</x-app-layout>