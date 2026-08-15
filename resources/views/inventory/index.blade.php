@extends('layouts.app')

@section('title', 'ស្តុកទំនិញ')

@push('styles')
    <style>
        .inventory-page {
            --accent: #e85d24;
            --accent-dark: #d94a10;
            --accent-soft: #fff7ed;
            --border: #e5e7eb;
            --danger: #dc2626;
            --muted: #64748b;
            --success: #059669;
            --surface: #fff;
            --text: #0f172a;
            --warning: #d97706;
            --shadow: 0 12px 32px rgba(15, 23, 42, .07);
        }

        .inventory-header {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .inventory-title {
            color: var(--text);
            font-size: 30px;
            font-weight: 900;
            margin: 0;
        }

        .inventory-subtitle {
            color: var(--muted);
            margin: 6px 0 0;
        }

        .inventory-btn {
            align-items: center;
            border: 0;
            border-radius: 8px;
            display: inline-flex;
            font-weight: 900;
            gap: 8px;
            justify-content: center;
            min-height: 40px;
            padding: 9px 14px;
            text-decoration: none;
            transition: background .15s ease, color .15s ease, transform .15s ease;
            white-space: nowrap;
        }

        .inventory-btn:hover {
            transform: translateY(-1px);
        }

        .inventory-btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            box-shadow: 0 10px 22px rgba(232, 93, 36, .18);
            color: #fff;
        }

        .inventory-btn-primary:hover {
            color: #fff;
        }

        .inventory-btn-soft {
            background: #f3f4f6;
            color: #374151;
        }

        .inventory-btn-soft:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .export-group {
            align-items: center;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: inline-flex;
            overflow: hidden;
        }

        .export-btn {
            align-items: center;
            color: #374151;
            display: inline-flex;
            font-weight: 900;
            gap: 8px;
            min-height: 40px;
            padding: 9px 14px;
            text-decoration: none;
            transition: background .15s ease, color .15s ease;
        }

        .export-btn:hover {
            background: #f3f4f6;
            color: #111827;
        }

        .inventory-stats {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: var(--shadow);
            overflow: hidden;
            padding: 20px 18px;
            position: relative;
            transition: box-shadow .15s ease, transform .15s ease;
        }

        .stat-card:hover {
            box-shadow: 0 16px 36px rgba(15, 23, 42, .1);
            transform: translateY(-1px);
        }

        .stat-card::before {
            background: var(--accent);
            content: "";
            inset: 0 auto 0 0;
            position: absolute;
            width: 4px;
        }

        .stat-card.stat-success::before { background: var(--success); }
        .stat-card.stat-warning::before { background: var(--warning); }
        .stat-card.stat-danger::before { background: var(--danger); }

        .stat-label {
            color: var(--muted);
            font-size: 18px;
            font-weight: 900;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .stat-value {
            color: var(--text);
            font-size: 32px;
            font-weight: 900;
            line-height: 1.2;
            margin-top: 12px;
        }

        .filter-card,
        .inventory-table-card,
        .empty-state {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .filter-card {
            margin-bottom: 16px;
            overflow: hidden;
            padding: 0;
        }

        .toolbar-row {
            align-items: stretch;
            display: flex;
        }

        .toolbar-field {
            align-items: center;
            display: flex;
            flex-shrink: 0;
            gap: 9px;
            padding: 0 16px;
        }

        .toolbar-search {
            flex: 1 1 auto;
            min-width: 200px;
        }

        .toolbar-field > i {
            color: var(--muted);
            flex-shrink: 0;
            font-size: 13px;
        }

        .toolbar-field input,
        .toolbar-field select {
            background: transparent;
            border: 0;
            color: var(--text);
            font-size: 14px;
            font-weight: 700;
            min-height: 46px;
            outline: none;
            padding: 0;
            width: 100%;
        }

        .toolbar-field select {
            cursor: pointer;
        }

        .toolbar-date {
            display: none;
        }

        .toolbar-date.show {
            display: flex;
        }

        .toolbar-divider {
            align-self: center;
            background: var(--border);
            flex-shrink: 0;
            height: 26px;
            width: 1px;
        }

        .toolbar-search-btn {
            align-items: center;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border: 0;
            border-radius: 0 7px 7px 0;
            color: #fff;
            display: inline-flex;
            flex-shrink: 0;
            font-weight: 900;
            gap: 8px;
            justify-content: center;
            padding: 0 22px;
            white-space: nowrap;
        }

        .toolbar-search-btn:hover {
            background: linear-gradient(135deg, var(--accent-dark), var(--accent-dark));
        }

        .inventory-table-card {
            overflow: hidden;
        }

        .inventory-table th {
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            padding: 14px 16px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .inventory-table td {
            border-bottom: 1px solid #edf0f4;
            color: var(--text);
            padding: 14px 16px;
            vertical-align: middle;
        }

        .inventory-table tr:hover td {
            background: #fbfdff;
        }

        .inventory-table tr[data-status="out"] td:first-child {
            border-left: 4px solid var(--danger);
        }

        .inventory-table tr[data-status="low"] td:first-child {
            border-left: 4px solid var(--warning);
        }

        .stock-unit {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            margin-left: 3px;
        }

        .product-name {
            font-weight: 900;
        }

        .product-meta {
            color: var(--muted);
            font-size: 12px;
            margin-top: 3px;
        }

        .stock-cell {
            min-width: 160px;
        }

        .stock-number {
            color: var(--text);
            cursor: pointer;
            font-size: 20px;
            font-weight: 900;
            text-decoration: underline;
            text-decoration-color: rgba(232, 93, 36, .35);
            text-underline-offset: 4px;
        }

        .stock-progress {
            background: #edf2f7;
            border-radius: 999px;
            height: 7px;
            margin: 7px auto 0;
            max-width: 120px;
            overflow: hidden;
        }

        .stock-progress span {
            background: var(--success);
            border-radius: inherit;
            display: block;
            height: 100%;
            min-width: 4px;
        }

        .stock-progress.low span {
            background: var(--warning);
        }

        .stock-progress.out span {
            background: var(--danger);
        }

        .status-pill {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 900;
            gap: 6px;
            padding: 6px 10px;
            white-space: nowrap;
        }

        .status-good {
            background: #d1fae5;
            color: #065f46;
        }

        .status-low {
            background: #fef3c7;
            color: #92400e;
        }

        .status-out {
            background: #fee2e2;
            color: #991b1b;
        }

        .movement-pill {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 900;
            gap: 6px;
            padding: 6px 10px;
        }

        .movement-out {
            background: #fff7ed;
            color: #c2410c;
        }

        .movement-in {
            background: #ecfdf5;
            color: #047857;
        }

        .movement-time {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            margin-top: 5px;
        }

        .action-row {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .icon-action {
            align-items: center;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            color: #2563eb;
            display: inline-flex;
            height: 34px;
            justify-content: center;
            text-decoration: none;
            width: 34px;
        }

        .icon-action:hover {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .icon-danger {
            background: #fef2f2;
            border-color: #fecaca;
            color: var(--danger);
        }

        .icon-danger:hover {
            background: #fee2e2;
            color: #b91c1c;
        }

        .icon-restock {
            background: #ecfdf5;
            border-color: #bbf7d0;
            color: #047857;
        }

        .icon-restock:hover {
            background: #d1fae5;
            color: #065f46;
        }

        .icon-reduce {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }

        .icon-reduce:hover {
            background: #ffedd5;
            color: #9a3412;
        }

        .restock-overlay {
            --accent: #e85d24;
            --accent-dark: #d94a10;
            --accent-soft: #fff7ed;
            --border: #e5e7eb;
            --danger: #dc2626;
            --muted: #64748b;
            --text: #0f172a;
            align-items: center;
            background: rgba(15, 23, 42, .42);
            display: none;
            inset: 0;
            justify-content: center;
            padding: 20px;
            position: fixed;
            z-index: 1100;
        }

        .restock-overlay.show {
            display: flex;
        }

        .restock-modal {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
            max-width: 440px;
            overflow: hidden;
            width: 100%;
        }

        .restock-header {
            align-items: center;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            display: flex;
            justify-content: space-between;
            padding: 16px 18px;
        }

        .restock-title {
            font-size: 17px;
            font-weight: 900;
            margin: 0;
        }

        .restock-close {
            align-items: center;
            background: rgba(255, 255, 255, .18);
            border: 0;
            border-radius: 8px;
            color: #fff;
            display: inline-flex;
            height: 34px;
            justify-content: center;
            width: 34px;
        }

        .restock-body {
            padding: 18px;
        }

        .restock-product {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 14px;
            padding: 12px;
        }

        .restock-product-name {
            color: var(--text);
            font-size: 16px;
            font-weight: 900;
        }

        .restock-product-meta {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            margin-top: 4px;
        }

        .restock-field label {
            color: var(--muted);
            display: block;
            font-size: 12px;
            font-weight: 900;
            margin-bottom: 7px;
            text-transform: uppercase;
        }

        .restock-input {
            border: 1.5px solid #d9dee7;
            border-radius: 8px;
            color: var(--text);
            font-size: 22px;
            font-weight: 900;
            min-height: 52px;
            outline: none;
            padding: 10px 12px;
            width: 100%;
        }

        .restock-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(232, 93, 36, .13);
        }

        .restock-preview {
            align-items: center;
            display: grid;
            gap: 8px;
            grid-template-columns: 1fr auto 1fr;
            margin-top: 14px;
        }

        .restock-preview-card {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .restock-preview-label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .restock-preview-value {
            color: var(--text);
            font-size: 20px;
            font-weight: 900;
            margin-top: 2px;
        }

        .restock-preview-plus {
            color: var(--accent);
            font-weight: 900;
        }

        .restock-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .restock-actions .inventory-btn-primary {
            color: #fff;
            min-width: 118px;
        }

        .restock-actions .inventory-btn-primary:hover {
            color: #fff;
        }

        .restock-error {
            color: var(--danger);
            display: none;
            font-size: 12px;
            font-weight: 800;
            margin-top: 8px;
        }

        .restock-error.show {
            display: block;
        }

        .reason-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 8px;
        }

        .reason-chip {
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 999px;
            color: #475569;
            cursor: pointer;
            font-size: 12px;
            font-weight: 800;
            padding: 6px 12px;
        }

        .reason-chip:hover {
            background: #f1f5f9;
        }

        .reason-chip.active {
            background: #ffedd5;
            border-color: #fdba74;
            color: #9a3412;
        }

        .empty-state {
            padding: 64px 20px;
            text-align: center;
        }

        .empty-state i {
            color: var(--accent);
            font-size: 42px;
            margin-bottom: 14px;
        }

        .empty-state h3 {
            color: var(--text);
            font-size: 22px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--muted);
            margin-bottom: 18px;
        }

        @media (max-width: 1100px) {
            .inventory-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .toolbar-row {
                flex-wrap: wrap;
            }

            .toolbar-field {
                border-bottom: 1px solid var(--border);
                flex: 1 1 50%;
            }

            .toolbar-divider {
                display: none;
            }

            .toolbar-search-btn {
                border-radius: 0;
                flex: 1 1 100%;
                justify-content: center;
                padding: 12px;
            }
        }

        @media (max-width: 700px) {
            .inventory-header {
                align-items: stretch;
                flex-direction: column;
            }

            .inventory-stats {
                grid-template-columns: 1fr;
            }

            .toolbar-field {
                flex: 1 1 100%;
            }

            .inventory-btn {
                width: 100%;
            }
        }

        .pager-wrap {
            margin-top: 16px;
        }
    </style>
@endpush

@section('content')
    @php
        $movementActive = $movementDate || in_array(request('period'), ['month', 'year'], true);
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
    @endphp

    <div class="container-fluid py-4 inventory-page">
        <div class="inventory-header">
            <div>
                <h2 class="inventory-title">ស្តុកទំនិញ</h2>
                <p class="inventory-subtitle">តាមដានចំនួនទំនិញ កម្រិតស្តុក និងទីតាំងស្តុកសម្រាប់ការរៀបចំទំនិញ</p>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <div class="export-group">
                    <a href="{{ route('inventory.export.excel', request()->query()) }}" class="export-btn" title="នាំចេញ Excel">
                        <i class="fas fa-file-excel" style="color:#059669;"></i> Excel
                    </a>
                    <span class="toolbar-divider"></span>
                    <a href="{{ route('inventory.export.pdf', request()->query()) }}" class="export-btn" target="_blank" title="នាំចេញ PDF">
                        <i class="fas fa-file-pdf" style="color:#dc2626;"></i> PDF
                    </a>
                </div>
                @unless(auth()->user()->isAuditor())
                    <a href="{{ route('inventory.create') }}" class="inventory-btn inventory-btn-primary">
                        <i class="fas fa-plus"></i> បន្ថែមស្តុក
                    </a>
                @endunless
            </div>
        </div>

        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="inventory-stats">
            <div class="stat-card">
                <div class="stat-label">មុខទំនិញសរុប</div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="stat-card stat-success">
                <div class="stat-label">មានក្នុងស្តុក</div>
                <div class="stat-value text-success">{{ number_format($stats['in_stock']) }}</div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-label">ជិតអស់</div>
                <div class="stat-value" style="color:var(--warning);">{{ number_format($stats['low_stock']) }}</div>
            </div>
            <div class="stat-card stat-danger">
                <div class="stat-label">អស់ស្តុក</div>
                <div class="stat-value text-danger">{{ number_format($stats['out_stock']) }}</div>
            </div>
        </div>

        @php
            $toolbarPeriod = request('period', 'today');
            $toolbarCustomDate = !in_array($toolbarPeriod, ['today', 'yesterday', 'all'], true);
        @endphp

        <div class="filter-card">
            <form id="inventoryFilters" method="GET" action="{{ route('inventory.index') }}">
                <div class="toolbar-row">
                    <div class="toolbar-field toolbar-search">
                        <i class="fas fa-search"></i>
                        <input type="search" name="search" id="inventorySearch" value="{{ request('search') }}" placeholder="ស្វែងរកទំនិញ ឬប្រភេទ..." autocomplete="off">
                    </div>

                    <div class="toolbar-divider"></div>

                    <div class="toolbar-field">
                      
                        <select id="statusFilter" name="status" onchange="this.form.submit()">
                            <option value="">គ្រប់ស្ថានភាព</option>
                            <option value="in" {{ request('status') === 'in' ? 'selected' : '' }}>មានក្នុងស្តុក</option>
                            <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>ជិតអស់</option>
                            <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>អស់ស្តុក</option>
                        </select>
                    </div>

                    <div class="toolbar-divider"></div>

                    <div class="toolbar-field">
                        <i class="fas fa-warehouse"></i>
                        <select id="warehouseFilter" name="warehouse" onchange="this.form.submit()">
                            <option value="">គ្រប់ទីតាំង</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ strtolower($warehouse) }}" {{ request('warehouse') === strtolower($warehouse) ? 'selected' : '' }}>{{ $warehouse }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="toolbar-divider"></div>

                    <div class="toolbar-field">
                        <i class="fas fa-calendar-day"></i>
                        <select id="periodSelect" name="period" onchange="handlePeriodChange(this)">
                            <option value="today" {{ $toolbarPeriod === 'today' ? 'selected' : '' }}>ថ្ងៃនេះ</option>
                            <option value="yesterday" {{ $toolbarPeriod === 'yesterday' ? 'selected' : '' }}>ម្សិលមិញ</option>
                            <option value="everyday" {{ $toolbarPeriod === 'everyday' ? 'selected' : '' }}>រៀងរាល់ថ្ងៃ</option>
                            <option value="" {{ $toolbarCustomDate ? 'selected' : '' }}>ជ្រើសកាលបរិច្ឆេទ...</option>
                        </select>
                    </div>

                    <div class="toolbar-divider toolbar-date {{ $toolbarCustomDate ? 'show' : '' }}" id="toolbarDateDivider"></div>

                    <div class="toolbar-field toolbar-date {{ $toolbarCustomDate ? 'show' : '' }}" id="toolbarDateField">
                        <i class="fas fa-calendar"></i>
                        <input type="date" name="date" id="toolbarDate" value="{{ request('date') }}" title="ត្រងតាមកាលបរិច្ឆេទចលនាស្តុក">
                    </div>

                    <button type="submit" class="toolbar-search-btn">
                        <i class="fas fa-search"></i> ស្វែងរក
                    </button>
                </div>
            </form>
        </div>

        @if($inventories->count())
            <div class="inventory-table-card">
                <div class="table-responsive">
                    <table class="table inventory-table mb-0">
                        <thead>
                            <tr>
                                <th>ទំនិញ</th>
                                <th>ប្រភេទ</th>
                                <th>ទីតាំងស្តុក</th>
                                <th class="text-center">ចំនួន</th>
                                @if($movementActive)
                                    <th class="text-center">ចលនាស្តុក</th>
                                @endif
                                <th class="text-center" title="ស្តុកគួរបន្ថែម នៅពេលចំនួនធ្លាក់ចុះដល់កម្រិតនេះ">កម្រិតកណត់ថាជិតអស់ <i class="fas fa-circle-info" style="font-size:11px;opacity:.6;"></i></th>
                                <th class="text-center">ស្ថានភាព</th>
                                <th class="text-end">សកម្មភាព</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            @foreach($inventories as $inv)
                                @php
                                    $isOut = $inv->quantity <= 0;
                                    $isLow = !$isOut && $inv->quantity <= $inv->reorder_level;
                                    $status = $isOut ? 'out' : ($isLow ? 'low' : 'in');
                                    $statusClass = $isOut ? 'status-out' : ($isLow ? 'status-low' : 'status-good');
                                    $statusLabel = $isOut ? 'អស់ស្តុក' : ($isLow ? 'ជិតអស់' : 'មានក្នុងស្តុក');
                                    $progressClass = $isOut ? 'out' : ($isLow ? 'low' : '');
                                    $progress = $inv->reorder_level > 0
                                        ? min(100, max(0, ($inv->quantity / max($inv->reorder_level * 2, 1)) * 100))
                                        : 100;
                                    $movement = $movementsByInventory->get($inv->id);
                                @endphp
                                <tr data-name="{{ strtolower($inv->product?->name ?? '') }}"
                                    data-category="{{ strtolower($inv->product?->category ?? '') }}"
                                    data-status="{{ $status }}"
                                    data-warehouse="{{ strtolower($inv->warehouse_location ?? '') }}">
                                    <td>
                                        <div class="product-name">{{ $inv->product?->name ?? 'មិនមានឈ្មោះ' }}</div>
                                        <div class="product-meta">{{ $inv->product?->sku ?? 'SKU មិនមាន' }}@if($inv->product?->unit) &bull; ខ្នាត: {{ $unitLabels[$inv->product->unit] ?? $inv->product->unit }} @endif</div>
                                    </td>
                                    <td>{{ $inv->product?->category ?? 'មិនមាន' }}</td>
                                    <td>{{ $inv->warehouse_location ?? 'មិនមាន' }}</td>
                                    <td class="text-center stock-cell">
                                        <span class="stock-number" @unless(auth()->user()->isAuditor()) onclick="openQuickUpdate({{ $inv->id }}, {{ $inv->quantity }})" @endunless>
                                            {{ number_format($inv->quantity) }}
                                        </span>
                                        @if($inv->product?->unit)
                                            <span class="stock-unit">{{ $unitLabels[$inv->product->unit] ?? $inv->product->unit }}</span>
                                        @endif
                                        <div class="stock-progress {{ $progressClass }}">
                                            <span style="width: {{ $progress }}%;"></span>
                                        </div>
                                    </td>
                                    @if($movementActive)
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                @if(($movement?->cut_out ?? 0) > 0)
                                                    <span class="movement-pill movement-out">
                                                        <i class="fas fa-minus"></i> {{ number_format($movement->cut_out) }}
                                                    </span>
                                                @endif
                                                @if(($movement?->added_back ?? 0) > 0)
                                                    <span class="movement-pill movement-in">
                                                        <i class="fas fa-plus"></i> {{ number_format($movement->added_back) }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if($movement?->last_movement_at)
                                                <div class="movement-time">{{ \Carbon\Carbon::parse($movement->last_movement_at)->format('h:i A') }}</div>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="text-center">{{ number_format($inv->reorder_level) }}<span class="stock-unit">{{ $inv->product?->unit ? ($unitLabels[$inv->product->unit] ?? $inv->product->unit) : '' }}</span></td>
                                    <td class="text-center">
                                        <span class="status-pill {{ $statusClass }}">
                                            <i class="fas fa-circle" style="font-size:7px;"></i> {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-row">
                                            @unless(auth()->user()->isAuditor())
                                                <button type="button" class="icon-action icon-restock"
                                                    onclick="openRestock({{ $inv->id }}, @js($inv->product?->name ?? 'ទំនិញ'), {{ $inv->quantity }})"
                                                    title="បន្ថែមចំនួនចូលស្តុក">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                                <button type="button" class="icon-action icon-reduce"
                                                    onclick="openReduce({{ $inv->id }}, @js($inv->product?->name ?? 'ទំនិញ'), {{ $inv->quantity }})"
                                                    title="កាត់ចេញពីស្តុក">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            @endunless
                                            <a href="{{ route('inventory.show', $inv) }}" class="icon-action" title="មើល">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @unless(auth()->user()->isAuditor())
                                                <a href="{{ route('inventory.edit', $inv) }}" class="icon-action" title="កែប្រែ">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="icon-action icon-danger"
                                                    onclick="deleteInventory({{ $inv->id }}, @js($inv->product?->name ?? 'ទំនិញ'))"
                                                    title="លុប">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @else
            <div class="empty-state">
               
                <h3>{{ $movementActive ? 'មិនទាន់មានចលនាស្តុកសម្រាប់ថ្ងៃនេះ' : 'មិនទាន់មានស្តុកទំនិញ' }}</h3>
                <p>{{ $movementActive ? 'ពេលបង្កើត ឬកែការបញ្ជាទិញ ប្រព័ន្ធនឹងបង្ហាញទំនិញដែលត្រូវបានកាត់ចេញនៅទីនេះ' : 'ចាប់ផ្តើមបង្កើតស្តុក ដើម្បីតាមដានចំនួនទំនិញ' }}</p>
                <!-- @unless(auth()->user()->isAuditor())
                    <a href="{{ route('inventory.create') }}" class="inventory-btn inventory-btn-primary">
                        បន្ថែមស្តុក
                    </a>
                @endunless -->
            </div>
        @endif
    </div>

    <div class="restock-overlay" id="restockOverlay" aria-hidden="true">
        <div class="restock-modal" role="dialog" aria-modal="true" aria-labelledby="restockTitle">
            <div class="restock-header">
                <h3 class="restock-title" id="restockTitle">បន្ថែមចំនួនចូលស្តុក</h3>

            </div>
            <form method="POST" id="restockForm">
                @csrf
                <div class="restock-body">
                    <div class="restock-product">
                        <div class="restock-product-name" id="restockProductName">ទំនិញ</div>
                        <div class="restock-product-meta">ចំនួនបច្ចុប្បន្ន: <span id="restockCurrentText">0</span></div>
                    </div>

                    <div class="restock-field">
                        <label for="restockQuantity">ចំនួនដែលត្រូវបន្ថែម</label>
                        <input type="number" min="1" step="1" name="quantity" id="restockQuantity" class="restock-input" autocomplete="off" required>
                        <div class="restock-error" id="restockError">សូមបញ្ចូលចំនួនធំជាង 0</div>
                    </div>

                    <div class="restock-preview">
                        <div class="restock-preview-card">
                            <div class="restock-preview-label">បច្ចុប្បន្ន</div>
                            <div class="restock-preview-value" id="restockCurrentValue">0</div>
                        </div>
                        <div class="restock-preview-plus">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="restock-preview-card">
                            <div class="restock-preview-label">បន្ទាប់ពីបន្ថែម</div>
                            <div class="restock-preview-value" id="restockNewValue">0</div>
                        </div>
                    </div>

                    <div class="restock-actions">
                        <button type="button" class="inventory-btn inventory-btn-soft" onclick="closeRestockModal()">Cancel</button>
                        <button type="submit" class="inventory-btn inventory-btn-primary">
                            <i class="fas fa-check"></i> Confirm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="restock-overlay" id="quickUpdateOverlay" aria-hidden="true">
        <div class="restock-modal" role="dialog" aria-modal="true" aria-labelledby="quickUpdateTitle">
            <div class="restock-header">
                <h3 class="restock-title" id="quickUpdateTitle">កែសម្រួលចំនួនស្តុក</h3>
            </div>
            <form method="POST" id="quickUpdateForm">
                @csrf
                <div class="restock-body">
                    <div class="restock-product">
                        <div class="restock-product-name" id="quickUpdateProductName">ទំនិញ</div>
                        <div class="restock-product-meta">ចំនួនបច្ចុប្បន្ន: <span id="quickUpdateCurrentText">0</span></div>
                    </div>

                    <div class="restock-field">
                        <label for="quickUpdateQuantity">ចំនួនស្តុកថ្មី</label>
                        <input type="number" min="0" step="1" name="quantity" id="quickUpdateQuantity" class="restock-input" autocomplete="off" required>
                        <div class="restock-error" id="quickUpdateError">សូមបញ្ចូលចំនួនត្រឹមត្រូវ (មិនអវិជ្ជមាន)</div>
                    </div>

                    <div class="restock-preview">
                        <div class="restock-preview-card">
                            <div class="restock-preview-label">បច្ចុប្បន្ន</div>
                            <div class="restock-preview-value" id="quickUpdateCurrentValue">0</div>
                        </div>
                        <div class="restock-preview-plus">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="restock-preview-card">
                            <div class="restock-preview-label">ថ្មី</div>
                            <div class="restock-preview-value" id="quickUpdateNewValue">0</div>
                        </div>
                    </div>

                    <div class="text-center" style="margin-top: 10px;">
                        <span class="movement-pill" id="quickUpdateDeltaBadge" style="display:none;"></span>
                    </div>

                    <div class="restock-field" style="margin-top: 14px;">
                        <label for="quickUpdateNote">មូលហេតុ (មិនចាំបាច់)</label>
                        <input type="text" name="note" id="quickUpdateNote" class="form-control" placeholder="ឧ. រាប់ស្តុកឡើងវិញ, ខូច, បាត់...">
                    </div>

                    <div class="restock-actions">
                        <button type="button" class="inventory-btn inventory-btn-soft" onclick="closeQuickUpdateModal()">Cancel</button>
                        <button type="submit" class="inventory-btn inventory-btn-primary">
                            <i class="fas fa-check"></i> រក្សាទុក
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="restock-overlay" id="reduceOverlay" aria-hidden="true">
        <div class="restock-modal" role="dialog" aria-modal="true" aria-labelledby="reduceTitle">
            <div class="restock-header">
                <h3 class="restock-title" id="reduceTitle">កាត់ចេញពីស្តុក</h3>
            </div>
            <form method="POST" id="reduceForm">
                @csrf
                <div class="restock-body">
                    <div class="restock-product">
                        <div class="restock-product-name" id="reduceProductName">ទំនិញ</div>
                        <div class="restock-product-meta">ចំនួនបច្ចុប្បន្ន: <span id="reduceCurrentText">0</span></div>
                    </div>

                    <div class="restock-field">
                        <label for="reduceQuantity">ចំនួនដែលត្រូវកាត់ចេញ</label>
                        <input type="number" min="1" step="1" name="quantity" id="reduceQuantity" class="restock-input" autocomplete="off" required>
                        <div class="restock-error" id="reduceQuantityError">សូមបញ្ចូលចំនួនធំជាង 0</div>
                    </div>

                    <div class="restock-preview">
                        <div class="restock-preview-card">
                            <div class="restock-preview-label">បច្ចុប្បន្ន</div>
                            <div class="restock-preview-value" id="reduceCurrentValue">0</div>
                        </div>
                        <div class="restock-preview-plus" style="color: #c2410c;">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="restock-preview-card">
                            <div class="restock-preview-label">នៅសល់</div>
                            <div class="restock-preview-value" id="reduceNewValue">0</div>
                        </div>
                    </div>

                    <div class="restock-field" style="margin-top: 14px;">
                        <label>មូលហេតុ</label>
                        <div class="reason-chips" id="reduceReasonChips">
                            <span class="reason-chip" data-reason="ខូច">ខូច (Damaged)</span>
                            <span class="reason-chip" data-reason="បាត់">បាត់ (Lost)</span>
                            <span class="reason-chip" data-reason="ការកែតម្រូវស្តុក">ការកែតម្រូវស្តុក (Recount)</span>
                            <span class="reason-chip" data-reason="">ផ្សេងៗ (Other)</span>
                        </div>
                        <input type="text" name="reason" id="reduceReason" class="form-control" placeholder="ឧ. ខូច, បាត់, ការកែតម្រូវស្តុក..." required>
                        <div class="restock-error" id="reduceReasonError">សូមបញ្ជាក់មូលហេតុ</div>
                    </div>

                    <div class="restock-actions">
                        <button type="button" class="inventory-btn inventory-btn-soft" onclick="closeReduceModal()">Cancel</button>
                        <button type="submit" class="inventory-btn inventory-btn-primary">
                            <i class="fas fa-check"></i> រក្សាទុក
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
      <div class="pager-wrap">
        {{ $inventories->links('pagination::bootstrap-5') }}
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('inventorySearch');
            const statusFilter = document.getElementById('statusFilter');
            const warehouseFilter = document.getElementById('warehouseFilter');
            const tableBody = document.getElementById('inventoryTableBody');

            if (!searchInput || !statusFilter || !warehouseFilter || !tableBody) return;

            function filterTable() {
                const search = searchInput.value.toLowerCase();
                const status = statusFilter.value;
                const warehouse = warehouseFilter.value;

                tableBody.querySelectorAll('tr').forEach(row => {
                    const rowName = row.dataset.name || '';
                    const rowCategory = row.dataset.category || '';
                    const rowStatus = row.dataset.status || '';
                    const rowWarehouse = row.dataset.warehouse || '';
                    const matchesSearch = !search || rowName.includes(search) || rowCategory.includes(search);
                    const matchesStatus = !status || rowStatus === status;
                    const matchesWarehouse = !warehouse || rowWarehouse === warehouse;

                    row.style.display = matchesSearch && matchesStatus && matchesWarehouse ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterTable);
            statusFilter.addEventListener('change', filterTable);
            warehouseFilter.addEventListener('change', filterTable);
            filterTable();
        });

        function handlePeriodChange(select) {
            const dateDivider = document.getElementById('toolbarDateDivider');
            const dateField = document.getElementById('toolbarDateField');
            const dateInput = document.getElementById('toolbarDate');

            if (select.value === '') {
                dateDivider.classList.add('show');
                dateField.classList.add('show');
                dateInput.focus();
            } else {
                dateInput.value = '';
                select.form.submit();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const toolbarDate = document.getElementById('toolbarDate');
            toolbarDate?.addEventListener('change', function () {
                if (this.value) this.form.submit();
            });
        });

        function deleteInventory(id, name) {
            if (!confirm(`តើអ្នកពិតជាចង់លុបស្តុក "${name}" មែនទេ?`)) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/inventory/' + id;
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') + '"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }

        function openQuickUpdate(id, currentQty) {
            const overlay = document.getElementById('quickUpdateOverlay');
            const form = document.getElementById('quickUpdateForm');
            const currentText = document.getElementById('quickUpdateCurrentText');
            const currentValue = document.getElementById('quickUpdateCurrentValue');
            const quantityInput = document.getElementById('quickUpdateQuantity');
            const noteInput = document.getElementById('quickUpdateNote');
            const error = document.getElementById('quickUpdateError');

            form.action = '/inventory/' + id + '/quick-update';
            form.dataset.currentQty = currentQty;
            currentText.textContent = currentQty.toLocaleString();
            currentValue.textContent = currentQty.toLocaleString();
            quantityInput.value = currentQty;
            noteInput.value = '';
            error.classList.remove('show');
            updateQuickUpdatePreview();
            overlay.classList.add('show');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            setTimeout(() => { quantityInput.focus(); quantityInput.select(); }, 50);
        }

        function closeQuickUpdateModal() {
            const overlay = document.getElementById('quickUpdateOverlay');
            overlay.classList.remove('show');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function updateQuickUpdatePreview() {
            const form = document.getElementById('quickUpdateForm');
            const quantityInput = document.getElementById('quickUpdateQuantity');
            const newValue = document.getElementById('quickUpdateNewValue');
            const badge = document.getElementById('quickUpdateDeltaBadge');

            const currentQty = parseInt(form.dataset.currentQty || '0', 10);
            const newQty = parseInt(quantityInput.value, 10);
            const safeNewQty = Number.isFinite(newQty) ? newQty : 0;
            newValue.textContent = safeNewQty.toLocaleString();

            const delta = safeNewQty - currentQty;
            if (!quantityInput.value || delta === 0) {
                badge.style.display = 'none';
            } else {
                badge.style.display = 'inline-flex';
                badge.className = 'movement-pill ' + (delta > 0 ? 'movement-in' : 'movement-out');
                badge.innerHTML = '<i class="fas fa-' + (delta > 0 ? 'plus' : 'minus') + '"></i> ' + Math.abs(delta).toLocaleString();
            }
        }

        function openReduce(id, name, currentQty) {
            const overlay = document.getElementById('reduceOverlay');
            const form = document.getElementById('reduceForm');
            const productName = document.getElementById('reduceProductName');
            const currentText = document.getElementById('reduceCurrentText');
            const currentValue = document.getElementById('reduceCurrentValue');
            const newValue = document.getElementById('reduceNewValue');
            const quantityInput = document.getElementById('reduceQuantity');
            const reasonInput = document.getElementById('reduceReason');
            const quantityError = document.getElementById('reduceQuantityError');
            const reasonError = document.getElementById('reduceReasonError');

            form.action = '/inventory/' + id + '/reduce';
            form.dataset.currentQty = currentQty;
            productName.textContent = name;
            currentText.textContent = currentQty.toLocaleString();
            currentValue.textContent = currentQty.toLocaleString();
            newValue.textContent = currentQty.toLocaleString();
            quantityInput.value = '';
            reasonInput.value = '';
            quantityError.classList.remove('show');
            reasonError.classList.remove('show');
            document.querySelectorAll('#reduceReasonChips .reason-chip').forEach(chip => chip.classList.remove('active'));
            overlay.classList.add('show');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            setTimeout(() => quantityInput.focus(), 50);
        }

        function closeReduceModal() {
            const overlay = document.getElementById('reduceOverlay');
            overlay.classList.remove('show');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        function openRestock(id, name, currentQty) {
            const overlay = document.getElementById('restockOverlay');
            const form = document.getElementById('restockForm');
            const productName = document.getElementById('restockProductName');
            const currentText = document.getElementById('restockCurrentText');
            const currentValue = document.getElementById('restockCurrentValue');
            const newValue = document.getElementById('restockNewValue');
            const quantityInput = document.getElementById('restockQuantity');
            const error = document.getElementById('restockError');

            form.action = '/inventory/' + id + '/restock';
            form.dataset.currentQty = currentQty;
            productName.textContent = name;
            currentText.textContent = currentQty.toLocaleString();
            currentValue.textContent = currentQty.toLocaleString();
            newValue.textContent = currentQty.toLocaleString();
            quantityInput.value = '';
            error.classList.remove('show');
            overlay.classList.add('show');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            setTimeout(() => quantityInput.focus(), 50);
        }

        function closeRestockModal() {
            const overlay = document.getElementById('restockOverlay');
            overlay.classList.remove('show');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const overlay = document.getElementById('restockOverlay');
            const form = document.getElementById('restockForm');
            const quantityInput = document.getElementById('restockQuantity');
            const newValue = document.getElementById('restockNewValue');
            const error = document.getElementById('restockError');

            function updateRestockPreview() {
                const currentQty = parseInt(form.dataset.currentQty || '0', 10);
                const addQty = parseInt(quantityInput.value || '0', 10);
                newValue.textContent = (currentQty + Math.max(addQty || 0, 0)).toLocaleString();
                error.classList.remove('show');
            }

            quantityInput?.addEventListener('input', updateRestockPreview);

            form?.addEventListener('submit', function (event) {
                const addQty = parseInt(quantityInput.value || '0', 10);
                if (!Number.isInteger(addQty) || addQty <= 0) {
                    event.preventDefault();
                    error.classList.add('show');
                    quantityInput.focus();
                }
            });

            overlay?.addEventListener('click', function (event) {
                if (event.target === overlay) closeRestockModal();
            });

            const quickOverlay = document.getElementById('quickUpdateOverlay');
            const quickForm = document.getElementById('quickUpdateForm');
            const quickQuantityInput = document.getElementById('quickUpdateQuantity');
            const quickError = document.getElementById('quickUpdateError');

            quickQuantityInput?.addEventListener('input', updateQuickUpdatePreview);

            quickForm?.addEventListener('submit', function (event) {
                const newQty = parseInt(quickQuantityInput.value, 10);
                if (!Number.isInteger(newQty) || newQty < 0) {
                    event.preventDefault();
                    quickError.classList.add('show');
                    quickQuantityInput.focus();
                }
            });

            quickOverlay?.addEventListener('click', function (event) {
                if (event.target === quickOverlay) closeQuickUpdateModal();
            });

            const reduceOverlay = document.getElementById('reduceOverlay');
            const reduceForm = document.getElementById('reduceForm');
            const reduceQuantityInput = document.getElementById('reduceQuantity');
            const reduceReasonInput = document.getElementById('reduceReason');
            const reduceNewValue = document.getElementById('reduceNewValue');
            const reduceQuantityError = document.getElementById('reduceQuantityError');
            const reduceReasonError = document.getElementById('reduceReasonError');

            function updateReducePreview() {
                const currentQty = parseInt(reduceForm.dataset.currentQty || '0', 10);
                const removeQty = parseInt(reduceQuantityInput.value || '0', 10);
                reduceNewValue.textContent = (currentQty - Math.max(removeQty || 0, 0)).toLocaleString();
                reduceQuantityError.classList.remove('show');
            }

            reduceQuantityInput?.addEventListener('input', updateReducePreview);

            document.querySelectorAll('#reduceReasonChips .reason-chip').forEach(chip => {
                chip.addEventListener('click', function () {
                    document.querySelectorAll('#reduceReasonChips .reason-chip').forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');
                    reduceReasonInput.value = chip.dataset.reason;
                    reduceReasonError.classList.remove('show');
                    if (!chip.dataset.reason) reduceReasonInput.focus();
                });
            });

            reduceReasonInput?.addEventListener('input', function () {
                document.querySelectorAll('#reduceReasonChips .reason-chip').forEach(chip => {
                    chip.classList.toggle('active', chip.dataset.reason !== '' && chip.dataset.reason === reduceReasonInput.value);
                });
                reduceReasonError.classList.remove('show');
            });

            reduceForm?.addEventListener('submit', function (event) {
                const removeQty = parseInt(reduceQuantityInput.value, 10);
                let hasError = false;

                if (!Number.isInteger(removeQty) || removeQty <= 0) {
                    reduceQuantityError.classList.add('show');
                    hasError = true;
                }

                if (!reduceReasonInput.value.trim()) {
                    reduceReasonError.classList.add('show');
                    hasError = true;
                }

                if (hasError) {
                    event.preventDefault();
                    if (!Number.isInteger(removeQty) || removeQty <= 0) {
                        reduceQuantityInput.focus();
                    } else {
                        reduceReasonInput.focus();
                    }
                }
            });

            reduceOverlay?.addEventListener('click', function (event) {
                if (event.target === reduceOverlay) closeReduceModal();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') return;
                if (overlay?.classList.contains('show')) closeRestockModal();
                if (quickOverlay?.classList.contains('show')) closeQuickUpdateModal();
                if (reduceOverlay?.classList.contains('show')) closeReduceModal();
            });
        });
    </script>
@endpush
