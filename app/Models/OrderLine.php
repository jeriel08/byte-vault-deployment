<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    protected $table = 'orderline';
    protected $primaryKey = 'orderLineID';
    protected $fillable = ['productID', 'orderID', 'quantity', 'price'];

    public function order()
    {
        return $this->belongsTo(PointOfSale::class, 'orderID', 'orderID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID', 'productID');
    }
}