<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\User;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_id',
        'quantity',
        'price', // price snapshot
    ];

    // Relation to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relation to User (Customer)
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
