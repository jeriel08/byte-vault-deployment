@section('title', 'Edit Supplier Order | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl fw-semibold mb-0"> Order No. {{ $supplierOrder->supplierOrderID }}</h2>
            <x-secondary-button href="{{ route('supplier_orders.index') }}">
                <span class="material-icons-outlined">arrow_back</span>
                Go back
            </x-secondary-button>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="card account-settings-card p-3">
            <div class="card-body">
                <form action="{{ route('supplier_orders.update', $supplierOrder->supplierOrderID) }}" method="POST" id="supplierOrderForm">
                    @csrf
                    @method('PUT')
                    <h5 class="fw-semibold mb-3">Order Information</h5>
                    <div class="mb-3">
                        <label for="supplierID" class="form-label fw-semibold">Supplier</label>
                        <select name="supplierID" id="supplierID" class="custom-select2 select2" required>
                            <option value="">Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->supplierID }}" {{ $supplierOrder->supplierID == $supplier->supplierID ? 'selected' : '' }}>
                                    {{ $supplier->supplierName }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplierID') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="orderDate" class="form-label fw-semibold">Order Date</label>
                        <input type="date" name="orderDate" id="orderDate" class="form-control" value="{{ $supplierOrder->orderDate->format('Y-m-d') }}" required>
                        @error('orderDate') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="expectedDeliveryDate" class="form-label fw-semibold">Expected Delivery Date</label>
                        <input type="date" name="expectedDeliveryDate" id="expectedDeliveryDate" class="form-control" value="{{ $supplierOrder->expectedDeliveryDate?->format('Y-m-d') }}">
                        @error('expectedDeliveryDate') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <hr class="mb-4">
                    <!-- Order Details Section -->
                    <div id="orderDetails" class="mb-3">
                        <h5 class="fw-semibold mb-3">Order Details</h5>
                        <div id="productList">
                            @foreach ($supplierOrder->details as $index => $detail)
                                <div class="card account-manager-card p-3 d-flex flex-row align-items-center mb-3" data-detail-id="{{ $detail->supplierOrderDetailID }}">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 fw-semibold">{{ $detail->product->productName }}</h5>
                                    </div>
                                    <div class="d-flex align-items-center mx-3">
                                        <span class="vr me-3"></span>
                                        <div class="d-flex flex-row gap-3 align-items-start">
                                            <div class="text-start" style="min-width: 80px;">
                                                <span class="text-muted d-block"><small>Quantity</small></span>
                                                <span class="fw-semibold fs-5">{{ $detail->quantity }}</span>
                                            </div>
                                            <div class="text-start" style="min-width: 100px;">
                                                <span class="text-muted d-block"><small>Unit Cost</small></span>
                                                <span class="fw-semibold fs-5">₱{{ number_format($detail->unitCost, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ms-5 d-flex gap-2">
                                        <x-primary-button type="button" class="btn-sm edit-product" data-bs-toggle="modal" data-bs-target="#editProductModal-{{ $detail->supplierOrderDetailID }}">
                                            <span class="material-icons-outlined">edit</span>
                                        </x-primary-button>
                                    </div>
                                    <input type="hidden" name="details[{{ $index }}][supplierOrderDetailID]" value="{{ $detail->supplierOrderDetailID }}">
                                    <input type="hidden" name="details[{{ $index }}][productID]" value="{{ $detail->productID }}">
                                    <input type="hidden" name="details[{{ $index }}][quantity]" value="{{ $detail->quantity }}">
                                    <input type="hidden" name="details[{{ $index }}][unitCost]" value="{{ $detail->unitCost }}">
                                </div>
                        
                                <!-- Edit Product Modal -->
                                <x-modal name="editProductModal-{{ $detail->supplierOrderDetailID }}" maxWidth="lg">
                                    <div class="modal-header custom-modal-header">
                                        <h5 class="modal-title">Edit Product: {{ $detail->product->productName }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body custom-modal-body">
                                        <div class="row mb-3">
                                            <label class="form-label fw-semibold">Quantity</label>
                                            <input type="number" class="form-control edit-quantity" value="{{ $detail->quantity }}" min="1" required>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="form-label fw-semibold">Unit Cost</label>
                                            <input type="number" class="form-control edit-unitCost" value="{{ $detail->unitCost }}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer custom-modal-footer">
                                        <x-primary-button type="button" class="btn btn-primary update-product" data-detail-id="{{ $detail->supplierOrderDetailID }}">Update</x-primary-button>
                                        <x-secondary-button type="button" data-bs-dismiss="modal">Close</x-secondary-button>
                                    </div>
                                </x-modal>
                            @endforeach
                        </div>
                    </div>
                    <hr class="mb-3">
                    <div class="row d-flex justify-content-center">
                        <div class="col-4 d-flex justify-content-center">
                            <x-primary-button type="submit" class="mt-4">
                                <span class="material-icons-outlined">save</span>
                                Update Order
                            </x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('click', function(e) {
            if (e.target.closest('.update-product')) {
                const modal = e.target.closest('.modal');
                const detailId = e.target.dataset.detailId;
                const card = document.querySelector(`.card[data-detail-id="${detailId}"]`);
                const quantity = parseInt(modal.querySelector('.edit-quantity').value);
                const unitCost = modal.querySelector('.edit-unitCost').value;
    
                // Update card display
                card.querySelector('.text-start:nth-child(1) .fw-semibold').textContent = quantity;
                card.querySelector('.text-start:nth-child(2) .fw-semibold').textContent = `₱${parseFloat(unitCost).toFixed(2)}`;
    
                // Update hidden inputs
                card.querySelector('input[name$="[quantity]"]').value = quantity;
                card.querySelector('input[name$="[unitCost]"]').value = unitCost;
    
                bootstrap.Modal.getInstance(modal).hide();
            }
        });
    </script>
    
    @push('scripts')
    <script>
        $(document).ready(function () {
            // Select outside modal
            $('#supplierID').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: "Select Supplier",
                allowClear: false
            });
        });
    </script>
    @endpush
</x-app-layout>