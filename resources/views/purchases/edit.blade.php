@extends('layouts.app')

@section('title', 'Edit Purchase #' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT))

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

        body {
            background: var(--bg);
        }

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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
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
    </style>
@endpush

@section('content')

    <div class="form-container">
        <!-- Header -->
        <div class="form-header">
            <h1 class="form-title">📦 Edit Purchase</h1>
            <p class="form-subtitle">Update purchase #{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p>
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
            <form action="{{ route('purchases.update', $purchase) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Reference Number -->
                <div class="form-group">
                    <label class="form-label">Reference Number (Optional)</label>
                    <input type="text" name="reference_number" class="form-input" placeholder="PO-001, SUPP-2024-001, etc."
                        value="{{ old('reference_number', $purchase->reference_number) }}">
                </div>

                <!-- Supplier Name -->
                <div class="form-group">
                    <label class="form-label">Supplier Name *</label>
                    <input type="text" name="supplier_name" class="form-input"
                        placeholder="e.g., Fresh Foods Inc, Local Supplier"
                        value="{{ old('supplier_name', $purchase->supplier_name) }}" required>
                </div>

                <!-- Purchase Date -->
                <div class="form-group">
                    <label class="form-label">Purchase Date * (Cambodia Time - UTC+7)</label>
                    <input type="date" name="purchase_date" class="form-input"
                        value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}" required>
                </div>

                <!-- Total Amount -->
                <div class="form-group">
                    <label class="form-label">Total Amount ($) *</label>
                    <input type="number" name="total_amount" class="form-input" min="0.01" step="0.01" placeholder="0.00"
                        value="{{ old('total_amount', $purchase->total_amount) }}" required>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="">-- Select status --</option>
                        <option value="pending" {{ old('status', $purchase->status) == 'pending' ? 'selected' : '' }}>⏱
                            Pending</option>
                        <option value="received" {{ old('status', $purchase->status) == 'received' ? 'selected' : '' }}>✓
                            Received</option>
                        <option value="cancelled" {{ old('status', $purchase->status) == 'cancelled' ? 'selected' : '' }}>✕
                            Cancelled</option>
                    </select>
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea name="notes" class="form-textarea" rows="4"
                        placeholder="Add any additional notes about this purchase...">{{ old('notes', $purchase->notes) }}</textarea>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Update Purchase</button>
                    <a href="{{ route('purchases.show', $purchase) }}" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

@endsection