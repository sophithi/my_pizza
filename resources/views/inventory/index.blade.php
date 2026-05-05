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

        .inventory-stats {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow);
            overflow: hidden;
            padding: 16px;
            position: relative;
        }

        .stat-card::before {
            background: var(--accent);
            content: "";
            inset: 0 auto 0 0;
            position: absolute;
            width: 4px;
        }

        .stat-top {
            align-items: center;
            display: flex;
            justify-content: space-between;
        }

        .stat-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .stat-icon {
            align-items: center;
            background: var(--accent-soft);
            border-radius: 8px;
            color: var(--accent);
            display: inline-flex;
            height: 34px;
            justify-content: center;
            width: 34px;
        }

        .stat-value {
            color: var(--text);
            font-size: 28px;
            font-weight: 900;
            margin-top: 8px;
        }

        .movement-summary {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .movement-card {
            align-items: center;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow);
            display: flex;
            gap: 12px;
            padding: 14px;
        }

        .movement-card i {
            align-items: center;
            background: var(--accent-soft);
            border-radius: 8px;
            color: var(--accent);
            display: inline-flex;
            height: 36px;
            justify-content: center;
            width: 36px;
        }

        .movement-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .movement-value {
            color: var(--text);
            font-size: 22px;
            font-weight: 900;
            line-height: 1.1;
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
            padding: 14px;
        }

        .filter-form {
            align-items: center;
            display: grid;
            gap: 10px;
            grid-template-columns: minmax(280px, 1fr) 170px 170px auto;
        }

        .filter-form .form-control,
        .filter-form .form-select {
            border-color: #d9dee7;
            border-radius: 8px;
            min-height: 42px;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .quick-filter {
            align-items: center;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: #475569;
            display: inline-flex;
            font-size: 13px;
            font-weight: 800;
            gap: 7px;
            min-height: 36px;
            padding: 7px 11px;
            text-decoration: none;
        }

        .quick-filter.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .date-filter {
            display: flex;
            gap: 8px;
            margin-left: auto;
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

            .movement-summary {
                grid-template-columns: 1fr;
            }

            .filter-form {
                grid-template-columns: 1fr 1fr;
            }

            .filter-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 700px) {
            .inventory-header {
                align-items: stretch;
                flex-direction: column;
            }

            .inventory-stats,
            .filter-form {
                grid-template-columns: 1fr;
            }

            .quick-filters,
            .date-filter,
            .filter-actions {
                flex-direction: column;
                width: 100%;
            }

            .quick-filter,
            .inventory-btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $items = $inventories->getCollection();
        $movementActive = $movementDate || in_array(request('period'), ['month', 'year'], true);
    @endphp

    <div class="container-fluid py-4 inventory-page">
        <div class="inventory-header">
            <div>
                <h2 class="inventory-title">ស្តុកទំនិញ</h2>
                <p class="inventory-subtitle">តាមដានចំនួនទំនិញ កម្រិតស្តុក និងទីតាំងស្តុកសម្រាប់ការរៀបចំទំនិញ</p>
            </div>
            <a href="{{ route('inventory.create') }}" class="inventory-btn inventory-btn-primary">
                <i class="fas fa-plus"></i> បន្ថែមស្តុក
            </a>
        </div>

        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="inventory-stats">
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">មុខទំនិញសរុប</div>
                    <div class="stat-icon"><i class="fas fa-boxes"></i></div>
                </div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">មានក្នុងស្តុក</div>
                    <div class="stat-icon" style="background:#ecfdf5;color:#059669;"><i class="fas fa-check"></i></div>
                </div>
                <div class="stat-value text-success">{{ number_format($stats['in_stock']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">ជិតអស់</div>
                    <div class="stat-icon" style="background:#fffbeb;color:#d97706;"><i class="fas fa-triangle-exclamation"></i></div>
                </div>
                <div class="stat-value" style="color:#d97706;">{{ number_format($stats['low_stock']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-top">
                    <div class="stat-label">អស់ស្តុក</div>
                    <div class="stat-icon" style="background:#fef2f2;color:#dc2626;"><i class="fas fa-circle-xmark"></i></div>
                </div>
                <div class="stat-value text-danger">{{ number_format($stats['out_stock']) }}</div>
            </div>
        </div>

        @if($movementActive)
            <div class="movement-summary">
                <div class="movement-card">
                    <i class="fas fa-arrow-down"></i>
                    <div>
                        <div class="movement-label">កាត់ចេញតាមការបញ្ជាទិញ</div>
                        <div class="movement-value">{{ number_format($movementSummary['cut_out'] ?? 0) }}</div>
                    </div>
                </div>
                <div class="movement-card">
                    <i class="fas fa-arrow-up"></i>
                    <div>
                        <div class="movement-label">បានបន្ថែម/ត្រឡប់ចូល</div>
                        <div class="movement-value">{{ number_format($movementSummary['added_back'] ?? 0) }}</div>
                    </div>
                </div>
                <div class="movement-card">
                    <i class="fas fa-layer-group"></i>
                    <div>
                        <div class="movement-label">មុខទំនិញមានចលនា</div>
                        <div class="movement-value">{{ number_format($movementSummary['products'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        @endif

        <div class="filter-card">
            <form id="inventoryFilters" method="GET" action="{{ route('inventory.index') }}">
                <div class="filter-form">
                    <input type="search" name="search" id="inventorySearch" value="{{ request('search') }}" class="form-control" placeholder="ស្វែងរកទំនិញ ឬប្រភេទ..." autocomplete="off">

                    <select id="statusFilter" name="status" class="form-select">
                        <option value="">គ្រប់ស្ថានភាព</option>
                        <option value="in" {{ request('status') === 'in' ? 'selected' : '' }}>មានក្នុងស្តុក</option>
                        <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>ជិតអស់</option>
                        <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>អស់ស្តុក</option>
                    </select>

                    <select id="warehouseFilter" name="warehouse" class="form-select">
                        <option value="">គ្រប់ទីតាំង</option>
                        @foreach($items->pluck('warehouse_location')->unique()->reject(fn($x) => !$x) as $warehouse)
                            <option value="{{ strtolower($warehouse) }}" {{ request('warehouse') === strtolower($warehouse) ? 'selected' : '' }}>{{ $warehouse }}</option>
                        @endforeach
                    </select>

                    <div class="filter-actions">

                        <button type="button" class="inventory-btn inventory-btn-soft" onclick="exportInventoryCsv()">
                            <i class="fas fa-download"></i> ទាញយក
                        </button>
                    </div>
                </div>

                <div class="quick-filters">
                    <a href="{{ route('inventory.index', array_merge(request()->except('page'), ['period' => 'today'])) }}"
                        class="quick-filter {{ request('period') === 'today' ? 'active' : '' }}">
                        <i class="fas fa-calendar-day"></i> ថ្ងៃនេះ
                    </a>
                    <a href="{{ route('inventory.index', array_merge(request()->except('page'), ['period' => 'yesterday'])) }}"
                        class="quick-filter {{ request('period') === 'yesterday' ? 'active' : '' }}">
                        <i class="fas fa-calendar-minus"></i> ម្សិលមិញ
                    </a>
                    <a href="{{ route('inventory.index', ['period' => 'all']) }}" class="quick-filter">
                        <i class="fas fa-rotate-left"></i> សម្អាត
                    </a>

                    <div class="date-filter">
                        <input type="date" name="date" value="{{ request('date') }}" class="form-control">
                        <button type="submit" class="inventory-btn inventory-btn-soft">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
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
                                <th class="text-center">កម្រិតត្រូវបំពេញ</th>
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
                                        <div class="product-meta">{{ $inv->product?->sku ?? 'SKU មិនមាន' }}</div>
                                    </td>
                                    <td>{{ $inv->product?->category ?? 'មិនមាន' }}</td>
                                    <td>{{ $inv->warehouse_location ?? 'មិនមាន' }}</td>
                                    <td class="text-center stock-cell">
                                        <span class="stock-number" onclick="openQuickUpdate({{ $inv->id }}, {{ $inv->quantity }})">
                                            {{ number_format($inv->quantity) }}
                                        </span>
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
                                    <td class="text-center">{{ number_format($inv->reorder_level) }}</td>
                                    <td class="text-center">
                                        <span class="status-pill {{ $statusClass }}">
                                            <i class="fas fa-circle" style="font-size:7px;"></i> {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-row">
                                            <button type="button" class="icon-action icon-restock"
                                                onclick="openRestock({{ $inv->id }}, @js($inv->product?->name ?? 'ទំនិញ'), {{ $inv->quantity }})"
                                                title="បន្ថែមចំនួនចូលស្តុក">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <a href="{{ route('inventory.show', $inv) }}" class="icon-action" title="មើល">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('inventory.edit', $inv) }}" class="icon-action" title="កែប្រែ">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="icon-action icon-danger"
                                                onclick="deleteInventory({{ $inv->id }}, @js($inv->product?->name ?? 'ទំនិញ'))"
                                                title="លុប">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $inventories->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>{{ $movementActive ? 'មិនមានចលនាស្តុកសម្រាប់ថ្ងៃនេះ' : 'មិនទាន់មានស្តុកទំនិញ' }}</h3>
                <p>{{ $movementActive ? 'ពេលបង្កើត ឬកែការបញ្ជាទិញ ប្រព័ន្ធនឹងបង្ហាញទំនិញដែលត្រូវបានកាត់ចេញនៅទីនេះ' : 'ចាប់ផ្តើមបង្កើតស្តុក ដើម្បីតាមដានចំនួនទំនិញ' }}</p>
                <a href="{{ route('inventory.create') }}" class="inventory-btn inventory-btn-primary">
                    បន្ថែមស្តុកដំបូង
                </a>
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

        function exportInventoryCsv() {
            const tableBody = document.getElementById('inventoryTableBody');
            if (!tableBody) return;

            let csv = 'ទំនិញ,ប្រភេទ,ទីតាំង,ចំនួន,កម្រិតត្រូវបំពេញ,ស្ថានភាព\n';

            tableBody.querySelectorAll('tr').forEach(row => {
                if (row.style.display === 'none') return;
                const cells = row.querySelectorAll('td');
                const values = [
                    cells[0]?.innerText.trim() || '',
                    cells[1]?.innerText.trim() || '',
                    cells[2]?.innerText.trim() || '',
                    cells[3]?.innerText.trim() || '',
                    cells[4]?.innerText.trim() || '',
                    cells[5]?.innerText.trim() || '',
                ];
                csv += values.map(value => `"${value.replaceAll('"', '""')}"`).join(',') + '\n';
            });

            const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'stock.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

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
            const newQty = prompt(`បញ្ចូលចំនួនស្តុកថ្មី\n\nចំនួនបច្ចុប្បន្ន: ${currentQty}`, currentQty);
            if (newQty === null || newQty === '' || newQty == currentQty) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/inventory/' + id + '/quick-update';
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') + '"><input type="hidden" name="quantity" value="' + newQty + '">';
            document.body.appendChild(form);
            form.submit();
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

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && overlay?.classList.contains('show')) {
                    closeRestockModal();
                }
            });
        });
    </script>
@endpush
