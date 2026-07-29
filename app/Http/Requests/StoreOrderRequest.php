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
            'small_pack_qty' => 'nullable|integer|min:0',
            'big_pack_qty' => 'nullable|integer|min:0',
            'order_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'taxi_phone' => 'nullable|string|max:20',
            'discount_amount' => 'nullable|numeric|min:0',
            'delivery_fee_khr' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'in:pending,processing,completed,cancelled',
            'payment_status' => 'in:unpaid,partial,paid',
            'payment_method' => 'nullable|in:Cash,ABA,ACLEDA,Wing',
            'notes' => 'nullable|string',
            'order_items' => 'required|json',
            'free_products' => 'nullable|array',
            'free_products.*.product_id' => 'nullable|exists:products,id',
            'free_products.*.qty' => 'nullable|integer|min:1',
        ];
    }
}
