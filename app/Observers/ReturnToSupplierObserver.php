<?php

namespace App\Observers;

use App\Models\ReturnToSupplier;
use App\Traits\LogsAudit;

class ReturnToSupplierObserver
{
    use LogsAudit;

    /**
     * Handle the ReturnToSupplier "created" event.
     */
    public function created(ReturnToSupplier $returnToSupplier): void
    {
        //
        $this->logAudit($returnToSupplier, 'created');
    }

    /**
     * Handle the ReturnToSupplier "updated" event.
     */
    public function updated(ReturnToSupplier $returnToSupplier): void
    {
        //
        $changes = [];
        foreach ($returnToSupplier->getChanges() as $key => $value) {
            if (!in_array($key, ['updated_at', 'created_at'])) {
                $changes[$key] = [
                    'old' => $returnToSupplier->getOriginal($key),
                    'new' => $value,
                ];
            }
        }
        if (!empty($changes)) {
            $this->logAudit($returnToSupplier, 'update', $changes);
        }
    }

    /**
     * Handle the ReturnToSupplier "deleted" event.
     */
    public function deleted(ReturnToSupplier $returnToSupplier): void
    {
        //
        $this->logAudit($returnToSupplier, 'deleted');
    }

    /**
     * Handle the ReturnToSupplier "restored" event.
     */
    public function restored(ReturnToSupplier $returnToSupplier): void
    {
        //
    }

    /**
     * Handle the ReturnToSupplier "force deleted" event.
     */
    public function forceDeleted(ReturnToSupplier $returnToSupplier): void
    {
        //
    }
}
