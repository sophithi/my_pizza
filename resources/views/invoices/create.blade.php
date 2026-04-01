@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">Create Invoice</h2>
            <p style="color: #666; margin-top: 8px;">Select an order to create an invoice</p>
        </div>
    </div>

    @if ($orders->count() > 0)
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body" style="padding: 24px;">
                    <div class="table-responsive">
                        <table class="table table-hover" style="margin-bottom: 0;">
                            <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                                <tr>
                                    <th style="padding: 12px; color: #666; font-weight: 600;">Order ID</th>
                                    <th style="padding: 12px; color: #666; font-weight: 600;">Customer</th>
                                    <th style="padding: 12px; color: #666; font-weight: 600;">Total Amount</th>
                                    <th style="padding: 12px; color: #666; font-weight: 600;">Date</th>
                                    <th style="padding: 12px; color: #666; font-weight: 600;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 12px; color: #333; font-weight: 500;">#{{ $order->id }}</td>
                                    <td style="padding: 12px; color: #666;">{{ $order->customer->name }}</td>
                                    <td style="padding: 12px; color: #333; font-weight: 500;">${{ number_format($order->total_amount, 2) }}</td>
                                    <td style="padding: 12px; color: #666;">{{ $order->order_date->format('M d, Y') }}</td>
                                    <td style="padding: 12px;">
                                        <form action="{{ route('invoices.store') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                                            <input type="hidden" name="invoice_date" value="{{ now()->format('Y-m-d') }}">
                                            <button type="submit" class="btn btn-sm" style="background: #e85d24; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">
                                                Create Invoice
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px;">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info" role="alert" style="border-radius: 8px; padding: 16px; background: #cce5ff; color: #004085; border: 1px solid #b6d4fe;">
        <i class="fas fa-info-circle"></i> All orders already have invoices. <a href="{{ route('invoices.index') }}" style="color: #004085; font-weight: 600;">View invoices</a>
    </div>
    @endif
</div>
@endsection
