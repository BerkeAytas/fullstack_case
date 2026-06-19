<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with(['fieldValues.field', 'fieldValues.option', 'variations.options.field'])
            ->where('slug', $slug)
            ->firstOrFail();

        return new ProductResource($product);
    }

  public function store(StoreProductRequest $request)
{
    $product = Product::create($request->safe()->except('fields'));

    if ($request->filled('fields')) {
        foreach ($request->input('fields') as $field) {
            $product->fieldValues()->create($field);
        }
    }

    return new ProductResource($product->load('fieldValues.field', 'fieldValues.option', 'variations.options.field'));
}

public function update(UpdateProductRequest $request, int $id)
{
    $product = Product::findOrFail($id);
    $product->update($request->safe()->except('fields'));

    if ($request->has('fields')) {
        foreach ($request->input('fields') as $field) {
            $product->fieldValues()->updateOrCreate(
                ['product_field_id' => $field['product_field_id']],
                ['value' => $field['value'] ?? null, 'product_field_option_id' => $field['product_field_option_id'] ?? null]
            );
        }
    }

    return new ProductResource($product->load('fieldValues.field', 'fieldValues.option', 'variations.options.field'));
}
}