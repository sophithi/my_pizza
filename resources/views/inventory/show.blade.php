@extends('layouts.app')

@section('title', ($inventory->product ? $inventory->product->name : 'Inventory') . ' Details')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .show-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 24px;
        }

        /* ── Header ── */
        .show-header {
            background: white;
            padding: 20px 28px;
            border-radius: 12px;
            border-left: 4px solid #e85d24;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .06);
        }

        .show-header h1 {
            font-size: 22px;
            font-weight: 800;
            color: #1a1d29;
            margin: 0;
        }

        .show-header p {
            font-size: 12px;
            color: #aaa;
            margin: 3px 0 0;
        }

        .header-meta {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .meta-pill {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            background: #f5f6fa;
            color: #666;
        }

        /* ── Top Grid (image + info) ── */
        .top-grid {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid #ebebeb;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        .card-header {
            padding: 14px 20px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
            font-weight: 800;
            color: #1a1d29;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-header .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e85d24;
        }

        .card-body {
            padding: 20px;
        }

        /* ── Image ── */
        .prod-img {
            width: 100%;
            height: 340px;
            background: #f8f8f8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .prod-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .prod-img.no-img {
            color: #ddd;
            font-size: 56px;
        }

        .img-caption {
            font-size: 11px;
            color: #bbb;
            text-align: center;
            margin: 0;
        }

        /* ── Info rows ── */
        .product-title {
            font-size: 22px;
            font-weight: 900;
            color: #1a1d29;
            margin: 0 0 2px;
        }

        .product-cat {
            font-size: 12px;
            color: #aaa;
            margin: 0 0 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .badge-good {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-warn {
            background: #fff3e0;
            color: #e65100;
        }

        .badge-bad {
            background: #ffebee;
            color: #c62828;
        }

        .stat-row {
            padding: 11px 0;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-row:last-child {
            border-bottom: none;
        }

        .stat-label {
            font-size: 12px;
            color: #aaa;
            font-weight: 600;
        }

        .stat-value {
            font-size: 14px;
            font-weight: 800;
            color: #1a1d29;
        }

        .prices {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 14px 0;
        }

        .price-card {
            padding: 12px;
            background: #f8f9fc;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #ebebeb;
        }

        .price-card .amount {
            font-size: 18px;
            font-weight: 900;
            color: #1a1d29;
        }

        .price-card .currency {
            font-size: 10px;
            color: #aaa;
            font-weight: 600;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .btns {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }

        .btn-edit {
            flex: 1;
            padding: 11px;
            background: #e85d24;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: background .2s;
        }

        .btn-edit:hover {
            background: #d94a10;
        }

        .btn-back {
            flex: 1;
            padding: 11px;
            background: #f5f6fa;
            color: #1a1d29;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: background .2s;
        }

        .btn-back:hover {
            background: #ebebeb;
        }

        /* ── Bottom Grid (stats + transactions + timeline) ── */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        /* ── Stat Boxes ── */
        .kpi-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .kpi {
            padding: 16px;
            background: #f8f9fc;
            border-radius: 8px;
            border: 1px solid #ebebeb;
            text-align: center;
        }

        .kpi .kpi-val {
            font-size: 22px;
            font-weight: 900;
            color: #1a1d29;
        }

        .kpi .kpi-label {
            font-size: 11px;
            color: #aaa;
            font-weight: 600;
            margin-top: 2px;
        }

        .kpi.highlight .kpi-val {
            color: #e85d24;
        }

        /* Stock level bar */
        .stock-bar-wrap {
            margin-top: 16px;
        }

        .stock-bar-label {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #aaa;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .stock-bar-bg {
            height: 8px;
            background: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .stock-bar-fill {
            height: 100%;
            border-radius: 4px;
            background: #e85d24;
            transition: width .6s ease;
        }

        .stock-bar-fill.low {
            background: #ff9800;
        }

        .stock-bar-fill.good {
            background: #4caf50;
        }

        /* Created / Updated info */
        .date-info {
            margin-top: 16px;
        }

        .date-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f5f5f5;
            font-size: 12px;
        }

        .date-row:last-child {
            border-bottom: none;
        }

        .date-row .dlabel {
            color: #aaa;
            font-weight: 600;
        }

        .date-row .dval {
            color: #1a1d29;
            font-weight: 700;
        }

        /* ── Transaction Table ── */
        .tx-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .tx-table th {
            text-align: left;
            padding: 8px 10px;
            background: #f8f9fc;
            color: #aaa;
            font-weight: 700;
            font-size: 10px;
            letter-spacing: .5px;
            text-transform: uppercase;
            border-bottom: 1px solid #ebebeb;
        }

        .tx-table td {
            padding: 9px 10px;
            border-bottom: 1px solid #f5f5f5;
            color: #1a1d29;
            font-weight: 600;
            vertical-align: middle;
        }

        .tx-table tr:last-child td {
            border-bottom: none;
        }

        .tx-table tr:hover td {
            background: #fafafa;
        }

        .tx-in {
            color: #2e7d32;
            font-weight: 800;
        }

        .tx-out {
            color: #c62828;
            font-weight: 800;
        }

        .tx-adj {
            color: #e65100;
            font-weight: 800;
        }

        .tx-type {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
        }

        .tx-type.in {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .tx-type.out {
            background: #ffebee;
            color: #c62828;
        }

        .tx-type.adj {
            background: #fff3e0;
            color: #e65100;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #ccc;
            font-size: 13px;
            font-weight: 600;
        }

        .empty-state i {
            font-size: 32px;
            display: block;
            margin-bottom: 8px;
        }

        /* ── Timeline ── */
        .timeline {
            list-style: none;
            padding: 0;
            margin: 0;
            position: relative;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 14px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #f0f0f0;
        }

        .tl-item {
            display: flex;
            gap: 14px;
            padding: 0 0 18px 0;
            position: relative;
        }

        .tl-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-top: 4px;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            border: 2px solid white;
            box-shadow: 0 0 0 2px #e85d24;
            background: #e85d24;
            margin-left: 10px;
        }

        .tl-dot.green {
            box-shadow: 0 0 0 2px #4caf50;
            background: #4caf50;
        }

        .tl-dot.orange {
            box-shadow: 0 0 0 2px #ff9800;
            background: #ff9800;
        }

        .tl-dot.red {
            box-shadow: 0 0 0 2px #f44336;
            background: #f44336;
        }

        .tl-dot.gray {
            box-shadow: 0 0 0 2px #bbb;
            background: #bbb;
        }

        .tl-body {
            flex: 1;
        }

        .tl-title {
            font-size: 12px;
            font-weight: 700;
            color: #1a1d29;
            margin: 0 0 2px;
        }

        .tl-time {
            font-size: 10px;
            color: #bbb;
            font-weight: 600;
        }

        .tl-note {
            font-size: 11px;
            color: #888;
            margin-top: 2px;
        }

        /* ── Full-width transaction section ── */
        .full-card {
            margin-bottom: 20px;
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .top-grid {
                grid-template-columns: 1fr;
            }

            .bottom-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .bottom-grid {
                grid-template-columns: 1fr;
            }

            .show-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
@endpush

@section('content')

    @if(!$inventory->product)
        {{-- ── Orphaned record ── --}}
        <div class="show-container">
            <div class="show-header" style="border-left-color:#dc2626; background:#fee2e2;">
                <div>
                    <h1 style="color:#dc2626;">⚠️ Product Not Found</h1>
                    <p>The product associated with this inventory record no longer exists.</p>
                </div>
            </div>
            <div style="text-align:center;padding:40px;background:white;border-radius:12px;">
                <p style="color:#666;margin-bottom:20px;">This inventory record has been orphaned.</p>
                <a href="{{ route('inventory.index') }}"
                    style="display:inline-block;padding:10px 24px;background:#e85d24;color:white;text-decoration:none;border-radius:8px;font-weight:700;">
                    Back to Inventory
                </a>
            </div>
        </div>

    @else

        @php
            $isOut = $inventory->quantity == 0;
            $isLow = !$isOut && $inventory->quantity <= $inventory->reorder_level;

            $unitLabels = [
             'kg' => 'គីឡូក្រាម',
                                        'g' => 'ក្រាម',
                                        'L' => 'លីត្រ',
                                        'ml' => 'កំប៉ុង',
                                        'pcs' => 'បន្ទះ',
                                        'bag' => 'ដើម',
                                        'box1' => 'កេស',
                                        'box2' => 'ប្រអប់',
                                        'pack' => 'កញ្ចប់',
            ];
            $unit = $unitLabels[$inventory->product->unit] ?? $inventory->product->unit;

            // Stock bar percentage (cap at 100)
            $maxDisplay = max($inventory->reorder_level * 3, $inventory->quantity, 1);
            $pct = min(100, round(($inventory->quantity / $maxDisplay) * 100));
            $barClass = $isOut ? '' : ($isLow ? 'low' : 'good');

            // Latest transactions (requires StockTransaction model / relation)
            // Adjust relation name/model to match your actual setup
            $transactions = method_exists($inventory, 'transactions')
                ? $inventory->transactions()->latest()->take(10)->get()
                : collect();

            // Totals from transactions
            $totalIn = $transactions->where('type', 'in')->sum('quantity');
            $totalOut = $transactions->where('type', 'out')->sum('quantity');
        @endphp

        <div class="show-container">

            {{-- ── Header ── --}}
            <div class="show-header">
                <div>
                    <h1>{{ $inventory->product->name ?? 'Unknown Product' }}</h1>
                    <p>Inventory ID #{{ $inventory->id }} &nbsp;·&nbsp; Last updated
                        {{ $inventory->updated_at ? $inventory->updated_at->diffForHumans() : '—' }}</p>
                </div>
                <div class="header-meta">
                    @if($inventory->warehouse_location)
                        <span class="meta-pill"> {{ $inventory->warehouse_location }}</span>
                    @endif
                    <span class="meta-pill">{{ $inventory->product->category ?? '—' }}</span>
                </div>
            </div>

            {{-- ── Top: Image + Details ── --}}
            <div class="top-grid">

                {{-- Image --}}
                <div class="card">
                    <div class="card-header"><span class="dot"></span> Product Image</div>
                    <div class="card-body">
                        <div class="prod-img {{ !($inventory->product->image ?? null) ? 'no-img' : '' }}">
                            @if($inventory->product->imageUrl())
                                <img src="{{ $inventory->product->imageUrl() }}" alt="{{ $inventory->product->name }}">
                            @else
                                <i class="fas fa-image"></i>
                            @endif
                        </div>
                        <p class="img-caption">{{ $inventory->product->name ?? '—' }} &nbsp;·&nbsp;
                            {{ $inventory->product->category ?? '—' }}</p>
                    </div>
                </div>

                {{-- Details --}}
                <div class="card">
                    <div class="card-header"><span class="dot"></span> Inventory Details</div>
                    <div class="card-body">
                        <h2 class="product-title">{{ $inventory->product->name ?? '—' }}</h2>
                        <p class="product-cat">{{ $inventory->product->category ?? '—' }}</p>

                        <span class="badge {{ $isOut ? 'badge-bad' : ($isLow ? 'badge-warn' : 'badge-good') }}">
                            @if($isOut) ✕ Out of Stock
                            @elseif($isLow) ⚠ Low Stock
                            @else ✓ In Stock
                            @endif
                        </span>

                        <div class="stat-row">
                            <span class="stat-label">ចំនួនក្នុងស្តុក</span>
                            <span class="stat-value" style="color:#e85d24;">{{ $inventory->quantity }} {{ $unit }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">កម្រិតចំនួន (Reorder)</span>
                            <span class="stat-value">{{ $inventory->reorder_level }} {{ $unit }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">ទីតាំងស្តុក</span>
                            <span class="stat-value">{{ $inventory->warehouse_location ?? '—' }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">ខ្នាត</span>
                            <span class="stat-value">{{ $unit }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">SKU / បាកូដ</span>
                            <span
                                class="stat-value">{{ $inventory->product->sku ?? $inventory->product->barcode ?? '—' }}</span>
                        </div>

                        {{-- Pricing --}}
                        <div class="prices">
                            <div class="price-card">
                                <div class="amount">${{ number_format($inventory->product->price_usd ?? 0, 2) }}</div>
                                <div class="currency">USD</div>
                            </div>
                            <div class="price-card">
                                <div class="amount">៛{{ number_format($inventory->product->price_khr ?? 0, 0) }}</div>
                                <div class="currency">KHR</div>
                            </div>
                        </div>

                        {{-- Stock level bar --}}
                        <div class="stock-bar-wrap">
                            <div class="stock-bar-label">
                                <span>Stock Level</span>
                                <span>{{ $pct }}%</span>
                            </div>
                            <div class="stock-bar-bg">
                                <div class="stock-bar-fill {{ $barClass }}" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>

                        {{-- Action buttons --}}
                        <div class="btns">
                            <a href="{{ route('inventory.edit', $inventory) }}" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('inventory.index') }}" class="btn-back">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Bottom 3-col: KPIs / Full Transactions / Activity Timeline ── --}}
            <div class="bottom-grid">

                {{-- KPIs + Dates --}}
                <div class="card">
                    <div class="card-header"><span class="dot"></span> Quick Stats</div>
                    <div class="card-body">
                        <div class="kpi-grid">
                            <div class="kpi highlight">
                                <div class="kpi-val">{{ $inventory->quantity }}</div>
                                <div class="kpi-label">Current Stock</div>
                            </div>
                            <div class="kpi">
                                <div class="kpi-val">{{ $inventory->reorder_level }}</div>
                                <div class="kpi-label">Reorder Level</div>
                            </div>
                            <div class="kpi">
                                <div class="kpi-val" style="color:#2e7d32;">+{{ $totalIn }}</div>
                                <div class="kpi-label">Total Stock In</div>
                            </div>
                            <div class="kpi">
                                <div class="kpi-val" style="color:#c62828;">-{{ $totalOut }}</div>
                                <div class="kpi-label">Total Stock Out</div>
                            </div>
                        </div>

                        <div class="date-info">
                            <div class="date-row">
                                <span class="dlabel"> Created At</span>
                                <span
                                    class="dval">{{ $inventory->created_at ? $inventory->created_at->format('d M Y, H:i') : '—' }}</span>
                            </div>
                            <div class="date-row">
                                <span class="dlabel"> Updated At</span>
                                <span
                                    class="dval">{{ $inventory->updated_at ? $inventory->updated_at->format('d M Y, H:i') : '—' }}</span>
                            </div>
                            <div class="date-row">
                                <span class="dlabel"> Time</span>
                                <span
                                    class="dval">{{ $inventory->created_at ? $inventory->created_at->diffForHumans() : '—' }}</span>
                            </div>
                            @if($inventory->product->created_at ?? null)
                                <div class="date-row">
                                    <span class="dlabel"> Product Since</span>
                                    <span class="dval">{{ $inventory->product->created_at->format('d M Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Recent Transactions --}}
                <div class="card">
                    <div class="card-header"><span class="dot"></span> Recent Transactions</div>
                    <div class="card-body" style="padding:0;">
                        @if($transactions->isEmpty())
                            <div class="empty-state">
                                <i class="fas fa-exchange-alt"></i>
                                No transactions recorded yet
                            </div>
                        @else
                            <table class="tx-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Qty</th>
                                        <th>Note</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $tx)
                                        <tr>
                                            <td>
                                                <span class="tx-type {{ $tx->type }}">
                                                    {{ strtoupper($tx->type) }}
                                                </span>
                                            </td>
                                            <td class="tx-{{ $tx->type }}">
                                                {{ $tx->type === 'in' ? '+' : ($tx->type === 'out' ? '-' : '±') }}{{ $tx->quantity }}
                                            </td>
                                            <td
                                                style="color:#888; max-width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                                {{ $tx->note ?? $tx->reason ?? '—' }}
                                            </td>
                                            <td style="color:#bbb; white-space:nowrap;">
                                                {{ $tx->created_at ? $tx->created_at->format('d/m H:i') : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                {{-- Activity Timeline --}}
                <div class="card">
                    <div class="card-header"><span class="dot"></span> Activity Timeline</div>
                    <div class="card-body">
                        @if($transactions->isEmpty() && !$inventory->created_at)
                            <div class="empty-state">
                                <i class="fas fa-history"></i>
                                No activity recorded
                            </div>
                        @else
                            <ul class="timeline">

                                {{-- Created record --}}
                                <li class="tl-item">
                                    <div class="tl-dot gray"></div>
                                    <div class="tl-body">
                                        <p class="tl-title">Inventory Record Created</p>
                                        <p class="tl-time">
                                            {{ $inventory->created_at ? $inventory->created_at->format('d M Y, H:i') : '—' }}</p>
                                    </div>
                                </li>

                                {{-- Transactions as timeline events --}}
                                @foreach($transactions->take(7) as $tx)
                                    <li class="tl-item">
                                        <div
                                            class="tl-dot {{ $tx->type === 'in' ? 'green' : ($tx->type === 'out' ? 'red' : 'orange') }}">
                                        </div>
                                        <div class="tl-body">
                                            <p class="tl-title">
                                                @if($tx->type === 'in') Stock In
                                                @elseif($tx->type === 'out') Stock Out
                                                @else Adjustment
                                                @endif
                                                <span style="color:#e85d24;">
                                                    {{ $tx->type === 'in' ? '+' : ($tx->type === 'out' ? '-' : '±') }}{{ $tx->quantity }}
                                                    {{ $unit }}
                                                </span>
                                            </p>
                                            <p class="tl-time">{{ $tx->created_at ? $tx->created_at->format('d M Y, H:i') : '—' }}</p>
                                            @if($tx->note ?? $tx->reason ?? null)
                                                <p class="tl-note">{{ $tx->note ?? $tx->reason }}</p>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach

                                {{-- Last updated (if different from created) --}}
                                @if($inventory->updated_at && $inventory->updated_at->ne($inventory->created_at))
                                    <li class="tl-item">
                                        <div class="tl-dot"></div>
                                        <div class="tl-body">
                                            <p class="tl-title">Record Last Updated</p>
                                            <p class="tl-time">{{ $inventory->updated_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </li>
                                @endif

                            </ul>
                        @endif
                    </div>
                </div>

            </div>{{-- /bottom-grid --}}

        </div>{{-- /show-container --}}
    @endif

@endsection
