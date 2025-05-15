<?php

namespace App\Observers;

use App\Models\Category;
use App\Traits\LogsAudit;

class CategoryObserver
{
    use LogsAudit;
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        //
        $this->logAudit($category, 'create');
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        //
        $changes = [];
        foreach ($category->getChanges() as $key => $value) {
            if (!in_array($key, ['updated_at', 'created_at'])) {
                $changes[$key] = [
                    'old' => $category->getOriginal($key),
                    'new' => $value,
                ];
            }
        }
        if (!empty($changes)) {
            $this->logAudit($category, 'update', $changes);
        }
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        //
        $this->logAudit($category, 'delete');
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        //
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
