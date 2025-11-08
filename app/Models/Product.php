<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'description', 'price', 'status', 'brand_id', 'category_id'];

    protected $casts = [
        'category_ids' => 'array', // JSON to array
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function categories()
    {
        return Category::whereIn('id', $this->category_ids)->get();
    }
}
