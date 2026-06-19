<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFieldOption extends Model
{
    protected $fillable = ['product_field_id', 'label'];

    public function field()
    {
        return $this->belongsTo(ProductField::class, 'product_field_id');
    }
}