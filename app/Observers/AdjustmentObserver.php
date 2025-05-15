<?php

namespace App\Observers;

use App\Models\Adjustment;
use App\Traits\LogsAudit;

class AdjustmentObserver
{
    use LogsAudit;

    /**
     * Handle the Adjustment "created" event.
     */
    public function created(Adjustment $adjustment): void
    {
        //
        $this->logAudit($adjustment, 'created');
    }

    /**
     * Handle the Adjustment "updated" event.
     */
    public function updated(Adjustment $adjustment): void
    {
        //
        $changes = [];
        foreach ($adjustment->getChanges() as $key => $value) {
            if (!in_array($key, ['updated_at', 'created_at'])) {
                $changes[$key] = [
                    'old' => $adjustment->getOriginal($key),
                    'new' => $value,
                ];
            }
        }
        if (!empty($changes)) {
            $this->logAudit($adjustment, 'update', $changes);
        }
    }

    /**
     * Handle the Adjustment "deleted" event.
     */
    public function deleted(Adjustment $adjustment): void
    {
        //
        $this->logAudit($adjustment, 'delete');
    }

    /**
     * Handle the Adjustment "restored" event.
     */
    public function restored(Adjustment $adjustment): void
    {
        //
    }

    /**
     * Handle the Adjustment "force deleted" event.
     */
    public function forceDeleted(Adjustment $adjustment): void
    {
        //
    }
}
