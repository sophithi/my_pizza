@extends('layouts.app')

@section('title', 'Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT))

@push('styles')
<style>
    :root {
        --accent: #e85d24;
        --bg: #f4f5f7;
        --surface: #ffffff;
        --border: #e9ecef;
        --text: #1a1d29;
        --text-muted: #6c757d;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --info: #0d6efd;
    }

    body { background: var(--bg); }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 40px;
        gap: 24px;
    }

    .header-info {
        flex: 1;
    }

    .order-title {
        font-size: 32px;
        font-weight: 800;
        color: var(--text);
        margin: 0 0 8px 0;
    }

    .order-meta {
        display: flex;
        gap: 16px;
        color: var(--text-muted);
        font-size: 14px;
    }

    .header-amount {
        text-align: right;
    }

    .order-amount {
        font-size: 38px;
        font-weight: 800;
        color: var(--accent);
        margin: 0 0 4px 0;
    }

    .order-currency {
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 32px;
        transition: all 0.3s ease;
    }

    .card-body {
        padding: 32px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 28px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-box {
        background: linear-gradient(135deg, rgba(232, 93, 36, 0.05) 0%, rgba(232, 93, 36, 0.02) 100%);
        padding: 20px;
        border-radius: 10px;
        border: 1px solid var(--border);
        border-left: 4px solid var(--accent);
    }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .stat-secondary {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 600;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-pending {
        background: rgba(255, 193, 7, 0.15);
        color: #856404;
    }

    .status-processing {
        background: rgba(13, 110, 253, 0.15);
        color: #0c5de4;
    }

    .status-completed {
        background: rgba(40, 167, 69, 0.15);
        color: #1e7e34;
    }

    .status-cancelled {
        background: rgba(220, 53, 69, 0.15);
        color: #b02622;
    }

    .payment-paid {
        background: rgba(40, 167, 69, 0.15);
        color: #1e7e34;
    }

    .payment-unpaid {
        background: rgba(255, 193, 7, 0.15);
        color: #856404;
    }

    .payment-partial {
        background: rgba(13, 110, 253, 0.15);
        color: #0c5de4;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .table thead th {
        padding: 16px;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text);
        border-bottom: 2px solid var(--border);
    }

    .table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.2s ease;
    }

    .table tbody tr:hover {
        background: rgba(232, 93, 36, 0.02);
    }

    .table tbody td {
        padding: 16px;
        color: var(--text);
        font-size: 14px;
    }

    .product-name {
        font-weight: 600;
        color: var(--text);
    }

    .price {
        font-weight: 600;
        color: var(--accent);
    }

    .notes-section {
        background: linear-gradient(135deg, rgba(232, 93, 36, 0.05) 0%, rgba(232, 93, 36, 0.02) 100%);
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid var(--accent);
        margin-bottom: 32px;
    }

    .notes-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }

    .notes-content {
        color: var(--text);
        line-height: 1.6;
        font-size: 14px;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent) 0%, #d94a10 100%);
        color: white;
    }

    .btn-primary:hover {
        box-shadow: 0 8px 20px rgba(232, 93, 36, 0.3);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: var(--bg);
        color: var(--text);
        border: 1px solid var(--border);
    }

    .btn-secondary:hover {
        background: var(--surface);
        border-color: var(--accent);
        color: var(--accent);
    }

    .btn-danger {
        background: linear-gradient(135deg, var(--danger) 0%, #bb2d3b 100%);
        color: white;
    }

    .btn-danger:hover {
        box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 60px 24px;
        color: var(--text-muted);
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .card { animation: slideUp 0.6s ease-out; }

    .section-divider {
        border-bottom: 2px solid var(--border);
        margin: 32px 0;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
        }

        .header-amount {
            text-align: left;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .order-title {
            font-size: 24px;
        }

        .order-amount {
            font-size: 28px;
        }
    }
</style>
@endpush

@section('content')

<style>
    .order-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .order-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        gap: 0;
        min-height: fit-content;
    }
    
    .order-section {
        padding: 24px;
        border-right: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .order-section:nth-child(4n) { border-right: none; }
    .order-section:nth-last-child(-n+4) { border-bottom: none; }
    
    .section-header { font-size: 12px; font-weight: 700; color: #999; text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px; }
    .section-value { font-size: 16px; font-weight: 700; color: #1a1d29; }
    .section-sub { font-size: 12px; color: #666; margin-top: 4px; }
    
    .items-section {
        grid-column: 1 / -1;
        padding: 24px;
        background: #fafafa;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .items-header { font-size: 12px; font-weight: 700; color: #999; text-transform: uppercase; margin-bottom: 16px; letter-spacing: 0.5px; }
    
    .item-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid #e9ecef;
        align-items: center;
    }
    
    .item-row:last-child { border-bottom: none; }
    
    .item-name { font-weight: 600; color: #1a1d29; font-size: 14px; }
    .item-unit { font-size: 12px; color: #999; }
    .item-qty { font-weight: 700; text-align: center; }
    .item-price { font-size: 14px; }
    .item-total { font-weight: 700; color: #e85d24; text-align: right; }
    
    .summary-section {
        grid-column: 1 / -1;
        padding: 24px;
    }
    
    .summary-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 32px;
        padding: 16px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .summary-row:last-child { border-bottom: none; }
    
    .summary-label { font-size: 12px; font-weight: 600; color: #666; }
    .summary-amount { font-size: 20px; font-weight: 800; color: #e85d24; }
    .summary-currency { font-size: 12px; color: #999; margin-top: 2px; }
    
    .action-section {
        grid-column: 1 / -1;
        padding: 24px;
        display: flex;
        gap: 12px;
        background: #fafafa;
    }
    
    .btn { padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
    .btn-primary { background: #e85d24; color: white; }
    .btn-primary:hover { background: #d04a1a; }
    .btn-danger { background: #dc3545; color: white; }
    .btn-danger:hover { background: #c82333; }
    .btn-secondary { background: #6c757d; color: white; }
    .btn-secondary:hover { background: #5a6268; }
    
    .badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-danger { background: #f8d7da; color: #721c24; }
    .badge-info { background: #d1ecf1; color: #0c5460; }
</style>

<div class="order-container">
    <!-- Header Row -->
    <div class="order-grid">
        <div class="order-section">
            <div class="section-header">📋 Order Number</div>
            <div class="section-value">ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="order-section">
            <div class="section-header">👤 Customer</div>
            <div class="section-value" style="font-size: 14px;">{{ $order->customer->name }}</div>
        </div>
        <div class="order-section">
            <div class="section-header">📅 Date</div>
            <div class="section-value" style="font-size: 14px;">{{ $order->order_date->format('M d, Y') }}</div>
        </div>
        <div class="order-section">
            <div class="section-header">⏰ Time</div>
            <div class="section-value" style="font-size: 14px;">{{ $order->order_date->format('h:i A') }}</div>
        </div>
        
        <!-- Status Row -->
        <div class="order-section">
            <div class="section-header">Order Status</div>
            <div>
                @if($order->status === 'completed')
                    <span class="badge badge-success">✓ Completed</span>
                @elseif($order->status === 'processing')
                    <span class="badge badge-info">⟳ Processing</span>
                @elseif($order->status === 'cancelled')
                    <span class="badge badge-danger">✕ Cancelled</span>
                @else
                    <span class="badge badge-warning">⏱ Pending</span>
                @endif
            </div>
        </div>
        <div class="order-section">
            <div class="section-header">Payment Status</div>
            <div>
                @if($order->payment_status === 'paid')
                    <span class="badge badge-success">✓ Paid</span>
                @elseif($order->payment_status === 'partial')
                    <span class="badge badge-info">⟳ Partial</span>
                @else
                    <span class="badge badge-warning">⏱ Unpaid</span>
                @endif
            </div>
        </div>
        <div class="order-section">
            <div class="section-header">Items Count</div>
            <div class="section-value">{{ $order->items->count() }}</div>
        </div>
        <div class="order-section">
            <div class="section-header">Total Amount</div>
            <div class="section-value">${{ number_format($order->total_amount, 2) }}</div>
            <div class="section-sub">៛{{ number_format($order->total_amount * 4000, 0) }}</div>
        </div>
    </div>
    
    <!-- Items Section -->
    @if($order->items->count())
    <div class="items-section">
        <div class="items-header">📦 Order Items</div>
        @foreach($order->items as $item)
        <div class="item-row">
            <div>
                <div class="item-name">{{ $item->product->name }}</div>
                <div class="item-unit">{{ $item->product->unit }}</div>
            </div>
            <div class="item-qty">{{ $item->quantity }} x</div>
            <div class="item-price">
                <div style="font-weight: 600;">${{ number_format($item->unit_price, 2) }}</div>
                <div style="font-size: 12px; color: #999;">៛{{ number_format($item->unit_price * 4000, 0) }}</div>
            </div>
            <div class="item-total">
                <div>${{ number_format($item->total_price, 2) }}</div>
                <div style="font-size: 12px; color: #999;">៛{{ number_format($item->total_price * 4000, 0) }}</div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
    
    <!-- Summary Section -->
    <div class="summary-section">
        <div style="font-size: 12px; font-weight: 700; color: #999; text-transform: uppercase; margin-bottom: 20px; letter-spacing: 0.5px;">💰 Amount Breakdown</div>
        
        <div class="summary-row">
            <div>
                <div class="summary-label">Subtotal (USD)</div>
                <div class="summary-amount" style="font-size: 18px;">${{ number_format($order->subtotal, 2) }}</div>
            </div>
            <div>
                <div class="summary-label">Subtotal (KHR)</div>
                <div class="summary-amount" style="font-size: 18px;">៛{{ number_format($order->subtotal * 4000, 0) }}</div>
            </div>
            @if($order->discount_amount > 0)
            <div>
                <div class="summary-label">Discount (USD)</div>
                <div class="summary-amount" style="color: #dc3545;">-${{ number_format($order->discount_amount, 2) }}</div>
            </div>
            <div>
                <div class="summary-label">Discount (KHR)</div>
                <div class="summary-amount" style="color: #dc3545;">-៛{{ number_format($order->discount_amount * 4000, 0) }}</div>
            </div>
            @else
            <div colspan="2"></div>
            @endif
        </div>
        
        @if($order->tax_amount > 0)
        <div class="summary-row">
            <div>
                <div class="summary-label">Tax (USD)</div>
                <div class="summary-amount" style="color: #28a745; font-size: 18px;">+${{ number_format($order->tax_amount, 2) }}</div>
            </div>
            <div>
                <div class="summary-label">Tax (KHR)</div>
                <div class="summary-amount" style="color: #28a745; font-size: 18px;">+៛{{ number_format($order->tax_amount * 4000, 0) }}</div>
            </div>
            <div colspan="2"></div>
        </div>
        @endif
        
        <div class="summary-row" style="background: #f8f9fa; padding: 16px; margin: 0 -24px -24px -24px; border: none;">
            <div>
                <div class="summary-label">TOTAL (USD)</div>
                <div class="summary-amount" style="font-size: 24px;">${{ number_format($order->total_amount, 2) }}</div>
            </div>
            <div>
                <div class="summary-label">TOTAL (KHR)</div>
                <div class="summary-amount" style="font-size: 24px;">៛{{ number_format($order->total_amount * 4000, 0) }}</div>
            </div>
            <div colspan="2"></div>
        </div>
    </div>
    
    <!-- Payment Section -->
    <div style="padding: 24px; background: white; border-radius: 12px; border: 1px solid #e9ecef; margin-bottom: 24px;">
        <div style="font-size: 14px; font-weight: 700; color: #1a1d29; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            💳 Payment Management
        </div>
        
        <!-- Payment Status & Amount Due -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #e9ecef;">
            <div>
                <div style="font-size: 12px; color: #6c757d; margin-bottom: 6px;">Payment Status</div>
                <div>
                    @if($order->payment_status === 'paid')
                        <span style="display: inline-block; background: #28a745; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">✓ PAID</span>
                    @elseif($order->payment_status === 'partial')
                        <span style="display: inline-block; background: #ffc107; color: #1a1d29; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">⟳ PARTIAL</span>
                    @else
                        <span style="display: inline-block; background: #dc3545; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">✕ UNPAID</span>
                    @endif
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: #6c757d; margin-bottom: 6px;">Amount Due</div>
                <div style="font-size: 18px; font-weight: 700; color: #dc3545;">
                    ${{ number_format($order->total_amount - $order->payments->sum('amount'), 2) }}
                </div>
                <div style="font-size: 12px; color: #6c757d;">៛{{ number_format(($order->total_amount - $order->payments->sum('amount')) * 4000, 0) }}</div>
            </div>
        </div>
        
        <!-- Record Payment Form -->
        <div style="margin-bottom: 24px; padding: 16px; background: #f8f9fa; border-radius: 8px;">
            <div style="font-size: 13px; font-weight: 600; color: #1a1d29; margin-bottom: 16px;">Record Payment</div>
            <form id="paymentForm" style="display: grid; gap: 12px;">
                @csrf
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 12px; color: #6c757d; display: block; margin-bottom: 6px;">Amount ($)</label>
                        <input type="number" id="paymentAmount" name="amount" min="0.01" step="0.01" placeholder="0.00" 
                            style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 6px; font-size: 14px; box-sizing: border-box;"
                            max="{{ $order->total_amount - $order->payments->sum('amount') }}">
                    </div>
                    <div>
                        <label style="font-size: 12px; color: #6c757d; display: block; margin-bottom: 6px;">Payment Method</label>
                        <select id="paymentMethod" name="method" required style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 6px; font-size: 14px; box-sizing: border-box; background: white;">
                            <option value="">Select method</option>
                            <option value="cash">💵 Cash</option>
                            <option value="card">💳 Card</option>
                            <option value="bank_transfer">🏦 Bank Transfer</option>
                            <option value="check">✓ Check</option>
                            <option value="other">📝 Other</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label style="font-size: 12px; color: #6c757d; display: block; margin-bottom: 6px;">Reference (Optional)</label>
                    <input type="text" id="paymentReference" name="reference" placeholder="Check #, Credit Card last 4, etc."
                        style="width: 100%; padding: 10px; border: 1px solid #e9ecef; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                </div>
                <button type="submit" style="padding: 10px 16px; background: #28a745; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                    Record Payment
                </button>
            </form>
        </div>
        
        <!-- Payment History -->
        @if($order->payments->count() > 0)
        <div>
            <div style="font-size: 13px; font-weight: 600; color: #1a1d29; margin-bottom: 12px;">Payment History</div>
            <div style="max-height: 300px; overflow-y: auto;">
                @foreach($order->payments as $payment)
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-weight: 600; color: #28a745;">${{ number_format($payment->amount, 2) }}</div>
                        <div style="font-size: 12px; color: #6c757d;">{{ ucfirst(str_replace('_', ' ', $payment->method)) }} • {{ $payment->created_at->format('M d, Y H:i') }}</div>
                        @if($payment->reference)
                        <div style="font-size: 11px; color: #6c757d;">Ref: {{ $payment->reference }}</div>
                        @endif
                    </div>
                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this payment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; font-size: 18px; padding: 0;">
                            🗑️
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div style="padding: 20px; text-align: center; color: #6c757d; background: #f8f9fa; border-radius: 8px;">
            No payments recorded yet
        </div>
        @endif
    </div>
    
    <!-- Actions Section -->
    <div class="action-section">
        <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
            ✏️ Edit Order
        </a>
        <button onclick="deleteOrder({{ $order->id }}, 'ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}')" class="btn btn-danger">
            🗑️ Delete Order
        </button>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            ← Back to Orders
        </a>
    </div>
    
    @if($order->notes)
    <div style="padding: 24px; border-top: 1px solid #f0f0f0; background: #fafafa;">
        <div style="font-size: 12px; font-weight: 700; color: #999; text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px;">📝 Notes</div>
        <div style="background: white; padding: 12px; border-radius: 6px; border-left: 4px solid #e85d24; color: #1a1d29; line-height: 1.6;">
            {{ $order->notes }}
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function deleteOrder(id, orderNum) {
        if (confirm(`Delete order "${orderNum}"? This will restore inventory quantities.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/orders/' + id;
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') + '"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Payment form submission
    document.getElementById('paymentForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const amount = document.getElementById('paymentAmount').value;
        const method = document.getElementById('paymentMethod').value;
        const reference = document.getElementById('paymentReference').value;

        if (!amount || amount <= 0) {
            alert('Please enter a valid amount');
            return;
        }

        if (!method) {
            alert('Please select a payment method');
            return;
        }

        try {
            const response = await fetch(`/orders/{{ $order->id }}/payments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    amount: parseFloat(amount),
                    method: method,
                    reference: reference
                })
            });

            const data = await response.json();

            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message || 'Error recording payment');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error recording payment');
        }
    });
</script>
@endpush

@endsection
