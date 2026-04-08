@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">{{ $invoice->invoice_number }}</h2>
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('invoices.print', $invoice) }}" class="btn" style="background: #6c757d; color: white; border: none; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-print"></i> Print
                    </a>
                    @if ($invoice->status !== 'paid')
                    <a href="{{ route('invoices.edit', $invoice) }}" class="btn" style="background: #007bff; color: white; border: none; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
                <div class="card-body" style="padding: 24px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div>
                            <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Customer</p>
                            @if($invoice->order && $invoice->order->customer)
                            <p style="color: #333; font-size: 16px; font-weight: 600;">{{ $invoice->order->customer->name }}</p>
                            <p style="color: #666; font-size: 14px; margin: 8px 0;">{{ $invoice->order->customer->email }}</p>
                            <p style="color: #666; font-size: 14px;">{{ $invoice->order->customer->phone }}</p>
                            @else
                            <p style="color: #999; font-size: 14px;">No customer information available</p>
                            @endif
                        </div>
                        <div>
                            <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px;">Invoice Details</p>
                            <p style="color: #333; margin: 8px 0;"><strong>Invoice Date:</strong> {{ $invoice->invoice_date->translatedFormat('M d, Y') }}</p>
                            <p style="color: #333; margin: 8px 0;"><strong>Due Date:</strong> {{ $invoice->due_date ? $invoice->due_date->translatedFormat('M d, Y') : 'N/A' }}</p>
                            <p style="color: #333; margin: 8px 0;"><strong>Status:</strong>
                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;
                                    background: {{ $invoice->status === 'paid' ? '#d4edda' : ($invoice->status === 'sent' ? '#cce5ff' : ($invoice->status === 'cancelled' ? '#f8d7da' : '#fff3cd')) }};
                                    color: {{ $invoice->status === 'paid' ? '#155724' : ($invoice->status === 'sent' ? '#004085' : ($invoice->status === 'cancelled' ? '#721c24' : '#856404')) }};">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <h5 style="color: #333; font-weight: 600; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #e9ecef;">Order Items</h5>
                    <div class="table-responsive" style="margin-bottom: 20px;">
                        @if($invoice->order && $invoice->order->items && count($invoice->order->items) > 0)
                        <table class="table" style="margin-bottom: 0;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 8px; color: #666; font-weight: 600;">Product</th>
                                    <th style="padding: 8px; color: #666; font-weight: 600; text-align: right;">Qty</th>
                                    <th style="padding: 8px; color: #666; font-weight: 600; text-align: right;">Price</th>
                                    <th style="padding: 8px; color: #666; font-weight: 600; text-align: right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->order->items as $item)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 8px; color: #333;">{{ $item->product->name }}</td>
                                    <td style="padding: 8px; color: #666; text-align: right;">{{ $item->quantity }}</td>
                                    <td style="padding: 8px; text-align: right;"><span style="color: #666;">${{ number_format($item->unit_price, 2) }}</span><br><span style="color: #999; font-size: 12px;">៛{{ number_format($item->unit_price * 4000, 0) }}</span></td>
                                    <td style="padding: 8px; text-align: right;"><span style="color: #333; font-weight: 500;">${{ number_format($item->total_price, 2) }}</span><br><span style="color: #999; font-size: 12px;">៛{{ number_format($item->total_price * 4000, 0) }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p style="color: #999; padding: 16px; text-align: center;">No items found in this order</p>
                        @endif
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                        <div style="width: 300px;">
                            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e9ecef;">
                                <span style="color: #666;">Subtotal:</span>
                                <div style="text-align: right;"><span style="color: #333; font-weight: 500;">${{ number_format($invoice->subtotal, 2) }}</span><br><span style="color: #999; font-size: 12px;">៛{{ number_format($invoice->subtotal * 4000, 0) }}</span></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e9ecef;">
                                <span style="color: #666;">Discount:</span>
                                <div style="text-align: right;"><span style="color: #333; font-weight: 500;">${{ number_format($invoice->discount_amount, 2) }}</span><br><span style="color: #999; font-size: 12px;">៛{{ number_format($invoice->discount_amount * 4000, 0) }}</span></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                                <span style="color: #333; font-weight: 600;">Total:</span>
                                <div style="text-align: right;"><span style="color: #e85d24; font-weight: 700; font-size: 18px;">${{ number_format($invoice->total_amount, 2) }}</span><br><span style="color: #999; font-size: 13px;">៛{{ number_format($invoice->total_amount * 4000, 0) }}</span></div>
                            </div>
                        </div>
                    </div>

                    @if ($invoice->notes)
                    <div style="margin-top: 20px; padding: 12px; background: #f8f9fa; border-radius: 8px;">
                        <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">Notes</p>
                        <p style="color: #333; margin: 0;">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body" style="padding: 24px;">
                    <h5 style="color: #333; font-weight: 600; margin-bottom: 16px;">Quick Actions</h5>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="{{ route('invoices.index') }}" class="btn" style="background: #6c757d; color: white; border: none; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; text-align: center;">
                            <i class="fas fa-list"></i> Back to Invoices
                        </a>
                        @if($invoice->order)
                        <a href="{{ route('orders.show', $invoice->order) }}" class="btn" style="background: #17a2b8; color: white; border: none; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 500; text-align: center;">
                            <i class="fas fa-receipt"></i> View Order
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
