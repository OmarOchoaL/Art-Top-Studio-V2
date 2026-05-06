<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\User;

class Order extends Model
{
    protected $fillable = [
    'user_id',
    'customer_name',
    'customer_phone',
    'origin',
    'status',
    'total',
    'anticipo',
    'fecha_entrega',
    'payment_method',
    'image',
    'shipping_address_id',
    'paypal_order_id',
];

    protected $casts = [
        'image' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}