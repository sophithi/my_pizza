@extends('layouts.app')

@section('title', 'Inventory')

@section('content')

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">Inventory Management</h2>
                <a href="{{ route('inventory.create') }}" class="btn" style="background: #e85d24; color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add Inventory
                </a>
            </div>
        </div>
    </div>

    @if($message = Session::get('success'))
    <div class="alert alert-success" style="border-radius: 8px; padding: 16px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
        <i class="fas fa-check-circle"></i> {{ $message }}
    </div>
    @endif

    @if($inventories->count() > 0)
    <div class="row">
        @forelse($inventories as $inv)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; transition: all 0.3s ease;">
                <!-- Product Image -->
                <div style="height: 200px; background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                    @if($inv->product->image)
                        <img src="{{ asset('storage/' . $inv->product->image) }}" alt="{{ $inv->product->name }}" 
                            style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="text-align: center; color: white;">
                            <i class="fas fa-box" style="font-size: 48px; margin-bottom: 8px;"></i>
                            <p style="margin: 0; font-weight: 600;">No Image</p>
                        </div>
                    @endif
                </div>

                <!-- Card Body -->
                <div class="card-body" style="padding: 20px;">
                    <!-- Product Name & Category -->
                    <div style="margin-bottom: 12px;">
                        <h5 style="color: #333; font-weight: 600; margin: 0 0 4px 0; font-size: 16px;">{{ $inv->product->name }}</h5>
                        <p style="color: #666; font-size: 12px; margin: 0;">
                            <i class="fas fa-tag" style="margin-right: 4px;"></i>{{ $inv->product->category }}
                        </p>
                    </div>

                    <!-- Stock Information -->
                    <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #666; font-size: 12px;">Quantity in Stock:</span>
                            <span style="color: #333; font-weight: 600;">{{ $inv->quantity }} {{ $inv->product->unit }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #666; font-size: 12px;">Warehouse:</span>
                            <span style="color: #333; font-weight: 500;">{{ $inv->warehouse_location ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div style="margin-bottom: 12px;">
                        <span style="padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;
                            background: {{ $inv->quantity == 0 ? '#f8d7da' : ($inv->quantity <= $inv->reorder_level ? '#fff3cd' : '#d4edda') }};
                            color: {{ $inv->quantity == 0 ? '#721c24' : ($inv->quantity <= $inv->reorder_level ? '#856404' : '#155724') }};">
                            @if($inv->quantity == 0)
                                <i class="fas fa-exclamation-circle"></i> Out of Stock
                            @elseif($inv->quantity <= $inv->reorder_level)
                                <i class="fas fa-exclamation-triangle"></i> Low Stock (Min: {{ $inv->reorder_level }})
                            @else
                                <i class="fas fa-check-circle"></i> In Stock
                            @endif
                        </span>
                    </div>

                    <!-- Pricing Section -->
                    <div style="border-top: 1px solid #e9ecef; padding-top: 12px; margin-top: 12px;">
                        <p style="color: #666; font-size: 11px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Pricing</p>
                        
                        @if($inv->product->price)
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="color: #666; font-size: 12px;">PHP:</span>
                            <span style="color: #e85d24; font-weight: 600;">₱{{ number_format($inv->product->price, 2) }}</span>
                        </div>
                        @endif

                        @if($inv->product->price_usd)
                        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e9ecef;">
                            <span style="color: #666; font-size: 12px;">USD:</span>
                            <span style="color: #17a2b8; font-weight: 600;">${{ number_format($inv->product->price_usd, 2) }}</span>
                        </div>
                        @endif

                        @if($inv->product->price_khr)
                        <div style="display: flex; justify-content: space-between; padding: 6px 0;">
                            <span style="color: #666; font-size: 12px;">KHR:</span>
                            <span style="color: #28a745; font-weight: 600;">៛{{ number_format($inv->product->price_khr, 0) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="card-footer" style="background: #f8f9fa; padding: 12px 20px; border-top: 1px solid #e9ecef; display: flex; gap: 8px;">
                    <a href="{{ route('inventory.show', $inv) }}" class="btn btn-sm" 
                        style="background: #e85d24; color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; flex: 1; text-align: center;">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('inventory.edit', $inv) }}" class="btn btn-sm" 
                        style="background: #007bff; color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; flex: 1; text-align: center;">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info" style="border-radius: 8px; padding: 24px; background: #cce5ff; color: #004085; border: 1px solid #b6d4fe; text-align: center;">
                <i class="fas fa-info-circle"></i> No inventory records found. <a href="{{ route('inventory.create') }}" style="color: #004085; font-weight: 600;">Add one now</a>
            </div>
        </div>
        @endforelse
    </div>

    <div style="margin-top: 20px;">
        {{ $inventories->links() }}
    </div>
    @else
    <div class="alert alert-info" style="border-radius: 8px; padding: 24px; background: #cce5ff; color: #004085; border: 1px solid #b6d4fe; text-align: center;">
        <i class="fas fa-info-circle"></i> No inventory records found. <a href="{{ route('inventory.create') }}" style="color: #004085; font-weight: 600;">Add one now</a>
    </div>
    @endif
</div>

@endsection
