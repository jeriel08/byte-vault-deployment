@section('title', 'Create Brand | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl fw-semibold mb-0">Add New Brand</h2>
            <x-secondary-button href="{{ route('brands.index') }}">
                <span class="material-icons-outlined">arrow_back</span>
                Go back
            </x-secondary-button>
        </div>

        <div class="card account-settings-card">
            <div class="card-body">
                <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="brandName" class="form-label fw-semibold">Brand Name</label>
                        <input type="text" name="brandName" id="brandName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="brandStatus" class="form-label fw-semibold">Status</label>
                        <select name="brandStatus" id="brandStatus" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="brandProfileImage" class="form-label fw-semibold">Supplier Image</label>
                        <div class="file-upload-wrapper d-flex align-items-center">
                            <input type="file" name="brandProfileImage" id="brandProfileImage" class="file-input" accept="image/*">
                            <label for="brandProfileImage" class="file-button">
                                <span class="material-icons-outlined">upload</span>
                                Choose File
                            </label>
                            <span class="file-name">No file chosen</span>
                        </div>
                    </div>

                    <x-primary-button type="submit" class="mt-4">
                        <span class="material-icons-outlined">save</span>
                        Save Brand
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('brandProfileImage').addEventListener('change', function() {
            const fileName = this.files.length > 0 ? this.files[0].name : 'No file chosen';
            document.querySelector('.file-name').textContent = fileName;
        });
    </script>
</x-app-layout>