<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'sku'         => 'required|string|unique:products,sku,' . $this->product->id,
            // Either KHR or USD is required; KHR is considered the base
            'price_khr'   => 'required_without:price_usd|numeric|min:0',
            'price_usd'   => 'required_without:price_khr|numeric|min:0',
            'category'    => 'required|string|max:255',
            'unit'        => 'required|string|in:kg,g,L,ml,pcs,box,pack,bag',
            'supplier'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ];
    }

    protected function passedValidation(): void
    {
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