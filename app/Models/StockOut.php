<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    //
    protected $table = 'stock_outs';
    protected $primaryKey = 'stockOutID';
    protected $fillable = ['reasonType', 'referenceID', 'referenceTable', 'totalQuantity', 'created_by', 'updated_by'];

    public function reference()
    {
        return $this->morphTo('reference', 'referenceTable', 'referenceID');
    }

    public function details()
    {
        return $this->hasMany(StockOutDetail::class, 'stockOutID');
    }
}
