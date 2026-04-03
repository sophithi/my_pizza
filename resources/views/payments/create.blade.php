@extends('layouts.app')

@section('title', 'Record Payment')

@push('styles')
<style>
    :root {
        --accent: #e85d24;
        --bg: #f4f5f7;
        --surface: #ffffff;
        --border: #e9ecef;
        --text: #1a1d29;
        --text-muted: #6c757d;
        --danger: #dc3545;
    }

    body { background: var(--bg); }

    .form-container {
        max-width: 600px;
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

    .form-help {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 6px;
    }

    .tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        border-bottom: 2px solid var(--border);
    }

    .tab-button {
        padding: 12px 16px;
        border: none;
        background: transparent;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        font-weight: 600;
        color: var(--text-muted);
        transition: all 0.2s;
    }

    .tab-button.active {
        color: var(--accent);
        border-bottom-color: var(--accent);
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-cancel:hover {
        background: var(--bg);
        color: var(--text);
        border-color: var(--text);
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 24px;
        border-left: 4px solid var(--danger);
    }

    .info-box {
        background: #e7f3ff;
        border-left: 4px solid #0d6efd;
        padding: 12px;
        border-radius: 6px;
        font-size: 12px;
        color: #004085;
        margin-bottom: 16px;
    }
</style>
@endpush

@section('content')

<div class="form-container">
    <!-- Header -->
    <div class="form-header">
        <h1 class="form-title">💳 Record Payment</h1>
        <p class="form-subtitle">Record a new payment for an order or invoice</p>
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
        <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
            @csrf

            <!-- Tabs for Order vs Invoice -->
            <div class="tabs">
                <button type="button" class="tab-button active" data-tab="order-tab">
                    Order Payment
                </button>
                <button type="button" class="tab-button" data-tab="invoice-tab">
                    Invoice Payment
                </button>
            </div>

            <!-- Order Payment Tab -->
            <div id="order-tab" class="tab-content active">
                <div class="form-group">
                    <label class="form-label">Select Order *</label>
                    <select name="order_id" class="form-select" id="orderSelect">
                        <option value="">-- Choose an order --</option>
                        @foreach($orders as $order)
                        <option value="{{ $order->id }}"
                            data-amount="{{ $order->total_amount }}"
                            data-paid="{{ $order->payments->sum('amount') }}"
                            {{ old('order_id') == $order->id ? 'selected' : '' }}>
                            Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} - Customer: {{ $order->customer->name }} - Total: ${{ number_format($order->total_amount, 2) }}
                        </option>
                        @endforeach
                    </select>
                    <div class="form-help">Select the order to record payment for</div>
                    <div id="orderInfo" style="margin-top: 12px;"></div>
                </div>
                <input type="hidden" name="order_type" value="order">
            </div>

            <!-- Invoice Payment Tab -->
            <div id="invoice-tab" class="tab-content">
                <div class="form-group">
                    <label class="form-label">Select Invoice *</label>
                    <select name="invoice_id" class="form-select" id="invoiceSelect">
                        <option value="">-- Choose an invoice --</option>
                        @foreach($invoices as $invoice)
                        <option value="{{ $invoice->id }}"
                            data-amount="{{ $invoice->amount }}"
                            {{ old('invoice_id') == $invoice->id ? 'selected' : '' }}>
                            Invoice #{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }} - Amount: ${{ number_format($invoice->amount, 2) }}
                        </option>
                        @endforeach
                    </select>
                    <div class="form-help">Select the invoice to record payment for</div>
                </div>
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label class="form-label">Payment Amount ($) *</label>
                <input type="number" name="amount" class="form-input" min="0.01" step="0.01" 
                    value="{{ old('amount') }}" placeholder="0.00" required>
                <div class="form-help">Enter the amount paid</div>
            </div>

            <!-- Payment Method -->
            <div class="form-group">
                <label class="form-label">Payment Method *</label>
                <select name="method" class="form-select" required>
                    <option value="">-- Select method --</option>
                    <option value="cash" {{ old('method') == 'cash' ? 'selected' : '' }}>💵 Cash</option>
                    <option value="card" {{ old('method') == 'card' ? 'selected' : '' }}>💳 Credit/Debit Card</option>
                    <option value="bank_transfer" {{ old('method') == 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                    <option value="check" {{ old('method') == 'check' ? 'selected' : '' }}>✓ Check</option>
                    <option value="other" {{ old('method') == 'other' ? 'selected' : '' }}>📝 Other</option>
                </select>
            </div>

            <!-- Reference -->
            <div class="form-group">
                <label class="form-label">Reference (Optional)</label>
                <input type="text" name="reference" class="form-input" 
                    placeholder="Check #, Card last 4, Transfer ID, etc."
                    value="{{ old('reference') }}">
                <div class="form-help">Any reference information for this payment</div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">Record Payment</button>
                <a href="{{ route('payments.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Tab switching
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            // Remove active class from all buttons and contents
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Order selection handler
    document.getElementById('orderSelect').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const amount = parseFloat(option.dataset.amount) || 0;
        const paid = parseFloat(option.dataset.paid) || 0;
        const due = amount - paid;
        
        const infoDiv = document.getElementById('orderInfo');
        if (this.value) {
            infoDiv.innerHTML = `
                <div class="info-box">
                    <strong>Total Amount:</strong> $${amount.toFixed(2)}<br>
                    <strong>Amount Paid:</strong> $${paid.toFixed(2)}<br>
                    <strong>Amount Due:</strong> $${due.toFixed(2)}
                </div>
            `;
            document.querySelector('input[name="amount"]').max = due;
        } else {
            infoDiv.innerHTML = '';
            document.querySelector('input[name="amount"]').max = '';
        }
    });

    // Initial trigger
    if (document.getElementById('orderSelect').value) {
        document.getElementById('orderSelect').dispatchEvent(new Event('change'));
    }
</script>
@endpush

@endsection