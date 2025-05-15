<?php

namespace App\Traits;

use App\Models\AuditLog;
use App\Models\AuditLogDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait LogsAudit
{
    protected function logAudit($model, $actionType, $changes = null)
    {
        DB::transaction(function () use ($model, $actionType, $changes) {
            $log = AuditLog::create([
                'tableName' => $model->getTable(),
                'recordID' => $model->getKey(),
                'actionType' => $actionType,
                'employeeID' => Auth::id() ?? $model->employeeID ?? $model->getKey(),
                'timestamp' => now(),
            ]);

            if ($actionType === 'update' && $changes) {
                foreach ($changes as $column => $change) {
                    AuditLogDetail::create([
                        'logID' => $log->logID,
                        'columnName' => $column,
                        'oldValue' => $change['old'],
                        'newValue' => $change['new'],
                    ]);
                }
            } elseif ($actionType === 'create') {
                AuditLogDetail::create([
                    'logID' => $log->logID,
                    'columnName' => 'created',
                    'oldValue' => null,
                    'newValue' => json_encode($model->toArray()),
                ]);
            } elseif ($actionType === 'delete') {
                AuditLogDetail::create([
                    'logID' => $log->logID,
                    'columnName' => 'deleted',
                    'oldValue' => json_encode($model->toArray()),
                    'newValue' => null,
                ]);
            }
        });
    }
}
