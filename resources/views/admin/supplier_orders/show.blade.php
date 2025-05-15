@section('title', 'Supplier Order Details | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl fw-semibold mb-0">Order No. {{ $supplierOrder->supplierOrderID }}</h2>
            <div class="d-flex gap-2">
                @if ($supplierOrder->receivedDate)
                    <x-primary-button href="{{ route('supplier_returns.create', ['order' => $supplierOrder->supplierOrderID]) }}">
                        <span class="material-icons-outlined">undo</span>
                        Return to Supplier
                    </x-primary-button>
                @endif
                <x-secondary-button href="{{ route('supplier_orders.index') }}">
                    <span class="material-icons-outlined">arrow_back</span>
                    Go back
                </x-secondary-button>
            </div>
        </div>
        
        <div class="card account-settings-card p-3">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Order Information</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Supplier:</label>
                    <p class="form-control-plaintext">{{ $supplierOrder->supplier->supplierName }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Order Date:</label>
                    <p class="form-control-plaintext">{{ $supplierOrder->orderDate->format('M d, Y') }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Expected Delivery Date:</label>
                    <p class="form-control-plaintext">{{ $supplierOrder->expectedDeliveryDate?->format('M d, Y') ?? 'Not set' }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Total Order Cost:</label>
                    <p class="form-control-plaintext">₱{{ number_format($supplierOrder->totalCost, 2) }}</p>
                </div>
                @if ($supplierOrder->cancelledDate)
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Cancellation Reason:</label>
                        <p class="form-control-plaintext">{{ $supplierOrder->cancellationRemark }}</p>
                    </div>
                @endif
                <hr class="mb-4">
                
                <!-- Order Details Section -->
                <div id="orderDetails" class="mb-3">
                    <h5 class="fw-semibold mb-3">Order Details</h5>
                    <div id="productList">
                        @foreach ($supplierOrder->details as $detail)
                            <div class="card account-manager-card p-3 d-flex flex-row align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-semibold">{{ $detail->product->productName }}</h5>
                                </div>
                                <div class="d-flex align-items-center mx-3">
                                    <span class="vr me-5"></span>
                                    <div class="d-flex flex-row gap-3 align-items-start">
                                        <div class="text-start me-4" style="min-width: 80px;">
                                            <span class="text-muted d-block"><small>Quantity</small></span>
                                            <span class="fw-semibold fs-5">{{ $detail->quantity }}</span>
                                        </div>
                                        <div class="text-start me-4" style="min-width: 10em;">
                                            <span class="text-muted d-block"><small>Unit Cost</small></span>
                                            <span class="fw-semibold fs-5">₱{{ number_format($detail->unitCost, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>