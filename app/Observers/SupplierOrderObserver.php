<?php

namespace App\Observers;

use App\Models\SupplierOrder;
use App\Traits\LogsAudit;

class SupplierOrderObserver
{
    use LogsAudit;

    /**
     * Handle the SupplierOrder "created" event.
     */
    public function created(SupplierOrder $supplierOrder): void
    {
        //
        $this->logAudit($supplierOrder, 'create');
    }

    /**
     * Handle the SupplierOrder "updated" event.
     */
    public function updated(SupplierOrder $supplierOrder): void
    {
        //
        $changes = [];
        foreach ($supplierOrder->getChanges() as $key => $value) {
            if (!in_array($key, ['updated_at', 'created_at'])) {
                $changes[$key] = [
                    'old' => $supplierOrder->getOriginal($key),
                    'new' => $value,
                ];
            }
        }
        if (!empty($changes)) {
            $this->logAudit($supplierOrder, 'update', $changes);
        }
    }

    /**
     * Handle the SupplierOrder "deleted" event.
     */
    public function deleted(SupplierOrder $supplierOrder): void
    {
        //
        $this->logAudit($supplierOrder, 'delete');
    }

    /**
     * Handle the SupplierOrder "restored" event.
     */
    public function restored(SupplierOrder $supplierOrder): void
    {
        //
    }

    /**
     * Handle the SupplierOrder "force deleted" event.
     */
    public function forceDeleted(SupplierOrder $supplierOrder): void
    {
        //
    }
}
