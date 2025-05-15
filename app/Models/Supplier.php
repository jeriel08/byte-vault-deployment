<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    use HasFactory;

    // Define the primary key (if different from 'id')
    protected $primaryKey = 'supplierID';

    // Disable auto-incrementing for the primary key (if it's not an integer)
    public $incrementing = false;

    // Define fillable fields for mass assignment
    protected $fillable = [
        'supplierName',
        'supplierAddress',
        'supplierPhoneNumber',
        'supplierProfileImage',
        'supplierStatus',
        'created_by',
        'updated_by',
    ];

    // Define relationships (optional)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
