<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOutDetail extends Model
{
    //
    protected $primaryKey = 'stockOutDetailID';
    protected $fillable = ['stockOutID', 'productID', 'quantity'];

    public function stockOut()
    {
        return $this->belongsTo(StockOut::class, 'stockOutID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID');
    }
}
