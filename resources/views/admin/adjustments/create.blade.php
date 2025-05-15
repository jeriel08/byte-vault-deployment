@section('title', 'Create Adjustment | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl fw-semibold mb-0">Add Adjustment</h2>
            <x-secondary-button href="{{ route('adjustments.index') }}">
                <span class="material-icons-outlined">arrow_back</span>
                Go back
            </x-secondary-button>
        </div>
        
        <div class="card account-settings-card p-3">
            <div class="card-body">
                <form action="{{ route('adjustments.store') }}" method="POST" id="adjustmentForm">
                    @csrf
                    <h5 class="fw-semibold mb-3">Adjustment Information</h5>
                    <div class="mb-3">
                        <label for="adjustmentDate" class="form-label fw-semibold">Adjustment Date</label>
                        <input type="date" name="adjustmentDate" id="adjustmentDate" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        @error('adjustmentDate') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="adjustmentReason" class="form-label fw-semibold">Adjustment Reason</label>
                        <input type="text" name="adjustmentReason" id="adjustmentReason" class="form-control" placeholder="e.g., Damaged in storage" required>
                        @error('adjustmentReason') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <hr class="mb-4">
                    <div id="adjustmentDetails" class="mb-3">
                        <h5 class="fw-semibold mb-3">Adjustment Details</h5>
                        <x-secondary-button type="button" class="mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            Select Product
                        </x-secondary-button>
                        <div id="productList">
                            <!-- Dynamically added product cards will appear here -->
                        </div>
                    </div>
                    <hr class="mb-3">
                    <div class="row d-flex justify-content-center">
                        <div class="col-4 d-flex justify-content-center">
                            <x-primary-button type="submit" class="mt-4">
                                <span class="material-icons-outlined">save</span>
                                Save Adjustment
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Adding Products -->
    <x-modal name="addProductModal" maxWidth="lg">
        <div class="modal-header custom-modal-header">
            <h5 class="modal-title" id="addProductModal-label">Add Product to Adjustment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body custom-modal-body">
            <div class="row d-flex justify-content-center align-content-center">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Product</label>
                    <select id="productID" class="form-select custom-select2 select2" required>
                        <option value="">Select Product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->productID }}">{{ $product->productName}} (Stock: {{ $product->stockQuantity }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Quantity</label>
                    <input type="number" id="quantity" class="form-control" min="1" required>
                </div>
            </div>
        </div>
        <div class="modal-footer custom-modal-footer py-2">
            <x-primary-button type="button" id="addProductBtn">Add Product</x-primary-button>
            <x-secondary-button type="button" data-bs-dismiss="modal">Close</x-secondary-button>
        </div>
    </x-modal>

    <!-- JavaScript for Managing Products -->
    <script>
        let index = 0;
        document.getElementById('addProductBtn').addEventListener('click', function() {
            const productID = document.getElementById('productID').value;
            const quantity = document.getElementById('quantity').value;

            if (productID && quantity) {
                const productOptionText = document.querySelector(`#productID option[value="${productID}"]`).text;
                const productName = productOptionText.split(' (Stock:')[0]; // Extract only the name
                const productList = document.getElementById('productList');
                
                const productCard = document.createElement('div');
                productCard.className = 'card account-manager-card p-3 d-flex flex-row align-items-center mb-3';
                productCard.innerHTML = `
                    <div class="flex-grow-1">
                        <h5 class="mb-1 fw-semibold">${productName}</h5>
                    </div>
                    <div class="d-flex align-items-center mx-3">
                        <span class="vr me-3"></span>
                        <div class="d-flex flex-row gap-3 align-items-start">
                            <div class="text-start" style="min-width: 80px;">
                                <span class="text-muted d-block"><small>Quantity</small></span>
                                <span class="fw-semibold fs-5">${quantity}</span>
                            </div>
                        </div>
                    </div>
                    <div class="ms-5">
                        <button type="button" class="btn btn-danger btn-sm remove-product">
                            <span class="material-icons-outlined danger-badge fs-1">delete</span>
                        </button>
                    </div>
                    <input type="hidden" name="products[${index}][productID]" value="${productID}">
                    <input type="hidden" name="products[${index}][quantity]" value="${quantity}">
                `;
                productList.appendChild(productCard);
                index++;

                bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
                document.getElementById('productID').value = '';
                document.getElementById('quantity').value = '';
            }
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-product')) {
                e.target.closest('.card').remove();
            }
        });
    </script>

    @push('scripts')
    <script>
        $(document).ready(function () {
            // Select inside modal
            $('#productID').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: "Select Product",
                dropdownParent: $('#addProductModal'),
                allowClear: false
            });
        });   
    </script>
    @endpush

</x-app-layout>