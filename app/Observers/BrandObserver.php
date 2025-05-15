<?php

namespace App\Observers;

use App\Models\Brand;
use App\Traits\LogsAudit;

class BrandObserver
{
    use LogsAudit;

    /**
     * Handle the Brand "created" event.
     */
    public function created(Brand $brand): void
    {
        //
        $this->logAudit($brand, 'create');
    }

    /**
     * Handle the Brand "updated" event.
     */
    public function updated(Brand $brand): void
    {
        //
        $changes = [];
        foreach ($brand->getChanges() as $key => $value) {
            if (!in_array($key, ['updated_at', 'created_at'])) {
                $changes[$key] = [
                    'old' => $brand->getOriginal($key),
                    'new' => $value,
                ];
            }
        }
        if (!empty($changes)) {
            $this->logAudit($brand, 'update', $changes);
        }
    }

    /**
     * Handle the Brand "deleted" event.
     */
    public function deleted(Brand $brand): void
    {
        //
        $this->logAudit($brand, 'delete');
    }

    /**
     * Handle the Brand "restored" event.
     */
    public function restored(Brand $brand): void
    {
        //
    }

    /**
     * Handle the Brand "force deleted" event.
     */
    public function forceDeleted(Brand $brand): void
    {
        //
    }
}
