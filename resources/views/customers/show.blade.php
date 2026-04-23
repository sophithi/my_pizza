@extends('layouts.app')

@section('title', $customer->name)

@section('content')

    <div class="row">
        <div class="col-md-10">
            <!-- Customer Details Card -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 24px;">
                <div class="card-body" style="padding: 36px;">
                    <div
                        style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #e9ecef;">
                        <div>
                            <h2 style="font-size: 32px; font-weight: 700; color: #1a1d29; margin: 0 0 12px 0;">
                                {{ $customer->name }}</h2>
                            <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                                @if($customer->type == 'facebook')
                                    <span
                                        style="padding: 8px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #e7f3ff; color: #0a66c2; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fab fa-facebook-f"></i> Facebook
                                    </span>
                                @elseif($customer->type == 'telegram')
                                    <span
                                        style="padding: 8px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #e0f7ff; color: #0088cc; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fab fa-telegram"></i> Telegram
                                    </span>
                                @endif

                                @if($customer->status == 'active')
                                    <span
                                        style="padding: 8px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #d4edda; color: #155724; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @elseif($customer->status == 'inactive')
                                    <span
                                        style="padding: 8px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #f8d7da; color: #721c24; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-times-circle"></i> Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div style="margin-bottom: 32px;">
                        <h5 style="font-size: 16px; font-weight: 700; color: #1a1d29; margin-bottom: 16px;">
                            <i class="fas fa-phone" style="color: #e85d24; margin-right: 8px;"></i>Contact Information
                        </h5>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                            <div>
                                <p
                                    style="color: #6c757d; font-weight: 600; margin-bottom: 6px; font-size: 12px; text-transform: uppercase;">
                                    លេខទំនាក់ទំនង</p>
                                <p style="color: #1a1d29; margin: 0; font-size: 15px;">{{ $customer->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div style="margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid #e9ecef;">
                        <h5 style="font-size: 16px; font-weight: 700; color: #1a1d29; margin-bottom: 16px;">
                            <i class="fas fa-map-marker-alt" style="color: #e85d24; margin-right: 8px;"></i>Address
                        </h5>
                        @if($customer->address || $customer->city || $customer->postal_code)
                            <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                                @if($customer->address)
                                    <div>
                                        <p
                                            style="color: #6c757d; font-weight: 600; margin-bottom: 6px; font-size: 12px; text-transform: uppercase;">
                                            ទីតាំងអតិថិជន</p>
                                        <p style="color: #1a1d29; margin: 0; font-size: 15px; line-height: 1.6;">
                                            {{ $customer->address }}</p>
                                    </div>
                                @endif
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                    @if($customer->city)
                                        <div>
                                            <p
                                                style="color: #6c757d; font-weight: 600; margin-bottom: 6px; font-size: 12px; text-transform: uppercase;">
                                                ខេត្ត/ក្រុង</p>
                                            <p style="color: #1a1d29; margin: 0; font-size: 15px;">{{ $customer->city }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p style="color: #999; font-style: italic;">No address information available</p>
                        @endif
                    </div>

                    <!-- Notes Section -->
                    @if($customer->notes)
                        <div style="margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid #e9ecef;">
                            <h5 style="font-size: 16px; font-weight: 700; color: #1a1d29; margin-bottom: 16px;">
                                <i class="fas fa-sticky-note" style="color: #e85d24; margin-right: 8px;"></i>Notes &
                                Descriptions
                            </h5>
                            <div
                                style="background: #f8f9fa; padding: 16px; border-radius: 8px; border-left: 4px solid #e85d24;">
                                <p style="color: #1a1d29; margin: 0; line-height: 1.6;">{{ $customer->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 12px; padding-top: 24px; border-top: 1px solid #e9ecef;">
                        <a href="{{ route('customers.edit', $customer) }}" class="btn"
                            style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('customers.index') }}" class="btn"
                            style="background: #f8f9fa; color: #1a1d29; padding: 12px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-arrow-left"></i> Back to Customers
                        </a>
                    </div>
                </div>
            </div>

            <!-- Orders Section -->
            @if($customer->orders->count())
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 36px;">
                        <h4 style="font-size: 20px; font-weight: 700; color: #1a1d29; margin: 0 0 24px 0;">
                            <i class="fas fa-shopping-bag" style="color: #e85d24; margin-right: 10px;"></i>Order History
                            ({{ $customer->orders_count ?? $customer->orders->count() }} Orders)
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th
                                            style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6c757d;">
                                            Order ID</th>
                                        <th
                                            style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6c757d;">
                                            Date</th>
                                        <th
                                            style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6c757d;">
                                            Items</th>
                                        <th
                                            style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6c757d;">
                                            Amount</th>
                                        <th
                                            style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6c757d;">
                                            Status</th>
                                        <th
                                            style="padding: 12px; font-size: 12px; font-weight: 600; text-transform: uppercase; color: #6c757d;">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->orders as $order)
                                        <tr style="border-bottom: 1px solid #e9ecef;">
                                            <td style="padding: 12px; color: #e85d24; font-weight: 700; font-size: 14px;">
                                                #{{ $order->id }}</td>
                                            <td style="padding: 12px; color: #6c757d; font-size: 14px;">
                                                {{ $order->order_date->translatedFormat('M d, Y') }}</td>
                                            <td style="padding: 12px; color: #1a1d29; font-size: 14px;">{{ $order->items->count() }}
                                                item(s)</td>
                                            <td style="padding: 12px; color: #1a1d29; font-weight: 700; font-size: 14px;">
                                                ${{ number_format($order->total_amount, 2) }}</td>
                                            <td style="padding: 12px;">
                                                @if($order->status == 'pending')
                                                    <span
                                                        style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #fff3cd; color: #856404; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fas fa-clock"></i> Pending
                                                    </span>
                                                @elseif($order->status == 'completed')
                                                    <span
                                                        style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #d4edda; color: #155724; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fas fa-check-circle"></i> Completed
                                                    </span>
                                                @elseif($order->status == 'cancelled')
                                                    <span
                                                        style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f8d7da; color: #721c24; display: inline-flex; align-items: center; gap: 4px;">
                                                        <i class="fas fa-times-circle"></i> Cancelled
                                                    </span>
                                                @else
                                                    <span
                                                        style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #e2e3e5; color: #383d41;">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="padding: 12px;">
                                                <a href="{{ route('orders.show', $order) }}"
                                                    style="color: #0d6efd; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #f8f9fa;">
                    <div class="card-body" style="padding: 48px; text-align: center;">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 16px; display: block;"></i>
                        <p style="color: #6c757d; font-size: 16px; margin: 0;">No orders yet. This customer hasn't placed any
                            orders.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection