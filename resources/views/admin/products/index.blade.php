@section('title', 'Products | ByteVault')

<x-app-layout>
    <div class="container-fluid py-6 position-relative">
        <!-- Header with Search and Add Product -->
        <div class="d-flex justify-content-between align-items-center mx-4 mb-4">
            <form method="GET" action="{{ route('products.index') }}" id="searchForm" class="w-50">
                <div class="input-group">
                    <input type="text" class="search-input" name="search" 
                           placeholder="Search by product name" aria-label="Search products"
                           value="{{ request('search') }}">
                    <button class="search-button d-flex align-items-center justify-content-center" type="submit">
                        <span class="material-icons-outlined">search</span>
                    </button>
                </div>
            </form>
            <x-primary-button href="{{ route('products.create') }}" class="py-2">
                <span class="material-icons-outlined">add</span>
                Add Product
            </x-primary-button>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4 mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row mx-2">
            <!-- Product Filter Panel -->
            <div class="col-lg-3 col-md-4 col-sm-12">
                <div class="card filter-panel">
                    <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                        <div class="card-body p-3">
                            <h5 class="fw-semibold mb-3">Filter Products</h5>
                            
                            <!-- Product Status -->
                            <label class="fw-semibold mb-2">Product Status</label>
                            <div class="btn-group d-flex flex-wrap gap-2 mb-3" role="group">
                                <button type="submit" name="productStatus" value="Active" 
                                        class="btn category-filter-button flex-grow-1 {{ request('productStatus') === 'Active' ? 'active' : '' }}">
                                    <span class="badge me-2">{{ $allProducts->where('productStatus', 'Active')->count() }}</span> Active
                                </button>
                                <button type="submit" name="productStatus" value="Inactive" 
                                        class="btn category-filter-button flex-grow-1 {{ request('productStatus') === 'Inactive' ? 'active' : '' }}">
                                    <span class="badge me-2">{{ $allProducts->where('productStatus', 'Inactive')->count() }}</span> Inactive
                                </button>
                                <button type="submit" name="productStatus" value="All" 
                                        class="btn category-filter-button flex-grow-1 {{ !request('productStatus') || request('productStatus') === 'All' ? 'active' : '' }}">
                                    <span class="badge me-2">{{ $allProducts->count() }}</span> All
                                </button>
                            </div>
            
                            <hr>
            
                            <!-- Sort By -->
                            <label class="fw-semibold mb-2">Sort By</label>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sortBy" id="priceAsc" 
                                           value="price_asc" {{ request('sortBy') === 'price_asc' ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <label class="form-check-label" for="priceAsc">Price: Low to High</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sortBy" id="priceDesc" 
                                           value="price_desc" {{ request('sortBy') === 'price_desc' ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <label class="form-check-label" for="priceDesc">Price: High to Low</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sortBy" id="nameAsc" 
                                           value="name_asc" {{ request('sortBy') === 'name_asc' ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <label class="form-check-label" for="nameAsc">Name: A-Z</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sortBy" id="nameDesc" 
                                           value="name_desc" {{ request('sortBy') === 'name_desc' ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <label class="form-check-label" for="nameDesc">Name: Z-A</label>
                                </div>
                            </div>
            
                            <hr>
            
                            <!-- Category -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="fw-semibold">Category</label>
                                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
                                    <span class="material-icons-outlined">settings</span>
                                </a>
                            </div>
                            <div class="accordion mb-3" id="categoryAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="categoryHeading">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#categoryCollapse" aria-expanded="false" aria-controls="categoryCollapse">
                                            Select Categories
                                        </button>
                                    </h2>
                                    <div id="categoryCollapse" class="accordion-collapse collapse" 
                                         aria-labelledby="categoryHeading" data-bs-parent="#categoryAccordion">
                                        <div class="accordion-body">
                                            @foreach ($allProducts->pluck('category')->unique('categoryID') as $category)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="category[]" 
                                                        id="category{{ $category->categoryID }}" value="{{ $category->categoryID }}"
                                                        {{ in_array($category->categoryID, request('category', [])) ? 'checked' : '' }}
                                                        onchange="this.form.submit()">
                                                    <label class="form-check-label" for="category{{ $category->categoryID }}">
                                                        {{ $category->categoryName }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
            
                            <hr>
            
                            <!-- Brand -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="fw-semibold">Brand</label>
                                <a href="{{ route('brands.index') }}" class="btn btn-outline-secondary btn-sm d-flex justify-content-center align-items-center">
                                    <span class="material-icons-outlined">settings</span>
                                </a>
                            </div>
                            <div class="accordion mb-3" id="brandAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="brandHeading">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#brandCollapse" aria-expanded="false" aria-controls="brandCollapse">
                                            Select Brands
                                        </button>
                                    </h2>
                                    <div id="brandCollapse" class="accordion-collapse collapse" 
                                         aria-labelledby="brandHeading" data-bs-parent="#brandAccordion">
                                        <div class="accordion-body">
                                            @foreach ($allProducts->pluck('brand')->unique('brandID') as $brand)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="brand[]" 
                                                        id="brand{{ $brand->brandID }}" value="{{ $brand->brandID }}"
                                                        {{ in_array($brand->brandID, request('brand', [])) ? 'checked' : '' }}
                                                        onchange="this.form.submit()">
                                                    <label class="form-check-label" for="brand{{ $brand->brandID }}">
                                                        {{ $brand->brandName }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
            
                            <hr>
            
                            <!-- Reset Filters -->
                            <button type="button" onclick="window.location='{{ route('products.index') }}'" 
                                    class="btn btn-outline-danger w-100">Reset Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Product Cards -->
            <div class="col-lg-9 col-md-8 col-sm-12 product-table" id="productTable">
                @if ($products->isEmpty())
                    <div class="text-center p-5">
                        <h5 class="text-muted d-flex justify-content-center align-items-center gap-3">
                            No products yet.
                            <span class="material-icons-outlined fs-2">inventory</span>
                        </h5>
                    </div>
                @else
                    <div class="row">
                        @foreach ($products as $product)
                            <div class="col-12 mb-4">
                                <div class="card account-manager-card p-3 d-flex flex-row align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 fw-semibold fs-4">{{ $product->productName }}</h5>
                                        <p class="mb-0 text-muted d-flex align-items-center gap-1">
                                            {{ $product->category->categoryName }} • 
                                            {{ $product->brand->brandName }} •
                                            {{-- Product status badge --}}
                                            @if ($product->productStatus === 'Active')
                                                <span class="badge bg-success fixed-badge">Active</span>•
                                            @elseif ($product->productStatus === 'Inactive')
                                                <span class="badge bg-danger fixed-badge">Inactive</span>•
                                            @endif 
                                            <strong>Stock: {{ $product->stockQuantity }}</strong>
                                        
                                            {{-- Low stock warning badge --}}
                                            @if ($product->stockQuantity >= 0 && $product->stockQuantity <= 10)
                                                <a href="{{ route('supplier_orders.index', ['supplier_id' => $product->supplierID]) }}" class="badge bg-danger ms-2">
                                                    <span class="material-icons-outlined danger-badge">priority_high</span>
                                                </a>
                                            @endif
                                        
                                            
                                        </p>                                        
                                    </div>
                    
                                    <div class="d-flex align-items-center mx-3 price-section">
                                        <span class="vr me-3"></span>
                                        <div class="d-flex flex-column">
                                            <span class="text-muted"><small>Selling Price</small></span>
                                            <span class="fw-semibold fs-5 d-flex align-items-center gap-1" style="width: 7rem;">
                                                ₱{{ number_format($product->price, 2) }}
                                                @if ($product->price == 0)
                                                    <a href="{{ route('supplier_orders.index', ['supplier_id' => $product->supplierID]) }}" class="badge bg-danger ms-2">
                                                        <span class="material-icons-outlined danger-badge">priority_high</span>
                                                    </a>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="ms-5 d-flex flex-column gap-2">
                                        <x-primary-button class="btn-sm" data-bs-toggle="modal" data-bs-target="#productDetailsModal-{{ $product->productID }}">
                                            <span class="material-icons-outlined">visibility</span>
                                        </x-primary-button>
                                        <x-primary-button href="{{ route('products.edit', $product->productID) }}" class="btn-sm">
                                            <span class="material-icons-outlined">edit</span>
                                        </x-primary-button>
                                    </div>

                                    <!-- Modal for Product Details -->
                                    <x-modal name="productDetailsModal-{{ $product->productID }}" maxWidth="lg">
                                        <div class="modal-header custom-modal-header">
                                            <h5 class="modal-title" id="productDetailsModal-{{ $product->productID }}-label">{{ $product->productName }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body custom-modal-body">
                                            <p><strong class="label">Category:</strong> <span class="value">{{ $product->category->categoryName }}</span></p>
                                            <p><strong class="label">Brand:</strong> <span class="value">{{ $product->brand->brandName }}</span></p>
                                            <p>
                                                <strong class="label">Price:</strong> 
                                                <span class="value d-inline-flex align-items-center gap-1">
                                                    ₱{{ number_format($product->price, 2) }}
                                                    @if ($product->price == 0)
                                                        <a href="{{ route('supplier_orders.index', ['supplier_id' => $product->supplierID]) }}" class="badge bg-danger ms-1">
                                                            <span class="material-icons-outlined danger-badge">priority_high</span>
                                                        </a>
                                                    @endif
                                                </span>
                                            </p>
                                            <p>
                                                <strong class="label">Stock:</strong> 
                                                <span class="value d-inline-flex align-items-center gap-1">
                                                    {{ $product->stockQuantity }}
                                                    @if ($product->stockQuantity >= 0 && $product->stockQuantity <= 10)
                                                        <a href="{{ route('supplier_orders.index', ['supplier_id' => $product->supplierID]) }}" class="badge bg-danger ms-1">
                                                            <span class="material-icons-outlined danger-badge">priority_high</span>
                                                        </a>
                                                    @endif
                                                </span>
                                            </p>
                                            <p><strong class="label">Description:</strong> <span class="value">{{ $product->productDescription ?? 'No description available' }}</span></p>
                                            <p><strong class="label">Status:</strong> <span class="value">{{ $product->productStatus }}</span></p>
                                        </div>
                                        <div class="modal-footer custom-modal-footer">
                                            <button type="button" class="btn custom-btn-close" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </x-modal>
                                </div>
                            </div>
                        @endforeach
                    </div>

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
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            const searchForm = document.getElementById('searchForm');
        
            // Submit on Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchForm.submit();
                }
            });
        
            // Debounced search on typing
            let timeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    searchForm.submit();
                }, 500); // 500ms delay after typing stops
            });
        });
    </script>
</x-app-layout>