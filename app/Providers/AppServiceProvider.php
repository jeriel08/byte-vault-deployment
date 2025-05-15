<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use App\Models\Category;
use App\Observers\CategoryObserver;
use App\Models\Brand;
use App\Observers\BrandObserver;
use App\Models\Supplier;
use App\Observers\SupplierObserver;
use App\Models\SupplierOrder;
use App\Observers\SupplierOrderObserver;
use App\Models\SupplierOrderDetail;
use App\Observers\SupplierOrderDetailObserver;
use App\Models\ReturnToSupplier;
use App\Observers\ReturnToSupplierObserver;
use App\Listeners\AuthAuditListener;
use App\Models\Adjustment;
use App\Observers\AdjustmentObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Relation::enforceMorphMap([
            'adjustments' => 'App\Models\Adjustment',
            // Add others later, e.g., 'returntosupplier' => 'App\Models\ReturnToSupplier'
        ]);

        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);
        Brand::observe(BrandObserver::class);
        Supplier::observe(SupplierObserver::class);
        SupplierOrder::observe(SupplierOrderObserver::class);
        SupplierOrderDetail::observe(SupplierOrderDetailObserver::class);
        ReturnToSupplier::observe(ReturnToSupplierObserver::class);
        Adjustment::observe(AdjustmentObserver::class);

        // Register authentication event listeners
        Event::listen(Login::class, [AuthAuditListener::class, 'handle']);
        Event::listen(Logout::class, [AuthAuditListener::class, 'handle']);
    }
}
