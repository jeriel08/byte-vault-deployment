<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLogDetail extends Model
{
    //
    protected $primaryKey = 'detailID';
    protected $fillable = [
        'logID',
        'columnName',
        'oldValue',
        'newValue',
    ];
    public $timestamps = false;
}
