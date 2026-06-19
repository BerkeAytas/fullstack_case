<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'price', 'stock', 'image_url'];

    public function fieldValues()
    {
        return $this->hasMany(ProductFieldValue::class);
    }
    public function variations()
{
    return $this->hasMany(ProductVariation::class);
}
}