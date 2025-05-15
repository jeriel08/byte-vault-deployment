<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnToSupplier extends Model
{
    //
    protected $primaryKey = 'returnSupplierID';
    protected $fillable = [
        'supplierID',
        'supplierOrderID',
        'returnDate',
        'returnSupplierReason',
        'adjustmentDatePlaced',
        'completionDate',
        'cancellationDate',
        'cancellationRemark',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'returnDate' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_REJECTED = 'Rejected';

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplierID', 'supplierID');
    }

    public function supplierOrder()
    {
        return $this->belongsTo(SupplierOrder::class, 'supplierOrderID', 'supplierOrderID');
    }

    public function stockOut()
    {
        return $this->hasOne(StockOut::class, 'referenceID', 'returnSupplierID')
            ->where('referenceTable', 'return_to_suppliers');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
