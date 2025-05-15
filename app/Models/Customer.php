<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'customerID'; // Tell Laravel to use customerID, not id
    protected $fillable = ['name'];       // Fields you can fill via create/store
    
}