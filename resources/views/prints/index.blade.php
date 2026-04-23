@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <h2 style="font-size:28px;font-weight:600;color:#333;margin:0;">រៀបចំទំនិញ</h2>
                </div>
            </div>
        </div>
        <!-- Date Filter -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body" style="padding: 16px 24px;">
                <form method="GET" action="{{ route('print.index') }}" id="printFilter"
                    style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">

                    <input type="hidden" name="period" id="periodInput" value="{{ request('period') }}">

                    <a href="{{ route('print.index', ['period' => 'today']) }}"
                        style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid {{ request('period') === 'today' ? '#e85d24' : '#e9ecef' }}; background: {{ request('period') === 'today' ? '#e85d24' : '#fff' }}; color: {{ request('period') === 'today' ? '#fff' : '#6c757d' }}; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-calendar-day"></i> ថ្ងៃនេះ
                    </a>
                    <a href="{{ route('print.index', ['period' => 'yesterday']) }}"
                        style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid {{ request('period') === 'yesterday' ? '#e85d24' : '#e9ecef' }}; background: {{ request('period') === 'yesterday' ? '#e85d24' : '#fff' }}; color: {{ request('period') === 'yesterday' ? '#fff' : '#6c757d' }}; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-calendar-minus"></i> ម្សិលមិញ
                    </a>
                    <a href="{{ route('print.index', ['period' => 'month']) }}"
                        style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid {{ request('period') === 'month' ? '#e85d24' : '#e9ecef' }}; background: {{ request('period') === 'month' ? '#e85d24' : '#fff' }}; color: {{ request('period') === 'month' ? '#fff' : '#6c757d' }}; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-calendar-alt"></i> ខែនេះ
                    </a>
                    <a href="{{ route('print.index', ['period' => 'year']) }}"
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
                </form>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:12px;">
                    <div class="card-body" style="padding:24px;">
                        @if($invoices->count())
                            <div class="table-responsive">
                                <table class="table">
                                    <thead style="background:#f8f9fa;">
                                        <tr>
                                            <th>វិក័្កយប័ត្រ</th>
                                            <th>ឈ្មោះអតិថិជន</th>
                                            <th>កាលបរិច្ឆេទ</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $invoice)
                                            <tr>
                                                <td>{{ $invoice->invoice_number }}</td>
                                                <td>{{ $invoice->order?->customer?->name ?? 'N/A' }}</td>
                                                <td>{{ $invoice->invoice_date->translatedFormat('M d, Y') }}</td>
                                                <td style="text-align:right">
                                                    <a href="{{ route('print.prep', $invoice) }}" target="_blank" class="btn"
                                                        style="background:#1a1d29;color:white;border:none;padding:6px 10px;border-radius:6px;font-weight:600;margin-right:6px;">
                                                        <i class="fas fa-print"></i> រៀបចំទំនិញ
                                                    </a>
                                                    <a href="{{ route('print.customer', $invoice) }}" target="_blank" class="btn"
                                                        style="background:#e85d24;color:white;border:none;padding:6px 10px;border-radius:6px;font-weight:600;">
                                                        <i class="fas fa-print"></i> វិក្ក័យបត្រភ្ញៀវ
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div style="margin-top:12px;">
                                {{ $invoices->links() }}
                            </div>
                        @else
                            <p style="color:#666;">No invoices available for preparation.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection