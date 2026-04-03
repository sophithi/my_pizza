@extends('layouts.app')

@section('title', 'Edit Delivery')

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

    .form-input:disabled, .form-select:disabled {
        background: #f8f9fa;
        color: var(--text-muted);
        cursor: not-allowed;
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

    .status-info {
        padding: 12px;
        background: #e7f3ff;
        border-radius: 6px;
        margin-bottom: 24px;
        border-left: 4px solid var(--accent);
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-preparing { background: #e2e3e5; color: #383d41; }
    .status-out_for_delivery { background: #cfe2ff; color: #084298; }
    .status-delivered { background: #d1e7dd; color: #0f5132; }
    .status-cancelled { background: #f8d7da; color: #721c24; }
</style>
@endpush

@section('content')

<div class="form-container">
    <!-- Header -->
    <div class="form-header">
        <h1 class="form-title">Edit Delivery</h1>
        <p class="form-subtitle">Update delivery information for Delivery #{{ $delivery->id }}</p>
    </div>

    <!-- Status Info -->
    <div class="status-info">
        <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px; font-weight: 600; text-transform: uppercase;">Current Status</div>
        <span class="status-badge status-{{ $delivery->status }}">
            @switch($delivery->status)
                @case('pending') ⏳ Pending @break
                @case('preparing') 📦 Preparing @break
                @case('out_for_delivery') 🚗 Out for Delivery @break
                @case('delivered') ✅ Delivered @break
                @case('cancelled') ❌ Cancelled @break
            @endswitch
        </span>
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
        <form action="{{ route('deliveries.update', $delivery->id) }}" method="POST">
            @csrf @method('PUT')

            <!-- Order Info (disabled) -->
            <div class="form-group">
                <label class="form-label">Order #</label>
                <input type="text" class="form-input" disabled
                    value="Order #{{ str_pad($delivery->order->id, 4, '0', STR_PAD_LEFT) }} - {{ $delivery->order->customer->name }}">
                <small style="color: var(--text-muted); margin-top: 4px; display: block;">Cannot change order (create new delivery if needed)</small>
            </div>

            <!-- Delivery Address -->
            <div class="form-group">
                <label class="form-label">Delivery Address *</label>
                <input type="text" name="delivery_address" class="form-input"
                    placeholder="123 Main St, Phnom Penh"
                    value="{{ old('delivery_address', $delivery->delivery_address) }}" required>
            </div>

            <!-- Delivery Phone -->
            <div class="form-group">
                <label class="form-label">Delivery Phone (Optional)</label>
                <input type="tel" name="delivery_phone" class="form-input"
                    placeholder="+855 12 345 678"
                    value="{{ old('delivery_phone', $delivery->delivery_phone) }}">
            </div>

            <!-- Scheduled Date/Time -->
            <div class="form-group">
                <label class="form-label">Scheduled Delivery Date & Time * (Cambodia Time - UTC+7)</label>
                <input type="datetime-local" name="scheduled_delivery_at" class="form-input"
                    value="{{ old('scheduled_delivery_at', $delivery->scheduled_delivery_at->format('Y-m-d\TH:i')) }}" required>
            </div>

            <!-- Driver Section -->
            <div style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 24px;">
                <div style="font-size: 12px; font-weight: 700; color: #6c757d; text-transform: uppercase; margin-bottom: 16px; letter-spacing: 0.5px;">👨‍🚗 Driver Information</div>
                
                <div class="form-section">
                    <div>
                        <label class="form-label">Driver Name (Optional)</label>
                        <input type="text" name="driver_name" class="form-input"
                            placeholder="John Doe"
                            value="{{ old('driver_name', $delivery->driver_name) }}">
                    </div>
                    <div>
                        <label class="form-label">Driver Phone (Optional)</label>
                        <input type="tel" name="driver_phone" class="form-input"
                            placeholder="+855 12 345 678"
                            value="{{ old('driver_phone', $delivery->driver_phone) }}">
                    </div>
                </div>
            </div>

            <!-- Delivery Fee -->
            <div class="form-group">
                <label class="form-label">Delivery Fee ($) *</label>
                <input type="number" name="delivery_fee" class="form-input"
                    min="0" step="0.01" placeholder="0.00"
                    value="{{ old('delivery_fee', $delivery->delivery_fee) }}" required>
            </div>

            <!-- Status Selection -->
            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="pending" {{ $delivery->status === 'pending' ? 'selected' : '' }}>
                        ⏳ Pending
                    </option>
                    <option value="preparing" {{ $delivery->status === 'preparing' ? 'selected' : '' }}>
                        📦 Preparing
                    </option>
                    <option value="out_for_delivery" {{ $delivery->status === 'out_for_delivery' ? 'selected' : '' }}>
                        🚗 Out for Delivery
                    </option>
                    <option value="delivered" {{ $delivery->status === 'delivered' ? 'selected' : '' }}>
                        ✅ Delivered
                    </option>
                    <option value="cancelled" {{ $delivery->status === 'cancelled' ? 'selected' : '' }}>
                        ❌ Cancelled
                    </option>
                </select>
            </div>

            <!-- Notes -->
            <div class="form-group">
                <label class="form-label">Delivery Notes (Optional)</label>
                <textarea name="notes" class="form-textarea" rows="3"
                    placeholder="Gate code, building instructions, preferences, etc.">{{ old('notes', $delivery->notes) }}</textarea>
            </div>

            <!-- Rejection Reason (shown for cancelled deliveries) -->
            @if($delivery->status === 'cancelled')
            <div class="form-group">
                <label class="form-label">Cancellation Reason (Optional)</label>
                <textarea name="rejection_reason" class="form-textarea" rows="3"
                    placeholder="Reason for cancellation...">{{ old('rejection_reason', $delivery->rejection_reason) }}</textarea>
            </div>
            @endif

            <!-- Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">Update Delivery</button>
                <a href="{{ route('deliveries.show', $delivery->id) }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
