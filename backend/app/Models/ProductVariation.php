<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
protected $fillable = ['product_id', 'sku', 'price', 'stock'];

public function product()
{
    return $this->belongsTo(Product::class);
}

public function options()
{
    return $this->belongsToMany(ProductFieldOption::class, 'product_variation_options');
}
}
