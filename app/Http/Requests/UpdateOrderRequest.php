<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Staff, Managers, Bosses, and Admins can update orders
        $user = auth()->user();
        return $user && in_array($user->role, ['staff', 'manager', 'boss', 'admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function prepareForValidation(): void
    {
        $decoded = json_decode((string) $this->input('order_items', '[]'), true);
        $this->merge(['validated_items' => $decoded]);
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'delivery_id' => 'required|exists:deliveries,id',
            'small_pack_qty' => 'nullable|integer|min:0',
            'big_pack_qty' => 'nullable|integer|min:0',
            'order_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'taxi_phone' => 'nullable|string|max:20',
            'discount_amount' => 'nullable|numeric|min:0',
            'delivery_fee_khr' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string',
            'invoice_template' => 'nullable|in:family,special,mayo,tamon',
            'order_items' => 'required|json',
            'validated_items' => 'required|array|min:1',
            'validated_items.*' => 'required|array',
            'validated_items.*.product_id' => 'required|integer|exists:products,id',
            'validated_items.*.quantity' => 'required|integer|min:1',
            'validated_items.*.unit_price' => 'required|numeric|min:0',
            'validated_items.*.unit_price_khr' => 'nullable|numeric|min:0',
            'validated_items.*.total_price' => 'required|numeric|min:0',
            'validated_items.*.discount_percent' => 'nullable|numeric|between:0,100',
            'validated_items.*.is_custom_price' => 'nullable|boolean',
            'free_products' => 'nullable|array',
            'free_products.*.product_id' => 'nullable|exists:products,id,allow_free,1',
            'free_products.*.qty' => 'nullable|integer|min:1',
        ];
    }
}
