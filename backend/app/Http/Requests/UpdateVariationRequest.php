<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVariationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('product_variations', 'sku')->ignore($this->route('id'))],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'option_ids' => ['sometimes', 'array', 'min:1'],
            'option_ids.*' => ['integer', 'exists:product_field_options,id'],
        ];
    }
}