@section('title', 'Brands | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="row d-flex justify-content-between align-items-center mb-4">
            <!-- Search Form -->
            <div class="col-5">
                <form action="{{ route('brands.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search brands..." value="{{ request('search') }}">
                    <x-primary-button type="submit" class="py-2">
                        <span class="material-icons-outlined">search</span>
                        Search
                    </x-primary-button>
                </form>
            </div>
            <div class="col-auto d-flex align-items-center gap-2">
                {{-- Add Brand Button --}}
                <x-primary-button href="{{ route('brands.create') }}">
                    <span class="material-icons-outlined">add</span>
                    Add Brand
                </x-primary-button>
                <x-secondary-button href="{{ route('products.index') }}">
                    <span class="material-icons-outlined">arrow_back</span>
                    Go back
                </x-secondary-button>
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
            @if ($brands->isEmpty())
                <!-- No Brands Card -->
                <div class="col-12">
                    <div class="card account-manager-card text-center p-5">
                        <h5 class="text-muted d-flex justify-content-center align-items-center gap-3">
                            There's no brand yet.
                            <span class="material-icons-outlined fs-2">
                                inventory_2
                            </span>
                        </h5>
                    </div>
                </div>
            @else
                <!-- Brand Cards -->
                @foreach ($brands as $brand)
                    <div class="col-12 mt-3">
                        <div class="card account-manager-card p-3 d-flex flex-row align-items-center">
                            <img src="{{ $brand->brandProfileImage ? asset('storage/' . $brand->brandProfileImage) : asset('images/default-brand.png') }}"
                                alt="{{ $brand->brandName }}"
                                class="supplier-image rounded-circle me-3"
                                style="width: 150px; height: 150px; object-fit: cover;" />

                            <!-- Brand Details -->
                            <div class="flex-grow-1 ms-2">
                                <h5 class="mb-1 fs-2 fw-semibold">{{ $brand->brandName }}</h5>
                                <span class="badge {{ $brand->brandStatus === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $brand->brandStatus }}
                                </span>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex flex-column gap-2">
                                <x-primary-button href="{{ route('brands.edit', $brand->brandID) }}">
                                    <span class="material-icons-outlined">edit</span>
                                    Edit
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    <ul class="pagination">
                        <!-- Previous Page Link -->
                        @if ($brands->onFirstPage())
                            <li class="page-item disabled">
                                <span class="material-icons-outlined page-link">navigate_before</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link d-flex justify-content-center align-items-center" href="{{ $brands->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="prev">
                                    <span class="material-icons-outlined">navigate_before</span>
                                </a>
                            </li>
                        @endif
                
                        <!-- Page Numbers -->
                        @for ($i = 1; $i <= $brands->lastPage(); $i++)
                            <li class="page-item {{ $brands->currentPage() === $i ? 'active' : '' }}">
                                @if ($brands->currentPage() === $i)
                                    <span class="page-link">{{ $i }}</span>
                                @else
                                    <a class="page-link" href="{{ $brands->url($i) }}&{{ http_build_query(request()->except('page')) }}">{{ $i }}</a>
                                @endif
                            </li>
                        @endfor
                
                        <!-- Next Page Link -->
                        @if ($brands->hasMorePages())
                            <li class="page-item">
                                <a class="page-link d-flex justify-content-center align-items-center" href="{{ $brands->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="next">
                                    <span class="material-icons-outlined">navigate_next</span>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="material-icons-outlined page-link">navigate_next</span>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>