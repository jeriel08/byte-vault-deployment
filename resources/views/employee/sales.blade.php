@extends('employee.main')

@section('title', 'Sales | ByteVault')

@section('content')
    <div class="container my-5">
        <!-- Error Message -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Sales Overview Section -->
        <div class="row mb-4">
            <!-- Total Sales Card (Left) -->
            <div class="col-md-6 mb-3">
                <div class="sales-card">
                    <h4>Total Sales</h4>
                    <h3 class="display-4">₱{{ number_format($totalSales ?? 0, 2) }}</h3>
                    <p>All-time sales revenue</p>
                </div>
            </div>
            <!-- Sales for Today Card (Right) -->
            <div class="col-md-6 mb-3">
                <div class="sales-card">
                    <h4>Sales for Today</h4>
                    <h3 class="display-4">₱{{ number_format($todaySales ?? 0, 2) }}</h3>
                    <p>Revenue for {{ date('F j, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- POS Records Section -->
        <div class="table-container">
            <h4 class="mb-3">POS Records</h4>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Order ID</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Product(s)</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Total Amount</th>
                            <th scope="col">Payment Status</th>
                            <th scope="col">Order Status</th>
                            <th scope="col">Date</th>
                            <th scope="col">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                @php
                                    \Illuminate\Support\Facades\Log::info('Order Data:', $order->toArray());
                                @endphp
                                <td>#{{ $order->orderID }}</td>
                                <td>{{ $order->customer ? $order->customer->name : 'N/A' }}</td>
                                <td>
                                    @foreach ($order->orderLines ?? ($order->orderlines ?? []) as $line)
                                        {{ $line->product ? $line->product->productName : 'N/A' }}
                                        @if (!$loop->last), @endif
                                    @endforeach
                                </td>
                                <td>{{ $order->total_items }}</td>
                                <td>₱{{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge {{ $order->payment_status === 'cash' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">Completed</span>
                                </td>
                                <td>{{ $order->created_at->format('F j, Y') }}</td>
                                <td>
                                    <span class="material-icons-outlined view-icon" data-bs-toggle="modal" data-bs-target="#receiptModal" data-order='@json($order)'>
                                        visibility
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiptModalLabel">Transaction Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="receipt-container">
                        <div class="receipt-header">
                            <h3>POS Receipt</h3>
                            <p>Order #<span id="receipt-order-id"></span></p>
                        </div>
                        <div class="receipt-details">
                            <p><strong>Customer:</strong> <span id="receipt-customer"></span></p>
                            <p><strong>Date:</strong> <span id="receipt-date"></span></p>
                            <p><strong>Payment Status:</strong> <span id="receipt-payment-status"></span></p>
                            <p class="gcash-field" style="display: none;"><strong>Reference Number:</strong> <span id="receipt-reference-number"></span></p>
                            <p class="gcash-field" style="display: none;"><strong>Phone Number:</strong> <span id="receipt-phone-number"></span></p>
                        </div>
                        <div class="receipt-products">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="receipt-products-body"></tbody>
                            </table>
                        </div>
                        <div class="receipt-totals">
                            <p><strong>Grand Total:</strong> ₱<span id="receipt-grand-total"></span></p>
                            <p><strong>Amount Received:</strong> ₱<span id="receipt-amount-received"></span></p>
                            <p><strong>Change:</strong> ₱<span id="receipt-change"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const receiptModal = document.getElementById('receiptModal');
            receiptModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const order = JSON.parse(button.getAttribute('data-order'));
                console.log('Order data:', order);

                // Populate receipt details
                document.getElementById('receipt-order-id').textContent = order.orderID || 'N/A';
                document.getElementById('receipt-customer').textContent = order.customer ? order.customer.name : 'N/A';
                document.getElementById('receipt-date').textContent = order.created_at
                    ? new Date(order.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })
                    : 'N/A';
                document.getElementById('receipt-payment-status').textContent = order.payment_status
                    ? (order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1))
                    : 'N/A';

                // Handle GCash-specific fields
                const referenceNumberElement = document.getElementById('receipt-reference-number');
                const phoneNumberElement = document.getElementById('receipt-phone-number');
                const gcashFields = document.querySelectorAll('.gcash-field');
                if (order.payment_status === 'gcash' && referenceNumberElement && phoneNumberElement) {
                    referenceNumberElement.textContent = order.reference_number || 'N/A';
                    phoneNumberElement.textContent = order.gcash_number || 'N/A';
                    gcashFields.forEach(field => field.style.display = 'block');
                } else {
                    gcashFields.forEach(field => field.style.display = 'none');
                }

                document.getElementById('receipt-grand-total').textContent = order.total
                    ? parseFloat(order.total).toFixed(2)
                    : '0.00';
                document.getElementById('receipt-amount-received').textContent = order.amount_received
                    ? parseFloat(order.amount_received).toFixed(2)
                    : '0.00';
                document.getElementById('receipt-change').textContent = order.change
                    ? parseFloat(order.change).toFixed(2)
                    : '0.00';

                // Populate products table
                const productsBody = document.getElementById('receipt-products-body');
                productsBody.innerHTML = '';
                const orderLines = order.orderLines || order.orderlines || [];
                console.log('Order lines:', orderLines);

                if (!orderLines.length) {
                    const row = document.createElement('tr');
                    row.innerHTML = `<td colspan="4" class="no-products">No products found for this order</td>`;
                    productsBody.appendChild(row);
                    console.warn(`No orderLines for Order #${order.orderID}`);
                    return;
                }

                let calculatedTotal = 0;
                orderLines.forEach((line, index) => {
                    console.log(`Processing line ${index}:`, line);
                    const productName = line.product && line.product.productName ? line.product.productName : 'Unknown Product';
                    const quantity = line.quantity || 0;
                    const price = line.price || 0;
                    const total = (quantity * price).toFixed(2);
                    calculatedTotal += parseFloat(total);

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${productName}</td>
                        <td>${quantity}</td>
                        <td>₱${parseFloat(price).toFixed(2)}</td>
                        <td>₱${total}</td>
                    `;
                    productsBody.appendChild(row);
                });

                // Verify grand total consistency
                if (order.total && Math.abs(calculatedTotal - parseFloat(order.total)) > 0.01) {
                    console.warn(`Total mismatch for Order #${order.orderID}: Calculated=${calculatedTotal}, Stored=${order.total}`);
                }
            });
        });
    </script>
@endsection