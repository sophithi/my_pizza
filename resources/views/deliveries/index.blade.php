@extends('layouts.app')

@section('title', 'Deliveries Management')

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
        align-items: center;
        margin-bottom: 40px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.2s;
    }

    .btn-primary:hover { background: #d64a1a; }

    .btn-small {
        padding: 6px 12px;
        font-size: 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-info {
        background: var(--info);
        color: white;
    }

    .btn-warning {
        background: var(--warning);
        color: #1a1d29;
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .delivery-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--surface);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .delivery-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid var(--border);
    }

    .delivery-table thead th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .delivery-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .delivery-table tbody tr:hover {
        background: #f8f9fa;
    }

    .delivery-table tbody td {
        padding: 16px;
        color: var(--text);
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-preparing { background: #e2e3e5; color: #383d41; }
    .status-out_for_delivery { background: #cce5ff; color: #004085; }
    .status-delivered { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: var(--surface);
        padding: 24px;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
        background: var(--surface);
        border-radius: 8px;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }

    .empty-state-text {
        font-size: 16px;
        margin-bottom: 16px;
    }
</style>
@endpush

@section('content')

<div style="max-width: 1200px; margin: 0 auto; padding: 24px;">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">ការដឹកជញ្ជូនទំនិញ</h1>
        <a href="{{ route('deliveries.create') }}" class="btn-primary">បន្ថែម</a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 16px; border-radius: 8px; margin-bottom: 24px; border-left: 4px solid #28a745;">
        ✓ {{ session('success') }}
    </div>
    @endif

    <!-- Statistics -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">ការដឹកជញ្ជូនទំនិញ</div>
            <div class="stat-value">{{ $deliveries->total() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">កំពុងរងចាំ</div>
            <div class="stat-value" style="color: #ffc107;">
                {{ $deliveries->where('status', 'pending')->count() }}
            </div>
        </div>
   
        <div class="stat-card">
            <div class="stat-label">បានដឹករួចរាល់</div>
            <div class="stat-value" style="color: #28a745;">
                {{ $deliveries->where('status', 'delivered')->count() }}
            </div>
        </div>
    </div>

    <!-- Deliveries Table -->
    @if($deliveries->count() > 0)
    <table class="delivery-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Address</th>
                <th>Delivery Type</th>
                <th>Service</th>
                <th>Driver</th>
                <th>Scheduled</th>
                <th>Status</th>
                <th>Fee</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deliveries as $delivery)
            <tr>
                <td>
                    <a href="{{ route('orders.show', $delivery->order) }}" style="color: var(--accent); font-weight: 600; text-decoration: none;">
                        #{{ str_pad($delivery->order->id, 4, '0', STR_PAD_LEFT) }}
                    </a>
                </td>
                <td>{{ Str::limit($delivery->delivery_address, 40) }}</td>
                <td><strong>{{ $delivery->delivery_type }}</strong></td>
                <td>{{ $delivery->name_service ?? '—' }}<br><small style="color: var(--text-muted);">${{ number_format($delivery->price_of_delivery, 2) }}</small></td>
                <td>{{ $delivery->driver_name ?? '-' }}</td>
                <td>{{ $delivery->scheduled_delivery_at->format('M d, Y H:i') }}</td>
                <td>
                    <span class="status-badge status-{{ $delivery->status }}">
                        @if($delivery->status === 'pending')
                            ⏱ Pending
                        @elseif($delivery->status === 'preparing')
                            👨‍🍳 Preparing
                        @elseif($delivery->status === 'out_for_delivery')
                            🚗 Out
                        @elseif($delivery->status === 'delivered')
                            ✓ Delivered
                        @else
                            ✕ Cancelled
                        @endif
                    </span>
                </td>
                <td>${{ number_format($delivery->delivery_fee, 2) }}</td>
                <td>
                    <a href="{{ route('deliveries.show', $delivery) }}" class="btn-small btn-info">View</a>
                    <a href="{{ route('deliveries.edit', $delivery) }}" class="btn-small btn-info">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div style="margin-top: 32px;">
        {{ $deliveries->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">🚗</div>
        <div class="empty-state-text">មិនទាន់មានការដឹកជញ្ជូន</div>
        <a href="{{ route('deliveries.create') }}" class="btn-primary">Schedule First Delivery</a>
    </div>
    @endif
</div>

@endsection
