@extends('layouts.app')

@section('title', 'បញ្ជាទិញ #' . str_pad($order->id, 4, '0', STR_PAD_LEFT))

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    :root {
        --accent: #e85d24;
        --accent-dark: #d14a10;
        --bg: #f0f2f5;
        --surface: #ffffff;
        --border: #e5e7eb;
        --text: #111827;
        --text-secondary: #4b5563;
        --text-muted: #9ca3af;
        --success: #059669;
        --success-bg: #ecfdf5;
        --warning: #d97706;
        --warning-bg: #fffbeb;
        --info: #2563eb;
        --info-bg: #eff6ff;
        --danger: #dc2626;
        --danger-bg: #fef2f2;
        --radius: 16px;
        --radius-sm: 10px;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
        --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
        --shadow-lg: 0 12px 32px rgba(0,0,0,0.12);
    }

    .order-page {
        max-width: 1100px;
        margin: 0 auto;
        padding: 32px 24px 64px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        -webkit-font-smoothing: antialiased;
    }

    /* ─── Header ─── */
    .order-hero {
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        color: white;
        padding: 48px 40px;
        border-radius: var(--radius);
        margin-bottom: 28px;
        box-shadow: 0 12px 40px rgba(232, 93, 36, 0.25);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 32px;
        position: relative;
        overflow: hidden;
    }

    .order-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }

    .order-hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        right: 15%;
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }

    .hero-left { position: relative; z-index: 1; }

    .hero-order-id {
        font-size: 36px;
        font-weight: 900;
        letter-spacing: -1px;
        margin: 0 0 12px;
        line-height: 1;
    }

    .hero-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
        opacity: 0.92;
        font-weight: 500;
        flex-wrap: wrap;
    }

    .hero-meta .sep {
        width: 4px;
        height: 4px;
        background: rgba(255,255,255,0.5);
        border-radius: 50%;
    }

    .hero-right {
        text-align: right;
        position: relative;
        z-index: 1;
    }

    .hero-total {
        font-size: 44px;
        font-weight: 900;
        letter-spacing: -1px;
        line-height: 1.1;
    }

    .hero-total-khr {
        font-size: 15px;
        opacity: 0.8;
        margin-top: 6px;
        font-weight: 600;
    }

    /* ─── Quick Stats ─── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: var(--surface);
        padding: 22px 24px;
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    .stat-content {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
    }

    /* ─── Badges ─── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .badge-success { background: var(--success-bg); color: var(--success); }
    .badge-warning { background: var(--warning-bg); color: var(--warning); }
    .badge-info { background: var(--info-bg); color: var(--info); }
    .badge-danger { background: var(--danger-bg); color: var(--danger); }

    /* ─── Section Card ─── */
    .section {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        margin-bottom: 24px;
        overflow: hidden;
        animation: fadeUp 0.5s ease-out both;
    }

    .section:nth-child(2) { animation-delay: 0.05s; }
    .section:nth-child(3) { animation-delay: 0.1s; }
    .section:nth-child(4) { animation-delay: 0.15s; }

    .section-header {
        padding: 20px 28px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
    }

    .section-header .icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        background: linear-gradient(135deg, rgba(232,93,36,0.1) 0%, rgba(232,93,36,0.05) 100%);
    }

    .section-body {
        padding: 28px;
    }

    /* ─── Items Table ─── */
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table thead th {
        padding: 14px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-muted);
        background: #fafafa;
        border-bottom: 1px solid var(--border);
    }

    .items-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.15s ease;
    }

    .items-table tbody tr:last-child { border-bottom: none; }

    .items-table tbody tr:hover {
        background: #fafbfc;
    }

    .items-table tbody td {
        padding: 18px 20px;
        font-size: 14px;
        color: var(--text);
    }

    .item-num {
        width: 40px;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 13px;
    }

    .item-name {
        font-weight: 600;
        color: var(--text);
        font-size: 14px;
    }

    .item-qty {
        font-weight: 700;
        color: var(--text-secondary);
        font-size: 14px;
    }

    .item-price {
        font-weight: 700;
        color: var(--accent);
        font-size: 14px;
    }

    .text-center { text-align: center; }
    .text-right { text-align: right; }

    /* ─── Summary Footer ─── */
    .summary-footer {
        background: #f9fafb;
        border-top: 1px solid var(--border);
        padding: 24px 28px;
        display: flex;
        justify-content: flex-end;
    }

    .summary-table {
        width: 320px;
    }

    .summary-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 14px;
        color: var(--text-secondary);
    }

    .summary-line .label { font-weight: 500; }
    .summary-line .value { font-weight: 700; color: var(--text); }

    .summary-line.discount .value {
        color: var(--danger);
    }

    .summary-line.total {
        border-top: 2px solid var(--border);
        margin-top: 8px;
        padding-top: 16px;
    }

    .summary-line.total .label {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
    }

    .summary-line.total .value {
        font-size: 22px;
        font-weight: 900;
        color: var(--accent);
    }

    /* ─── Customer Grid ─── */
    .customer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
    }

    .customer-field .field-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .customer-field .field-value {
        font-size: 15px;
        font-weight: 600;
        color: var(--text);
        line-height: 1.5;
    }

    .customer-field .field-value.empty {
        color: var(--text-muted);
        font-weight: 400;
    }

    /* ─── Notes ─── */
    .notes-content {
        background: #fefce8;
        border: 1px solid #fef08a;
        border-radius: var(--radius-sm);
        padding: 20px 24px;
        color: var(--text);
        line-height: 1.7;
        font-size: 14px;
    }

    /* ─── Actions ─── */
    .actions-bar {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 32px;
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--radius-sm);
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        font-family: inherit;
        letter-spacing: 0.1px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        color: white;
        box-shadow: 0 4px 14px rgba(232, 93, 36, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(232, 93, 36, 0.35);
        color: white;
        text-decoration: none;
    }

    .btn-outline {
        background: var(--surface);
        color: var(--text-secondary);
        border: 1.5px solid var(--border);
    }

    .btn-outline:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: rgba(232, 93, 36, 0.03);
        text-decoration: none;
    }

    .btn-danger-outline {
        background: var(--surface);
        color: var(--danger);
        border: 1.5px solid #fecaca;
    }

    .btn-danger-outline:hover {
        background: var(--danger-bg);
        border-color: var(--danger);
        text-decoration: none;
    }

    /* ─── Animations ─── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ─── Responsive ─── */
    @media (max-width: 768px) {
        .order-page { padding: 16px 12px 48px; }
        .order-hero { flex-direction: column; padding: 32px 24px; text-align: center; }
        .hero-right { text-align: center; }
        .hero-order-id { font-size: 28px; }
        .hero-total { font-size: 32px; }
        .hero-meta { justify-content: center; }
        .stats-row { grid-template-columns: 1fr 1fr; }
        .customer-grid { grid-template-columns: 1fr; }
        .summary-footer { justify-content: stretch; }
        .summary-table { width: 100%; }
        .actions-bar { flex-direction: column; }
        .btn { justify-content: center; }
    }
</style>
@endpush

@section('content')
<div class="order-page">

    <!-- Hero Header -->
    <div class="order-hero">
        <div class="hero-left">
            <h1 class="hero-order-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h1>
            <div class="hero-meta">
                <span>{{ $order->customer->name }}</span>
                <span class="sep"></span>
                <span>{{ $order->order_date->translatedFormat('d M Y') }}</span>
                <span class="sep"></span>
                <span>{{ $order->order_date->translatedFormat('h:i A') }}</span>
            </div>
        </div>
        <div class="hero-right">
            <div class="hero-total">${{ number_format($order->total_amount, 2) }}</div>
            <div class="hero-total-khr">៛{{ number_format($order->total_amount * 4000, 0) }}</div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">ស្ថានភាពបញ្ជាទិញ</div>
            <div class="stat-content">
                @if($order->status === 'completed')
                    <span class="badge badge-success">✓ បានបញ្ចប់</span>
                @elseif($order->status === 'processing')
                    <span class="badge badge-info">⟳ កំពុងដំណើរការ</span>
                @elseif($order->status === 'cancelled')
                    <span class="badge badge-danger">✕ បានបោះបង់</span>
                @else
                    <span class="badge badge-warning">⏱ រង់ចាំ</span>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">ស្ថានភាពបង់ប្រាក់</div>
            <div class="stat-content">
                @if($order->payment_status === 'paid')
                    <span class="badge badge-success">✓ បានបង់</span>
                @elseif($order->payment_status === 'partial')
                    <span class="badge badge-info">⟳ បង់មួយផ្នែក</span>
                @else
                    <span class="badge badge-warning">⏱ មិនទាន់បង់</span>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">ចំនួនមុខទំនិញ</div>
            <div class="stat-content" style="font-size: 24px; font-weight: 800;">{{ $order->items->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">សរុប</div>
            <div class="stat-content" style="font-size: 20px; font-weight: 800;">${{ number_format($order->subtotal, 2) }}</div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="section">
        <div class="section-header">
          
            <h3>រាយនាមទំនិញ</h3>
        </div>
        @if($order->items->count())
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="item-num">#</th>
                        <th>ផលិតផល</th>
                        <th class="text-center">ចំនួន</th>
                        <th class="text-right">តម្លៃ</th>
                        <th class="text-right">សរុប</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $i => $item)
                    <tr>
                        <td class="item-num">{{ $i + 1 }}</td>
                        <td><span class="item-name">{{ $item->product->name }}</span></td>
                        <td class="text-center"><span class="item-qty">{{ $item->quantity }}</span></td>
                        <td class="text-right"><span class="item-price">${{ number_format($item->unit_price, 2) }}</span></td>
                        <td class="text-right"><span class="item-price">${{ number_format($item->total_price, 2) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="summary-footer">
                <div class="summary-table">
                    <div class="summary-line">
                        <span class="label">សរុប</span>
                        <span class="value">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="summary-line discount">
                        <span class="label">បញ្ចុះតម្លៃ</span>
                        <span class="value">-${{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="summary-line total">
                        <span class="label">តម្លៃសរុប</span>
                        <span class="value">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="section-body" style="text-align: center; color: var(--text-muted); padding: 48px;">
                មិនមានទំនិញនៅឡើយទេ
            </div>
        @endif
    </div>
    <!-- Customer Info -->
    <div class="section">
        <div class="section-header">
          
            <h3>ព័ត៌មានអតិថិជន</h3>
        </div>
        <div class="section-body">
            <div class="customer-grid">
                <div class="customer-field">
                    <div class="field-label">ឈ្មោះ</div>
                    <div class="field-value">{{ $order->customer->name }}</div>
                </div>
                <div class="customer-field">
                    <div class="field-label">ទូរសព្ទ</div>
                    <div class="field-value {{ !$order->customer->phone ? 'empty' : '' }}">{{ $order->customer->phone ?? '—' }}</div>
                </div>
                <div class="customer-field">
                    <div class="field-label">ទីតាំង</div>
                    <div class="field-value {{ !$order->customer->location ? 'empty' : '' }}">{{ $order->customer->location ?? '—' }}</div>
                </div>
            </div>
            @php
                $deliveryItems = $order->items->filter(fn($item) => $item->delivery_id);
            @endphp
            @if($deliveryItems->count())
            <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border);">
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-muted); margin-bottom: 10px;">ការដឹកជញ្ជូន</div>
                @foreach($deliveryItems as $dItem)
                <div style="font-size: 14px; color: var(--text); margin-bottom: 6px;">
                    <span style="font-weight: 600;">{{ $dItem->product->name }}</span>
                    <span style="color: var(--text-muted);"> → {{ $dItem->delivery->delivery_name }} — ៛{{ number_format($dItem->delivery->delivery_price_khr, 0) }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
    <!-- Notes -->
    @if($order->notes)
    <div class="section">
        <div class="section-header">
         
            <h3>កំណត់ចំណាំ</h3>
        </div>
        <div class="section-body">
            <div class="notes-content">{{ $order->notes }}</div>
        </div>
    </div>
    @endif

    <!-- Preparation Info -->
    @if($order->prepared_by)
    <div class="detail-section" style="margin-bottom: 1.5rem;">
        <h3 class="section-title" style="font-size: 1rem;">🧑‍🍳 ព័ត៌មានរៀបចំ</h3>
        <div style="display: flex; gap: 2rem; padding: 1rem; background: #f0fdf4; border-radius: 10px; border: 1px solid #bbf7d0;">
            <div>
                <span style="color: #6b7280; font-size: 0.85rem;">រៀបចំដោយ</span><br>
                <strong>{{ $order->preparer->name ?? 'N/A' }}</strong>
            </div>
            <div>
                <span style="color: #6b7280; font-size: 0.85rem;">ពេលវេលា</span><br>
                <strong>{{ $order->prepared_at ? $order->prepared_at->translatedFormat('d/M/Y h:i A') : 'N/A' }}</strong>
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="actions-bar">
        {{-- Preparation workflow buttons --}}
        @if($order->status === 'pending' && (auth()->user()->isStaffInventory() || auth()->user()->isManager() || auth()->user()->isAdmin()))
            <form action="{{ route('orders.prepare', $order) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-primary" style="background: #2563eb;">
                    🔧 ចាប់ផ្តើមរៀបចំ
                </button>
            </form>
        @endif

        @if($order->status === 'processing' && (auth()->user()->isStaffInventory() || auth()->user()->isManager() || auth()->user()->isAdmin()))
            <form action="{{ route('orders.ready', $order) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-primary" style="background: #16a34a;">
                    ✓ រួចរាល់
                </button>
            </form>
        @endif

        @if(!auth()->user()->isStaffInventory())
        <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
             កែសម្រួល
        </a>
        <a href="{{ route('deliveries.create') }}" class="btn btn-primary">
            កំណត់ការដឹកជញ្ជូន
        </a>
        <a href="{{ route('invoices.create', ['order_id' => $order->id]) }}" class="btn btn-outline">
             បង្កើតវិក្កយបត្រ
        </a>
        @endif
        <a href="{{ route('orders.index') }}" class="btn btn-outline">
            ← ត្រឡប់ក្រោយ
        </a>
        @if(!auth()->user()->isStaffInventory())
        <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display:inline;" onsubmit="return confirm('លុបការបញ្ជាទិញនេះ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger-outline">
                 លុប
            </button>
        </form>
        @endif
    </div>

</div>
@endsection 
