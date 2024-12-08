<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'brand',
        'name',
        'size',
        'price',
        'qty',
        'category_id',
        'admin_id', // Include admin_id
    ];

    // Relationship with Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with Admin
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
