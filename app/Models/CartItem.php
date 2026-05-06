<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'customer_name',
        'customer_phone',
        'quantity',
        'size',
        'color',
        'description',
        'reference_images',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'reference_images' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}