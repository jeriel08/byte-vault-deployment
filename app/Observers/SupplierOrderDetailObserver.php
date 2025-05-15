<?php

namespace App\Observers;

use App\Models\SupplierOrderDetail;
use App\Traits\LogsAudit;

class SupplierOrderDetailObserver
{
    use LogsAudit;

    /**
     * Handle the SupplierOrderDetail "created" event.
     */
    public function created(SupplierOrderDetail $supplierOrderDetail): void
    {
        //
        $this->logAudit($supplierOrderDetail, 'create');
    }

    /**
     * Handle the SupplierOrderDetail "updated" event.
     */
    public function updated(SupplierOrderDetail $supplierOrderDetail): void
    {
        //
        $changes = [];
        foreach ($supplierOrderDetail->getChanges() as $key => $value) {
            if (!in_array($key, ['updated_at', 'created_at'])) {
                $changes[$key] = [
                    'old' => $supplierOrderDetail->getOriginal($key),
                    'new' => $value,
                ];
            }
        }
        if (!empty($changes)) {
            $this->logAudit($supplierOrderDetail, 'update', $changes);
        }
    }

    /**
     * Handle the SupplierOrderDetail "deleted" event.
     */
    public function deleted(SupplierOrderDetail $supplierOrderDetail): void
    {
        //
        $this->logAudit($supplierOrderDetail, 'delete');
    }

    /**
     * Handle the SupplierOrderDetail "restored" event.
     */
    public function restored(SupplierOrderDetail $supplierOrderDetail): void
    {
        //
    }

    /**
     * Handle the SupplierOrderDetail "force deleted" event.
     */
    public function forceDeleted(SupplierOrderDetail $supplierOrderDetail): void
    {
        //
    }
}
