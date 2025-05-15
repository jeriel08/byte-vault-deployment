@section('title', 'Create Product | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl fw-semibold mb-0">Add New Product</h2>
            <x-secondary-button href="{{ route('products.index') }}">
                <span class="material-icons-outlined">arrow_back</span>
                Go back
            </x-secondary-button>
        </div>
        <div class="card account-settings-card">
            <div class="card-body">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="productName" class="form-label fw-semibold">Product Name</label>
                        <input type="text" name="productName" id="productName" class="form-control" required>
                        @error('productName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label fw-semibold">Description</label>
                        <textarea name="productDescription" id="productDescription" class="form-control"></textarea>
                        @error('productDescription') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="brandID" class="form-label fw-semibold">Brand</label>
                        <select name="brandID" id="brandID" class="form-select select2 custom-select2" data-placeholder="Select a brand" required>
                            <option value="">Select Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->brandID }}">{{ $brand->brandName }}</option>
                            @endforeach
                        </select>
                        @error('brandID') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="categoryID" class="form-label fw-semibold">Category</label>
                        <select name="categoryID" id="categoryID" class="form-select select2 custom-select2" data-placeholder="Select a category" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->categoryID }}">{{ $category->categoryName }}</option>
                            @endforeach
                        </select>
                        @error('categoryID') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="productStatus" class="form-label fw-semibold">Status</label>
                        <select name="productStatus" id="productStatus" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        @error('productStatus') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-4 d-flex justify-content-center align-items-center">
                            <x-primary-button type="submit" class="mt-4 align-items-center">
                                <span class="material-icons-outlined">save</span>
                                Save Product
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: "Select an option",
                allowClear: false // set true if you want an X to clear
            });
        });
    </script>
    @endpush

</x-app-layout>