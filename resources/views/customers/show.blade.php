@extends('layouts.app')

@section('title', $customer->name)

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
            <div class="card-body" style="padding: 28px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px;">
                    <div>
                        <h2 style="font-size: 28px; font-weight: 700; color: #1a1d29; margin: 0;">{{ $customer->name }}</h2>
                        <div style="display: flex; gap: 12px; align-items: center; margin-top: 12px;">
                            @if($customer->type == 'facebook')
                                <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #e7f3ff; color: #0a66c2;">
                                    <i class="fab fa-facebook-f"></i> Facebook
                                </span>
                            @elseif($customer->type == 'telegram')
                                <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #e0f7ff; color: #0088cc;">
                                    <i class="fab fa-telegram"></i> Telegram
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Email</p>
                        <p style="color: #1a1d29; margin: 0;">{{ $customer->email }}</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Phone</p>
                        <p style="color: #1a1d29; margin: 0;">{{ $customer->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">City</p>
                        <p style="color: #1a1d29; margin: 0;">{{ $customer->city ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Status</p>
                        <p style="color: #1a1d29; margin: 0; text-transform: capitalize;">{{ $customer->status }}</p>
                    </div>
                </div>

                @if($customer->address)
                <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e9ecef;">
                    <p style="color: #6c757d; font-weight: 600; margin-bottom: 8px;">Location</p>
                    <p style="color: #1a1d29; margin: 0;">{{ $customer->address }}</p>
                </div>
                @endif

                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('customers.edit', $customer) }}" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn" style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        @if($customer->orders->count())
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body" style="padding: 28px;">
                <h4 style="font-size: 18px; font-weight: 700; color: #1a1d29; margin-bottom: 20px;">Recent Orders</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">Order ID</th>
                                <th style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">Date</th>
                                <th style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">Amount</th>
                                <th style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;">Status</th>
                                <th style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->orders as $order)
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td style="padding: 12px; color: #e85d24; font-weight: 600;">#{{ $order->id }}</td>
                                <td style="padding: 12px; color: #6c757d;">{{ $order->order_date->format('M d, Y') }}</td>
                                <td style="padding: 12px; color: #1a1d29; font-weight: 600;">${{ number_format($order->total_amount, 2) }}</td>
                                <td style="padding: 12px;">
                                    <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #d4edda; color: #155724;">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td style="padding: 12px;">
                                    <a href="{{ route('orders.show', $order) }}" style="color: #0d6efd; text-decoration: none; font-size: 12px;"><i class="fas fa-eye"></i></a>
                                </td>
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
