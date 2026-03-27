@extends('layouts.app')

@section('title', $inventory->product->name . ' Inventory')

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body" style="padding: 28px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 24px;">
                    <div>
                        <h2 style="font-size: 28px; font-weight: 700; color: #1a1d29; margin: 0;">{{ $inventory->product->name }}</h2>
                        <p style="color: #6c757d; margin: 8px 0 0 0;">{{ $inventory->product->category }}</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div style="background: #f8f9fa; padding: 16px; border-radius: 8px;">
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Current Stock</p>
                        <h3 style="font-size: 24px; color: #e85d24; font-weight: 700; margin: 0;">{{ $inventory->quantity }} {{ $inventory->product->unit }}</h3>
                    </div>
                    <div style="background: #f8f9fa; padding: 16px; border-radius: 8px;">
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Status</p>
                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: {{ $inventory->status === 'out_of_stock' ? '#f8d7da' : ($inventory->status === 'low_stock' ? '#fff3cd' : '#d4edda') }}; color: {{ $inventory->status === 'out_of_stock' ? '#721c24' : ($inventory->status === 'low_stock' ? '#856404' : '#155724') }};">
                            {{ ucfirst(str_replace('_', ' ', $inventory->status)) }}
                        </span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e9ecef;">
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Reorder Level</p>
                        <p style="color: #1a1d29; font-weight: 600; margin: 0;">{{ $inventory->reorder_level }} units</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Warehouse Location</p>
                        <p style="color: #1a1d29; font-weight: 600; margin: 0;">{{ $inventory->warehouse_location ?? 'Not assigned' }}</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Cost Per Unit</p>
                        <p style="color: #1a1d29; font-weight: 600; margin: 0;">${{ number_format($inventory->cost_per_unit, 2) }}</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Sellling Price</p>
                        <p style="color: #1a1d29; font-weight: 600; margin: 0;">${{ number_format($inventory->product->price, 2) }}</p>
                    </div>
                </div>

                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('inventory.edit', $inventory) }}" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn" style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
