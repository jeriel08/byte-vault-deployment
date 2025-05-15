@section('title', 'Order Details | ByteVault')

<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-2xl font-bold m-0">Order No. {{ $order->orderID }}</h2>
            <div class="d-flex gap-2">
                <x-secondary-button href="{{ route('orders.index') }}">
                    <span class="material-icons-outlined">arrow_back</span>
                    Go back
                </x-secondary-button>
            </div>
        </div>

        <!-- Order Information Card -->
        <div class="card account-settings-card p-3">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">Order Information</h5>
                <div class="row">
                    <div class="col-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Customer:</label>
                            <p class="form-control-plaintext">{{ $order->customer ? $order->customer->name : 'N/A' }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Order Date:</label>
                            <p class="form-control-plaintext">{{ $order->date ? \Carbon\Carbon::parse($order->date)->format('M d, Y') : $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Total Items:</label>
                            <p class="form-control-plaintext">{{ $order->total_items }}</p>
                        </div>
                        @if ($order->payment_status == 'cash')
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Payment method:</label>
                            <p class="form-control-plaintext">Cash</p>
                        </div>
                        @endif
                    </div>
                    <div class="col-6">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Total Order Cost:</label>
                            <p class="form-control-plaintext">₱{{ number_format($order->total, 2) }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Amount Received:</label>
                            <p class="form-control-plaintext">₱{{ number_format($order->amount_received, 2) }}</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Change:</label>
                            <p class="form-control-plaintext">₱{{ number_format($order->change, 2) }}</p>
                        </div>
                    </div>
                    @if ($order->payment_status == 'gcash')
                        <div class="row">
                            <hr>
                            <div class="col-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Payment method:</label>
                                    <p class="form-control-plaintext">GCash</p>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Reference Number:</label>
                                    <p class="form-control-plaintext">{{ $order->reference_number }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Customer Phone Number:</label>
                                    <p class="form-control-plaintext">{{ $order->gcash_number }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                
                <hr class="mb-4">

                <!-- Order Details Section -->
                <div id="orderDetails" class="mb-3">
                    <h5 class="fw-semibold mb-3">Order Details</h5>
                    <div id="productList">
                        @forelse ($order->orderlines as $orderline)
                            <div class="card account-manager-card p-3 d-flex flex-row align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-semibold">{{ $orderline->product ? $orderline->product->productName : 'N/A' }}</h5>
                                </div>
                                <div class="d-flex align-items-center mx-3">
                                    <span class="vr me-5"></span>
                                    <div class="d-flex flex-row gap-3 align-items-start">
                                        <div class="text-start me-4" style="min-width: 80px;">
                                            <span class="text-muted d-block"><small>Quantity</small></span>
                                            <span class="fw-semibold fs-5">{{ $orderline->quantity }}</span>
                                        </div>
                                        <div class="text-start me-4" style="min-width: 10em;">
                                            <span class="text-muted d-block"><small>Unit Price</small></span>
                                            <span class="fw-semibold fs-5">₱{{ number_format($orderline->price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>No items in this order.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>