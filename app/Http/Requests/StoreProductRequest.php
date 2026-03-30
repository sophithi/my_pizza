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
            'name'        => 'required|string|max:255',
            'sku'         => 'required|string|unique:products,sku',
            'price_usd'   => 'required|numeric|min:0',
            'price_khr'   => 'nullable|numeric|min:0',
            'category'    => 'required|string|max:255',
            'unit'        => 'nullable|string|max:50',
            'supplier'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Product name is required.',
            'sku.required'       => 'SKU is required.',
            'sku.unique'         => 'This SKU already exists.',
            'price_usd.required' => 'Price (USD) is required.',
            'price_usd.numeric'  => 'Price must be a number.',
            'category.required'  => 'Category is required.',
        ];
    }

    protected function passedValidation(): void
    {
        // Auto-calculate KHR if not provided
        if (!$this->price_khr && $this->price_usd) {
            $this->merge([
                'price_khr' => round($this->price_usd * 4100, 2),
            ]);
        }
    }
}