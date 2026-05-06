<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Color;
use App\Models\ProductColorSize;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity',
        'size',
        'category_id',
        'image'
    ];

    public function colores()
    {
        return $this->belongsToMany(
            Color::class,
            'color_producto',
            'producto_id',
            'color_id'
        )->withPivot('cantidad');
    }

    public function colorSizes()
{
    return $this->hasMany(ProductColorSize::class);
}

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

}