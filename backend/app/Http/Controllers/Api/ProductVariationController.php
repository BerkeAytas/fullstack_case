<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVariationRequest;
use App\Http\Requests\UpdateVariationRequest;
use App\Models\Product;
use App\Models\ProductVariation;

class ProductVariationController extends Controller
{
    public function store(StoreVariationRequest $request, int $productId)
    {
        $product = Product::findOrFail($productId);

        $variation = $product->variations()->create($request->only(['sku', 'price', 'stock']));
        $variation->options()->attach($request->input('option_ids'));

        return response()->json($variation->load('options'), 201);
    }

    public function update(UpdateVariationRequest $request, int $id)
    {
        $variation = ProductVariation::findOrFail($id);
        $variation->update($request->only(['sku', 'price', 'stock']));

        if ($request->has('option_ids')) {
            $variation->options()->sync($request->input('option_ids'));
        }

        return response()->json($variation->load('options'));
    }
}