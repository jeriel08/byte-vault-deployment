<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'categoryID';
    protected $fillable = ['categoryName', 'categoryDescription', 'parentCategoryID', 'categoryStatus', 'created_by', 'updated_by'];
    public $timestamps = true;

    public function products()
    {
        return $this->hasMany(Product::class, 'categoryID', 'categoryID');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parentCategoryID', 'categoryID');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parentCategoryID', 'categoryID');
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
