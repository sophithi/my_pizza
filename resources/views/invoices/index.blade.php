@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">វិក្ក័យប័ត្រ</h2>
                <a href="{{ route('invoices.create') }}" class="btn" style="background: #e85d24; color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                    <i class="fas fa-plus"></i> ចេញវិក្ក័យបត្រ
                </a>
            </div>
        </div>
    </div>

    @if ($invoices->count() > 0)
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body" style="padding: 24px;">
            <div class="table-responsive">
                <table class="table table-hover" style="margin-bottom: 0;">
                    <thead style="background: #f8f9fa; border-bottom: 2px solid #e9ecef;">
                        <tr>
                            <th style="padding: 12px; color: #666; font-weight: 600;">Invoice #</th>
                            <th style="padding: 12px; color: #666; font-weight: 600;">អតិថិជន</th>
                            <th style="padding: 12px; color: #666; font-weight: 600;">ការកម្មង់</th>
                            <th style="padding: 12px; color: #666; font-weight: 600;">Amount</th>
                            <th style="padding: 12px; color: #666; font-weight: 600;">កាលបរិច្ឆេទ</th>
                            <th style="padding: 12px; color: #666; font-weight: 600;">Status</th>
                            <th style="padding: 12px; color: #666; font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                        <tr style="border-bottom: 1px solid #e9ecef;">
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $invoice->invoice_number }}</td>
                            <td style="padding: 12px; color: #666;">{{ $invoice->order?->customer?->name ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #666;">##{{ $invoice->order?->id }}
                            <td style="padding: 12px; color: #333; font-weight: 500;">${{ number_format($invoice->total_amount, 2) }}</td>
                            <td style="padding: 12px; color: #666;">{{ $invoice->invoice_date->format('M d, Y') }}</td>
                            <td style="padding: 12px;">
                                <span style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;
                                    background: {{ $invoice->status === 'paid' ? '#d4edda' : ($invoice->status === 'sent' ? '#cce5ff' : ($invoice->status === 'cancelled' ? '#f8d7da' : '#fff3cd')) }};
                                    color: {{ $invoice->status === 'paid' ? '#155724' : ($invoice->status === 'sent' ? '#004085' : ($invoice->status === 'cancelled' ? '#721c24' : '#856404')) }};">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td style="padding: 12px;">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm" style="background: #e85d24; color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; margin-right: 4px;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-sm" style="background: #6c757d; color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; margin-right: 4px;">
                                    <i class="fas fa-print"></i>
                                </a>
                                @if ($invoice->status !== 'paid')
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm" style="background: #007bff; color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;">
                                    <i class="fas fa-edit"></i>
                                </a>
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
        {{ $invoices->links() }}
    </div>
    @else
    <div class="alert alert-info" role="alert" style="border-radius: 8px; padding: 16px; background: #cce5ff; color: #004085; border: 1px solid #b6d4fe;">
        <i class="fas fa-info-circle"></i> No invoices found. <a href="{{ route('invoices.create') }}" style="color: #004085; font-weight: 600;">បង្កើតថ្មី</a>
    </div>
    @endif
</div>
@endsection
