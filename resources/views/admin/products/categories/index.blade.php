@section('title', 'Categories | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="row d-flex justify-content-between align-items-center mb-4">
            <!-- Search Form -->
            <div class="col-5">
                <form action="{{ route('categories.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Search categories..." value="{{ request('search') }}">
                    <x-primary-button type="submit" class="py-2">
                        <span class="material-icons-outlined">search</span>
                        Search
                    </x-primary-button>
                </form>
            </div>
            <div class="col-auto d-flex align-items-center gap-2">
                <x-primary-button href="{{ route('categories.create') }}">
                    <span class="material-icons-outlined">add</span>
                    Add Category
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
            @if ($parentCategories->isEmpty() && $standaloneCategories->isEmpty() && $childCategories->isEmpty())
                <div class="col-12">
                    <div class="card account-manager-card text-center p-5">
                        <h5 class="text-muted d-flex justify-content-center align-items-center gap-3">
                            No categories yet.
                            <span class="material-icons-outlined fs-2">category</span>
                        </h5>
                    </div>
                </div>
            @else
                <!-- Accordion for Top-Level Parent Categories with Children -->
                <div class="category-accordion" id="categoriesAccordion">
                    @foreach ($parentCategories as $parent)
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="heading{{ $parent->categoryID }}">
                                <button class="accordion-button p-4 d-flex justify-content-between align-items-center collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $parent->categoryID }}" aria-expanded="false" aria-controls="collapse{{ $parent->categoryID }}">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 fw-semibold">{{ $parent->categoryName }}</h5>
                                        <p class="mb-1">{{ $parent->categoryDescription ?? 'No description' }}</p>
                                        <span class="badge {{ $parent->categoryStatus === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $parent->categoryStatus }}
                                        </span>
                                    </div>
                                    <span class="material-icons-outlined accordion-icon ms-3 fs-3">
                                        expand_more
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse{{ $parent->categoryID }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $parent->categoryID }}" data-bs-parent="#categoriesAccordion">                                <div class="accordion-body">
                                    <!-- Edit Button for Parent (moved outside collapse button) -->
                                    <x-primary-button href="{{ route('categories.edit', $parent->categoryID) }}" class="mb-3" style="">
                                        <span class="material-icons-outlined">edit</span>
                                        Edit
                                    </x-primary-button>
                                    @foreach ($parent->children as $child)
                                        @if ($child->children->isEmpty())
                                            <!-- Child without children: Card -->
                                            <div class="card account-manager-card p-3 d-flex flex-row align-items-center mb-3">
                                                <div class="flex-grow-1 ms-2">
                                                    <h5 class="mb-1 fw-semibold">{{ $child->categoryName }}</h5>
                                                    <p class="mb-1">{{ $child->categoryDescription ?? 'No description' }}</p>
                                                    <span class="badge {{ $child->categoryStatus === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $child->categoryStatus }}
                                                    </span>
                                                </div>
                                                <x-primary-button href="{{ route('categories.edit', $child->categoryID) }}">
                                                    <span class="material-icons-outlined">edit</span>
                                                    Edit
                                                </x-primary-button>
                                            </div>
                                        @else
                                            <!-- Child with children: Nested Accordion -->
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading{{ $child->categoryID }}">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $child->categoryID }}" aria-expanded="false" aria-controls="collapse{{ $child->categoryID }}">
                                                        <div class="flex-grow-1">
                                                            <h5 class="mb-1 fw-semibold">{{ $child->categoryName }}</h5>
                                                            <p class="mb-1">{{ $child->categoryDescription ?? 'No description' }}</p>
                                                            <span class="badge {{ $child->categoryStatus === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $child->categoryStatus }}
                                                            </span>
                                                        </div>
                                                    </button>
                                                    <!-- Edit Button for Child with Children -->
                                                    <x-primary-button href="{{ route('categories.edit', $child->categoryID) }}" class="ms-2 me-3 align-self-center" style="flex-shrink: 0;">
                                                        <span class="material-icons-outlined">edit</span>
                                                        Edit
                                                    </x-primary-button>
                                                </h2>
                                                <div id="collapse{{ $child->categoryID }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $child->categoryID }}">
                                                    <div class="accordion-body">
                                                        @foreach ($child->children as $grandchild)
                                                            <div class="card account-manager-card p-3 d-flex flex-row align-items-center mb-3">
                                                                <div class="flex-grow-1 ms-2">
                                                                    <h5 class="mb-1 fw-semibold">{{ $grandchild->categoryName }}</h5>
                                                                    <p class="mb-1">{{ $grandchild->categoryDescription ?? 'No description' }}</p>
                                                                    <span class="badge {{ $grandchild->categoryStatus === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                                                        {{ $grandchild->categoryStatus }}
                                                                    </span>
                                                                </div>
                                                                <x-primary-button href="{{ route('categories.edit', $grandchild->categoryID) }}">
                                                                    <span class="material-icons-outlined">edit</span>
                                                                    Edit
                                                                </x-primary-button>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Standalone Top-Level Categories (no children) -->
                @foreach ($standaloneCategories as $standalone)
                    <div class="col-12 mb-3">
                        <div class="card account-manager-card px-3 py-4 d-flex flex-row align-items-center">
                            <div class="flex-grow-1 ms-2">
                                <h5 class="mb-1 fw-semibold">{{ $standalone->categoryName }}</h5>
                                <p class="mb-1">{{ $standalone->categoryDescription ?? 'No description' }}</p>
                                <span class="badge {{ $standalone->categoryStatus === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $standalone->categoryStatus }}
                                </span>
                            </div>
                            <x-primary-button href="{{ route('categories.edit', $standalone->categoryID) }}">
                                <span class="material-icons-outlined">edit</span>
                                Edit
                            </x-primary-button>
                        </div>
                    </div>
                @endforeach

                <!-- Child Categories (only shown in search results) -->
                @if (request('search') && $childCategories->isNotEmpty())
                    <h5 class="mt-3 mb-3">Child Categories</h5>
                    @foreach ($childCategories as $child)
                        <div class="col-12 mb-3">
                            <div class="card account-manager-card px-3 py-4 d-flex flex-row align-items-center">
                                <div class="flex-grow-1 ms-2">
                                    <h5 class="mb-1 fw-semibold">{{ $child->categoryName }}</h5>
                                    <p class="mb-1">{{ $child->categoryDescription ?? 'No description' }}</p>
                                    <p class="mb-0 text-muted d-flex align-items-center gap-2">
                                        <span class="badge {{ $child->categoryStatus === 'Active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $child->categoryStatus }}
                                        </span> •
                                        @if ($child->parent)
                                            <span>Parent: {{ $child->parent->categoryName }}</span>
                                        @endif
                                    </p>
                                    
                                </div>
                                <x-primary-button href="{{ route('categories.edit', $child->categoryID) }}">
                                    <span class="material-icons-outlined">edit</span>
                                    Edit
                                </x-primary-button>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="d-flex justify-content-center mt-4">
                    <ul class="pagination">
                        <!-- Previous Page Link -->
                        @if ($categories->onFirstPage())
                            <li class="page-item disabled">
                                <span class="material-icons-outlined page-link">navigate_before</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link d-flex justify-content-center align-items-center" href="{{ $categories->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="prev">
                                    <span class="material-icons-outlined">navigate_before</span>
                                </a>
                            </li>
                        @endif
                
                        <!-- Page Numbers -->
                        @for ($i = 1; $i <= $categories->lastPage(); $i++)
                            <li class="page-item {{ $categories->currentPage() === $i ? 'active' : '' }}">
                                @if ($categories->currentPage() === $i)
                                    <span class="page-link">{{ $i }}</span>
                                @else
                                    <a class="page-link" href="{{ $categories->url($i) }}&{{ http_build_query(request()->except('page')) }}">{{ $i }}</a>
                                @endif
                            </li>
                        @endfor
                
                        <!-- Next Page Link -->
                        @if ($categories->hasMorePages())
                            <li class="page-item">
                                <a class="page-link d-flex justify-content-center align-items-center" href="{{ $categories->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" rel="next">
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