<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'postal_code',
        'alias',
        'recipient_name',
        'phone',
        'street',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'references',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}