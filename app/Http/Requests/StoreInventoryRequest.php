<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id|unique:inventories',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'warehouse_location' => 'nullable|string|max:100',
        ];
    }
}
