@extends('layouts.app')

@section('title', 'Purchase #' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT))

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

    .purchase-title {
        font-size: 32px;
        font-weight: 800;
        color: var(--text);
        margin: 0 0 8px 0;
    }

    .purchase-meta {
        display: flex;
        gap: 24px;
        color: var(--text-muted);
        font-size: 14px;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
        color: var(--text);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .info-card {
        background: var(--surface);
        padding: 24px;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }

    .info-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
        margin-bottom: 20px;
    }

    .info-group {
        flex: 1;
    }

    .info-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .info-value {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-received {
        background: #d4edda;
        color: #155724;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .amount-large {
        font-size: 28px;
        color: var(--accent);
    }

    .notes-section {
        background: #fafafa;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid var(--accent);
    }

    .action-section {
        background: var(--surface);
        padding: 24px;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        display: flex;
        gap: 12px;
    }
</style>
@endpush

@section('content')

<div style="max-width: 900px; margin: 0 auto; padding: 24px;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-info">
            <h1 class="purchase-title">📦 Purchase #{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</h1>
            <div class="purchase-meta">
                <span>📅 {{ $purchase->purchase_date->translatedFormat('M d, Y') }}</span>
                <span>🏢 {{ $purchase->supplier_name }}</span>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-primary">✏️ Edit</a>
            <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this purchase?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">🗑️ Delete</button>
            </form>
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">← Back</a>
        </div>
    </div>

    <!-- Purchase Details -->
    <div class="info-card">
        <div class="info-row">
            <div class="info-group">
                <div class="info-label">Reference Number</div>
                <div class="info-value">
                    {{ $purchase->reference_number ?? 'N/A' }}
                </div>
            </div>
            <div class="info-group">
                <div class="info-label">Status</div>
                <div>
                    @if($purchase->status === 'pending')
                        <span class="status-pending">⏱ Pending</span>
                    @elseif($purchase->status === 'received')
                        <span class="status-received">✓ Received</span>
                    @else
                        <span class="status-cancelled">✕ Cancelled</span>
                    @endif
                </div>
            </div>
            <div class="info-group">
                <div class="info-label">Total Amount</div>
                <div class="info-value amount-large">${{ number_format($purchase->total_amount, 2) }}</div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-group">
                <div class="info-label">Supplier Name</div>
                <div class="info-value">{{ $purchase->supplier_name }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Purchase Date</div>
                <div class="info-value">{{ $purchase->purchase_date->translatedFormat('M d, Y') }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Created</div>
                <div class="info-value">{{ $purchase->created_at ? $purchase->created_at->setTimezone('Asia/Phnom_Penh')->translatedFormat('M d, Y h:i A') : '' }}</div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($purchase->notes)
    <div class="info-card">
        <div style="font-size: 12px; font-weight: 700; color: #999; text-transform: uppercase; margin-bottom: 12px; letter-spacing: 0.5px;">📝 Notes</div>
        <div class="notes-section">
            {{ $purchase->notes }}
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="action-section">
        <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-primary">Edit Purchase</a>
        <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this purchase?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete Purchase</button>
        </form>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Back to Purchases</a>
    </div>
</div>

@endsection
