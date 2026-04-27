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

    @if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 12px 20px; border-radius: 10px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
        <span>✓ {{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #065f46; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    @endif

    @if(session('error'))
    <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 20px; border-radius: 10px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
        <span>✗ {{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #991b1b; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    @endif

    @if(session('stockWarnings'))
    <div style="background: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 12px 20px; border-radius: 10px; margin-bottom: 16px; font-size: 14px;">
        <strong>⚠ ការព្រមានស្តុក:</strong>
        <ul style="margin: 8px 0 0 20px; padding: 0;">
            @foreach(session('stockWarnings') as $warning)
                <li>{{ $warning }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Hero Header -->
    <div class="order-hero">
        <div class="hero-left">
            <h1 class="hero-order-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h1>
            <div class="hero-meta">
                <span>{{ $order->customer->name }}</span>
                <span class="sep"></span>
                <span>{{ $order->order_date->translatedFormat('d M Y') }}</span>
                <span class="sep"></span>
                <span>{{ $order->order_date->setTimezone('Asia/Phnom_Penh')->format('h:i A') }}</span>
            </div>
        </div>
        <div class="hero-right">
            <div class="hero-total">${{ number_format($order->total_amount, 2) }}</div>
            <div class="hero-total-khr">៛{{ number_format($order->total_amount * 4000, 0) }}</div>
            @if((float) $order->delivery_fee_khr > 0)
                <div style="font-size: 13px; margin-top: 6px; opacity: .9;">
                    {{ $order->delivery->delivery_name ?? 'Delivery' }}: ៛{{ number_format($order->delivery_fee_khr, 0) }}
                </div>
            @endif
        </div>
    </div>

    <!-- Preparation Info -->
    @if($order->prepared_by)
    <div class="detail-section" style="margin-bottom: 1.5rem;">
        <h3 class="section-title" style="font-size: 1rem;"> ព័ត៌មានរៀបចំ</h3>
        <div style="display: flex; gap: 2rem; padding: 1rem; background: #f0fdf4; border-radius: 10px; border: 1px solid #bbf7d0;">
            <div>
                <span style="color: #6b7280; font-size: 0.85rem;">រៀបចំដោយ</span><br>
                <strong>{{ $order->preparer->name ?? 'N/A' }}</strong>
            </div>
            <div>
                <span style="color: #6b7280; font-size: 0.85rem;">ពេលវេលា</span><br>
                        <strong>{{ $order->prepared_at ? $order->prepared_at->setTimezone('Asia/Phnom_Penh')->format('d/m/Y h:i A') : 'N/A' }}</strong>
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="actions-bar">
        {{-- 1) Print invoice for customer view --}}
        @if($order->invoice)
            <button id="printCustomerBtn" class="btn btn-primary" style="background: #6c757d;" data-url="{{ route('packing.customer', $order->invoice) }}" title="វិក្ក័យបត្រ / ស្លាកភ្ញៀវ">
                <i class="fas fa-print"></i> ព្រីនវិក្ក័យបត្រ
            </button>
        @else
            <button class="btn btn-primary" style="background: #6c757d;" disabled>
                <i class="fas fa-print"></i> ព្រីនវិក្ក័យបត្រ
            </button>
        @endif

        {{-- 2) "ដាក់រៀបចំ" — show notification only (no navigation) --}}
        <button id="prepareBtn" class="btn btn-warning" style="background: #e85d24; border:none;">
            <i class="fas fa-box"></i> ដាក់រៀបចំ
        </button>

        {{-- 3) Edit button --}}
        @if($order->status === 'pending')
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary" style="background: #2563eb;">
                <i class="fas fa-edit"></i> កែប្រែ
            </a>
        @endif

        {{-- 4) Back button --}}
        <a href="{{ route('orders.index') }}" class="btn btn-outline">
            ← ត្រឡប់ក្រោយ
        </a>
    </div>

</div>
@endsection

    {{-- Removed client-only toast; preparation print opens prep sticker in a new tab --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const prepareBtn = document.getElementById('prepareBtn');
                if (!prepareBtn) return;
                prepareBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    try {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'ការការបញ្ជាទិញត្រូវបានបញ្ជូលសម្រាប់រៀបចំ',
                            showConfirmButton: false,
                            timer: 2200,
                            timerProgressBar: true
                        });
                    } catch (err) {
                        alert('ការការបញ្ជាទិញត្រូវបានបញ្ជូលសម្រាប់រៀបចំ');
                    }
                });
                // Print customer invoice: open print in a new tab then redirect current page to create order
                const printCustomerBtn = document.getElementById('printCustomerBtn');
                if (printCustomerBtn) {
                    printCustomerBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        const url = this.dataset.url;
                        if (!url) return;
                        // Open print in a new tab
                        window.open(url, '_blank');
                        // Redirect current tab to create order page
                        window.location.href = '{{ route('orders.create') }}';
                    });
                }
            });
        </script>
    @endpush
