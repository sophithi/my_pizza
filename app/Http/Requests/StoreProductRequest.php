<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'sku'         => 'required|string|unique:products,sku',
            'price_khr'   => 'required_without:price_usd|numeric|min:0',
            'price_usd'   => 'required_without:price_khr|numeric|min:0',
            'category'    => 'required|string|max:255',
            'unit'        => 'required|string|in:kg,g,L,ml,pcs,box,box1,box2,pack,bag',
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
            'price_usd.required' => 'Price (USD) is required when KHR is empty.',
            'price_usd.numeric'  => 'Price must be a number.',
            'price_khr.required_without' => 'Price (KHR) is required when USD is empty.',
            'category.required'  => 'Category is required.',
            'unit.required'      => 'Unit is required.',
            'unit.in'            => 'Invalid unit selected.',
        ];
    }

    protected function passedValidation(): void
    {
        // Use a consistent rate (1 USD = 4,000 KHR) and calculate the missing value
        $rate = 4000;
        if (!$this->price_khr && $this->price_usd) {
            $this->merge([
                'price_khr' => (int) round($this->price_usd * $rate),
            ]);
        }

        if (!$this->price_usd && $this->price_khr) {
            $this->merge([
                'price_usd' => round($this->price_khr / $rate, 3),
            ]);
        }
    }
}