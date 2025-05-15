<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $primaryKey = 'productID';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'productName',
        'productDescription',
        'brandID',
        'categoryID',
        'price',
        'stockQuantity',
        'productStatus',
        'created_by',
        'updated_by',
    ];
    public $timestamps = true;

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brandID', 'brandID');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryID', 'categoryID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function orderLines()
    {
        return $this->hasMany(Orderline::class, 'productID', 'productID');
    }
}
