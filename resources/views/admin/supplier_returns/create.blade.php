@section('title', 'Create Return to Supplier | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl fw-semibold mb-0">Add Return to Supplier</h2>
            <x-secondary-button href="{{ route('supplier_returns.index') }}">
                <span class="material-icons-outlined">arrow_back</span>
                Go back
            </x-secondary-button>
        </div>
        
        <div class="card account-settings-card p-3">
            <div class="card-body">
                <form action="{{ route('supplier_returns.store') }}" method="POST" id="returnForm">
                    @csrf
                    <h5 class="fw-semibold mb-3">Return Information</h5>
                    <div class="mb-3">
                        <label for="supplierOrderID" class="form-label fw-semibold">Supplier Order</label>
                        <select name="supplierOrderID" id="supplierOrderID" class="form-select custom-select2 select2" required>
                            <option value="">Select Supplier Order</option>
                            @foreach ($orders as $supplierOrder)
                                <option value="{{ $supplierOrder->supplierOrderID }}" {{ $order && $order->supplierOrderID === $supplierOrder->supplierOrderID ? 'selected' : '' }} data-details="{{ $supplierOrder->details->toJson() }}">
                                    Order #{{ $supplierOrder->supplierOrderID }} - {{ $supplierOrder->supplier->name }} ({{ $supplierOrder->orderDate->format('M d, Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('supplierOrderID') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="returnDate" class="form-label fw-semibold">Return Date</label>
                        <input type="date" name="returnDate" id="returnDate" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        @error('returnDate') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="returnSupplierReason" class="form-label fw-semibold">Return Reason</label>
                        <input type="text" name="returnSupplierReason" id="returnSupplierReason" class="form-control" placeholder="e.g., Defective items" required>
                        @error('returnSupplierReason') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <hr class="mb-4">
                    <div id="returnDetails" class="mb-3">
                        <h5 class="fw-semibold mb-3">Return Details</h5>
                        <div id="productList">
                            @if ($order)
                                @foreach ($order->details as $index => $detail)
                                    <div class="card account-manager-card p-3 d-flex flex-row align-items-center mb-3">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1 fw-semibold">{{ $detail->product->productName }}</h5>
                                        </div>
                                        <div class="d-flex align-items-center mx-3">
                                            <span class="vr me-3"></span>
                                            <div class="d-flex flex-row gap-3 align-items-start">
                                                <div class="text-start" style="min-width: 80px;">
                                                    <span class="text-muted d-block"><small>Quantity</small></span>
                                                    <input type="number" class="form-control quantity-input" name="details[{{ $index }}][quantity]" value="0" min="0" max="{{ $detail->quantity }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ms-5">
                                            <button type="button" class="btn btn-danger btn-sm remove-product">
                                                <span class="material-icons-outlined danger-badge fs-1">delete</span>
                                            </button>
                                        </div>
                                        <input type="hidden" name="details[{{ $index }}][productID]" value="{{ $detail->productID }}">
                                    </div>
                                @endforeach
                            @else
                                <div class="card account-manager-card text-center p-5">
                                    <h5 class="text-muted d-flex justify-content-center align-items-center gap-3">
                                        Please select a supplier order to load products.
                                        <span class="material-icons-outlined fs-2">inventory</span>
                                    </h5>
                                </div>
                            @endif
                        </div>
                    </div>
                    <hr class="mb-3">
                    <div class="row d-flex justify-content-center">
                        <div class="col-4 d-flex justify-content-center">
                            <x-primary-button type="submit" class="mt-4">
                                <span class="material-icons-outlined">save</span>
                                Save Return
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
            $('#supplierOrderID').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: "Select Supplier",
                allowClear: false
            });

            let index = {{ $order ? $order->details->count() : 0 }};

            // Proper change listener for Select2
            $('#supplierOrderID').on('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const details = JSON.parse(selectedOption.getAttribute('data-details') || '[]');
                const productList = document.getElementById('productList');
                productList.innerHTML = '';
                index = 0;

                if (details.length === 0) {
                    productList.innerHTML = `
                        <div class="card account-manager-card text-center p-5">
                            <h5 class="text-muted d-flex justify-content-center align-items-center gap-3">
                                Please select a supplier order to load products.
                                <span class="material-icons-outlined fs-2">inventory</span>
                            </h5>
                        </div>
                    `;
                    return;
                }

                details.forEach(detail => {
                    const productCard = document.createElement('div');
                    productCard.className = 'card account-manager-card p-3 d-flex flex-row align-items-center mb-3';
                    productCard.innerHTML = `
                        <div class="flex-grow-1">
                            <h5 class="mb-1 fw-semibold">${detail.product.productName}</h5>
                        </div>
                        <div class="d-flex align-items-center mx-3">
                            <span class="vr me-3"></span>
                            <div class="d-flex flex-row gap-3 align-items-start">
                                <div class="text-start" style="min-width: 80px;">
                                    <span class="text-muted d-block"><small>Quantity</small></span>
                                    <input type="number" class="form-control quantity-input" name="details[${index}][quantity]" value="0" min="0" max="${detail.quantity}">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="details[${index}][productID]" value="${detail.productID}">
                    `;
                    productList.appendChild(productCard);
                    index++;
                });
            });
        });
    </script>
    @endpush
</x-app-layout>