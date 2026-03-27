@extends('layouts.app')

@section('title', 'Orders')

@section('content')

<div class="mb-4 d-flex justify-content-between align-items-center">
    <h2 style="font-size: 24px; font-weight: 700; color: #1a1d29; margin: 0;">Orders</h2>
    <a href="{{ route('orders.create') }}" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
        <i class="fas fa-plus"></i> Create Order
    </a>
</div>

@if($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body" style="padding: 24px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">#Order ID</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Customer</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Date</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Amount</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Status</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Payment</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr style="border-bottom: 1px solid #e9ecef;">
                        <td style="padding: 12px; color: #e85d24; font-weight: 600;">ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td style="padding: 12px; color: #1a1d29;">{{ $order->customer->name }}</td>
                        <td style="padding: 12px; color: #6c757d;">{{ $order->order_date->format('M d, Y') }}</td>
                        <td style="padding: 12px; color: #1a1d29; font-weight: 600;">${{ number_format($order->total_amount, 2) }}</td>
                        <td style="padding: 12px;">
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                                <i class="fas fa-circle" style="font-size: 8px;"></i> {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td style="padding: 12px;">
                            <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: {{ $order->payment_status === 'paid' ? '#d4edda' : '#fff3cd' }}; color: {{ $order->payment_status === 'paid' ? '#155724' : '#856404' }};">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td style="padding: 12px;">
                            <a href="{{ route('orders.show', $order) }}" style="color: #0d6efd; text-decoration: none; font-size: 12px;"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('orders.edit', $order) }}" style="color: #0d6efd; text-decoration: none; font-size: 12px; margin-left: 8px;"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 32px; text-align: center; color: #6c757d;">No orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $orders->links() }}
</div>

@endsection
