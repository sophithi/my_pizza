@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">Inventory Report</h2>
            <p style="color: #666; margin-top: 8px;">Monitor stock levels and inventory status</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body" style="padding: 24px;">
            <form method="GET" action="{{ route('reports.inventory') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: #333;">View by</label>
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}>Current Status</option>
                        <option value="today" {{ ($period ?? '') === 'today' ? 'selected' : '' }}>Today's Changes</option>
                        <option value="week" {{ ($period ?? '') === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ ($period ?? '') === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ ($period ?? '') === 'year' ? 'selected' : '' }}>This Year</option>
                    </select>
                </div>
                <div class="col-md-9">
                </div>
            </form>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #e85d24;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Total Products</p>
                    <h3 style="color: #e85d24; font-size: 32px; font-weight: 700; margin: 0;">{{ $totalProducts }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #ffc107;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Low Stock Items</p>
                    <h3 style="color: #ffc107; font-size: 32px; font-weight: 700; margin: 0;">{{ $lowStockProducts->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #dc3545;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Out of Stock</p>
                    <h3 style="color: #dc3545; font-size: 32px; font-weight: 700; margin: 0;">{{ $outOfStockCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #17a2b8;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Total Inventory Value</p>
                    <h3 style="color: #17a2b8; font-size: 32px; font-weight: 700; margin: 0;">₱{{ number_format($totalInventoryValue, 2) }}</h3>
                    <p style="color: #999; font-size: 12px; margin-top: 8px; margin-bottom: 0;">Current stock value at cost price</p>
                </div>
            </div>
        </div>
    </div>

    @if ($lowStockProducts->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning" role="alert" style="border-radius: 8px; padding: 16px; background: #fff3cd; color: #856404; border: 1px solid #ffeaa7;">
                <i class="fas fa-exclamation-triangle"></i> <strong>Alert:</strong> {{ $lowStockProducts->count() }} product(s) need restocking
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
                    <h5 style="color: #333; font-weight: 600; margin: 0;">Low Stock Products</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div class="table-responsive">
                        <table class="table table-hover" style="margin-bottom: 0;">
                            <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                                <tr>
                                    <th style="padding: 12px; color: #666; font-weight: 600;">Product</th>
                                    <th style="padding: 12px; color: #666; font-weight: 600; text-align: right;">Current Stock</th>
                                    <th style="padding: 12px; color: #666; font-weight: 600; text-align: right;">Reorder Level</th>
                                    <th style="padding: 12px; color: #666; font-weight: 600; text-align: right;">Shortage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lowStockProducts as $low)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 12px; color: #333; font-weight: 500;">{{ $low->product->name }}</td>
                                    <td style="padding: 12px; color: #666; text-align: right;">{{ $low->quantity }}</td>
                                    <td style="padding: 12px; color: #666; text-align: right;">{{ $low->reorder_level }}</td>
                                    <td style="padding: 12px; color: #dc3545; font-weight: 600; text-align: right;">{{ $low->reorder_level - $low->quantity }} units</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Inventory Details -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
            <h5 style="color: #333; font-weight: 600; margin: 0;">Inventory Status</h5>
        </div>
        <div class="card-body" style="padding: 24px;">
            <div class="table-responsive">
                <table class="table table-hover" style="margin-bottom: 0;">
                    <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                        <tr>
                            <th style="padding: 12px; color: #666; font-weight: 600;">Product</th>
                            <th style="padding: 12px; color: #666; font-weight: 600; text-align: right;">Current Stock</th>
                            <th style="padding: 12px; color: #666; font-weight: 600; text-align: right;">Reorder Level</th>
                            <th style="padding: 12px; color: #666; font-weight: 600; text-align: right;">Value</th>
                            <th style="padding: 12px; color: #666; font-weight: 600; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventory as $inv)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td style="padding: 12px; color: #333;">{{ $inv->product->name }}</td>
                            <td style="padding: 12px; color: #666; text-align: right;">{{ $inv->quantity }}</td>
                            <td style="padding: 12px; color: #666; text-align: right;">{{ $inv->reorder_level }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500; text-align: right;">
                                ₱{{ number_format(($inv->cost_per_unit ?? 0) * $inv->quantity, 2) }}
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                @if ($inv->quantity == 0)
                                    <span style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #f8d7da; color: #721c24;">
                                        Out
                                    </span>
                                @elseif ($inv->quantity <= $inv->reorder_level)
                                    <span style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #fff3cd; color: #856404;">
                                        Low
                                    </span>
                                @else
                                    <span style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #d4edda; color: #155724;">
                                        OK
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="margin-top: 20px;">
        {{ $inventory->links() }}
    </div>

    <div style="margin-top: 20px;">
        <a href="{{ route('reports.dashboard') }}" class="btn" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
            Back to Dashboard
        </a>
    </div>
</div>
@endsection
