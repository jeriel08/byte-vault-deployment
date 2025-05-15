<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierOrderDetail extends Model
{
    //
    protected $primaryKey = 'supplierOrderDetailID';
    protected $fillable = [
        'supplierOrderID',
        'productID',
        'quantity',
        'unitCost',
    ];

    public function supplierOrder()
    {
        return $this->belongsTo(SupplierOrder::class, 'supplierOrderID', 'supplierOrderID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID', 'productID');
    }
}
