<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check()
            && (auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isStaffInventory());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
            'warehouse_location' => 'nullable|string|max:100',
        ];
    }
}
