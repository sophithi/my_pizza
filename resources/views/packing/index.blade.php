@extends('layouts.app')

@section('title', 'រៀបចំទំនិញ')

@push('styles')
    <style>
        .packing-new-badge {
            align-items: center;
            background: #dcfce7;
            border: 1px solid #86efac;
            border-radius: 999px;
            color: #047857;
            display: inline-flex;
            font-size: 11px;
            font-weight: 800;
            gap: 5px;
            margin-left: 8px;
            padding: 3px 8px;
            vertical-align: middle;
        }

        .packing-sent-time {
            color: #6b7280;
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-top: 3px;
        }

        .packing-complete-btn,
        .packing-complete-done {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 7px;
            min-height: 32px;
            padding: 6px 12px;
            white-space: nowrap;
        }

        .packing-complete-btn {
            background: #fff;
            border: 1px solid #d1d5db;
            color: #374151;
            cursor: pointer;
        }

        .packing-complete-btn:hover {
            background: #ecfdf5;
            border-color: #86efac;
            color: #047857;
        }

        .packing-complete-done {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #047857;
        }

        .packing-row-completed {
            background: #f9fafb;
            opacity: .78;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm" style="border-radius: 10px;">
                {{ session('success') }}
            </div>
        @endif

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
                <form method="GET" action="{{ route('packing.index') }}" id="packingFilter"
                    style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">

                    <input type="hidden" name="period" id="periodInput" value="{{ request('period') }}">

                    <a href="{{ route('packing.index', ['period' => 'today']) }}"
                        style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid {{ request('period') === 'today' ? '#e85d24' : '#e9ecef' }}; background: {{ request('period') === 'today' ? '#e85d24' : '#fff' }}; color: {{ request('period') === 'today' ? '#fff' : '#6c757d' }}; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-calendar-day"></i> ថ្ងៃនេះ
                    </a>
                    <a href="{{ route('packing.index', ['period' => 'yesterday']) }}"
                        style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid {{ request('period') === 'yesterday' ? '#e85d24' : '#e9ecef' }}; background: {{ request('period') === 'yesterday' ? '#e85d24' : '#fff' }}; color: {{ request('period') === 'yesterday' ? '#fff' : '#6c757d' }}; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-calendar-minus"></i> ម្សិលមិញ
                    </a>
                    <a href="{{ route('packing.index', ['period' => 'month']) }}"
                        style="padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: 1.5px solid {{ request('period') === 'month' ? '#e85d24' : '#e9ecef' }}; background: {{ request('period') === 'month' ? '#e85d24' : '#fff' }}; color: {{ request('period') === 'month' ? '#fff' : '#6c757d' }}; text-decoration: none; transition: all 0.2s;">
                        <i class="fas fa-calendar-alt"></i> ខែនេះ
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
                                            <th>វិក័្កយបត្រ</th>
                                            <th>ឈ្មោះអតិថិជន</th>
                                            <th>កាលបរិច្ឆេទ</th>
                                            <th>ផ្ញើមក</th>
                                            <th>រៀបចំរួច</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $invoice)
                                            <tr class="{{ $invoice->packing_completed_at ? 'packing-row-completed' : '' }}">
                                                <td>
                                                    {{ $invoice->invoice_number }}
                                                    @if(!$invoice->packing_completed_at && $invoice->packing_sent_at && $invoice->packing_sent_at->gt(now()->subMinutes(30)))
                                                        <span class="packing-new-badge">
                                                            <i class="fas fa-circle"></i> ថ្មី
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $invoice->order?->customer?->name ?? 'N/A' }}</td>
                                                <td>{{ $invoice->invoice_date->translatedFormat('M d, Y') }}</td>
                                                <td>
                                                    {{ $invoice->packing_sent_at?->setTimezone('Asia/Phnom_Penh')->format('h:i A') ?? 'N/A' }}
                                                    <span
                                                        class="packing-sent-time">{{ $invoice->packing_sent_at?->diffForHumans() }}</span>
                                                </td>
                                                <td>
                                                    @if($invoice->packing_completed_at)
                                                        <span class="packing-complete-done">
                                                            <i class="fas fa-check-circle"></i> រួចរាល់
                                                        </span>
                                                        <span class="packing-sent-time">
                                                            {{ $invoice->packing_completed_at->setTimezone('Asia/Phnom_Penh')->format('h:i A') }}
                                                        </span>
                                                    @else
                                                        <form method="POST" action="{{ route('packing.complete', $invoice) }}"
                                                            class="m-0">
                                                            @csrf
                                                            <button type="submit" class="packing-complete-btn">
                                                                <i class="far fa-square"></i> បញ្ជាក់
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                                <td style="text-align:right">
                                                    <a href="{{ route('packing.prep', $invoice) }}" target="_blank" class="btn"
                                                        style="background:#1a1d29;color:white;border:none;padding:6px 10px;border-radius:6px;font-weight:600;margin-right:6px;">
                                                        <i class="fas fa-print"></i> រៀបចំទំនិញ
                                                    </a>
                                                    <a href="{{ route('packing.customer', $invoice) }}" target="_blank" class="btn"
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
                            <p style="color:#666;">មិនទាន់មានវិក្ក័យបត្រ.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const refreshKey = 'packingRefresh';
            const seenKey = 'packingRefreshSeenAt';

            function refreshPackingPage(payload) {
                if (!payload?.time || sessionStorage.getItem(seenKey) === String(payload.time)) {
                    return;
                }

                if (Date.now() - payload.time > 120000) {
                    return;
                }

                sessionStorage.setItem(seenKey, String(payload.time));
                const targetUrl = payload.url || @json(route('packing.index', ['period' => 'today']));

                if (window.location.href === targetUrl) {
                    window.location.reload();
                    return;
                }

                window.location.href = targetUrl;
            }

            function readRefreshSignal() {
                try {
                    return JSON.parse(localStorage.getItem(refreshKey) || 'null');
                } catch (error) {
                    return null;
                }
            }

            window.addEventListener('storage', (event) => {
                if (event.key === refreshKey) {
                    refreshPackingPage(readRefreshSignal());
                }
            });

            refreshPackingPage(readRefreshSignal());

            setInterval(() => {
                if (document.hidden) {
                    return;
                }

                const activeTag = document.activeElement?.tagName;
                if (activeTag === 'INPUT' || activeTag === 'SELECT' || activeTag === 'TEXTAREA') {
                    return;
                }

                window.location.reload();
            }, 60000);
        })();
    </script>
@endpush
