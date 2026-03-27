@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')

<div class="row">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
            <div class="card-body" style="padding: 28px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px;">
                    <div>
                        <h2 style="font-size: 28px; font-weight: 700; color: #1a1d29; margin: 0;">Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h2>
                        <p style="color: #6c757d; margin: 8px 0 0 0;">{{ $order->customer->name }} • {{ $order->order_date->format('M d, Y') }}</p>
                    </div>
                    <div style="text-align: right;">
                        <h3 style="font-size: 24px; color: #e85d24; font-weight: 700; margin: 0;">${{ number_format($order->total_amount, 2) }}</h3>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Status</p>
                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #d4edda; color: #155724;">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Payment Status</p>
                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background: {{ $order->payment_status === 'paid' ? '#d4edda' : '#fff3cd' }}; color: {{ $order->payment_status === 'paid' ? '#155724' : '#856404' }};">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Subtotal</p>
                        <p style="color: #1a1d29; font-weight: 600; margin: 0;">${{ number_format($order->subtotal, 2) }}</p>
                    </div>
                </div>

                <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e9ecef;">
                    @if($order->notes)
                    <p style="color: #6c757d; font-weight: 600; margin-bottom: 8px;">Notes</p>
                    <p style="color: #1a1d29; margin: 0;">{{ $order->notes }}</p>
                    @endif
                </div>

                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('orders.edit', $order) }}" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('orders.index') }}" class="btn" style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        @if($order->items->count())
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body" style="padding: 28px;">
                <h4 style="font-size: 18px; font-weight: 700; color: #1a1d29; margin-bottom: 20px;">Order Items</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">Product</th>
                                <th style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">Quantity</th>
                                <th style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">Price</th>
                                <th style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td style="padding: 12px; color: #1a1d29; font-weight: 600;">{{ $item->product->name }}</td>
                                <td style="padding: 12px; color: #6c757d;">{{ $item->quantity }} {{ $item->product->unit }}</td>
                                <td style="padding: 12px; color: #1a1d29;">${{ number_format($item->unit_price, 2) }}</td>
                                <td style="padding: 12px; color: #1a1d29; font-weight: 600;">${{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
