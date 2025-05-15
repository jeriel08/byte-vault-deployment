<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountManagerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierOrderController;
use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\ReturnToSupplierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PointOfSaleController;
use Illuminate\Support\Facades\Route;

// Public Route
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Dashboard (Admin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/inventory/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Account Manager (Admin)
Route::middleware('auth')->group(function () {
    Route::get('/admin/account-manager', [AccountManagerController::class, 'index'])->name('account.manager');
    Route::get('/admin/account-manager/add', [AccountManagerController::class, 'add'])->name('account.add');
    Route::post('/admin/account-manager/store', [AccountManagerController::class, 'store'])->name('account.store');
    Route::get('/admin/account-manager/edit/{employeeID}', [AccountManagerController::class, 'edit'])->name('account.edit');
    Route::patch('/admin/account-manager/update/{employeeID}', [AccountManagerController::class, 'update'])->name('account.update');
});

// Suppliers (Admin)
Route::middleware('auth')->group(function () {
    Route::get('/inventory/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/inventory/suppliers/add', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/inventory/suppliers/store', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/inventory/suppliers/edit/{supplier}', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::patch('/inventory/suppliers/update/{supplierID}', [SupplierController::class, 'update'])->name('suppliers.update');
});

// Brands (Admin, under products)
Route::middleware('auth')->group(function () {
    Route::get('/inventory/products/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/inventory/products/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/inventory/products/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/inventory/products/brands/{brandID}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/inventory/products/brands/{brandID}', [BrandController::class, 'update'])->name('brands.update');
});

// Categories (Admin, under products)
Route::middleware('auth')->group(function () {
    Route::get('/inventory/products/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/inventory/products/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/inventory/products/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/inventory/products/categories/{categoryID}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/inventory/products/categories/{categoryID}', [CategoryController::class, 'update'])->name('categories.update');
});

// Products (Admin)
Route::middleware('auth')->group(function () {
    Route::get('/inventory/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/inventory/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/inventory/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/inventory/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/inventory/products/{productID}', [ProductController::class, 'update'])->name('products.update');
});

// Supplier Orders (Admin)
Route::middleware('auth')->group(function () {
    Route::get('/inventory/supplier-orders', [SupplierOrderController::class, 'index'])->name('supplier_orders.index');
    Route::get('/inventory/supplier-orders/create', [SupplierOrderController::class, 'create'])->name('supplier_orders.create');
    Route::post('/inventory/supplier-orders', [SupplierOrderController::class, 'store'])->name('supplier_orders.store');
    Route::get('/inventory/supplier-orders/{supplierOrder}/edit', [SupplierOrderController::class, 'edit'])->name('supplier_orders.edit');
    Route::put('/inventory/supplier-orders/{supplierOrderID}', [SupplierOrderController::class, 'update'])->name('supplier_orders.update');
    Route::get('/inventory/supplier-orders/{supplierOrder}', [SupplierOrderController::class, 'show'])->name('supplier_orders.show');
});

// Adjustments (Admin)
Route::middleware('auth')->group(function () {
    Route::get('/inventory/adjustments', [AdjustmentController::class, 'index'])->name('adjustments.index');
    Route::get('/inventory/adjustments/create', [AdjustmentController::class, 'create'])->name('adjustments.create');
    Route::post('/inventory/adjustments', [AdjustmentController::class, 'store'])->name('adjustments.store');
    Route::get('/inventory/adjustments/{adjustment}/edit', [AdjustmentController::class, 'edit'])->name('adjustments.edit');
    Route::put('/inventory/adjustments/{adjustmentID}', [AdjustmentController::class, 'update'])->name('adjustments.update');
    Route::get('/inventory/adjustments/{adjustment}', [AdjustmentController::class, 'show'])->name('adjustments.show');
});

// Return To Supplier (Admin)
Route::middleware('auth')->group(function () {
    Route::get('/inventory/supplier_returns', [ReturnToSupplierController::class, 'index'])->name('supplier_returns.index');
    Route::get('/inventory/supplier_returns/create', [ReturnToSupplierController::class, 'create'])->name('supplier_returns.create');
    Route::post('/inventory/supplier_returns', [ReturnToSupplierController::class, 'store'])->name('supplier_returns.store');
    Route::get('/inventory/supplier_returns/{returnSupplierID}', [ReturnToSupplierController::class, 'show'])->name('supplier_returns.show');
    Route::patch('/inventory/supplier_returns/{returnSupplierID}/complete', [ReturnToSupplierController::class, 'complete'])->name('supplier_returns.complete');
    Route::patch('/inventory/supplier_returns/{returnSupplierID}/reject', [ReturnToSupplierController::class, 'reject'])->name('supplier_returns.reject');
});

// Orders (Admin)
Route::middleware('auth')->group(function () {
    Route::resource('orders', \App\Http\Controllers\OrderController::class);
    Route::get('/inventory/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/inventory/orders/create', [\App\Http\Controllers\OrderController::class, 'create'])->name('orders.create');
    Route::post('/inventory/orders', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
    Route::get('/inventory/orders/{order}/edit', [\App\Http\Controllers\OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/inventory/orders/{order}', [\App\Http\Controllers\OrderController::class, 'update'])->name('orders.update');
    Route::delete('/inventory/orders/{order}', [\App\Http\Controllers\OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/inventory/orders/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
});

// Audit Logs (Admin)
Route::middleware('auth')->group(function () {
    // Existing routes...
    Route::get('/admin/audit', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');
});

// Inventory Report (Admin)
Route::middleware('auth')->group(function () {

    // Route to display the inventory report page
    Route::get('/reports/inventory', [ReportController::class, 'inventoryReport'])
        ->name('reports.inventory'); // Use '.index' for consistency if you like

    // Route to download the inventory report as PDF
    Route::get('/reports/inventory/download/pdf', [ReportController::class, 'downloadInventoryPdf'])
        ->name('reports.inventory.download.pdf');

    // Future route for Excel download would go here too
    Route::get('/reports/inventory/download/excel', [ReportController::class, 'downloadInventoryExcel'])
        ->name('reports.inventory.download.excel');
});

// POS Section (Employee)
Route::middleware('auth')->group(function () {
    Route::get('/pos', [PointOfSaleController::class, 'products'])->name('pos.products');
    Route::get('/pos/sales', [PointOfSaleController::class, 'sales'])->name('pos.sales');
    Route::post('/pos/store', [PointOfSaleController::class, 'storeOrder'])->name('pos.store');
});




// POS Section (Category)


require __DIR__ . '/auth.php';
