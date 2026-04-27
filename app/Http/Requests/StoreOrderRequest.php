<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Staff, Managers, and Admins can create orders
        $user = auth()->user();
        return $user && in_array($user->role, ['staff', 'manager', 'admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'delivery_id' => 'nullable|exists:deliveries,id',
            'order_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'delivery_fee_khr' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'in:pending,processing,completed,cancelled',
            'payment_status' => 'in:unpaid,partial,paid',
            'notes' => 'nullable|string',
            'order_items' => 'required|json',
        ];
    }
}
