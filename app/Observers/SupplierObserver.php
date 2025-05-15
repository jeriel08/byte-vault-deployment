<?php

namespace App\Observers;

use App\Models\Supplier;
use App\Traits\LogsAudit;

class SupplierObserver
{
    use LogsAudit;

    /**
     * Handle the Supplier "created" event.
     */
    public function created(Supplier $supplier): void
    {
        //
        $this->logAudit($supplier, 'create');
    }

    /**
     * Handle the Supplier "updated" event.
     */
    public function updated(Supplier $supplier): void
    {
        //
        $changes = [];
        foreach ($supplier->getChanges() as $key => $value) {
            if (!in_array($key, ['updated_at', 'created_at'])) {
                $changes[$key] = [
                    'old' => $supplier->getOriginal($key),
                    'new' => $value,
                ];
            }
        }
        if (!empty($changes)) {
            $this->logAudit($supplier, 'update', $changes);
        }
    }

    /**
     * Handle the Supplier "deleted" event.
     */
    public function deleted(Supplier $supplier): void
    {
        //
        $this->logAudit($supplier, 'delete');
    }

    /**
     * Handle the Supplier "restored" event.
     */
    public function restored(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "force deleted" event.
     */
    public function forceDeleted(Supplier $supplier): void
    {
        //
    }
}
