@section('title', 'Edit Brand | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl mb-0">Edit Brand: <strong>{{ $brand->brandName }}</strong></h2>
            <x-secondary-button href="{{ route('brands.index') }}">
                <span class="material-icons-outlined">arrow_back</span>
                Go back
            </x-secondary-button>
        </div>    

        <div class="card account-settings-card">
            <div class="card-body">
                {{-- {{ route('brands.update', $brand->brandID) }} --}}
                <form action="{{ route('brands.update', $brand->brandID) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="brandName" class="form-label fw-semibold">Brand Name</label>
                        <input type="text" name="brandName" id="brandName" class="form-control" value="{{ old('brandName', $brand->brandName) }}" required>
                        @error('brandName')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="brandStatus" class="form-label fw-semibold">Status</label>
                        <select name="brandStatus" id="brandStatus" class="form-select" required>
                            <option value="Active" {{ $brand->brandStatus == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $brand->brandStatus == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('brandStatus')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="brandProfileImage" class="form-label fw-semibold">Brand Image</label>
                        <div class="file-upload-wrapper d-flex align-items-center">
                            <input type="file" name="brandProfileImage" id="brandProfileImage" class="file-input" accept="image/*">
                            <label for="brandProfileImage" class="file-button">
                                <span class="material-icons-outlined">upload</span>
                                Choose File
                            </label>
                            <span class="file-name">
                                {{ $brand->brandProfileImage ? basename($brand->brandProfileImage) : 'No file chosen' }}
                            </span>
                        </div>
                        @if ($brand->brandProfileImage)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $brand->brandProfileImage) }}" alt="{{ $brand->brandName }}" class="supplier-image">
                            </div>
                        @endif
                        @error('brandProfileImage')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <x-primary-button type="submit" class="mt-4">
                        <span class="material-icons-outlined">save</span>
                        Update Brand
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('brandProfileImage').addEventListener('change', function() {
            ctene fileName = this.files.length > 0 ? this.files[0].name : '{{ $brand->brandProfileImage ? basename($brand->brandProfileImage) : 'No file chosen' }}';
            document.querySelector('.file-name').textContent = fileName;
        });
    </script>
</x-app-layout>