<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierOrder extends Model
{
    //
    protected $primaryKey = 'supplierOrderID';
    protected $fillable = [
        'supplierID',
        'orderDate',
        'expectedDeliveryDate',
        'totalCost',
        'orderPlacedDate',
        'receivedDate',
        'cancelledDate',
        'cancellationRemark',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    // Cast dates to Carbon instances
    protected $casts = [
        'orderDate' => 'date',
        'expectedDeliveryDate' => 'date',
        'orderPlacedDate' => 'date',
        'receivedDate' => 'date',
        'cancelledDate' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierID', 'supplierID');
    }

    public function details()
    {
        return $this->hasMany(SupplierOrderDetail::class, 'supplierOrderID', 'supplierOrderID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getStatusAttribute()
    {
        if ($this->receivedDate) {
            return 'Received';
        }
        if ($this->cancelledDate) {
            return 'Cancelled';
        }
        return 'Pending';
    }

    public function getStatusBadgeClassAttribute()
    {
        if ($this->receivedDate) {
            return 'bg-success';
        }
        if ($this->cancelledDate) {
            return 'bg-danger';
        }
        return 'bg-warning';
    }
}
