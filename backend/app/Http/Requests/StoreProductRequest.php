<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'string', 'max:255'],
            'fields' => ['sometimes', 'array'],
'fields.*.product_field_id' => ['required_with:fields', 'integer', 'exists:product_fields,id'],
'fields.*.value' => ['nullable', 'string'],
'fields.*.product_field_option_id' => ['nullable', 'integer', 'exists:product_field_options,id'],
        ];
    }
}