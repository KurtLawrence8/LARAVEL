<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'admin_id']; // Include admin_id in fillable attributes

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
