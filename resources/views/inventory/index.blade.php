@extends('layouts.app')

@section('title', 'Inventory')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
<style>
    :root {
        --accent:      #e85d24;
        --accent-light:#fff0ea;
        --accent-dim:  #f07a45;
        --bg:          #f4f5f7;
        --surface:     #ffffff;
        --surface2:    #f9f9fb;
        --border:      #e8e8ee;
        --text:        #1a1b25;
        --text2:       #4a4a5a;
        --muted:       #9898a8;
        --good:        #16a34a;
        --good-bg:     #dcfce7;
        --warn:        #d97706;
        --warn-bg:     #fef3c7;
        --bad:         #dc2626;
        --bad-bg:      #fee2e2;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        min-height: 100vh;
    }

    .page-wrap {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 28px;
    }

    /* ── TOP BAR ── */
    .top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 44px;
        gap: 20px;
        flex-wrap: wrap;
        animation: slideDown 0.5s ease;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .top-bar__eyebrow {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--accent);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .top-bar__eyebrow::before {
        content: '';
        width: 20px; height: 2px;
        background: var(--accent);
        border-radius: 2px;
        display: inline-block;
    }

    .top-bar__title {
        font-family: 'Syne', sans-serif;
        font-size: 38px;
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.02em;
        line-height: 1.1;
        margin: 0;
    }

    .btn-add {
        background: linear-gradient(135deg, var(--accent) 0%, #d94a10 100%);
        color: #fff;
        border: none;
        padding: 13px 28px;
        border-radius: 10px;
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.03em;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 6px 20px rgba(232,93,36,0.32);
        flex-shrink: 0;
    }

    .btn-add:hover {
        background: linear-gradient(135deg, #d94a10 0%, #b83a0a 100%);
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(232,93,36,0.42);
    }

    .btn-add:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(232,93,36,0.3);
    }

    /* ── ALERT ── */
    .alert-success {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
        color: var(--good);
        padding: 14px 20px;
        border-radius: 10px;
        margin-bottom: 28px;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideDown 0.4s ease;
    }

    .alert-success::before { 
        content: '✓'; 
        font-weight: 900;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        background: var(--good);
        color: white;
        border-radius: 50%;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── STATS ── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 48px;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 28px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: slideUp 0.5s ease both;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 160px;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--border);
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.15s; }
    .stat-card:nth-child(3) { animation-delay: 0.2s; }
    .stat-card:nth-child(4) { animation-delay: 0.25s; }

    .stat-card:hover {
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        transform: translateY(-8px);
        border-color: var(--accent);
    }

    .stat-card:nth-child(1)::before { background: var(--accent); }
    .stat-card:nth-child(2)::before { background: var(--good); }
    .stat-card:nth-child(3)::before { background: var(--warn); }
    .stat-card:nth-child(4)::before { background: var(--bad); }

    .stat-card__icon {
        font-size: 48px;
        display: block;
        margin-bottom: 12px;
        opacity: 0.9;
    }

    .stat-card__num {
        font-family: 'Syne', sans-serif;
        font-size: 42px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 8px;
        color: var(--text);
    }

    .stat-card:nth-child(1) .stat-card__num { color: var(--accent); }
    .stat-card:nth-child(2) .stat-card__num { color: var(--good); }
    .stat-card:nth-child(3) .stat-card__num { color: var(--warn); }
    .stat-card:nth-child(4) .stat-card__num { color: var(--bad); }

    .stat-card__label {
        font-size: 13px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .stat-card__accent-bar {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: 14px 14px 0 0;
    }

    .stat-card--total .stat-card__accent-bar { background: var(--accent); }
    .stat-card--good  .stat-card__accent-bar { background: var(--good); }
    .stat-card--warn  .stat-card__accent-bar { background: var(--warn); }
    .stat-card--bad   .stat-card__accent-bar { background: var(--bad); }

    .stat-card__num {
        font-family: 'Syne', sans-serif;
        font-size: 38px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 6px;
        color: var(--text);
    }

    .stat-card--good .stat-card__num { color: var(--good); }
    .stat-card--warn .stat-card__num { color: var(--warn); }
    .stat-card--bad  .stat-card__num { color: var(--bad); }

    .stat-card__label {
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    /* ── SECTION LABEL ── */
    .section-label {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 28px;
        margin-top: 44px;
    }

    .section-label span {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--accent);
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-label span::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        background: var(--accent);
        border-radius: 50%;
        flex-shrink: 0;
    }

    .section-label::after {
        content: '';
        flex: 1;
        height: 2px;
        background: linear-gradient(to right, var(--border), transparent);
    }

    /* ── GRID ── */
    .inv-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(275px, 1fr));
        gap: 18px;
    }

    /* ── CARD ── */
    .inv-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.25s, transform 0.25s, border-color 0.25s;
        animation: fadeUp 0.4s ease both;
    }

    .inv-card:nth-child(1)  { animation-delay: .04s }
    .inv-card:nth-child(2)  { animation-delay: .08s }
    .inv-card:nth-child(3)  { animation-delay: .12s }
    .inv-card:nth-child(4)  { animation-delay: .16s }
    .inv-card:nth-child(5)  { animation-delay: .20s }
    .inv-card:nth-child(6)  { animation-delay: .24s }
    .inv-card:nth-child(7)  { animation-delay: .28s }
    .inv-card:nth-child(8)  { animation-delay: .32s }
    .inv-card:nth-child(n+9){ animation-delay: .36s }

    .inv-card:hover {
        box-shadow: 0 12px 36px rgba(0,0,0,0.1);
        transform: translateY(-4px);
        border-color: #d0d0de;
    }

    .inv-card__img {
        width: 100%;
        height: 165px;
        background: var(--surface2);
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ccc;
        font-size: 44px;
    }

    .inv-card__img img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .inv-card:hover .inv-card__img img { transform: scale(1.05); }

    .status-pill {
        position: absolute;
        top: 10px; right: 10px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    .status-pill--good { background: var(--good-bg); color: var(--good); }
    .status-pill--warn { background: var(--warn-bg); color: var(--warn); }
    .status-pill--bad  { background: var(--bad-bg);  color: var(--bad); }

    .inv-card__body {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .inv-card__cat {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--accent);
        margin-bottom: 5px;
    }

    .inv-card__name {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        line-height: 1.3;
        margin-bottom: 14px;
    }

    /* qty bar */
    .qty-bar { margin-bottom: 14px; }

    .qty-bar__labels {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: var(--text2);
        margin-bottom: 6px;
    }

    .qty-bar__labels strong { color: var(--text); }

    .qty-bar__track {
        height: 5px;
        background: var(--bg);
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid var(--border);
    }

    .qty-bar__fill {
        height: 100%;
        border-radius: 6px;
        transition: width 0.7s ease;
    }

    .qty-bar__fill--good { background: var(--good); }
    .qty-bar__fill--warn { background: var(--warn); }
    .qty-bar__fill--bad  { background: var(--bad); }

    /* location */
    .loc-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        color: var(--text2);
        background: var(--surface2);
        border: 1px solid var(--border);
        border-radius: 5px;
        padding: 3px 9px;
        margin-bottom: 14px;
        width: fit-content;
    }

    .loc-tag i { color: var(--accent); font-size: 9px; }

    /* prices */
    .inv-card__prices {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
    }

    .price-chip {
        flex: 1;
        background: var(--surface2);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 7px 10px;
        text-align: center;
    }

    .price-chip__label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.07em;
        margin-bottom: 2px;
    }

    .price-chip__val {
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
    }

    /* actions */
    .inv-card__actions {
        display: flex;
        gap: 8px;
        margin-top: auto;
        padding-top: 4px;
    }

    .inv-card__actions a {
        flex: 1;
        padding: 9px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        transition: all 0.2s;
    }

    .btn-view {
        background: var(--accent);
        color: #fff !important;
        box-shadow: 0 2px 10px rgba(232,93,36,0.22);
    }

    .btn-view:hover {
        background: var(--accent-dim);
        box-shadow: 0 4px 16px rgba(232,93,36,0.32);
    }

    .btn-edit {
        background: var(--surface2);
        color: var(--text2) !important;
        border: 1px solid var(--border);
    }

    .btn-edit:hover {
        border-color: var(--accent);
        color: var(--accent) !important;
        background: var(--accent-light);
    }

    /* ── EMPTY STATE ── */
    .empty-state {
        text-align: center;
        padding: 100px 40px;
        background: linear-gradient(135deg, var(--surface) 0%, var(--surface2) 100%);
        border: 2px dashed var(--border);
        border-radius: 16px;
        animation: slideUp 0.5s ease 0.3s both;
    }

    .empty-state i {
        font-size: 64px;
        display: block;
        margin-bottom: 20px;
        color: var(--accent);
        opacity: 0.6;
    }

    .empty-state h3 {
        font-family: 'Syne', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--muted);
        font-size: 15px;
        margin-bottom: 28px;
        line-height: 1.6;
    }

    /* ── PAGINATION ── */
    .pagination-wrap { 
        margin-top: 32px; 
        display: flex;
        justify-content: center;
    }

    .pagination-wrap * {
        font-family: 'DM Sans', sans-serif !important;
    }

    .pagination-wrap a,
    .pagination-wrap span {
        padding: 10px 14px !important;
        margin: 4px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        border: 1px solid var(--border) !important;
        color: var(--text) !important;
        background: var(--surface) !important;
        transition: all 0.2s !important;
    }

    .pagination-wrap a:hover {
        background: var(--accent) !important;
        color: white !important;
        border-color: var(--accent) !important;
    }

    .pagination-wrap .active span {
        background: var(--accent) !important;
        color: white !important;
        border-color: var(--accent) !important;
    }

    /* ── ANIMATION ── */
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 600px) {
        .top-bar { flex-direction: column; align-items: flex-start; }
        .btn-add { width: 100%; justify-content: center; }
        .inv-grid { grid-template-columns: 1fr; }
        .top-bar__title { font-size: 26px; }
        .stats-row { grid-template-columns: 1fr; }
    }

    /* ── INPUT STYLING ── */
    input[type="text"]:focus,
    select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(232,93,36,0.1);
    }
</style>
@endpush

@section('content')

<div class="page-wrap">

    {{-- HEADER --}}
    <div class="top-bar">
        <div>
            <div class="top-bar__eyebrow">Stock Management</div>
            <h1 class="top-bar__title">Inventory</h1>
        </div>
        <a href="{{ route('inventory.create') }}" class="btn-add">
            <i class="fas fa-plus"></i> Add Item
        </a>
    </div>

    {{-- SUCCESS --}}
    @if($message = Session::get('success'))
    <div class="alert-success">{{ $message }}</div>
    @endif

    @if($inventories->count() > 0)

    {{-- SECTION LABEL --}}
    <div class="section-label">
        <span>{{ $inventories->count() }} {{ Str::plural('product', $inventories->count()) }}</span>
    </div>

<div style="background: var(--surface); border-radius: 12px; padding: 32px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); border: 1px solid var(--border); animation: slideUp 0.5s ease 0.25s both;">

        <!-- Search & Advanced Filters -->
        <div style="display: flex; gap: 16px; margin-bottom: 28px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 15px; pointer-events: none;"></i>
                <input type="text" id="search" placeholder="Search products, categories..." 
                       style="width: 100%; padding: 12px 14px 12px 40px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 14px; transition: all 0.3s; background: var(--surface); font-weight: 500;">
            </div>
            
            <select id="filter" style="padding: 12px 16px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 14px; background: var(--surface); color: var(--text); font-weight: 600; cursor: pointer; transition: all 0.3s; min-width: 140px;">
                <option value="">📊 All Status</option>
                <option value="in">✓ In Stock</option>
                <option value="low">⚠️ Low Stock</option>
                <option value="out">✕ Out of Stock</option>
            </select>

            <select id="warehouseFilter" style="padding: 12px 16px; border: 1.5px solid var(--border); border-radius: 10px; font-size: 14px; background: var(--surface); color: var(--text); font-weight: 600; cursor: pointer; transition: all 0.3s; min-width: 140px;">
                <option value="">📍 ទាំងអស់    </option>
                @foreach($inventories->pluck('warehouse_location')->unique()->reject(fn($x) => !$x) as $warehouse)
                <option value="{{ strtolower($warehouse) }}">{{ $warehouse }}</option>
                @endforeach
            </select>

            <button onclick="exportToCSV()" style="padding: 12px 16px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px rgba(16,185,129,0.2);" onmouseenter="this.style.boxShadow='0 6px 16px rgba(16,185,129,0.35)'; this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='0 2px 8px rgba(16,185,129,0.2)'; this.style.transform='translateY(0)'">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </div>

        {{-- TABLE --}}
        <div style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08); border: 1px solid var(--border); animation: slideUp 0.5s ease 0.3s both;">
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; background: var(--surface);">
            <thead style="background: linear-gradient(135deg, #fafbfc 0%, #f5f6f9 100%); border-bottom: 2px solid var(--border);">
                <tr>
                    <th style="text-align: left; font-weight: 700; color: var(--text); padding: 18px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; background: linear-gradient(135deg, #fafbfc 0%, #f5f6f9 100%); cursor: pointer;" onclick="sortTable('name')">ទំនិញ <i class="fas fa-sort" style="font-size: 10px; margin-left: 6px; opacity: 0.6;"></i></th>
                    <th style="text-align: left; font-weight: 700; color: var(--text); padding: 18px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; background: linear-gradient(135deg, #fafbfc 0%, #f5f6f9 100%); cursor: pointer;" onclick="sortTable('category')"> ប្រភេទ <i class="fas fa-sort" style="font-size: 10px; margin-left: 6px; opacity: 0.6;"></i></th>
                    <th style="text-align: left; font-weight: 700; color: var(--text); padding: 18px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; background: linear-gradient(135deg, #fafbfc 0%, #f5f6f9 100%);">📍 ស្តុក</th>
                    <th style="text-align: center; font-weight: 700; color: var(--text); padding: 18px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; background: linear-gradient(135deg, #fafbfc 0%, #f5f6f9 100%); cursor: pointer;" onclick="sortTable('qty')">ចំនួន<i class="fas fa-sort" style="font-size: 10px; margin-left: 6px; opacity: 0.6;"></i></th>
                    <th style="text-align: center; font-weight: 700; color: var(--text); padding: 18px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; background: linear-gradient(135deg, #fafbfc 0%, #f5f6f9 100%); cursor: pointer;" onclick="sortTable(' <i class="fas fa-sort" style="font-size: 10px; margin-left: 6px; opacity: 0.6;"></i></th>
                    <th style="text-align: center; font-weight: 700; color: var(--text); padding: 18px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; background: linear-gradient(135deg, #fafbfc 0%, #f5f6f9 100%);">Status</th>
                    <th style="text-align: center; font-weight: 700; color: var(--text); padding: 18px 16px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; background: linear-gradient(135deg, #fafbfc 0%, #f5f6f9 100%);">Action</th>
                </tr>
            </thead>
            <tbody id="tableBody" style="border-collapse: collapse;">
                @foreach($inventories as $inv)
                @php
                    $isOut = $inv->quantity == 0;
                    $isLow = !$isOut && $inv->quantity <= $inv->reorder_level;
                    $status = $isOut ? 'Out' : ($isLow ? 'Low' : 'In');
                    $color  = $isOut ? '#dc2626' : ($isLow ? '#d97706' : '#16a34a');
                    $bgColor = $isOut ? '#fee2e2' : ($isLow ? '#fffbeb' : '#f0fdf4');
                @endphp
                <tr data-name="{{ strtolower($inv->product?->name) }}" data-status="{{ $status }}" data-warehouse="{{ strtolower($inv->warehouse_location ?? '') }}" style="border-bottom: 1px solid var(--border); transition: all 0.25s ease; background: transparent;" onmouseenter="this.style.background='#fafbfc'; this.style.transform='none'" onmouseleave="this.style.background='transparent'">
                    <td style="padding: 16px; color: var(--text); font-size: 14px; font-weight: 600;">
                        {{ $inv->product?->name ?? '—' }}
                    </td>
                    <td style="padding: 16px; color: var(--text2); font-size: 13px;">
                        {{ $inv->product?->category ?? '—' }}
                    </td>
                    <td style="padding: 16px; color: var(--text2); font-size: 13px;">
                        @if($inv->warehouse_location)
                        <span style="background: var(--accent-light); color: var(--accent); padding: 6px 12px; border-radius: 8px; display: inline-block; font-weight: 600; font-size: 12px;">{{ $inv->warehouse_location }}</span>
                        @else
                        <span style="color: var(--muted);">—</span>
                        @endif
                    </td>
                    <td style="padding: 16px; text-align: center; color: var(--text); font-weight: 700; font-size: 15px;" data-qty="{{ $inv->quantity }}">
                        <span id="qty-{{ $inv->id }}" onclick="openQuickUpdate({{ $inv->id }}, {{ $inv->quantity }})">{{ $inv->quantity }}</span>
                    </td>
                    <td style="padding: 16px; text-align: center; color: var(--muted); font-size: 13px; font-weight: 600;">{{ $inv->reorder_level }}</td>
                    <td style="padding: 16px; text-align: center;">
                        <span style="display: inline-block; padding: 6px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; color: {{ $color }}; background: {{ $bgColor }}; transition: all 0.2s;">
                            <i class="fas fa-circle" style="font-size: 8px; margin-right: 6px;"></i>{{ $status }}
                        </span>
                    </td>
                    <td style="padding: 16px; text-align: center; display: flex; gap: 6px; justify-content: center;">
                        <a href="{{ route('inventory.show', $inv) }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; background: linear-gradient(135deg, var(--accent) 0%, #d94a10 100%); color: white; text-decoration: none; font-weight: 700; border-radius: 6px; font-size: 11px; transition: all 0.25s; box-shadow: 0 2px 6px rgba(232,93,36,0.2); min-width: 32px;" title="View" onmouseenter="this.style.boxShadow='0 4px 12px rgba(232,93,36,0.3)'; this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='0 2px 6px rgba(232,93,36,0.2)'; this.style.transform='translateY(0)'">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('inventory.edit', $inv) }}" style="display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white; text-decoration: none; font-weight: 700; border-radius: 6px; font-size: 11px; transition: all 0.25s; box-shadow: 0 2px 6px rgba(59,130,246,0.2); min-width: 32px;" title="Edit" onmouseenter="this.style.boxShadow='0 4px 12px rgba(59,130,246,0.3)'; this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='0 2px 6px rgba(59,130,246,0.2)'; this.style.transform='translateY(0)'">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteInventory({{ $inv->id }}, '{{ $inv->product?->name ?? 'Item' }}')" style="display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; font-weight: 700; border-radius: 6px; font-size: 11px; cursor: pointer; transition: all 0.25s; box-shadow: 0 2px 6px rgba(239,68,68,0.2); min-width: 32px;" title="Delete" onmouseenter="this.style.boxShadow='0 4px 12px rgba(239,68,68,0.3)'; this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='0 2px 6px rgba(239,68,68,0.2)'; this.style.transform='translateY(0)'">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

</div>
    @if($inventories->hasPages())
    <div class="pagination-wrap">{{ $inventories->links() }}</div>
    @endif

    @else

    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <h3>No Inventory Yet</h3>
        <p>Start by adding your first product to track stock levels.</p>
        <a href="{{ route('inventory.create') }}" class="btn-add">
            <i class="fas fa-plus"></i> Add First Item
        </a>
    </div>

    @endif

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const filterSelect = document.getElementById('filter');
        const warehouseFilter = document.getElementById('warehouseFilter');
        const tableBody = document.getElementById('tableBody');

        if (!searchInput || !filterSelect || !tableBody) return;

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusFilter = filterSelect.value.toLowerCase();
            const warehouseFilterVal = warehouseFilter?.value.toLowerCase() || '';
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const productName = row.getAttribute('data-name') || '';
                const status = row.getAttribute('data-status')?.toLowerCase() || '';
                const warehouse = row.getAttribute('data-warehouse') || '';
                const cells = row.querySelectorAll('td');
                const category = cells[1]?.textContent.toLowerCase() || '';

                const matchesSearch = productName.includes(searchTerm) || category.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;
                const matchesWarehouse = !warehouseFilterVal || warehouse.includes(warehouseFilterVal);

                row.style.display = (matchesSearch && matchesStatus && matchesWarehouse) ? '' : 'none';
            });
        }

        searchInput.addEventListener('keyup', filterTable);
        filterSelect.addEventListener('change', filterTable);
        if (warehouseFilter) warehouseFilter.addEventListener('change', filterTable);
    });

    // Export to CSV
   function exportToCSV() {
    const table = document.getElementById('tableBody');
    if (!table) return;

    let csv = "Product,Category,Warehouse,Quantity,Min,Status\n";

    table.querySelectorAll('tr').forEach(row => {
        if (row.style.display === 'none') return;

        const cells = row.querySelectorAll('td');
        if (cells.length > 0) {
            const product = cells[0]?.textContent.trim() || '';
            const category = cells[1]?.textContent.trim() || '';
            const warehouse = cells[2]?.textContent.trim() || '';
            const qty = cells[3]?.textContent.trim() || '';
            const min = cells[4]?.textContent.trim() || '';
            const status = cells[5]?.textContent.trim() || '';

            csv += `"${product}","${category}","${warehouse}","${qty}","${min}","${status}"\n`;
        }
    });

    //  ADD UTF-8 BOM (FIX KHMER)
    const BOM = "\uFEFF";

    //  Create blob with UTF-8
    const blob = new Blob([BOM + csv], { type: 'text/csv;charset=utf-8;' });

    //  Download file
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", "inventory.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
        
  

    // Sorting
    let sortColumn = null;
    let sortOrder = 'asc';
    
    function sortTable(column) {
        const table = document.getElementById('tableBody');
        const rows = Array.from(table.querySelectorAll('tr'));
        
        if (sortColumn === column) {
            sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            sortOrder = 'asc';
            sortColumn = column;
        }
        
        rows.sort((a, b) => {
            let aVal, bVal;
            
            if (column === 'name') {
                aVal = (a.getAttribute('data-name') || '').toLowerCase();
                bVal = (b.getAttribute('data-name') || '').toLowerCase();
            } else if (column === 'category') {
                aVal = a.querySelectorAll('td')[1]?.textContent.trim().toLowerCase() || '';
                bVal = b.querySelectorAll('td')[1]?.textContent.trim().toLowerCase() || '';
            } else if (column === 'qty') {
                aVal = parseInt(a.getAttribute('data-qty') || a.querySelectorAll('td')[3]?.textContent) || 0;
                bVal = parseInt(b.getAttribute('data-qty') || b.querySelectorAll('td')[3]?.textContent) || 0;
            } else if (column === 'min') {
                aVal = parseInt(a.querySelectorAll('td')[4]?.textContent) || 0;
                bVal = parseInt(b.querySelectorAll('td')[4]?.textContent) || 0;
            }
            
            if (typeof aVal === 'number') {
                return sortOrder === 'asc' ? aVal - bVal : bVal - aVal;
            } else {
                return sortOrder === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            }
        });
        
        table.innerHTML = '';
        rows.forEach(row => table.appendChild(row));
    }

    // Delete with confirmation
    function deleteInventory(id, name) {
        if (confirm(`Are you sure you want to delete inventory for "${name}"? This action cannot be undone.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/inventory/' + id;
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') + '"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Quick Stock Update
    function openQuickUpdate(id, currentQty) {
        const newQty = prompt(`Update quantity for this item:\n\nCurrent: ${currentQty}`, currentQty);
        if (newQty !== null && newQty !== '' && newQty != currentQty) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/inventory/' + id + '/quick-update';
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') + '"><input type="hidden" name="quantity" value="' + newQty + '">';
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush

@endsection