<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Traits\LogsAudit;
use Exception;

class ProductObserver
{
    use LogsAudit;
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->logAudit($product, 'create');
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $changes = [];
        foreach ($product->getChanges() as $key => $value) {
            if (!in_array($key, ['updated_at', 'created_at'])) {
                $changes[$key] = [
                    'old' => $product->getOriginal($key),
                    'new' => $value,
                ];
            }
        }
        if (!empty($changes)) {
            $this->logAudit($product, 'update', $changes);
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->logAudit($product, 'delete');
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
