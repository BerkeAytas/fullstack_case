<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'image_url' => $this->image_url,
            'fields' => $this->fieldValues->map(fn ($fv) => [
                'name' => $fv->field->name,
                'type' => $fv->field->type,
                'value' => $fv->value,
                'option' => $fv->option?->label,
            ]),
            'variations' => $this->variations->map(fn ($v) => [
                'id' => $v->id,
                'sku' => $v->sku,
                'price' => $v->price,
                'stock' => $v->stock,
                'options' => $v->options->map(fn ($o) => [
                    'field' => $o->field->name,
                    'value' => $o->label,
                ]),
            ]),
        ];
    }
}