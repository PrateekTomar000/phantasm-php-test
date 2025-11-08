<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'contact', 'address', 'latitude', 'longitude'
    ];

    // Relationship with Product (many-to-many)
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
