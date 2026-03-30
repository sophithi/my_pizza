@extends('layouts.app')

@section('title', 'Inventory')

@push('styles')
<style>
    body { background: #f5f6fa; }
    .header { background: white; padding: 32px; margin-bottom: 32px; border-radius: 12px; border-bottom: 3px solid #e85d24; display: flex; justify-content: space-between; align-items: center; }
    .header h1 { font-size: 28px; font-weight: 800; color: #1a1d29; margin: 0; }
    .header p { font-size: 13px; color: #999; margin: 8px 0 0 0; }
    .btn-add { background: #e85d24; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.3s ease; }
    .btn-add:hover { background: #d94a10; transform: translateY(-2px); }
    .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; margin-bottom: 24px; }
    .stat { background: white; padding: 16px; border-radius: 6px; text-align: center; border-left: 4px solid #e85d24; font-size: 24px; font-weight: 800; color: #1a1d29; }
    .stat p { margin: 4px 0 0 0; font-size: 11px; color: #999; font-weight: 600; }
    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
    .card { background: white; border-radius: 6px; overflow: hidden; border: 1px solid #e8e8e8; transition: all 0.3s ease; }
    .card:hover { transform: translateY(-4px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
    .card-img { width: 100%; height: 160px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #ccc; font-size: 40px; }
    .card-img img { width: 100%; height: 100%; object-fit: cover; }
    .card-body { padding: 14px; }
    .card-title { font-weight: 700; font-size: 15px; color: #1a1d29; margin: 0 0 4px 0; }
    .card-cat { font-size: 12px; color: #999; margin: 0 0 8px 0; }
    .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; margin-bottom: 8px; }
    .badge-good { background: #e8f5e9; color: #2e7d32; }
    .badge-warn { background: #fff3e0; color: #e65100; }
    .badge-bad { background: #ffebee; color: #c62828; }
    .info { font-size: 13px; margin: 8px 0; color: #666; }
    .price { display: flex; gap: 6px; margin: 8px 0; }
    .price span { font-size: 12px; padding: 4px 8px; background: #f5f6fa; border-radius: 4px; font-weight: 700; flex: 1; }
    .btns { display: flex; gap: 6px; margin-top: 10px; }
    .btns a { flex: 1; padding: 6px 8px; border: 1px solid #e8e8e8; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 700; text-align: center; color: #1a1d29; transition: all 0.3s ease; }
    .btns a:first-child { background: #e85d24; color: white; border-color: #e85d24; }
    .btns a:hover { border-color: #e85d24; color: #e85d24; }
    .empty { text-align: center; padding: 40px; background: white; border-radius: 6px; color: #999; }
    .empty i { font-size: 48px; display: block; margin-bottom: 12px; opacity: 0.2; }
    @media (max-width: 768px) { .header { flex-direction: column; gap: 12px; text-align: center; } .grid { grid-template-columns: 1fr; } .stats { grid-template-columns: repeat(2, 1fr); } }
</style>
@endpush

@section('content')

<div style="max-width: 1200px; margin: 0 auto; padding: 16px;">
    <!-- Header -->
    <div class="header">
        <div>
            <h1>📦 Inventory</h1>
            <p>Manage your stock levels</p>
        </div>
        <a href="{{ route('inventory.create') }}" class="btn-add"><i class="fas fa-plus"></i> Add</a>
    </div>

    @if($message = Session::get('success'))
    <div style="background: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; border-left: 4px solid #2e7d32;">
        {{ $message }}
    </div>
    @endif

    @if($inventories->count() > 0)
    <!-- Stats -->
    <div class="stats">
        <div class="stat">{{ $inventories->count() }}<p>Total</p></div>
        <div class="stat">{{ $inventories->where('quantity', '>', 0)->where('quantity', '>', DB::raw('reorder_level'))->count() }}<p>In Stock</p></div>
        <div class="stat">{{ $inventories->where('quantity', '<=', DB::raw('reorder_level'))->where('quantity', '>', 0)->count() }}<p>Low</p></div>
        <div class="stat">{{ $inventories->where('quantity', 0)->count() }}<p>Out</p></div>
    </div>

    <!-- Grid -->
    <div class="grid">
        @foreach($inventories as $inv)
        <div class="card">
            <div class="card-img">
                @if($inv->product->image)
                    <img src="{{ asset('storage/' . $inv->product->image) }}" alt="{{ $inv->product->name }}">
                @else
                    <i class="fas fa-image"></i>
                @endif
            </div>
            <div class="card-body">
                <h3 class="card-title">{{ $inv->product->name }}</h3>
                <p class="card-cat">{{ $inv->product->category }}</p>
                
                @php
                    $isOut = $inv->quantity == 0;
                    $isLow = !$isOut && $inv->quantity <= $inv->reorder_level;
                @endphp
                <span class="badge {{ $isOut ? 'badge-bad' : ($isLow ? 'badge-warn' : 'badge-good') }}">
                    {{ $isOut ? '✕ Out' : ($isLow ? '⚠ Low' : '✓ In Stock') }}
                </span>

                <div class="info">Qty: <strong>{{ $inv->quantity }}</strong> {{ $inv->product->unit }}</div>
                <div class="info">Min: <strong>{{ $inv->reorder_level }}</strong></div>
                <div class="info">📍 {{ $inv->warehouse_location ?? '—' }}</div>

                <div class="price">
                    @if($inv->product->price_usd)
                    <span>${{ number_format($inv->product->price_usd, 2) }}</span>
                    @endif
                    @if($inv->product->price_khr)
                    <span>៛{{ number_format($inv->product->price_khr, 0) }}</span>
                    @endif
                </div>

                <div class="btns">
                    <a href="{{ route('inventory.show', $inv) }}"><i class="fas fa-eye"></i> View</a>
                    <a href="{{ route('inventory.edit', $inv) }}"><i class="fas fa-edit"></i> Edit</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($inventories->hasPages())
    <div style="margin-top: 24px;">{{ $inventories->links() }}</div>
    @endif

    @else
    <div class="empty">
        <i class="fas fa-box-open"></i>
        <h3>No Inventory Yet</h3>
        <p>Add your first product inventory</p>
    </div>
    @endif
</div>
@endsection
