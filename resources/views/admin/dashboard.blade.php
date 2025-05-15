@section('title', 'Dashboard | ByteVault')

<x-app-layout>
    <div class="container">
        <div class="py-12">
            <div class="dashboard-container mx-auto">
                <div class="overflow-hidden">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mx-1 mb-4">
                            <h2>Hello, <strong>{{ Auth::user()->firstName }} {{ Auth::user()->lastName }}</strong>!</h2>
                            <x-primary-button href="{{ route('reports.inventory') }}" class="py-2">
                                <span class="material-icons-outlined">summarize</span>
                                Generate Inventory Report
                            </x-primary-button>
                        </div>

                        <!-- Row for summary cards -->
                        <div class="row mt-4">
                            <!-- Total Sales -->
                            <div class="col-md-3">
                                <div class="card shadow-sm rounded-4 p-3 d-flex flex-column justify-content-center align-items-center"
                                    style="min-height: 120px;">
                                    <span class="material-icons-outlined icon-summary total-sales">payments</span>
                                    <h3 class="fw-bold mb-0 mt-2">{{ $totalSales }}</h3>
                                    <p class="fw-semibold text-muted mb-0">Total Sales</p>
                                </div>
                            </div>
                            <!-- Total Orders -->
                            <div class="col-md-3">
                                <div class="card shadow-sm rounded-4 p-3 d-flex flex-column justify-content-center align-items-center"
                                    style="min-height: 120px;">
                                    <span class="material-icons-outlined icon-summary total-orders">shopping_cart</span>
                                    <h3 class="fw-bold mb-0 mt-2">{{ $totalOrders }}</h3>
                                    <p class="fw-semibold text-muted mb-0">Total Orders</p>
                                </div>
                            </div>
                            <!-- Total Products in Stock -->
                            <div class="col-md-3">
                                <div class="card shadow-sm rounded-4 p-3 d-flex flex-column justify-content-center align-items-center"
                                    style="min-height: 120px;">
                                    <span
                                        class="material-icons-outlined icon-summary products-in-stock">inventory</span>
                                    <h3 class="fw-bold mb-0 mt-2">{{ $totalProductsInStock }}</h3>
                                    <p class="fw-semibold text-muted mb-0">Products in Stock</p>
                                </div>
                            </div>
                            <!-- Low Stock Products -->
                            <div class="col-md-3">
                                <div class="card shadow-sm rounded-4 p-3 d-flex flex-column justify-content-center align-items-center"
                                    style="min-height: 120px;">
                                    <span class="material-icons-outlined icon-summary low-stock">warning</span>
                                    <h3 class="fw-bold mb-0 mt-2">{{ $lowStockProducts }}</h3>
                                    <p class="fw-semibold text-muted mb-0">Low Stock</p>
                                </div>
                            </div>
                        </div>

                        <!-- Row for Category Distribution and Sales Overview -->
                        <div class="row mt-4">
                            <!-- Category Distribution (40%) -->
                            <div class="col-md-5">
                                <div class="card shadow-sm rounded-4 p-3" style="height: 460px;">
                                    <div class="card-body d-flex flex-column h-100">
                                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                                            <span
                                                class="material-icons-outlined icon-title category-distribution me-2">pie_chart</span>
                                            Category Distribution
                                        </h5>
                                        <div class="d-flex flex-grow-1 align-items-center">
                                            <!-- Custom Legend on the Left -->
                                            <div class="me-4" style="min-width: 150px; max-width: 150px;">
                                                @foreach (json_decode($categoryLabels) as $index => $label)
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span
                                                            style="width: 20px; height: 20px; display: inline-block; margin-right: 10px; background-color: {{ ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'][$index] }}; border-radius: 4px;"></span>
                                                        <span class="fs-6 fw-medium text-truncate">{{ $label }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <!-- Chart Container on the Right -->
                                            <div class="flex-grow-1"
                                                style="position: relative; max-width: 100%; overflow: hidden;">
                                                <canvas id="categoryDistributionChart"
                                                    style="max-height: 300px; max-width: 100%;"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Sales Overview (60%) -->
                            <div class="col-md-7">
                                <div class="card shadow-sm rounded-4 p-3" style="height: 460px;">
                                    <div class="card-body d-flex flex-column h-100">
                                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                                            <span
                                                class="material-icons-outlined icon-title sales-overview me-2">trending_up</span>
                                            Sales Overview
                                        </h5>
                                        <div class="flex-grow-1">
                                            <canvas id="salesChart" style="max-height: 400px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row for Sales by Category and Top Sales -->
                        <div class="row mt-4 mb-5">
                            <!-- Sales by Category (70%) -->
                            <div class="col-md-8">
                                <div class="card shadow-sm rounded-4 p-4" style="min-height: 460px;">
                                    <div class="card-body d-flex flex-column h-100">
                                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                                            <span
                                                class="material-icons-outlined icon-title sales-by-category me-2">bar_chart</span>
                                            Sales by Category
                                        </h5>
                                        <div class="flex-grow-1">
                                            <canvas id="categorySalesChart" style="max-height: 400px;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Top Sales (30%) -->
                            <div class="col-md-4">
                                <div class="card shadow-sm rounded-4 p-4" style="min-height: 460px;">
                                    <div class="card-body">
                                        <h5 class="card-title fw-semibold mb-3 d-flex align-items-center">
                                            <span class="material-icons-outlined icon-title top-sales me-2">star</span>
                                            Top Sales
                                        </h5>
                                        @if (count($topSellingProducts) > 0)
                                            <ul class="list-group list-group-flush">
                                                @foreach ($topSellingProducts as $product)
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>{{ $product->productName }}</strong><br>
                                                            <small
                                                                class="text-muted">₱{{ number_format($product->total_revenue, 2) }}</small>
                                                        </div>
                                                        <span
                                                            class="badge bg-primary rounded-pill">{{ $product->total_quantity }}
                                                            sold</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted">No sales data available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Chart Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sales Overview Line Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: {!! $salesLabels !!},
                    datasets: [{
                        label: 'Total Sales (₱)',
                        data: {!! $salesData !!},
                        borderColor: '#4BC0C0',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                        pointBackgroundColor: '#4BC0C0',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: { display: false },
                            grid: { display: false },
                            ticks: {
                                color: '#2a5055',
                                font: { size: 14, weight: 600 }
                            }
                        },
                        y: {
                            title: { display: false },
                            grid: { display: false },
                            ticks: {
                                color: '#2a5055',
                                font: { size: 14, weight: 600 },
                                maxTicksLimit: 5,
                                callback: function (value) {
                                    return '₱' + value.toLocaleString('en-US');
                                }
                            },
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#faf8f5',
                            titleFont: { size: 14, weight: 600 },
                            bodyFont: { size: 14, weight: 600 },
                            titleColor: '#2a5055',
                            bodyColor: '#2a5055',
                            padding: 8,
                            callbacks: {
                                label: function (context) {
                                    return `₱${context.raw.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
                                }
                            }
                        }
                    }
                }
            });

            // Category Distribution Doughnut Chart with Hover Effect
            const categoryCtx = document.getElementById('categoryDistributionChart').getContext('2d');
            const categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! $categoryLabels !!},
                    datasets: [{
                        data: {!! $categoryData !!},
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} items (${percentage}%)`;
                                }
                            },
                            backgroundColor: '#faf8f5',
                            titleFont: { size: 14, weight: 600 },
                            bodyFont: { size: 14, weight: 600 },
                            titleColor: '#2a5055',
                            bodyColor: '#2a5055',
                            padding: 8
                        }
                    },
                    layout: {
                        padding: 10
                    }
                }
            });

            // Sales by Category Bar Chart
            const categorySalesCtx = document.getElementById('categorySalesChart').getContext('2d');
            const categorySalesChart = new Chart(categorySalesCtx, {
                type: 'bar',
                data: {
                    labels: {!! $salesCategoryLabels !!},
                    datasets: [{
                        label: 'Sales (₱)',
                        data: {!! $salesCategoryData !!},
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                        borderWidth: 1,
                        borderColor: '#fff',
                        borderRadius: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            title: { display: false },
                            grid: { display: false },
                            ticks: {
                                color: '#2a5055',
                                font: { size: 14, weight: 600 }
                            }
                        },
                        y: {
                            title: { display: false },
                            grid: { display: false },
                            ticks: {
                                color: '#2a5055',
                                font: { size: 14, weight: 600 },
                                maxTicksLimit: 5,
                                callback: function (value) {
                                    return '₱' + value.toLocaleString('en-US');
                                }
                            },
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return `${label}: ₱${value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                }
                            },
                            backgroundColor: '#faf8f5',
                            titleFont: { size: 14, weight: 600 },
                            bodyFont: { size: 14, weight: 600 },
                            titleColor: '#2a5055',
                            bodyColor: '#2a5055',
                            padding: 8
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>