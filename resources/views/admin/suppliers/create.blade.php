@section('title', 'Create Supplier | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl fw-semibold">Add New Supplier</h2>
            <x-secondary-button href="{{ route('suppliers.index') }}">
                <span class="material-icons-outlined">arrow_back</span>
                Go back
            </x-secondary-button>
        </div>
        <div class="card account-settings-card">
            <div class="card-body">
                <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="supplierName" class="form-label fw-semibold">Supplier Name</label>
                        <input type="text" class="form-control" id="supplierName" name="supplierName" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplierAddress" class="form-label fw-semibold">Address</label>
                        <textarea class="form-control" id="supplierAddress" name="supplierAddress" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="supplierPhoneNumber" class="form-label fw-semibold">Phone Number</label>
                        <input type="text" class="form-control" id="supplierPhoneNumber" name="supplierPhoneNumber">
                    </div>
                    <div class="mb-3">
                        <label for="supplierStatus" class="form-label fw-semibold">Status</label>
                        <select class="form-control" id="supplierStatus" name="supplierStatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="supplierProfileImage" class="form-label fw-semibold">Supplier Image</label>
                        <div class="file-upload-wrapper d-flex align-items-center">
                            <input type="file" name="supplierProfileImage" id="supplierProfileImage" class="file-input" accept="image/*">
                            <label for="supplierProfileImage" class="file-button">
                                <span class="material-icons-outlined">upload</span>
                                Choose File
                            </label>
                            <span class="file-name">No file chosen</span>
                        </div>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-auto d-flex justify-content-center">
                            <x-primary-button type="submit" class="mt-4">
                                <span class="material-icons-outlined">save</span>
                                Submit
                            </x-primary-button>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>

    <script>
        document.getElementById('supplierProfileImage').addEventListener('change', function() {
            const fileName = this.files.length > 0 ? this.files[0].name : 'No file chosen';
            document.querySelector('.file-name').textContent = fileName;
        });
    </script>
</x-app-layout>