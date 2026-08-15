@extends('layouts.app')

@section('title', ($inventory->product ? $inventory->product->name : 'Inventory') . ' Details')

@push('styles')
    <style>
        * {
            font-family: 'Poppins', 'Noto Sans Khmer', 'Hanuman', 'Battambang', 'Khmer OS', sans-serif;
            line-height: 1.6;
        }

        .show-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 24px;
        }

        /* ── Breadcrumb ── */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #aaa;
            margin-bottom: 14px;
        }

        .breadcrumb a {
            color: #888;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color .15s;
        }

        .breadcrumb a:hover {
            color: #e85d24;
        }

        .breadcrumb .crumb-sep {
            color: #ddd;
        }

        .breadcrumb .crumb-current {
            color: #1a1d29;
            font-weight: 700;
        }

        /* ── Header ── */
        .show-header {
            background: white;
            padding: 20px 28px;
            border-radius: 12px;
            border-left: 4px solid #e85d24;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .06);
        }

        .header-title-row {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .show-header h1 {
            font-size: 22px;
            font-weight: 800;
            color: #1a1d29;
            margin: 0;
        }

        .show-header p {
            font-size: 13px;
            color: #aaa;
            margin: 4px 0 0;
        }

        .header-meta {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .meta-pill {
            font-size: 12.5px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            background: #f5f6fa;
            color: #666;
            white-space: nowrap;
        }

        /* ── Status badge ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 700;
            white-space: nowrap;
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

        /* ── Top Grid (image + info) ── */
        .top-grid {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 20px;
            margin-bottom: 20px;
            align-items: start;
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
            font-size: 14.5px;
            font-weight: 800;
            color: #1a1d29;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-header i {
            color: #e85d24;
            font-size: 12px;
        }

        .card-body {
            padding: 20px;
        }

        /* ── Image ── */
        .prod-img {
            width: 100%;
            height: 260px;
            background: #f8f9fc;
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
            flex-direction: column;
            gap: 8px;
            color: #d8d8e0;
        }

        .prod-img.no-img i {
            font-size: 44px;
        }

        .prod-img.no-img span {
            font-size: 12.5px;
            font-weight: 700;
            color: #c5c5d0;
        }

        .img-caption {
            font-size: 12.5px;
            color: #bbb;
            text-align: center;
            margin: 0;
            line-height: 1.6;
        }

        /* ── Hero stock figure ── */
        .hero-stock {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 16px;
            margin-bottom: 16px;
            border-bottom: 1px solid #f5f5f5;
        }

        .hero-stock-num {
            font-size: 38px;
            font-weight: 900;
            color: #1a1d29;
            line-height: 1;
        }

        .hero-stock-num .unit {
            font-size: 14px;
            font-weight: 700;
            color: #aaa;
            margin-left: 6px;
        }

        .hero-stock-label {
            font-size: 12.5px;
            font-weight: 700;
            color: #aaa;
            letter-spacing: .2px;
            margin: 0 0 6px;
        }

        .hero-reorder {
            text-align: right;
            font-size: 12.5px;
            color: #aaa;
            font-weight: 600;
        }

        .hero-reorder b {
            display: block;
            font-size: 16px;
            color: #1a1d29;
            font-weight: 800;
        }

        /* Stock level bar */
        .stock-bar-wrap {
            margin-bottom: 18px;
        }

        .stock-bar-label {
            display: flex;
            justify-content: space-between;
            font-size: 12.5px;
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

        /* ── Key/value stat grid ── */
        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 20px;
            margin-bottom: 6px;
        }

        .stat-cell {
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .stat-cell.full {
            grid-column: 1 / -1;
        }

        .stat-label {
            display: block;
            font-size: 11.5px;
            color: #aaa;
            font-weight: 700;
            letter-spacing: .2px;
            margin-bottom: 3px;
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
            margin: 16px 0 4px;
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
            margin-top: 18px;
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

        /* ── KPI strip ── */
        .kpi-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        .kpi-tile {
            background: white;
            border: 1px solid #ebebeb;
            border-radius: 12px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, .05);
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .kpi-tile .kpi-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .kpi-tile .kpi-val {
            font-size: 24px;
            font-weight: 900;
            color: #1a1d29;
            line-height: 1.1;
        }

        .kpi-tile .kpi-label {
            font-size: 14px;
            color: #aaa;
            font-weight: 700;
            margin-top: 2px;
            letter-spacing: .2px;
        }

        .kpi-icon.in {  color: #2e7d32; }
        .kpi-icon.out {   color: #c62828; }
        .kpi-icon.net {   color: #e65100; }
        .kpi-icon.age {  color: #5b5be0; }

        /* ── Main grid (transactions + timeline) ── */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
            align-items: start;
        }

        /* ── Transaction Table ── */
        .tx-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .tx-table th {
            text-align: left;
            padding: 10px 16px;
            background: #f8f9fc;
            color: #999;
            font-weight: 700;
            font-size: 11.5px;
            letter-spacing: .2px;
            border-bottom: 1px solid #ebebeb;
        }

        .tx-table th.num, .tx-table td.num {
            text-align: right;
        }

        .tx-table td {
            padding: 11px 16px;
            border-bottom: 1px solid #f5f5f5;
            color: #1a1d29;
            font-weight: 600;
            vertical-align: middle;
        }

        .tx-table tr:last-child td {
            border-bottom: none;
        }

        .tx-table tr:nth-child(even) td {
            background: #fbfbfd;
        }

        .tx-table tr:hover td {
            background: #fdf3ee;
        }

        .tx-in {
            color: #2e7d32;
            font-weight: 800;
        }

        .tx-out {
            color: #c62828;
            font-weight: 800;
        }

        .tx-type {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 10px;
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
        }

        .tx-type.in {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .tx-type.out {
            background: #ffebee;
            color: #c62828;
        }

        .empty-state {
            text-align: center;
            padding: 44px 20px;
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

        .tl-item:last-child {
            padding-bottom: 0;
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
            min-width: 0;
        }

        .tl-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a1d29;
            margin: 0 0 2px;
        }

        .tl-time {
            font-size: 11.5px;
            color: #bbb;
            font-weight: 600;
        }

        .tl-note {
            font-size: 12.5px;
            color: #888;
            margin-top: 2px;
            overflow-wrap: break-word;
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .top-grid {
                grid-template-columns: 1fr;
            }

            .kpi-strip {
                grid-template-columns: 1fr 1fr;
            }

            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .show-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }

            .kpi-strip {
                grid-template-columns: 1fr;
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
                    <h1 style="color:#dc2626;"> រកមិនឃើញទំនិញ</h1>
                    <p>ទំនិញដែលភ្ជាប់ជាមួយកំណត់ត្រាស្តុកនេះលែងមានទៀតហើយ។</p>
                </div>
            </div>
            <div style="text-align:center;padding:40px;background:white;border-radius:12px;">
                <p style="color:#666;margin-bottom:20px;">កំណត់ត្រាស្តុកនេះឥឡូវនេះគ្មានទំនិញភ្ជាប់ជាមួយទេ។</p>
                <a href="{{ route('inventory.index') }}"
                    style="display:inline-block;padding:10px 24px;background:#e85d24;color:white;text-decoration:none;border-radius:8px;font-weight:700;">
                    ត្រឡប់ទៅស្តុកទំនិញ
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

            // Movement history (stock in/out/adjustments), newest first
            $movements = $inventory->movements()->with('user')->latest()->take(10)->get();
            $totalIn = (int) $inventory->movements()->where('quantity_change', '>', 0)->sum('quantity_change');
            $totalOut = (int) abs($inventory->movements()->where('quantity_change', '<', 0)->sum('quantity_change'));
            $netChange = $totalIn - $totalOut;

            // Estimated value currently held, using manual cost if set, else the product's USD price
            $unitCost = $inventory->cost_per_unit ?? $inventory->product->price_usd ?? 0;
            $stockValue = $inventory->quantity * $unitCost;

            $movementTypeLabels = [
                'stock_create' => 'បង្កើត',
                'manual_adjust' => 'កែសម្រួល',
                'quick_adjust' => 'កែសម្រួលរហ័ស',
                'stock_restock' => 'បន្ថែមស្តុក',
                'stock_reduce' => 'កាត់ស្តុក',
                'order_deduct' => 'កាត់ដោយការបញ្ជាទិញ',
                'order_restore' => 'ត្រឡប់ដោយការបញ្ជាទិញ',
            ];
            $movementLabel = fn($m) => $movementTypeLabels[$m->type] ?? $m->type;
        @endphp

        <div class="show-container">

            {{-- ── Breadcrumb ── --}}
            <div class="breadcrumb">
                <a href="{{ route('inventory.index') }}"><i class="fas fa-boxes"></i> ស្តុកទំនិញ</a>
                <span class="crumb-sep">/</span>
                <span class="crumb-current">{{ $inventory->product->name }}</span>
            </div>

            {{-- ── Header ── --}}
            <div class="show-header">
                <div>
                    <div class="header-title-row">
                        <h1>{{ $inventory->product->name ?? 'មិនស្គាល់ទំនិញ' }}</h1>
                        <span class="badge {{ $isOut ? 'badge-bad' : ($isLow ? 'badge-warn' : 'badge-good') }}">
                            @if($isOut) ✕ អស់ស្តុក
                            @elseif($isLow) ⚠ ជិតអស់ស្តុក
                            @else ✓ មានស្តុក
                            @endif
                        </span>
                    </div>
                    <p>លេខស្តុក #{{ $inventory->id }} &nbsp;·&nbsp; កែប្រែចុងក្រោយ
                        {{ $inventory->updated_at ? $inventory->updated_at->diffForHumans() : '—' }}</p>
                </div>
                <div class="header-meta">
                    @if($inventory->warehouse_location)
                        <span class="meta-pill"><i class="fas fa-warehouse"></i> {{ $inventory->warehouse_location }}</span>
                    @endif
                    <span class="meta-pill"><i class="fas fa-tag"></i> {{ $inventory->product->category ?? '—' }}</span>
                </div>
            </div>

            {{-- ── Top: Image + Details ── --}}
            <div class="top-grid">

                {{-- Image --}}
                <div class="card">
                    <div class="card-header"><i class="fas fa-image"></i> រូបភាពទំនិញ</div>
                    <div class="card-body">
                        <div class="prod-img {{ !$inventory->product->imageUrl() ? 'no-img' : '' }}">
                            @if($inventory->product->imageUrl())
                                <img src="{{ $inventory->product->imageUrl() }}" alt="{{ $inventory->product->name }}">
                            @else
                                <i class="fas fa-image"></i>
                                <span>គ្មានរូបភាព</span>
                            @endif
                        </div>
                        <p class="img-caption">{{ $inventory->product->name ?? '—' }} &nbsp;·&nbsp;
                            {{ $inventory->product->category ?? '—' }}
                            @if($inventory->product->created_at)
                                <br>ក្នុងស្តុកតាំងពី {{ $inventory->product->created_at->format('d M Y') }}
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Details --}}
                <div class="card">
                    <div class="card-header"><i class="fas fa-clipboard-list"></i> ព័ត៌មានលម្អិតស្តុក</div>
                    <div class="card-body">

                        {{-- Hero stock figure --}}
                        <div class="hero-stock">
                            <div>
                                <p class="hero-stock-label">ចំនួនក្នុងស្តុកបច្ចុប្បន្ន</p>
                                <div class="hero-stock-num" style="color:{{ $isOut ? '#c62828' : ($isLow ? '#e65100' : '#1a1d29') }};">
                                    {{ number_format($inventory->quantity) }}<span class="unit">{{ $unit }}</span>
                                </div>
                            </div>
                            <div class="hero-reorder">
                                កម្រិតត្រូវបំពេញ
                                <b>{{ number_format($inventory->reorder_level) }} {{ $unit }}</b>
                            </div>
                        </div>

                        <div class="stock-bar-wrap">
                            <div class="stock-bar-label">
                                <span>កម្រិតស្តុក</span>
                                <span>{{ $pct }}%</span>
                            </div>
                            <div class="stock-bar-bg">
                                <div class="stock-bar-fill {{ $barClass }}" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>

                        <div class="stat-grid">
                            <div class="stat-cell">
                                <span class="stat-label">ទីតាំងស្តុក</span>
                                <span class="stat-value">{{ $inventory->warehouse_location ?? '—' }}</span>
                            </div>
                            <div class="stat-cell">
                                <span class="stat-label">ខ្នាត</span>
                                <span class="stat-value">{{ $unit }}</span>
                            </div>
                            <div class="stat-cell">
                                <span class="stat-label">SKU / បាកូដ</span>
                                <span class="stat-value">{{ $inventory->product->sku ?? $inventory->product->barcode ?? '—' }}</span>
                            </div>
                            @if($inventory->cost_per_unit)
                                <div class="stat-cell">
                                    <span class="stat-label">តម្លៃដើម (កំណត់ដោយដៃ)</span>
                                    <span class="stat-value">${{ number_format($inventory->cost_per_unit, 2) }}</span>
                                </div>
                            @endif
                            @if($stockValue > 0)
                                <div class="stat-cell full">
                                    <span class="stat-label">តម្លៃស្តុកសរុប (ប៉ាន់ស្មាន)</span>
                                    <span class="stat-value" style="color:#e85d24;">${{ number_format($stockValue, 2) }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Pricing (KHR is the base price; USD is derived from it) --}}
                        <div class="prices">
                            <div class="price-card">
                                <div class="amount">៛{{ number_format($inventory->product->price_khr ?? 0, 0) }}</div>
                                <div class="currency">KHR</div>
                            </div>
                            <div class="price-card">
                                <div class="amount">${{ number_format($inventory->product->price_usd ?? 0, 2) }}</div>
                                <div class="currency">USD</div>
                            </div>
                        </div>

                        {{-- Action buttons --}}
                        <div class="btns">
                            @unless(auth()->user()->isAuditor())
                                <a href="{{ route('inventory.edit', $inventory) }}" class="btn-edit">
                                    <i class="fas fa-edit"></i> កែ
                                </a>
                            @endunless
                            <a href="{{ route('inventory.index') }}" class="btn-back">
                                <i class="fas fa-arrow-left"></i> ត្រឡប់
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── KPI strip ── --}}
            <div class="kpi-strip">
                <div class="kpi-tile">
                    <div class="kpi-icon in"></i></div>
                    <div>
                        <div class="kpi-val" style="color:#2e7d32;">+{{ number_format($totalIn) }}</div>
                        <div class="kpi-label">ស្តុកចូលសរុប</div>
                    </div>
                </div>
                <div class="kpi-tile">
                    <div class="kpi-icon out"></div>
                    <div>
                        <div class="kpi-val" style="color:#c62828;">-{{ number_format($totalOut) }}</div>
                        <div class="kpi-label">ស្តុកចេញសរុប</div>
                    </div>
                </div>
                <div class="kpi-tile">
                    <div class="kpi-icon net"></div>
                    <div>
                        <div class="kpi-val" style="color:{{ $netChange >= 0 ? '#2e7d32' : '#c62828' }};">
                            {{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange) }}
                        </div>
                        <div class="kpi-label">ស្តុក (ចូល − ចេញ)</div>
                    </div>
                </div>
                <div class="kpi-tile">
                    <div class="kpi-icon age"></div>
                    <div>
                        <div class="kpi-val" style="font-size:24px;">
                            {{ $inventory->created_at ? $inventory->created_at->diffForHumans(null, true) : '—' }}
                        </div>
                        <div class="kpi-label">តាមដានតាំងពី</div>
                    </div>
                </div>
            </div>

            {{-- ── Main: Movements (wide) + Timeline (narrow) ── --}}
            <div class="main-grid">

                {{-- Recent Movements --}}
                <div class="card">
                    <div class="card-header"><i class="fas fa-exchange-alt"></i> ចលនាស្តុកថ្មីៗ</div>
                    <div class="card-body" style="padding:0;">
                        @if($movements->isEmpty())
                            <div class="empty-state">
                                <i class="fas fa-exchange-alt"></i>
                                មិនទាន់មានចលនាស្តុកទេ
                            </div>
                        @else
                            <div style="overflow-x:auto;">
                                <table class="tx-table">
                                    <thead>
                                        <tr>
                                            <th>ប្រភេទ</th>
                                            <th class="num">ចំនួន</th>
                                            <th>មូលហេតុ</th>
                                            <th>អ្នកចេញវិក្ក័យបត្រ</th>
                                            <th>កាលបរិច្ឆេទ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($movements as $m)
                                            @php $isIn = $m->quantity_change > 0; @endphp
                                            <tr>
                                                <td>
                                                    <span class="tx-type {{ $isIn ? 'in' : 'out' }}">
                                                        {{ $movementLabel($m) }}
                                                    </span>
                                                </td>
                                                <td class="num {{ $isIn ? 'tx-in' : 'tx-out' }}">
                                                    {{ $isIn ? '+' : '' }}{{ number_format($m->quantity_change) }}
                                                </td>
                                                <td
                                                    style="color:#888; max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                                    title="{{ $m->note }}">
                                                    {{ $m->note ?? '—' }}
                                                </td>
                                                <td style="color:#888; white-space:nowrap;">
                                                    {{ $m->user?->name ?? '—' }}
                                                </td>
                                                <td style="color:#bbb; white-space:nowrap;">
                                                    {{ $m->created_at ? $m->created_at->format('d/m/Y H:i') : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Activity Timeline --}}
                <div class="card">
                    <div class="card-header"><i class="fas fa-history"></i> ប្រវត្តិសកម្មភាព</div>
                    <div class="card-body">
                        @if($movements->isEmpty() && !$inventory->created_at)
                            <div class="empty-state">
                                <i class="fas fa-history"></i>
                                មិនទាន់មានប្រវត្តិសកម្មភាពទេ
                            </div>
                        @else
                            <ul class="timeline">

                                {{-- Last updated (if different from created) — shown most recent first --}}
                                @if($inventory->updated_at && $inventory->updated_at->ne($inventory->created_at))
                                    <li class="tl-item">
                                        <div class="tl-dot"></div>
                                        <div class="tl-body">
                                            <p class="tl-title">ការកែចុងក្រោយ</p>
                                            <p class="tl-time">{{ $inventory->updated_at->format('d M Y, H:i') }}</p>
                                        </div>
                                    </li>
                                @endif

                                {{-- Movements as timeline events --}}
                                @foreach($movements->take(7) as $m)
                                    @php $isIn = $m->quantity_change > 0; @endphp
                                    <li class="tl-item">
                                        <div class="tl-dot {{ $isIn ? 'green' : 'red' }}"></div>
                                        <div class="tl-body">
                                            <p class="tl-title">
                                                {{ $movementLabel($m) }}
                                                <span style="color:#e85d24;">
                                                    {{ $isIn ? '+' : '' }}{{ number_format($m->quantity_change) }}
                                                    {{ $unit }}
                                                </span>
                                            </p>
                                            <p class="tl-time">{{ $m->created_at ? $m->created_at->format('d M Y, H:i') : '—' }}
                                                @if($m->user)&nbsp;·&nbsp;{{ $m->user->name }}@endif
                                            </p>
                                            @if($m->note)
                                                <p class="tl-note">{{ $m->note }}</p>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach

                                {{-- Created record --}}
                                <li class="tl-item">
                                    <div class="tl-dot gray"></div>
                                    <div class="tl-body">
                                        <p class="tl-title">បង្កើតកំណត់ត្រាស្តុក</p>
                                        <p class="tl-time">
                                            {{ $inventory->created_at ? $inventory->created_at->format('d M Y, H:i') : '—' }}</p>
                                    </div>
                                </li>

                            </ul>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    @endif

@endsection
