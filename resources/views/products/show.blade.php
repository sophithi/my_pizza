@extends('layouts.app')
@section('title', $product->name)
@push('styles')
<style>
    .product-detail { color:#0f172a; padding:6px 0 24px; }
    .pd-top { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom:22px; }
    .pd-back { color:#64748b; text-decoration:none; font-size:13px; display:inline-flex; gap:9px; align-items:center; }
    .pd-actions { display:flex; gap:8px; }
    .pd-btn { display:inline-flex; justify-content:center; align-items:center; gap:8px; padding:10px 16px; border:1px solid #e2e8f0; border-radius:9px; background:white; color:#475569; font-size:12px; text-decoration:none; font-weight:700; }
    .pd-btn.primary { background:#e85d24; border-color:#e85d24; color:white; }
    .pd-btn.danger { color:#dc2626; border-color:#fee2e2; }
    .pd-btn:hover { filter:brightness(.97); color:#e85d24; }
    .pd-btn.primary:hover { color:white; }
    .pd-card { background:#fff; border:1px solid #e8edf3; border-radius:16px; box-shadow:0 4px 20px #0f172a04; overflow:hidden; }
    .pd-hero { display:flex; align-items:center; gap:22px; padding:30px; border-top:3px solid #e85d24; margin-bottom:20px; }
    .pd-avatar { width:82px; height:82px; flex-shrink:0; border-radius:20px; display:grid; place-items:center; background:#fff2e9; color:#e85d24; font-size:30px; }
    .pd-hero h2 { font-size:25px; font-weight:800; line-height:1.6; margin:0 0 8px; }
    .pd-meta { display:flex; align-items:center; flex-wrap:wrap; gap:10px; color:#94a3b8; font-size:12px; }
    .pd-code { background:#f1f5f9; border-radius:5px; padding:4px 8px; color:#64748b; }
    .pd-price { margin-left:auto; text-align:right; white-space:nowrap; padding-left:24px; border-left:1px solid #edf1f5; }
    .pd-label { display:block; color:#94a3b8; font-size:12px; margin-bottom:9px; }
    .pd-price strong { font-size:29px; color:#e85d24; font-variant-numeric:tabular-nums; }
    .pd-price small { display:block; color:#64748b; font-size:13px; margin-top:6px; }
    .pd-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; margin-bottom:22px; }
    .pd-stat { padding:22px; display:flex; gap:15px; align-items:center; }
    .pd-stat i { display:grid; place-items:center; width:42px; height:42px; border-radius:12px; background:#fff2e9; color:#e85d24; }
    .pd-stat:nth-child(2) i { background:#ecfdf5; color:#059669; }
    .pd-stat:nth-child(3) i { background:#eff6ff; color:#3b82f6; }
    .pd-stat .pd-label { margin-bottom:5px; }
    .pd-stat strong { font-size:25px; font-variant-numeric:tabular-nums; }
    .pd-grid { display:grid; grid-template-columns:minmax(0,1.5fr) minmax(0,1fr); gap:20px; }
    .pd-section { padding:24px; }
    .pd-section h3 { font-size:16px; font-weight:800; margin:0 0 22px; display:flex; gap:10px; align-items:center; }
    .pd-section h3 i { color:#e85d24; font-size:14px; }
    .pd-fields { display:grid; grid-template-columns:1fr 1fr; gap:25px 20px; margin:0; }
    .pd-fields dt { font-size:12px; color:#94a3b8; font-weight:400; margin-bottom:8px; }
    .pd-fields dd { font-size:14px; margin:0; line-height:1.8; overflow-wrap:anywhere; }
    .pd-description { border-top:1px solid #edf1f5; margin-top:24px; padding-top:22px; font-size:13px; line-height:1.9; white-space:pre-line; }
    .pd-setting { display:flex; justify-content:space-between; align-items:center; padding:17px 0; border-top:1px solid #edf1f5; gap:14px; font-size:13px; }
    .pd-setting:first-of-type { border-top:0; padding-top:0; }
    .pd-badge { display:inline-flex; gap:6px; align-items:center; padding:5px 10px; border-radius:6px; background:#f1f5f9; color:#64748b; font-size:11px; white-space:nowrap; }
    .pd-badge.good { color:#047857; background:#ecfdf5; }
    .pd-badge.low { color:#b45309; background:#fffbeb; }
    .pd-badge.out { color:#dc2626; background:#fef2f2; }
    .pd-stock-link { width:100%; margin-top:24px; }
    .pd-note { color:#94a3b8; font-size:11px; margin:10px 2px 0; }
    @media(max-width:900px) { .pd-grid { grid-template-columns:1fr; } .pd-hero { flex-wrap:wrap; padding:22px; } .pd-price { margin-left:0; padding:18px 0 0; border-left:0; border-top:1px solid #edf1f5; width:100%; text-align:left; } }
    @media(max-width:600px) { .pd-stats { grid-template-columns:1fr; gap:10px; } .pd-stat { padding:16px; } .pd-hero h2 { font-size:20px; } .pd-avatar { width:55px; height:55px; font-size:22px; border-radius:14px; } .pd-section { padding:20px; } }
</style>
@endpush
@section('content')
@php
    $inventory = $product->inventory;
    $stockStatus = $inventory?->status;
    $statusClass = ['in_stock'=>'good','low_stock'=>'low','out_of_stock'=>'out'][$stockStatus] ?? '';
    $statusLabel = ['in_stock'=>'មានក្នុងស្តុក','low_stock'=>'ជិតអស់','out_of_stock'=>'អស់ស្តុក'][$stockStatus] ?? 'មិនទាន់មានស្តុក';
@endphp
<div class="product-detail">
    <div class="pd-top">
        <a href="{{ route('products.index') }}" class="pd-back"><i class="fas fa-arrow-left"></i> ទំនិញទាំងអស់</a>
        <div class="pd-actions">
            <a href="{{ route('products.edit', $product) }}" class="pd-btn primary"><i class="fas fa-pen"></i> កែប្រែទំនិញ</a>
            <form action="{{ route('products.destroy', $product) }}" method="POST" data-delete="Product" data-item-name="{{ $product->name }}">@csrf @method('DELETE')<button class="pd-btn danger" type="submit"><i class="fas fa-trash"></i> លុប</button></form>
        </div>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <header class="pd-card pd-hero">
        <div class="pd-avatar"><i class="fas fa-box"></i></div>
        <div><h2>{{ $product->name }}</h2><div class="pd-meta"><span class="pd-code">{{ $product->sku }}</span><span>{{ $product->category ?: '—' }}</span><span class="pd-badge {{ $statusClass }}">{{ $statusLabel }}</span></div></div>
        <div class="pd-price"><span class="pd-label">តម្លៃលក់ / {{ $product->unitLabel() }}</span><strong>៛{{ number_format($product->price_khr ?? 0) }}</strong><small>${{ number_format($product->price_usd ?? 0, 2) }} USD</small></div>
    </header>
    <div class="pd-stats">
        <div class="pd-card pd-stat"><i class="fas fa-boxes-stacked"></i><div><span class="pd-label">ស្តុកបច្ចុប្បន្ន</span><strong>{{ $inventory ? number_format($inventory->quantity) : '—' }}</strong> <small class="text-secondary">{{ $inventory ? $product->unitLabel() : '' }}</small></div></div>
        <div class="pd-card pd-stat"><i class="fas fa-shopping-bag"></i><div><span class="pd-label">ចំនួនលក់សរុប</span><strong>{{ number_format($orderTotals->sold ?? 0) }}</strong></div></div>
        <div class="pd-card pd-stat"><i class="fas fa-gift"></i><div><span class="pd-label">ចំនួនថែមសរុប</span><strong>{{ number_format($orderTotals->free_qty ?? 0) }}</strong></div></div>
    </div>
    <div class="pd-grid">
        <section class="pd-card pd-section">
            <h3><i class="fas fa-box"></i> ព័ត៌មានទំនិញ</h3>
            <dl class="pd-fields">
                <div><dt>កូដទំនិញ</dt><dd>{{ $product->sku }}</dd></div><div><dt>ប្រភេទ</dt><dd>{{ $product->category ?: '—' }}</dd></div>
                <div><dt>ខ្នាត</dt><dd>{{ $product->unitLabel() }}</dd></div><div><dt>អ្នកផ្គត់ផ្គង់</dt><dd>{{ $product->supplier ?: '—' }}</dd></div>
                <div><dt>កាលបរិច្ឆេទបង្កើត</dt><dd>{{ $product->created_at?->format('d/m/Y') ?? '—' }}</dd></div><div><dt>កែប្រែចុងក្រោយ</dt><dd>{{ $product->updated_at?->format('d/m/Y H:i') ?? '—' }}</dd></div>
            </dl>
            @if($product->description)<div class="pd-description"><span class="pd-label">បរិយាយ</span>{{ $product->description }}</div>@endif
        </section>
        <div>
            <section class="pd-card pd-section mb-3">
                <h3><i class="fas fa-sliders-h"></i> ជម្រើសទំនិញ</h3>
                <div class="pd-setting"><span>អនុញ្ញាតកែតម្លៃ</span><span class="pd-badge {{ $product->allow_custom_price ? 'low' : '' }}">{{ $product->allow_custom_price ? 'បើក' : 'បិទ' }}</span></div>
                <div class="pd-setting"><span>អនុញ្ញាតថែម</span><span class="pd-badge {{ $product->allow_free ? 'good' : '' }}">{{ $product->allow_free ? 'បើក' : 'បិទ' }}</span></div>
            </section>
            <section class="pd-card pd-section">
                <h3><i class="fas fa-warehouse"></i> ព័ត៌មានស្តុក</h3>
                @if($inventory)
                    <dl class="pd-fields"><div><dt>កម្រិតជិតអស់</dt><dd>{{ number_format($inventory->reorder_level) }} {{ $product->unitLabel() }}</dd></div><div><dt>ទីតាំងឃ្លាំង</dt><dd>{{ $inventory->warehouse_location ?: '—' }}</dd></div><div><dt>ថ្លៃដើម / {{ $product->unitLabel() }}</dt><dd>{{ $inventory->cost_per_unit !== null ? '៛' . number_format($inventory->cost_per_unit) : '—' }}</dd></div><div><dt>បន្ថែមស្តុកចុងក្រោយ</dt><dd>{{ $inventory->last_restocked?->format('d/m/Y') ?? '—' }}</dd></div></dl>
                    <a class="pd-btn pd-stock-link" href="{{ route('inventory.show', $inventory) }}">មើលចលនាស្តុក <i class="fas fa-arrow-right"></i></a>
                @else<p class="text-secondary small mb-0">មិនទាន់មានកំណត់ត្រាស្តុក</p>@endif
            </section>
        </div>
    </div>
    <p class="pd-note">ចំនួនលក់ / ថែមតាមការបញ្ជាទិញបច្ចុប្បន្នដែលមិនបានលុប ឬបោះបង់។</p>
</div>
@endsection
