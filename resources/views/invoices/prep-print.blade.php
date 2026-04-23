@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 style="font-size:28px;font-weight:600;color:#333;margin:0;">រៀបចំទំនិញ - Prep Printing</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:12px;">
                    <div class="card-body" style="padding:24px;">
                        @if($invoices->count())
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead style="background:#f8f9fa;">
                                        <tr>
                                            <th style="padding:12px;">Invoice</th>
                                            <th style="padding:12px;">Customer</th>
                                            <th style="padding:12px;">Date</th>
                                            <th style="padding:12px;text-align:right;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $invoice)
                                            <tr style="border-bottom:1px solid #e9ecef;">
                                                <td style="padding:12px;color:#333;font-weight:600;">{{ $invoice->invoice_number }}
                                                </td>
                                                <td style="padding:12px;color:#666;">
                                                    {{ optional($invoice->order->customer)->name ?? 'N/A' }}</td>
                                                <td style="padding:12px;color:#666;">
                                                    {{ $invoice->invoice_date->translatedFormat('M d, Y') }}</td>
                                                <td style="padding:12px;text-align:right;">
                                                    <a href="{{ route('print.prep', $invoice) }}" target="_blank" class="btn"
                                                        style="background:#1a1d29;color:white;border:none;padding:6px 12px;border-radius:6px;font-weight:600;font-size:12px;margin-right:6px;">
                                                        <i class="fas fa-print"></i> រៀបចំទំនិញ
                                                    </a>
                                                    <a href="{{ route('print.customer', $invoice) }}" target="_blank" class="btn"
                                                        style="background:#e85d24;color:white;border:none;padding:6px 12px;border-radius:6px;font-weight:600;font-size:12px;">
                                                        <i class="fas fa-print"></i> វិក្ក័យបត្រ
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div style="margin-top:20px;">
                                {{ $invoices->links() }}
                            </div>
                        @else
                            <p style="color:#999;text-align:center;padding:40px;">No invoices available for printing.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection