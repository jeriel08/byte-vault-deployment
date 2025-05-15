<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    //
    protected $table = 'audit_logs';
    protected $primaryKey = 'logID';
    public $timestamps = false;

    protected $fillable = [
        'tableName',
        'recordID',
        'actionType',
        'columnName',
        'oldValue',
        'newValue',
        'employeeID',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employeeID', 'employeeID');
    }

    public function details()
    {
        return $this->hasMany(AuditLogDetail::class, 'logID', 'logID');
    }
}
