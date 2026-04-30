@extends('layouts.app')

@section('title', ($inventory->product ? $inventory->product->name : 'Inventory') . ' Details')

@push('styles')
<style>
    .show-container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    
    .show-header { 
        background: white; 
        padding: 24px; 
        border-radius: 8px; 
        border-bottom: 3px solid #e85d24; 
        margin-bottom: 24px; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
    }
    
    .show-header h1 { 
        font-size: 28px; 
        font-weight: 800; 
        color: #1a1d29; 
        margin: 0; 
    }
    
    .show-header p { 
        font-size: 13px; 
        color: #999; 
        margin: 4px 0 0 0; 
    }
    
    .show-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 24px; 
    }
    
    .prod-img-box { 
        background: white; 
        border-radius: 8px; 
        border: 1px solid #e8e8e8; 
        padding: 16px; 
        text-align: center; 
    }
    
    .prod-img { 
        width: 100%; 
        height: 400px; 
        background: #f5f6fa; 
        border-radius: 6px; 
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
        font-size: 60px; 
    }
    
    .info-box { 
        background: white; 
        border-radius: 8px; 
        border: 1px solid #e8e8e8; 
        padding: 20px; 
    }
    
    .title { 
        font-size: 24px; 
        font-weight: 800; 
        color: #1a1d29; 
        margin: 0 0 4px 0; 
    }
    
    .cat { 
        font-size: 13px; 
        color: #999; 
        margin: 0 0 16px 0; 
    }
    
    .stat-row { 
        padding: 14px 0; 
        border-bottom: 1px solid #f0f0f0; 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
    }
    
    .stat-row:last-child { 
        border-bottom: none; 
    }
    
    .stat-label { 
        font-size: 13px; 
        color: #999; 
        font-weight: 600; 
    }
    
    .stat-value { 
        font-size: 16px; 
        font-weight: 800; 
        color: #1a1d29; 
    }
    
    .badge { 
        display: inline-block; 
        padding: 6px 12px; 
        border-radius: 4px; 
        font-size: 11px; 
        font-weight: 700; 
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
    
    .prices { 
        display: flex; 
        gap: 12px; 
        margin: 14px 0; 
        padding: 14px 0; 
        border-top: 1px solid #f0f0f0; 
        border-bottom: 1px solid #f0f0f0; 
    }
    
    .price { 
        flex: 1; 
        padding: 8px; 
        background: #f5f6fa; 
        border-radius: 4px; 
        text-align: center; 
        font-weight: 700; 
        font-size: 12px; 
    }
    
    .price small {
        font-size: 10px; 
        color: #999;
    }
    
    .btns { 
        display: flex; 
        gap: 8px; 
        margin-top: 16px; 
    }
    
    .btn-edit { 
        flex: 1; 
        padding: 10px; 
        background: #e85d24; 
        color: white; 
        border: none; 
        border-radius: 6px; 
        font-weight: 700; 
        cursor: pointer; 
        text-decoration: none; 
        text-align: center; 
        transition: all 0.3s; 
    }
    
    .btn-edit:hover { 
        background: #d94a10; 
    }
    
    .btn-back { 
        flex: 1; 
        padding: 10px; 
        background: #f0f0f0; 
        color: #1a1d29; 
        border: 1px solid #e8e8e8; 
        border-radius: 6px; 
        font-weight: 700; 
        cursor: pointer; 
        text-decoration: none; 
        text-align: center; 
        transition: all 0.3s; 
    }
    
    .btn-back:hover { 
        background: #e8e8e8; 
    }
    
    @media (max-width: 768px) { 
        .show-grid { grid-template-columns: 1fr; } 
        .show-header { flex-direction: column; text-align: center; gap: 8px; } 
    }
</style>
@endpush

@section('content')

@if(!$inventory->product)
<div class="show-container">
    <div class="show-header" style="background: #fee2e2; border-bottom: 3px solid #dc2626;">
        <div>
            <h1 style="color: #dc2626;">⚠️ Product Not Found</h1>
            <p>The product associated with this inventory record no longer exists.</p>
        </div>
    </div>
    <div style="text-align: center; padding: 40px; background: white; border-radius: 8px;">
        <p style="color: #666; margin-bottom: 20px;">This inventory record has been orphaned.</p>
        <a href="{{ route('inventory.index') }}" style="display: inline-block; padding: 10px 20px; background: #e85d24; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">Back to Inventory</a>
    </div>
</div>
@else
<div class="show-container">
    <!-- Header -->
    <div class="show-header">
        <div>
            <h1> {{ $inventory->product->name ?? 'Unknown Product' }}</h1>
        
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="show-grid">
        <!-- LEFT: Product Image -->
        <div class="prod-img-box">
            <div class="prod-img {{ !$inventory->product->image ? 'no-img' : '' }}">
                @if($inventory->product->image)
                    <img src="{{ asset('storage/' . $inventory->product->image) }}" alt="{{ $inventory->product->name }}">
                @else
                    <i class="fas fa-image"></i>
                @endif
            </div>
            <p style="font-size: 12px; color: #999; margin: 0;">{{ $inventory->product->name ?? '—' }} - {{ $inventory->product->category ?? '—' }}</p>
        </div>

        <!-- RIGHT: Details -->
        <div class="info-box">
            <h2 class="title">{{ $inventory->product->name ?? '—' }}</h2>
            <p class="cat"> {{ $inventory->product->category ?? '—' }}</p>

            @php
                $isOut = $inventory->quantity == 0;
                $isLow = !$isOut && $inventory->quantity <= $inventory->reorder_level;
                $unitLabels = [
                    'kg' => 'គីឡូក្រាម',
                    'g' => 'ក្រាម',
                    'L' => 'លីត្រ',
                    'ml' => 'មីលីលីត្រ',
                    'pcs' => 'បន្ទះ',
                    'box' => 'ប្រអប់',
                    'box1' => 'ប្រអប់ 1',
                    'box2' => 'ប្រអប់ 2',
                    'pack' => 'កញ្ចប់',
                    'bag' => 'បាវ',
                ];
                $unit = $unitLabels[$inventory->product->unit] ?? $inventory->product->unit;
            @endphp
            <span class="badge {{ $isOut ? 'badge-bad' : ($isLow ? 'badge-warn' : 'badge-good') }}">
                {{ $isOut ? '✕ Out of Stock' : ($isLow ? '⚠ Low Stock' : '✓ In Stock') }}
            </span>

            <!-- Stock Info -->
            <div class="stat-row">
                <span class="stat-label">ចំនួនក្នុងស្តុក</span>
                <span class="stat-value" style="color: #e85d24;">{{ $inventory->quantity }} {{ $unit }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">កម្រិតចំនួន</span>
                <span class="stat-value">{{ $inventory->reorder_level }} {{ $unit }}</span>
            </div>
            <div class="stat-row">
                <span class="stat-label">ទីតាំងស្តុក</span>
                <span class="stat-value">📍 {{ $inventory->warehouse_location ?? '—' }}</span>
            </div>

            <!-- Pricing -->
            <div class="prices">
                @if($inventory->product->price_usd)
                <div class="price">${{ number_format($inventory->product->price_usd, 2) }}<br><small>USD</small></div>
                @endif
                @if($inventory->product->price_khr)
                <div class="price">៛{{ number_format($inventory->product->price_khr, 0) }}<br><small>KHR</small></div>
                @endif
            </div>

            <!-- Actions -->
            <div class="btns">
                <a href="{{ route('inventory.edit', $inventory) }}" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('inventory.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection
