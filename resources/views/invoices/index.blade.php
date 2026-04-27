@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="font-size: 28px; font-weight: 600; color: #333; margin: 0;">វិក្ក័យប័ត្រ</h2>
                    <a href="{{ route('orders.create') }}" class="btn"
                        style="background: #e85d24; color: white; border: none; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-plus"></i> បង្កើតបញ្ជាទិញ
                    </a>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body" style="padding: 16px 24px;">
                <form method="GET" action="{{ route('invoices.index') }}" id="invoiceFilter"
                    style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">

                    <input type="hidden" name="period" id="periodInput" value="{{ request('period') }}">

                    <a href="{{ route('invoices.index', ['period' => 'today']) }}"
                        style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid {{ request('period') === 'today' ? '#e85d24' : '#e9ecef' }}; background: {{ request('period') === 'today' ? '#e85d24' : '#fff' }}; color: {{ request('period') === 'today' ? '#fff' : '#6c757d' }}; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-calendar-day"></i> ថ្ងៃនេះ
                    </a>
                    <a href="{{ route('invoices.index', ['period' => 'yesterday']) }}"
                        style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid {{ request('period') === 'yesterday' ? '#e85d24' : '#e9ecef' }}; background: {{ request('period') === 'yesterday' ? '#e85d24' : '#fff' }}; color: {{ request('period') === 'yesterday' ? '#fff' : '#6c757d' }}; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-calendar-minus"></i> ម្សិលមិញ
                    </a>
                    <a href="{{ route('invoices.index', ['period' => 'month']) }}"
                        style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid {{ request('period') === 'month' ? '#e85d24' : '#e9ecef' }}; background: {{ request('period') === 'month' ? '#e85d24' : '#fff' }}; color: {{ request('period') === 'month' ? '#fff' : '#6c757d' }}; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-calendar-alt"></i> ខែនេះ
                    </a>
                    <a href="{{ route('invoices.index', ['period' => 'year']) }}"
                        style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid {{ request('period') === 'year' ? '#e85d24' : '#e9ecef' }}; background: {{ request('period') === 'year' ? '#e85d24' : '#fff' }}; color: {{ request('period') === 'year' ? '#fff' : '#6c757d' }}; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-calendar"></i> ឆ្នាំនេះ
                    </a>

                    <div style="margin-left: auto; display: flex; align-items: center; gap: 8px;">
                        <input type="date" name="date" value="{{ request('date') }}"
                            style="padding: 8px 14px; border-radius: 8px; border: 1.5px solid #e9ecef; font-size: 13px; font-weight: 600; color: #1a1d29;">
                        <button type="submit"
                            style="padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; background: #e85d24; color: #fff; cursor: pointer;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    @if(request('period') || request('date'))
                        <a href="{{ route('invoices.index') }}"
                            style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid #e9ecef; background: #fff; color: #6c757d; text-decoration: none;">
                            <i class="fas fa-times"></i> សម្អាត
                        </a>
                    @endif
                </form>
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
                                        <td style="padding: 12px; color: #333; font-weight: 500;">{{ $invoice->invoice_number }}
                                        </td>
                                        <td style="padding: 12px; color: #666;">{{ $invoice->order?->customer?->name ?? 'N/A' }}
                                        </td>
                                        <td style="padding: 12px; color: #666;">##{{ $invoice->order?->id }}
                                        <td style="padding: 12px; color: #333; font-weight: 500;">
                                            ${{ number_format($invoice->total_amount, 2) }}</td>
                                        <td style="padding: 12px; color: #666;">
                                            {{ $invoice->invoice_date->translatedFormat('M d, Y') }}</td>
                                        <td style="padding: 12px;">
                                            <span
                                                style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;
                                                background: {{ $invoice->status === 'paid' ? '#d4edda' : ($invoice->status === 'sent' ? '#cce5ff' : ($invoice->status === 'cancelled' ? '#f8d7da' : '#fff3cd')) }};
                                                color: {{ $invoice->status === 'paid' ? '#155724' : ($invoice->status === 'sent' ? '#004085' : ($invoice->status === 'cancelled' ? '#721c24' : '#856404')) }};">
                                                {{ $invoice->status === 'paid' ? 'បានបង់' : ($invoice->status === 'sent' ? 'បានផ្ញើ' : ($invoice->status === 'cancelled' ? 'បានលុបចោល' : ($invoice->status === 'draft' ? 'ព្រាង' : ucfirst($invoice->status)))) }}
                                            </span>
                                        </td>
                                        <td style="padding: 12px;">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm"
                                                style="background: #e85d24; color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; margin-right: 4px;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-sm"
                                                style="background: #6c757d; color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; margin-right: 4px;">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @if ($invoice->status !== 'paid')
                                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm"
                                                    style="background: #007bff; color: white; border: none; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px;">
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
                {{ $invoices->appends(request()->query())->links() }}
            </div>
        @else
            <div class="alert alert-info" role="alert"
                style="border-radius: 8px; padding: 16px; background: #cce5ff; color: #004085; border: 1px solid #b6d4fe;">
                <i class="fas fa-info-circle"></i> មិនមាន វិក្ក័យប័ត្រ។ <a href="{{ route('orders.create') }}"
                    style="color: #004085; font-weight: 600;">បង្កើតបញ្ជាទិញ</a>
            </div>
        @endif
    </div>
@endsection