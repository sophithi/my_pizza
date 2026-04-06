@extends('layouts.app')

@section('title', 'Delivery Details')

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

    .detail-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 32px;
    }

    .detail-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
    }

    .header-actions {
        display: flex;
        gap: 8px;
    }

    .btn {
        padding: 10px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-primary:hover {
        background: #d64a1a;
    }

    .btn-secondary {
        background: transparent;
        color: var(--text-muted);
        border: 1px solid var(--border);
    }

    .btn-secondary:hover {
        background: var(--bg);
        border-color: var(--text);
    }

    .btn-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .btn-danger:hover {
        background: #f5c6cb;
    }

    .card {
        background: var(--surface);
        padding: 24px;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 20px;
    }

    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin: 0 0 20px 0;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--border);
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .info-group {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .info-value {
        font-size: 16px;
        color: var(--text);
        font-weight: 500;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        text-align: center;
        width: fit-content;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-preparing { background: #e2e3e5; color: #383d41; }
    .status-out_for_delivery { background: #cfe2ff; color: #084298; }
    .status-delivered { background: #d1e7dd; color: #0f5132; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .order-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 6px;
        color: var(--accent);
        text-decoration: none;
        border: 1px solid var(--border);
        transition: all 0.2s;
    }

    .order-link:hover {
        background: var(--accent);
        color: white;
    }

    .timeline {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin: 0;
    }

    .timeline-item {
        flex: 1;
        text-align: center;
        position: relative;
        padding: 16px;
        background: #f8f9fa;
        border-radius: 6px;
        border: 2px solid var(--border);
    }

    .timeline-item.active {
        background: #cfe2ff;
        border-color: var(--accent);
    }

    .timeline-item.completed {
        background: #d1e7dd;
        border-color: #0f5132;
    }

    .timeline-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 6px;
    }

    .timeline-icon {
        font-size: 20px;
        margin-bottom: 6px;
    }

    .two-col {
        grid-column: span 2;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        margin-top: 24px;
    }

    .success-message {
        background: #d1e7dd;
        color: #0f5132;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #0f5132;
    }
</style>
@endpush

@section('content')

<div class="detail-container">
    <!-- Success Message -->
    @if(session('success'))
    <div class="success-message">
        {{ session('success') }}
    </div>
    @endif

    <!-- Header -->
    <div class="detail-header">
        <div>
            <h1 class="detail-title">🚗 Delivery #{{ $delivery->id }}</h1>
            <p style="color: var(--text-muted); margin: 8px 0 0 0;">
                Scheduled: {{ $delivery->scheduled_delivery_at->format('M d, Y H:i') }} (Cambodia Time)
            </p>
        </div>
        <div class="header-actions">
            <a href="{{ route('deliveries.edit', $delivery->id) }}" class="btn btn-primary">
                ✏️ Edit
            </a>
            <form action="{{ route('deliveries.destroy', $delivery->id) }}" method="POST" style="display: inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this delivery?')">
                    🗑️ Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Status Card -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="card-title" style="margin: 0; border: none;">Current Status</h2>
            <span class="status-badge status-{{ $delivery->status }}">
                @switch($delivery->status)
                    @case('pending')
                        ⏳ Pending
                        @break
                    @case('preparing')
                        📦 Preparing
                        @break
                    @case('out_for_delivery')
                        🚗 Out for Delivery
                        @break
                    @case('delivered')
                        ✅ Delivered
                        @break
                    @case('cancelled')
                        ❌ Cancelled
                        @break
                @endswitch
            </span>
        </div>

        <div class="timeline">
            <div class="timeline-item {{ $delivery->status !== 'pending' ? 'completed' : 'active' }}">
                <div class="timeline-icon">⏳</div>
                <div class="timeline-label">Pending</div>
            </div>
            <div class="timeline-item {{ in_array($delivery->status, ['preparing', 'out_for_delivery', 'delivered']) ? 'completed' : '' }} {{ $delivery->status === 'preparing' ? 'active' : '' }}">
                <div class="timeline-icon">📦</div>
                <div class="timeline-label">Preparing</div>
            </div>
            <div class="timeline-item {{ in_array($delivery->status, ['out_for_delivery', 'delivered']) ? 'completed' : '' }} {{ $delivery->status === 'out_for_delivery' ? 'active' : '' }}">
                <div class="timeline-icon">🚗</div>
                <div class="timeline-label">Out for Delivery</div>
            </div>
            <div class="timeline-item {{ $delivery->status === 'delivered' ? 'active completed' : '' }}">
                <div class="timeline-icon">✅</div>
                <div class="timeline-label">Delivered</div>
            </div>
        </div>

        <!-- Status Action Buttons -->
        @if($delivery->status !== 'delivered' && $delivery->status !== 'cancelled')
        <div class="action-buttons">
            @if($delivery->status === 'pending' || $delivery->status === 'preparing')
            <form action="{{ route('deliveries.mark-out', ['delivery' => $delivery->id]) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary">🚗 Mark Out for Delivery</button>
            </form>
            @endif

            @if($delivery->status === 'out_for_delivery')
            <form action="{{ route('deliveries.mark-delivered', ['delivery' => $delivery->id]) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary">✅ Mark as Delivered</button>
            </form>
            @endif

            @if($delivery->status !== 'delivered')
            <form action="{{ route('deliveries.cancel', ['delivery' => $delivery->id]) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this delivery?')">
                    ❌ Cancel Delivery
                </button>
            </form>
            @endif
        </div>
        @endif

        <!-- Rejection Reason (if cancelled) -->
        @if($delivery->status === 'cancelled' && $delivery->rejection_reason)
        <div style="margin-top: 20px; padding: 12px; background: #f8d7da; border-radius: 6px; border-left: 4px solid #721c24;">
            <div style="color: #721c24; font-weight: 600; margin-bottom: 4px;">Cancellation Reason</div>
            <div style="color: #721c24;">{{ $delivery->rejection_reason }}</div>
        </div>
        @endif
    </div>

    <!-- Order Information -->
    <div class="card">
        <h2 class="card-title">📋 Order Information</h2>
        <div class="info-grid">
            <div class="info-group">
                <div class="info-label">Order Number</div>
                <a href="{{ route('orders.show', $delivery->order->id) }}" class="order-link">
                    Order #{{ str_pad($delivery->order->id, 4, '0', STR_PAD_LEFT) }}
                    <span>→</span>
                </a>
            </div>
            <div class="info-group">
                <div class="info-label">Order Date</div>
                <div class="info-value">{{ $delivery->order->created_at->format('M d, Y H:i') }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Customer Name</div>
                <div class="info-value">{{ $delivery->order->customer->name }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Customer Phone</div>
                <div class="info-value">{{ $delivery->order->customer->phone }}</div>
            </div>
            <div class="info-group two-col">
                <div class="info-label">Order Total</div>
                <div class="info-value" style="font-size: 20px; color: var(--accent);">
                    $ {{ number_format($delivery->order->total_amount, 2) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Details -->
    <div class="card">
        <h2 class="card-title">🚗 Delivery Details</h2>
        <div class="info-grid">
            <div class="info-group">
                <div class="info-label">Delivery Address</div>
                <div class="info-value">{{ $delivery->delivery_address }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Delivery Phone</div>
                <div class="info-value">{{ $delivery->delivery_phone ?? '—' }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Scheduled Delivery</div>
                <div class="info-value">{{ $delivery->scheduled_delivery_at->format('M d, Y H:i') }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Delivery Type</div>
                <div class="info-value"><strong>{{ $delivery->delivery_type }}</strong></div>
            </div>
            <div class="info-group">
                <div class="info-label">Service Name</div>
                <div class="info-value">{{ $delivery->name_service ?? '—' }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Service Price</div>
                <div class="info-value">${{ number_format($delivery->price_of_delivery, 2) }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Actual Delivery</div>
                <div class="info-value">
                    @if($delivery->actual_delivery_at)
                        {{ $delivery->actual_delivery_at->format('M d, Y H:i') }}
                    @else
                        —
                    @endif
                </div>
            </div>
            <div class="info-group">
                <div class="info-label">Delivery Fee</div>
                <div class="info-value">${{ number_format($delivery->delivery_fee, 2) }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Status</div>
                <div style="margin-top: 6px;">
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
            </div>
        </div>

        <!-- Driver Information (if available) -->
        @if($delivery->driver_name || $delivery->driver_phone)
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 16px;">👨‍🚗 Driver Information</div>
            <div class="info-grid">
                <div class="info-group">
                    <div class="info-label">Driver Name</div>
                    <div class="info-value">{{ $delivery->driver_name ?? '—' }}</div>
                </div>
                <div class="info-group">
                    <div class="info-label">Driver Phone</div>
                    <div class="info-value">{{ $delivery->driver_phone ?? '—' }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Notes (if available) -->
        @if($delivery->notes)
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">📝 Delivery Notes</div>
            <div class="info-value" style="white-space: pre-wrap; line-height: 1.6;">{{ $delivery->notes }}</div>
        </div>
        @endif
    </div>

    <!-- Back Button -->
    <div style="margin-top: 32px; text-align: center;">
        <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">
            ← Back to Deliveries
        </a>
    </div>
</div>

@endsection
