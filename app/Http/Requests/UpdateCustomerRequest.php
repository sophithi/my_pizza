<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && !auth()->user()->isStaffInventory();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:facebook,telegram',
            'email' => 'nullable|email|unique:customers,email,' . $this->customer->id . '|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
