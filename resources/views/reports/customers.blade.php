@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">Customer Report</h2>
            <p style="color: #666; margin-top: 8px;">Analyze customer activity and revenue distribution</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body" style="padding: 24px;">
            <form method="GET" action="{{ route('reports.customers') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: #333;">Period</label>
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="all" {{ ($period ?? 'all') === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ ($period ?? '') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ ($period ?? '') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="week" {{ ($period ?? '') === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ ($period ?? '') === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ ($period ?? '') === 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ ($period ?? '') === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                @if(($period ?? '') === 'custom')
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: #333;">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: #333;">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-weight: 600; color: #333;">&nbsp;</label>
                    <button type="submit" class="btn w-100" style="background: #e85d24; color: white; border: none; font-weight: 600;">Filter</button>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #e85d24;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Total Customers</p>
                    <h3 style="color: #e85d24; font-size: 32px; font-weight: 700; margin: 0;">{{ $totalCustomers }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #28a745;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Active Customers</p>
                    <h3 style="color: #28a745; font-size: 32px; font-weight: 700; margin: 0;">{{ $activeCustomers }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #17a2b8;">
                <div class="card-body" style="padding: 24px;">
                    <p style="color: #666; font-size: 12px; font-weight: 600; text-transform: uppercase; margin: 0 0 8px 0;">Total Credit Limit</p>
                    <h3 style="color: #17a2b8; font-size: 32px; font-weight: 700; margin: 0;">${{ number_format($totalCreditLimit, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Activity -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header" style="background: none; border-bottom: 2px solid #e9ecef; padding: 20px;">
            <h5 style="color: #333; font-weight: 600; margin: 0;">Customer Activity (Ordered by Order Count)</h5>
        </div>
        <div class="card-body" style="padding: 24px;">
            <div class="table-responsive">
                <table class="table table-hover" style="margin-bottom: 0;">
                    <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                        <tr>
                            <th style="padding: 12px; color: #666; font-weight: 600;">ឈ្មោះអតិថិជន</th>
                            <th style="padding: 12px; color: #666; font-weight: 600;">ទំនាក់ទំនង</th>
                            <th style="padding: 12px; color: #666; font-weight: 600; text-align: right;">ការកម្មង់</th>
                            <th style="padding: 12px; color: #666; font-weight: 600; text-align: right;">ការចំណាយ</th>
                            <th style="padding: 12px; color: #666; font-weight: 600; text-align: right;">ការចំណាយទូទៅ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customerActivity as $customer)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $customer->name }}</td>
                            <td style="padding: 12px; color: #666;">{{ $customer->email }}<br>{{ $customer->phone }}</td>
                            <td style="padding: 12px; color: #666; text-align: right;">
                                <span style="padding: 4px 8px; background: #e9ecef; border-radius: 4px; font-weight: 600;">
                                    {{ $customer->orders_count ?? 0 }}
                                </span>
                            </td>
                            <td style="padding: 12px; color: #333; font-weight: 500; text-align: right;">₱{{ number_format($customer->orders_sum_total_amount ?? 0, 2) }}</td>
                            <td style="padding: 12px; color: #666; text-align: right;">
                                ${{ number_format(($customer->orders_count && $customer->orders_sum_total_amount) ? $customer->orders_sum_total_amount / $customer->orders_count : 0, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div style="margin-top: 20px;">
        {{ $customerActivity->links() }}
    </div>
    <div style="margin-top: 20px;">
        <a href="{{ route('reports.dashboard') }}" class="btn" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
            Back to Dashboard
        </a>
    </div>
</div>
@endsection
