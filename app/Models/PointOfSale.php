<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointOfSale extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'orderID';

    protected $fillable = [
        'customerID',
        'total_items',
        'payment_status',
        'gcash_number',
        'reference_number',
        'amount_received',
        'change',
        'total',
        'created_by',
        'created_at',
    ];

    public function orderLines()
    {
        return $this->hasMany(Orderline::class, 'orderID', 'orderID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerID', 'customerID');
    }

    public static function getActiveCategories()
    {
        return Category::where('categoryStatus', 'Active')->get();
    }

    public static function getActiveBrands()
    {
        return Brand::where('brandStatus', 'Active')->get();
    }
}
