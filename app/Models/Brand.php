<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    //
    protected $primaryKey = 'brandID';
    protected $fillable = ['brandName', 'brandStatus', 'brandProfileImage', 'created_by', 'updated_by'];
    public $timestamps = true;

    // Relationship with products
    public function products()
    {
        return $this->hasMany(Product::class, 'brandID', 'brandID');
    }

    // Relationship with the user who created/updated the brand
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
