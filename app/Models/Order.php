<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    protected $primaryKey = 'orderID';
    protected $fillable = ['orderID', 'customerID', 'date', 'total', 'total_items', 'amount_received', 'change', 'created_by', 'updated_by'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerID', 'customerID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'productID', 'productID');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'employeeID');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'employeeID');
    }

    public function orderlines()
    {
        return $this->hasMany(OrderLine::class, 'orderID', 'orderID');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (Auth::check()) {
                $order->created_by = Auth::id();
            }
        });

        static::updating(function ($order) {
            if (Auth::check()) {
                $order->updated_by = Auth::id();
            }
        });
    }
}
