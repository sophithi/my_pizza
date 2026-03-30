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
            'price_usd'   => 'required|numeric|min:0',
            'price_khr'   => 'nullable|numeric|min:0',
            'category'    => 'required|string|max:255',
            'unit'        => 'nullable|string|max:50',
            'supplier'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
        ];
    }

    protected function passedValidation(): void
    {
        if (!$this->price_khr && $this->price_usd) {
            $this->merge([
                'price_khr' => round($this->price_usd * 4100, 2),
            ]);
        }
    }
}