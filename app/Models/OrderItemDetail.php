<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemDetail extends Model
{
    protected $fillable = [
        'order_item_id',
        'field_name',
        'field_value'
    ];

    public function item()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
