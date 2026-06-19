<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductFieldRequest;
use App\Http\Requests\UpdateProductFieldRequest;
use App\Models\ProductField;

class ProductFieldController extends Controller
{
    public function store(StoreProductFieldRequest $request)
    {
        $field = ProductField::create($request->only(['name', 'type', 'is_variation']));

        if ($request->filled('options')) {
            foreach ($request->input('options') as $label) {
                $field->options()->create(['label' => $label]);
            }
        }

        return response()->json($field->load('options'), 201);
    }

    public function update(UpdateProductFieldRequest $request, int $id)
    {
        $field = ProductField::findOrFail($id);
        $field->update($request->validated());

        return response()->json($field->load('options'));
    }
}