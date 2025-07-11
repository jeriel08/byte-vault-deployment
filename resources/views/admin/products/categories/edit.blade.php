@section('title', 'Edit Category | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl mb-0">Edit Category: <strong>{{ $category->categoryName }}</strong></h2>
            <x-secondary-button href="{{ route('categories.index') }}">
                <span class="material-icons-outlined">arrow_back</span>
                Go back
            </x-secondary-button>
        </div>
        <div class="card account-settings-card">
            <div class="card-body">
                <form action="{{ route('categories.update', $category->categoryID) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="categoryName" class="form-label fw-semibold">Category Name</label>
                        <input type="text" name="categoryName" id="categoryName" class="form-control" value="{{ $category->categoryName }}" required>
                        @error('categoryName') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label fw-semibold">Description</label>
                        <textarea name="categoryDescription" id="categoryDescription" class="form-control">{{ $category->categoryDescription }}</textarea>
                        @error('categoryDescription') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="parentCategoryID" class="form-label fw-semibold">Parent Category</label>
                        <select name="parentCategoryID" id="parentCategoryID" class="form-select">
                            <option value="">None (Top Level)</option>
                            @foreach ($categories as $parent)
                                <option value="{{ $parent->categoryID }}" {{ $category->parentCategoryID == $parent->categoryID ? 'selected' : '' }}>
                                    {{ $parent->categoryName }}
                                </option>
                            @endforeach
                        </select>
                        @error('parentCategoryID') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="categoryStatus" class="form-label fw-semibold">Status</label>
                        <select name="categoryStatus" id="categoryStatus" class="form-select" required>
                            <option value="Active" {{ $category->categoryStatus === 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $category->categoryStatus === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('categoryStatus') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-4 d-flex justify-content-center">
                            <x-primary-button type="submit" class="mt-4">
                                <span class="material-icons-outlined">save</span>
                                Update Category
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>