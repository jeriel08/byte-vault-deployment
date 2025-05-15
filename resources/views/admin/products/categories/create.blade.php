@section('title', 'Create Category | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl fw-semibold mb-0">Add New Category</h2>
            <x-secondary-button href="{{ route('categories.index') }}">
                <span class="material-icons-outlined">arrow_back</span>
                Go back
            </x-secondary-button>
        </div>
        <div class="card account-settings-card">
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="categoryName" class="form-label fw-semibold">Category Name</label>
                        <input type="text" name="categoryName" id="categoryName" class="form-control" required>
                        @error('categoryName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label fw-semibold">Description</label>
                        <textarea name="categoryDescription" id="categoryDescription" class="form-control"></textarea>
                        @error('categoryDescription') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="parentCategoryID" class="form-label fw-semibold">Parent Category</label>
                        <select name="parentCategoryID" id="parentCategoryID" class="form-select">
                            <option value="">None (Top Level)</option>
                            @foreach ($categories as $parent)
                                <option value="{{ $parent->categoryID }}">{{ $parent->categoryName }}</option>
                            @endforeach
                        </select>
                        @error('parentCategoryID') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="categoryStatus" class="form-label fw-semibold">Status</label>
                        <select name="categoryStatus" id="categoryStatus" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        @error('categoryStatus') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <x-primary-button type="submit" class="mt-4">
                        <span class="material-icons-outlined">save</span>
                        Save Category
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>