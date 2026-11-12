@extends('layouts.app')

@section('title', ($inventory->product ? $inventory->product->name : 'ទំនិញ') . ' - ព័ត៌មានលម្អិតស្តុក')

@push('styles')
    <style>
:root,
        .inventory-show-page,
        .restock-overlay {

            --accent: #e85d24;
            --accent-dark: #d94a10;
            --accent-soft: #fff7ed;
            --border: #e5e7eb;
            --border-light: #f1f5f9;
            --danger: #dc2626;
            --muted: #64748b;
            --success: #059669;
            --surface: #ffffff;
            --text: #0f172a;
            --warning: #d97706;
            --shadow: 0 10px 30px rgba(15, 23, 42, .06);
            --radius: 12px;

}

.show-container {
margin: 0 auto;
padding: 20px 16px 40px;
}

.breadcrumb-nav {
display: flex;
align-items: center;
gap: 8px;
font-size: 13px;
font-weight: 700;
color: var(--muted);
}

.breadcrumb-nav a {

            color: var(--muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color .15s ease;

}

.breadcrumb-nav a:hover {

            color: var(--accent);

}

.breadcrumb-nav .crumb-sep {

            color: #cbd5e1;

}

.breadcrumb-nav .crumb-current {

            color: var(--text);
            font-weight: 900;

}

.show-header-card {
background: var(--surface);
border: 1px solid var(--border);
margin-bottom: 20px;
display: flex;
justify-content: space-between;
align-items: center;
flex-wrap: wrap;
}

.header-title-group {

            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;

}

.show-header-title {
color: var(--text);
font-weight: 900;
margin: 0;
}

.show-header-sub {

            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
            margin: 6px 0 0;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;

}

.header-actions {
display: flex;
align-items: center;
}

.btn-action {
align-items: center;
border: 0;
cursor: pointer;
display: inline-flex;
font-size: 13px;
font-weight: 800;
gap: 7px;
justify-content: center;
padding: 8px 16px;
text-decoration: none;
transition: all .15s ease;
white-space: nowrap;
}

.btn-action:hover {

            transform: translateY(-1px);

}

.btn-primary-action {

            background-color: #e85d24 !important;
            background: linear-gradient(135deg, #e85d24, #d94a10) !important;
            box-shadow: 0 8px 18px rgba(232, 93, 36, .22) !important;
            color: #ffffff !important;
            border: 0 !important;

}

.btn-primary-action:hover {

            background-color: #d94a10 !important;
            background: linear-gradient(135deg, #d94a10, #b83d0c) !important;
            color: #ffffff !important;

}

.btn-restock-action {

            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;

}

.btn-restock-action:hover {

            background: #d1fae5;
            color: #065f46;

}

.btn-reduce-action {

            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #c2410c;

}

.btn-reduce-action:hover {

            background: #ffedd5;
            color: #9a3412;

}

.btn-soft-action {

            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #334155;

}

.btn-soft-action:hover {

            background: #e2e8f0;
            color: #0f172a;

}

.status-pill {

            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 900;
            gap: 6px;
            padding: 5px 12px;
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

.meta-tag {

            align-items: center;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--muted);
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            padding: 4px 10px;

}

.card-custom {
background: var(--surface);
border: 1px solid var(--border);
overflow: hidden;
}

.card-custom-header {

            align-items: center;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            display: flex;
            font-size: 14px;
            font-weight: 900;
            gap: 8px;
            justify-content: space-between;
            padding: 14px 20px;

}

.card-custom-header i {

            color: var(--accent);

}

.card-custom-body {

            padding: 20px;

}

.kpi-grid {
display: grid;
grid-template-columns: repeat(4, 1fr);
margin-bottom: 24px;
}

.kpi-card {
background: var(--surface);
border: 1px solid var(--border);
display: flex;
flex-direction: column;
justify-content: center;
}

.kpi-content {

            display: flex;
            flex-direction: column;

}

.kpi-label {
font-weight: 800;
letter-spacing: .2px;
}

.kpi-value {
color: var(--text);
font-weight: 900;
margin-top: 2px;
}

.movement-filter-card {
border: 1px solid var(--border);
border-radius: var(--radius) var(--radius) 0 0;
border-bottom: 1px solid var(--border);
}

.movement-filter-row {
display: flex;
flex-wrap: wrap;
}

.m-filter-field {
align-items: center;
background: #f8fafc;
border: 1px solid var(--border);
border-radius: 8px;
display: flex;
gap: 8px;
padding: 0 12px;
}

.m-filter-field i {

            color: var(--muted);
            font-size: 13px;
            flex-shrink: 0;

}

.m-filter-field input,
        .m-filter-field select {

            background: transparent;
            border: 0;
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
            height: 100%;
            outline: none;
            width: 100%;

}

.m-filter-search {

            flex: 1 1 200px;
            min-width: 180px;

}

.m-btn-filter {

            align-items: center;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            border: 0;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            font-size: 13px;
            font-weight: 900;
            gap: 6px;
            height: 38px;
            padding: 0 16px;
            white-space: nowrap;

}

.m-btn-reset {
align-items: center;
background: #f1f5f9;
border: 1px solid #e2e8f0;
color: var(--muted);
cursor: pointer;
display: inline-flex;
font-size: 13px;
font-weight: 800;
gap: 6px;
height: 38px;
text-decoration: none;
transition: all .15s ease;
white-space: nowrap;
}

.m-btn-reset:hover {

            background: #e2e8f0;
            color: var(--text);
            transform: translateY(-1px);

}

.movement-table-card {

            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 0 0 var(--radius) var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 24px;

}

.movement-table {

            margin-bottom: 0;
            width: 100%;

}

.movement-table th {
border-bottom: 1px solid var(--border);
color: var(--muted);
font-weight: 900;
text-transform: uppercase;
white-space: nowrap;
}

.movement-table td {
border-bottom: 1px solid var(--border-light);
color: var(--text);
font-size: 13px;
}

.movement-table tr:last-child td {

            border-bottom: none;

}

.movement-table tr:hover td {

            background: #fbfdff;

}

.m-type-pill {

            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            padding: 4px 10px;
            white-space: nowrap;

}

.m-type-deduct {
 background: #fee2e2; color: #991b1b;
}

.m-type-restock {
 background: #d1fae5; color: #065f46;
}

.m-type-reduce {
 background: #fff7ed; color: #c2410c;
}

.m-type-adjust {
 background: #eff6ff; color: #1e40af;
}

.m-type-restore {
 background: #f0fdf4; color: #166534;
}

.m-type-create {
 background: #faf5ff; color: #6b21a8;
}

.delta-tag {
font-weight: 900;
white-space: nowrap;
}

.balance-flow {
align-items: center;
color: var(--muted);
display: inline-flex;
font-size: 12.5px;
font-weight: 700;
gap: 6px;
}

.balance-flow strong {

            color: var(--text);
            font-weight: 900;

}

.order-link {
font-weight: 800;
}

.user-tag {

            align-items: center;
            color: var(--muted);
            display: inline-flex;
            font-size: 12px;
            font-weight: 700;
            gap: 5px;

}

.restock-overlay {

            align-items: center;
            background: rgba(15, 23, 42, .45);
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
            border-radius: 10px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
            max-width: 440px;
            overflow: hidden;
            width: 100%;

}

.restock-header {

            align-items: center;
            background-color: #e85d24 !important;
            background: linear-gradient(135deg, #e85d24, #d94a10) !important;
            color: #ffffff !important;
            display: flex;
            justify-content: space-between;
            padding: 16px 18px;

}

.restock-title {

            font-size: 17px;
            font-weight: 900;
            margin: 0;

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
            font-size: 15px;
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
            min-height: 50px;
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
            font-size: 18px;
            font-weight: 900;
            margin-top: 2px;

}

.restock-actions {

            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 18px;

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
            padding: 5px 12px;

}

.reason-chip:hover {

            background: #f1f5f9;

}

.reason-chip.active {

            background: #ffedd5;
            border-color: #fdba74;
            color: #9a3412;

}

@media (max-width: 1024px) {
.kpi-grid {

                grid-template-columns: repeat(2, 1fr);

}
}

@media (max-width: 680px) {
.show-header-card {

                flex-direction: column;
                align-items: stretch;

}

.header-actions {

                width: 100%;

}

.btn-action {

                flex: 1 1 auto;

}

.m-filter-field {

                flex: 1 1 100%;

}
}

.inventory-show-page .show-container {
 max-width:1400px;
}

.inventory-show-page .breadcrumb-nav {
 margin-bottom:14px;
}

.inventory-show-page .show-header-card {
 border-radius:12px; padding:22px; box-shadow:0 3px 12px rgba(15,23,42,.035); gap:18px;
}

.inventory-show-page .show-header-title {
 font-size:24px; line-height:1.5;
}

.inventory-show-page .header-actions {
 gap:8px; flex-wrap:wrap;
}

.inventory-show-page .btn-action {
 min-height:38px; border-radius:8px;
}

.inventory-show-page .kpi-grid {
 gap:12px; margin:18px 0 22px;
}

.inventory-show-page .kpi-card {
 padding:18px 20px; border-radius:12px; box-shadow:0 3px 12px rgba(15,23,42,.035);
}

.inventory-show-page .kpi-label {
 font-size:12px; color:#64748b;
}

.inventory-show-page .kpi-value {
 font-size:25px; line-height:1.5; font-variant-numeric:tabular-nums;
}

.inventory-show-page .card-custom {
 border-radius:12px!important; box-shadow:0 3px 12px rgba(15,23,42,.035)!important;
}

.inventory-show-page .movement-filter-card {
 padding:14px 18px; background:#fff;
}

.inventory-show-page .movement-filter-row {
 gap:10px; align-items:center;
}

.inventory-show-page .m-filter-field {
 flex:1 1 170px; min-width:140px; height:42px; border-color:#e2e8f0;
}

.inventory-show-page .m-filter-field:focus-within {
 border-color:#f97316; box-shadow:0 0 0 3px #fff0e6; background:#fff;
}

.inventory-show-page .m-filter-date {
 display:none;
}

.inventory-show-page .m-filter-date.show {
 display:flex;
}

.inventory-show-page .m-btn-reset {
 min-height:42px; padding:0 14px; border-radius:8px;
}

.inventory-show-page .movement-table th {
 padding:13px 18px; background:#f8fafc; font-size:12px;
}

.inventory-show-page .movement-table td {
 padding:16px 18px; vertical-align:middle;
}

.inventory-show-page .movement-table tbody tr:hover {
 background:#fafbfc;
}

.inventory-show-page .delta-tag {
 display:inline-block; min-width:58px; padding:5px 9px; border-radius:7px; font-size:14px; font-variant-numeric:tabular-nums;
}

.inventory-show-page .delta-in {
 background:#ecfdf5; color:#059669;
}

.inventory-show-page .delta-out {
 background:#fff7ed; color:#c2410c;
}

.inventory-show-page .balance-flow {
 font-variant-numeric:tabular-nums; white-space:nowrap;
}

.inventory-show-page .order-link {
 color:#ea580c; text-decoration:none; padding:4px 7px; border-radius:5px; background:#fff7ed;
}

.inventory-show-page .order-link:hover {
 text-decoration:underline;
}

@media(max-width:700px) {
.inventory-show-page .show-header-card {
 padding:16px;
}

.inventory-show-page .show-header-title {
 font-size:20px;
}

.inventory-show-page .header-actions {
 width:100%;
}

.inventory-show-page .header-actions .btn-action {
 flex:1; justify-content:center;
}

.inventory-show-page .kpi-grid {
 grid-template-columns:repeat(2,minmax(0,1fr));
}

.inventory-show-page .movement-table {
 min-width:820px;
}

.inventory-show-page .m-filter-field {
 flex-basis:calc(50% - 10px); min-width:0;
}
}
    @include('inventory.partials.modal-styles')
    </style>
@endpush

@section('content')

    @if(!$inventory->product)
        <div class="container-fluid py-4 inventory-show-page">
            <div class="show-container">
                <div class="show-header-card" style="border-left: 4px solid var(--danger);">
                    <div>
                        <h2 class="show-header-title text-danger">រកមិនឃើញទំនិញ</h2>
                        <p class="show-header-sub">កំណត់ត្រាស្តុកនេះមិនមានទំនិញភ្ជាប់ជាមួយទៀតទេ។</p>
                    </div>
                    <a href="{{ route('inventory.index') }}" class="btn-action btn-soft-action">
                        <i class="fas fa-arrow-left"></i> ត្រឡប់ទៅស្តុកទំនិញ
                    </a>
                </div>
            </div>
        </div>
    @else

        @php
            $isOut = $inventory->quantity <= 0;
            $isLow = !$isOut && $inventory->quantity <= $inventory->reorder_level;
            $statusClass = $isOut ? 'status-out' : ($isLow ? 'status-low' : 'status-good');
            $statusLabel = $isOut ? 'អស់ស្តុក' : ($isLow ? 'ជិតអស់' : 'មានក្នុងស្តុក');
            $progressClass = $isOut ? 'out' : ($isLow ? 'low' : '');

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

            // Progress percentage
            $maxDisplay = max($inventory->reorder_level * 2, abs($inventory->quantity), 1);
            $pct = $isOut ? 0 : min(100, max(0, round(($inventory->quantity / $maxDisplay) * 100)));

            // Estimated inventory value
            $unitCostUsd = $inventory->cost_per_unit ?? $inventory->product->price_usd ?? 0;
            $stockValueUsd = max(0, $inventory->quantity) * $unitCostUsd;
            $stockValueKhr = $stockValueUsd * 4000;

            $movementTypeLabels = [
                'stock_create' => ['បង្កើតស្តុក', 'm-type-create'],
                'manual_adjust' => ['កែសម្រួល', 'm-type-adjust'],
                'quick_adjust' => ['កែស្តុក', 'm-type-adjust'],
                'stock_restock' => ['បន្ថែមស្តុក', 'm-type-restock'],
                'stock_reduce' => ['កាត់ស្តុក', 'm-type-reduce'],
                'order_adjust' => ['កែចំនួនការបញ្ជាទិញ', 'm-type-adjust'],
                'order_deduct' => ['ការបញ្ជាទិញ', 'm-type-deduct'],
                'order_restore' => ['បញ្ចូលវិញ', 'm-type-restore'],
            ];

            $fromDateVal = request('from_date', request('start_date', request('from')));
            $toDateVal = request('to_date', request('end_date', request('to')));
            $hasDateRange = !empty($fromDateVal) || !empty($toDateVal);
            $filterPeriod = request('period', $hasDateRange ? 'custom' : '');
            $isCustomDate = $filterPeriod === 'custom' || $hasDateRange;
        @endphp

        <div class="container-fluid py-4 inventory-show-page">
            <div class="show-container">

                {{-- ── Breadcrumb ── --}}
                <div class="breadcrumb-nav">
                    <a href="{{ route('inventory.index') }}">ស្តុកទំនិញ</a>
                    <span class="crumb-sep">/</span>
                    <span class="crumb-current">{{ $inventory->product->name }}</span>
                </div>

                @if($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px; margin-bottom: 20px;">
                        <i class="fas fa-check-circle me-2"></i> {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px; margin-bottom: 20px;">
                        <i class="fas fa-circle-exclamation me-2"></i> {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- ── Main Header Card ── --}}
                <div class="show-header-card">
                    <div>
                        <div class="header-title-group">
                            <h1 class="show-header-title">{{ $inventory->product->name }}</h1>
                            <span class="status-pill {{ $statusClass }}">
                                <i class="fas fa-circle" style="font-size:7px;"></i> {{ $statusLabel }}
                            </span>
                        </div>
                        <div class="show-header-sub">
                            @if($inventory->product->sku)
                                <span class="meta-tag">SKU: {{ $inventory->product->sku }}</span>
                            @endif
                            @if($inventory->product->category)
                                <span class="meta-tag">{{ $inventory->product->category }}</span>
                            @endif
                            @if($inventory->warehouse_location)
                                <span class="meta-tag">{{ $inventory->warehouse_location }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="header-actions">
                        @unless(auth()->user()->isAuditor())
                            <button type="button" class="btn-action btn-restock-action"
                                onclick="openRestock({{ $inventory->id }}, @js($inventory->product->name), {{ $inventory->quantity }})">
                                <i class="fas fa-plus"></i> បន្ថែមស្តុក
                            </button>
                            <button type="button" class="btn-action btn-reduce-action"
                                onclick="openReduce({{ $inventory->id }}, @js($inventory->product->name), {{ $inventory->quantity }})">
                                <i class="fas fa-minus"></i> កាត់ស្តុក
                            </button>
                            <a href="{{ route('inventory.edit', $inventory) }}" class="btn-action btn-soft-action">
                                <i class="fas fa-edit"></i> កែប្រែ
                            </a>
                        @endunless
                        <a href="{{ route('inventory.index') }}" class="btn-action btn-soft-action">
                            <i class="fas fa-arrow-left"></i> ត្រឡប់
                        </a>
                    </div>
                </div>

                {{-- Compact stock summary --}}
                <div class="kpi-grid">
                    <div class="kpi-card"><div class="kpi-content">
                        <span class="kpi-label">ចូលសរុប</span>
                        <span class="kpi-value text-success">{{ number_format($totalIn) }}</span>
                    </div></div>
                    <div class="kpi-card"><div class="kpi-content">
                        <span class="kpi-label">ចេញសរុប</span>
                        <span class="kpi-value text-danger">{{ number_format($totalOut) }}</span>
                    </div></div>
                    <div class="kpi-card"><div class="kpi-content">
                        <span class="kpi-label">លក់ក្នុង Order</span>
                        <span class="kpi-value">{{ number_format($saleQty) }}</span>
                    </div></div>
                    <div class="kpi-card"><div class="kpi-content">
                        <span class="kpi-label">ថែមក្នុង Order</span>
                        <span class="kpi-value text-primary">{{ number_format($freeQty) }}</span>
                    </div></div>
                </div>
                {{-- ── Movements Filter & Table Section ── --}}
                <div class="card-custom" style="box-shadow: var(--shadow); border-radius: var(--radius); overflow: hidden;">

                    <div class="card-custom-header" style="background:#fff; padding: 16px 20px; border-bottom: 1px solid var(--border);">
                        <div>
                            <span style="font-size:15px;font-weight:900;">ប្រវត្តិចលនាស្តុក</span>
                            <span class="meta-tag ms-2" style="background:#f8fafc;">{{ number_format($movements->total()) }} កំណត់ត្រា</span>
                        </div>
                    </div>

                    @php
                        $hasFilter = !empty(request('item_type')) || !empty(request('type')) || !empty(request('period')) || !empty(request('search')) || $hasDateRange;
                    @endphp

                    {{-- Filter Form --}}
                    <div class="movement-filter-card">
                        <form method="GET" action="{{ route('inventory.show', $inventory) }}" id="movementFilterForm">
                            <div class="movement-filter-row">

                                <div class="m-filter-field">
                                    <select name="item_type" aria-label="លក់ ឬថែម" onchange="this.form.submit()">
                                        <option value="">លក់ និងថែមទាំងអស់</option>
                                        <option value="sale" @selected(request('item_type') === 'sale')>លក់</option>
                                        <option value="free" @selected(request('item_type') === 'free')>ថែម</option>
                                        <option value="unclassified" @selected(request('item_type') === 'unclassified')>មិនបានបែងចែក</option>
                                    </select>
                                </div>
                                <div class="m-filter-field">
                                    <select name="type" aria-label="ប្រភេទចលនា" onchange="this.form.submit()">
                                        <option value="">គ្រប់ប្រភេទ</option>
                                        <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>ចូល (+)</option>
                                        <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>ចេញ (-)</option>
                                        <option value="stock_create" {{ request('type') === 'stock_create' ? 'selected' : '' }}>បង្កើតស្តុក</option>
                                        <option value="quick_adjust" {{ request('type') === 'quick_adjust' ? 'selected' : '' }}>កែស្តុក</option>
                                        <option value="stock_restock" {{ request('type') === 'stock_restock' ? 'selected' : '' }}>បន្ថែមស្តុក</option>
                                        <option value="stock_reduce" {{ request('type') === 'stock_reduce' ? 'selected' : '' }}>កាត់ស្តុក</option>
                                        <option value="manual_adjust" {{ request('type') === 'manual_adjust' ? 'selected' : '' }}>កែសម្រួល</option>
                                        <option value="order_deduct" {{ request('type') === 'order_deduct' ? 'selected' : '' }}>ការបញ្ជាទិញ</option>
                                        <option value="order_restore" {{ request('type') === 'order_restore' ? 'selected' : '' }}>បញ្ចូលវិញ</option>
                                    </select>
                                </div>

                                <div class="m-filter-field">
                                    <select id="movementPeriodSelect" name="period" aria-label="កាលបរិច្ឆេទ" onchange="handleMovementPeriodChange(this)">
                                        <option value="" {{ empty(request('period')) && !$hasDateRange ? 'selected' : '' }}>គ្រប់ពេល</option>
                                        <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>ថ្ងៃនេះ</option>
                                        <option value="yesterday" {{ request('period') === 'yesterday' ? 'selected' : '' }}>ម្សិលមិញ</option>
                                        <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>ខែនេះ</option>
                                        <option value="custom" {{ $isCustomDate ? 'selected' : '' }}>ជ្រើសកាលបរិច្ឆេទ</option>
                                    </select>
                                </div>

                                <div class="m-filter-field m-filter-date {{ $isCustomDate ? 'show' : '' }}" style="gap: 5px;">
                                    <span style="font-size:11px;font-weight:800;color:var(--muted);white-space:nowrap;">ពី:</span>
                                    <input type="date" name="from_date" id="mFromDate" value="{{ $fromDateVal }}" onchange="this.form.submit()">
                                </div>

                                <div class="m-filter-field m-filter-date {{ $isCustomDate ? 'show' : '' }}" style="gap: 5px;">
                                    <span style="font-size:11px;font-weight:800;color:var(--muted);white-space:nowrap;">ដល់:</span>
                                    <input type="date" name="to_date" id="mToDate" value="{{ $toDateVal }}" onchange="this.form.submit()">
                                </div>

                                <a href="{{ route('inventory.show', $inventory) }}" class="m-btn-reset" title="Refresh">
                                    <i class="fas fa-rotate-right"></i> Refresh
                                </a>

                            </div>
                        </form>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table movement-table">
                            <thead>
                                <tr>
                                    <th style="width: 140px;">កាលបរិច្ឆេទ</th>
                                    <th style="width: 120px;">ប្រភេទ</th>
                                    <th class="text-center" style="width: 110px;">ចំនួន</th>
                                    <th style="width: 140px;">មុន ➔ ក្រោយ</th>
                                    <th>មូលហេតុ</th>
                                    <th style="width: 140px;">អ្នកកត់ត្រា</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $m)
                                    @php
                                        $isIn = $m->quantity_change > 0;
                                        [$typeText, $typeClass] = $movementTypeLabels[$m->type] ?? [$m->type, 'm-type-adjust'];
                                    @endphp
                                    <tr>
                                        <td style="white-space:nowrap;">
                                            <div style="font-weight:800;color:var(--text);">{{ $m->created_at ? $m->created_at->format('d/m/Y') : '—' }}</div>
                                            <div style="font-size:11.5px;color:var(--muted);font-weight:700;">{{ $m->created_at ? $m->created_at->format('h:i A') : '' }}</div>
                                        </td>
                                        <td>
                                            <span class="m-type-pill {{ $typeClass }}">
                                                {{ $typeText }} {{ $m->item_type === "sale" ? "(លក់)" : ($m->item_type === "free" ? "(ថែម)" : "") }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="delta-tag {{ $isIn ? 'delta-in' : 'delta-out' }}">
                                                {{ $isIn ? '+' : '' }}{{ number_format($m->quantity_change) }}
                                            </span>
                                            <span style="font-size:11px;color:var(--muted);font-weight:700;">{{ $unit }}</span>
                                        </td>
                                        <td>
                                            <div class="balance-flow">
                                                <span>{{ number_format($m->quantity_before) }}</span>
                                                <i class="fas fa-arrow-right" style="font-size:10px;opacity:.5;"></i>
                                                <strong>{{ number_format($m->quantity_after) }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $reasonText = match ($m->type) {
                                                    'order_deduct' => 'កាត់ស្តុកតាមការបញ្ជាទិញ',
                                                    'order_restore' => 'បញ្ចូលស្តុកវិញពីការបញ្ជាទិញ',
                                                    'order_adjust' => 'កែចំនួនទំនិញក្នុងការបញ្ជាទិញ',
                                                    'stock_create' => 'បង្កើតស្តុកដំបូង',
                                                    'stock_restock' => 'បន្ថែមទំនិញចូលស្តុក',
                                                    'stock_reduce' => $m->note ?: 'កាត់ទំនិញចេញពីស្តុក',
                                                    'manual_adjust' => $m->note ?: 'កែតម្រូវស្តុកដោយដៃ',
                                                    'quick_adjust' => $m->note ?: 'កែចំនួនស្តុក',
                                                    default => $m->note ?: 'មិនមានមូលហេតុ',
                                                };
                                            @endphp
                                            <div style="font-weight:600;color:var(--text);margin-bottom:5px;">{{ $reasonText }}</div>
                                            @if(in_array($m->item_type, ['sale', 'free']))
                                                <div class="small mb-1 {{ $m->item_type === 'free' ? 'text-primary' : 'text-muted' }}">
                                                    {{ $m->item_type === 'free' ? 'ទំនិញថែម' : 'ទំនិញលក់' }}៖ {{ number_format(abs($m->quantity_change)) }} {{ $unit }}
                                                </div>
                                            @elseif(in_array($m->type, ['order_deduct', 'order_restore', 'order_adjust']))
                                                <div class="small text-muted mb-1">ប្រវត្តិចាស់មិនបានបែងចែកលក់/ថែម</div>
                                            @endif
                                            @if($m->order_id)
                                                <div>
                                                    <a href="{{ route('orders.show', $m->order_id) }}" class="order-link">
                                                        #{{ $m->order_id }}
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($m->user)
                                                <span class="user-tag">{{ $m->user->name }}</span>
                                            @else
                                                <span style="color:#cbd5e1;">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="padding: 48px 20px; text-align:center;">
                                            <div style="font-size:14px;font-weight:800;color:var(--muted);margin-bottom:8px;">
                                                {{ $hasFilter ? 'មិនមានទិន្នន័យចលនាស្តុក' : 'មិនទាន់មានកំណត់ត្រាចលនាស្តុកនៅឡើយទេ' }}
                                            </div>
                                            @if($hasFilter)
                                                <a href="{{ route('inventory.show', $inventory) }}" class="btn-action btn-soft-action" style="padding:5px 14px;font-size:12px;display:inline-flex;">
                                                    Refresh
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($movements->hasPages())
                        <div style="padding: 16px 20px; border-top: 1px solid var(--border); background: #f8fafc;">
                            {{ $movements->links('pagination::bootstrap-5') }}
                        </div>
                    @endif

                </div>

            </div>
        </div>

        {{-- ── Restock Modal ── --}}
        <div class="restock-overlay" id="restockOverlay" aria-hidden="true">
            <div class="restock-modal" role="dialog" aria-modal="true" aria-labelledby="restockTitle">
                <div class="restock-header">
                    <h3 class="restock-title" id="restockTitle">បន្ថែមស្តុក</h3>
                </div>
                <form method="POST" id="restockForm">
                    @csrf
                    <div class="restock-body">
                        <div class="restock-product">
                            <div class="restock-product-name" id="restockProductName">{{ $inventory->product->name }}</div>
                            <div class="restock-product-meta">បច្ចុប្បន្ន: <span id="restockCurrentText">{{ number_format($inventory->quantity) }}</span> {{ $unit }}</div>
                        </div>

                        <div class="restock-field">
                            <label for="restockQuantity">ចំនួនបន្ថែម</label>
                            <input type="number" min="1" step="1" name="quantity" id="restockQuantity" class="restock-input" autocomplete="off" required>
                            <div class="restock-error" id="restockError">សូមបញ្ចូលចំនួនធំជាង 0</div>
                        </div>

                        <div class="restock-preview">
                            <div class="restock-preview-card">
                                <div class="restock-preview-label">បច្ចុប្បន្ន</div>
                                <div class="restock-preview-value" id="restockCurrentValue">{{ number_format($inventory->quantity) }}</div>
                            </div>
                            <div style="color:var(--accent);font-weight:900;text-align:center;">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="restock-preview-card">
                                <div class="restock-preview-label">ក្រោយបន្ថែម</div>
                                <div class="restock-preview-value" id="restockNewValue">{{ number_format($inventory->quantity) }}</div>
                            </div>
                        </div>

                        <div class="restock-actions">
                            <button type="button" class="btn-action btn-soft-action" onclick="closeRestockModal()">បោះបង់</button>
                            <button type="submit" class="btn-action btn-primary-action">
                                <i class="fas fa-check"></i> រក្សាទុក
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Reduce Modal ── --}}
        <div class="restock-overlay" id="reduceOverlay" aria-hidden="true">
            <div class="restock-modal" role="dialog" aria-modal="true" aria-labelledby="reduceTitle">
                <div class="restock-header">
                    <h3 class="restock-title" id="reduceTitle">កាត់ស្តុក</h3>
                </div>
                <form method="POST" id="reduceForm">
                    @csrf
                    <div class="restock-body">
                        <div class="restock-product">
                            <div class="restock-product-name" id="reduceProductName">{{ $inventory->product->name }}</div>
                            <div class="restock-product-meta">បច្ចុប្បន្ន: <span id="reduceCurrentText">{{ number_format($inventory->quantity) }}</span> {{ $unit }}</div>
                        </div>

                        <div class="restock-field">
                            <label for="reduceQuantity">ចំនួនកាត់</label>
                            <input type="number" min="1" step="1" name="quantity" id="reduceQuantity" class="restock-input" autocomplete="off" required>
                            <div class="restock-error" id="reduceQuantityError">សូមបញ្ចូលចំនួនធំជាង 0</div>
                        </div>

                        <div class="restock-preview">
                            <div class="restock-preview-card">
                                <div class="restock-preview-label">បច្ចុប្បន្ន</div>
                                <div class="restock-preview-value" id="reduceCurrentValue">{{ number_format($inventory->quantity) }}</div>
                            </div>
                            <div style="color:#c2410c;font-weight:900;text-align:center;">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="restock-preview-card">
                                <div class="restock-preview-label">នៅសល់</div>
                                <div class="restock-preview-value" id="reduceNewValue">{{ number_format($inventory->quantity) }}</div>
                            </div>
                        </div>

                        <div class="restock-field" style="margin-top: 14px;">
                            <label>មូលហេតុ</label>
                            <div class="reason-chips" id="reduceReasonChips">
                                <span class="reason-chip" data-reason="ខូច">ខូច</span>
                                <span class="reason-chip" data-reason="បាត់">បាត់</span>
                                <span class="reason-chip" data-reason="កែតម្រូវ">កែតម្រូវ</span>
                                <span class="reason-chip" data-reason="">ផ្សេងៗ</span>
                            </div>
                            <input type="text" name="reason" id="reduceReason" class="form-control" placeholder="មូលហេតុ..." required>
                            <div class="restock-error" id="reduceReasonError">សូមបញ្ជាក់មូលហេតុ</div>
                        </div>

                        <div class="restock-actions">
                            <button type="button" class="btn-action btn-soft-action" onclick="closeReduceModal()">បោះបង់</button>
                            <button type="submit" class="btn-action btn-primary-action">
                                <i class="fas fa-minus-circle"></i> កាត់ស្តុក
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Quick Update Modal ── --}}
        <div class="restock-overlay" id="quickUpdateOverlay" aria-hidden="true">
            <div class="restock-modal" role="dialog" aria-modal="true" aria-labelledby="quickUpdateTitle">
                <div class="restock-header">
                    <h3 class="restock-title" id="quickUpdateTitle">កែស្តុក</h3>
                </div>
                <form method="POST" id="quickUpdateForm">
                    @csrf
                    <div class="restock-body">
                        <div class="restock-product">
                            <div class="restock-product-name" id="quickUpdateProductName">{{ $inventory->product->name }}</div>
                            <div class="restock-product-meta">បច្ចុប្បន្ន: <span id="quickUpdateCurrentText">{{ number_format($inventory->quantity) }}</span> {{ $unit }}</div>
                        </div>

                        <div class="restock-field">
                            <label for="quickUpdateQuantity">ចំនួនថ្មី</label>
                            <input type="number" min="0" step="1" name="quantity" id="quickUpdateQuantity" class="restock-input" autocomplete="off" required>
                            <div class="restock-error" id="quickUpdateError">សូមបញ្ចូលចំនួនត្រឹមត្រូវ</div>
                        </div>

                        <div class="restock-preview">
                            <div class="restock-preview-card">
                                <div class="restock-preview-label">បច្ចុប្បន្ន</div>
                                <div class="restock-preview-value" id="quickUpdateCurrentValue">{{ number_format($inventory->quantity) }}</div>
                            </div>
                            <div style="color:var(--accent);font-weight:900;text-align:center;">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="restock-preview-card">
                                <div class="restock-preview-label">ថ្មី</div>
                                <div class="restock-preview-value" id="quickUpdateNewValue">{{ number_format($inventory->quantity) }}</div>
                            </div>
                        </div>

                        <div class="restock-field" style="margin-top: 14px;">
                            <label for="quickUpdateNote">មូលហេតុ</label>
                            <input type="text" name="note" id="quickUpdateNote" class="form-control" placeholder="មូលហេតុ (បើមាន)...">
                        </div>

                        <div class="restock-actions">
                            <button type="button" class="btn-action btn-soft-action" onclick="closeQuickUpdateModal()">បោះបង់</button>
                            <button type="submit" class="btn-action btn-primary-action">
                                <i class="fas fa-check"></i> រក្សាទុក
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @endif

@endsection

@push('scripts')
    <script>
        const inventoryBaseUrl = @js(url('inventory'));

        function handleMovementPeriodChange(select) {
            const dateElements = document.querySelectorAll('.m-filter-date');
            const fromInput = document.getElementById('mFromDate');
            const toInput = document.getElementById('mToDate');

            if (select.value === 'custom') {
                dateElements.forEach(el => el.classList.add('show'));
                if (fromInput) fromInput.focus();
            } else {
                dateElements.forEach(el => el.classList.remove('show'));
                if (fromInput) fromInput.value = '';
                if (toInput) toInput.value = '';
                select.form.submit();
            }
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

            form.action = inventoryBaseUrl + '/' + id + '/restock';
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

            form.action = inventoryBaseUrl + '/' + id + '/reduce';
            form.dataset.currentQty = currentQty;
            productName.textContent = name;
            currentText.textContent = currentQty.toLocaleString();
            currentValue.textContent = currentQty.toLocaleString();
            newValue.textContent = currentQty.toLocaleString();
            quantityInput.value = '';
            quantityInput.max = currentQty;
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

        function openQuickUpdate(id, currentQty) {
            const overlay = document.getElementById('quickUpdateOverlay');
            const form = document.getElementById('quickUpdateForm');
            const currentText = document.getElementById('quickUpdateCurrentText');
            const currentValue = document.getElementById('quickUpdateCurrentValue');
            const quantityInput = document.getElementById('quickUpdateQuantity');
            const noteInput = document.getElementById('quickUpdateNote');
            const error = document.getElementById('quickUpdateError');

            form.action = inventoryBaseUrl + '/' + id + '/quick-update';
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

            const newQty = parseInt(quantityInput.value, 10);
            const safeNewQty = Number.isFinite(newQty) ? newQty : 0;
            newValue.textContent = safeNewQty.toLocaleString();
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Restock Modal handlers
            const restockOverlay = document.getElementById('restockOverlay');
            const restockForm = document.getElementById('restockForm');
            const restockQuantityInput = document.getElementById('restockQuantity');
            const restockNewValue = document.getElementById('restockNewValue');
            const restockError = document.getElementById('restockError');

            function updateRestockPreview() {
                const currentQty = parseInt(restockForm.dataset.currentQty || '0', 10);
                const addQty = parseInt(restockQuantityInput.value || '0', 10);
                restockNewValue.textContent = (currentQty + Math.max(addQty || 0, 0)).toLocaleString();
                restockError.classList.remove('show');
            }

            restockQuantityInput?.addEventListener('input', updateRestockPreview);

            restockForm?.addEventListener('submit', function (event) {
                const addQty = parseInt(restockQuantityInput.value || '0', 10);
                if (!Number.isInteger(addQty) || addQty <= 0) {
                    event.preventDefault();
                    restockError.classList.add('show');
                    restockQuantityInput.focus();
                }
            });

            restockOverlay?.addEventListener('click', function (event) {
                if (event.target === restockOverlay) closeRestockModal();
            });

            // Reduce Modal handlers
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
                const remaining = currentQty - (Number.isFinite(removeQty) ? removeQty : 0);
                reduceNewValue.textContent = Math.max(0, remaining).toLocaleString();
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
                const currentQty = parseInt(reduceForm.dataset.currentQty || '0', 10);
                const removeQty = parseInt(reduceQuantityInput.value, 10);
                let hasError = false;

                if (!Number.isInteger(removeQty) || removeQty <= 0) {
                    reduceQuantityError.textContent = 'សូមបញ្ចូលចំនួនធំជាង 0';
                    reduceQuantityError.classList.add('show');
                    hasError = true;
                } else if (removeQty > currentQty) {
                    reduceQuantityError.textContent = 'ចំនួនដែលត្រូវកាត់ចេញមិនអាចលើសពីចំនួនក្នុងស្តុក (' + currentQty.toLocaleString() + ') ឡើយ';
                    reduceQuantityError.classList.add('show');
                    hasError = true;
                }

                if (!reduceReasonInput.value.trim()) {
                    reduceReasonError.classList.add('show');
                    hasError = true;
                }

                if (hasError) {
                    event.preventDefault();
                    if (!Number.isInteger(removeQty) || removeQty <= 0 || removeQty > currentQty) {
                        reduceQuantityInput.focus();
                    } else {
                        reduceReasonInput.focus();
                    }
                }
            });

            reduceOverlay?.addEventListener('click', function (event) {
                if (event.target === reduceOverlay) closeReduceModal();
            });

            // Quick Update Modal handlers
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

            // Global Escape key handler
            document.addEventListener('keydown', function (event) {
                if (event.key !== 'Escape') return;
                if (restockOverlay?.classList.contains('show')) closeRestockModal();
                if (reduceOverlay?.classList.contains('show')) closeReduceModal();
                if (quickOverlay?.classList.contains('show')) closeQuickUpdateModal();
            });
        });
    </script>
@endpush
