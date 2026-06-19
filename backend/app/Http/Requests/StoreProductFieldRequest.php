<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:text,number,checkbox,radio,select'],
            'is_variation' => ['sometimes', 'boolean'],
            'options' => ['required_if:type,select,radio', 'array'],
            'options.*' => ['string', 'max:255'],
        ];
    }
}