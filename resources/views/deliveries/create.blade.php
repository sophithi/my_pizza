@extends('layouts.app')

@section('title', 'Schedule Delivery')

@push('styles')
<style>
    :root {
        --accent: #e85d24;
        --bg: #f4f5f7;
        --surface: #ffffff;
        --border: #e9ecef;
        --text: #1a1d29;
        --text-muted: #6c757d;
    }

    body { background: var(--bg); }

    .form-container {
        max-width: 700px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .form-header {
        margin-bottom: 32px;
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 8px 0;
    }

    .form-subtitle {
        color: var(--text-muted);
        font-size: 14px;
    }

    .form-card {
        background: var(--surface);
        padding: 32px;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        color: var(--text);
        font-weight: 600;
        font-size: 14px;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(232, 93, 36, 0.1);
    }

    .form-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 32px;
    }

    .btn-submit {
        background: var(--accent);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.2s;
    }

    .btn-submit:hover {
        background: #d64a1a;
    }

    .btn-cancel {
        background: transparent;
        color: var(--text-muted);
        padding: 12px 24px;
        border: 1px solid var(--border);
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-cancel:hover {
        background: var(--bg);
        border-color: var(--text);
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 24px;
        border-left: 4px solid #dc3545;
    }

    .form-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    .form-section > * {
        margin-bottom: 0;
    }

    .two-cols {
        grid-column: span 2;
    }
</style>
@endpush

@section('content')

<div class="form-container">
    <!-- Header -->
    <div class="form-header">
        <h1 class="form-title">🚗 Schedule Delivery</h1>
        <p class="form-subtitle">Create a new delivery for an order</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="error-message">
        <strong>Please fix the following errors:</strong>
        <ul style="margin: 8px 0 0 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('deliveries.store') }}" method="POST">
            @csrf

            <!-- Select Order -->
            <div class="form-group">
                <label class="form-label">Select Order *</label>
                <select name="order_id" class="form-select" id="orderSelect" required>
                    <option value="">-- Choose an order --</option>
                    @foreach($orders as $order)
                    <option value="{{ $order->id }}" 
                        data-customer="{{ $order->customer->name }}"
                        data-phone="{{ $order->customer->phone }}"
                        data-location="{{ $order->customer->location }}">
                        Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} 
                        - {{ $order->customer->name }} 
                        - ${{ number_format($order->total_amount, 2) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Delivery Type -->
            <div class="form-group">
                <label class="form-label">Delivery Type *</label>
                <select name="delivery_type" class="form-select" id="deliveryType" required>
                    <option value="">-- Select delivery type --</option>
                    @foreach(\App\Models\Delivery::getDeliveryTypes() as $key => $label)
                    <option value="{{ $key }}" {{ old('delivery_type') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Service Name -->
            <div class="form-group">
                <label class="form-label">Service Name (Optional)</label>
                <input type="text" name="name_service" class="form-input"
                    placeholder="e.g., Fast Logistic, Taxi XYZ, Your Team, etc."
                    value="{{ old('name_service') }}">
            </div>

            <!-- Price of Delivery -->
            <div class="form-group">
                <label class="form-label">Price of Delivery Service ($) (Optional)</label>
                <input type="number" name="price_of_delivery" class="form-input"
                    min="0" step="0.01" placeholder="0.00"
                    value="{{ old('price_of_delivery', '0.00') }}">
            </div>

            <!-- Delivery Address -->
            <div class="form-group">
                <label class="form-label">Delivery Address *</label>
                <input type="text" name="delivery_address" class="form-input" id="deliveryAddress"
                    placeholder="123 Main St, Phnom Penh"
                    value="{{ old('delivery_address') }}" required>
            </div>

            <!-- Delivery Phone -->
            <div class="form-group">
                <label class="form-label">Delivery Phone (Optional)</label>
                <input type="tel" name="delivery_phone" class="form-input" id="deliveryPhone"
                    placeholder="+855 12 345 678"
                    value="{{ old('delivery_phone') }}">
            </div>

            <!-- Scheduled Date/Time -->
            <div class="form-group">
                <label class="form-label">Scheduled Delivery Date & Time * (Cambodia Time - UTC+7)</label>
                <input type="datetime-local" name="scheduled_delivery_at" class="form-input"
                    value="{{ old('scheduled_delivery_at', now()->addHours(2)->format('Y-m-d\TH:i')) }}" required>
            </div>

            <!-- Driver Section -->
            <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 700; color: #6c757d; text-transform: uppercase; margin-bottom: 16px; letter-spacing: 0.5px;">👨‍🚗 Driver Information</div>
                
                <div class="form-section">
                    <div>
                        <label class="form-label">Driver Name (Optional)</label>
                        <input type="text" name="driver_name" class="form-input"
                            placeholder="John Doe"
                            value="{{ old('driver_name') }}">
                    </div>
                    <div>
                        <label class="form-label">Driver Phone (Optional)</label>
                        <input type="tel" name="driver_phone" class="form-input"
                            placeholder="+855 12 345 678"
                            value="{{ old('driver_phone') }}">
                    </div>
                </div>
            </div>

            <!-- Delivery Fee -->
            <div class="form-group">
                <label class="form-label">Delivery Fee ($) *</label>
                <input type="number" name="delivery_fee" class="form-input"
                    min="0" step="0.01" placeholder="0.00"
                    value="{{ old('delivery_fee', '3.00') }}" required>
            </div>

            <!-- Notes -->
            <div class="form-group">
                <label class="form-label">Delivery Notes (Optional)</label>
                <textarea name="notes" class="form-textarea" rows="3"
                    placeholder="Gate code, building instructions, preferences, etc.">{{ old('notes') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">Schedule Delivery</button>
                <a href="{{ route('deliveries.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Auto-fill address from selected order
    document.getElementById('orderSelect').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const location = option.dataset.location;
        const phone = option.dataset.phone;

        if (location) {
            document.getElementById('deliveryAddress').value = location;
        }
        if (phone) {
            document.getElementById('deliveryPhone').value = phone;
        }
    });
</script>

@endsection
