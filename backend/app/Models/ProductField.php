<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductField extends Model
{
protected $fillable = ['name', 'type', 'is_variation'];

    public function options()
    {
        return $this->hasMany(ProductFieldOption::class);
    }
}