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

        .packing-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .packing-filter {
            align-items: center;
            background: #fff;
            border: 1.5px solid #e9ecef;
            border-radius: 8px;
            color: #6c757d;
            display: inline-flex;
            font-size: 13px;
            font-weight: 600;
            gap: 7px;
            padding: 8px 16px;
            text-decoration: none;
            transition: all .2s;
        }

        .packing-filter.active {
            background: #e85d24;
            border-color: #e85d24;
            color: #fff;
        }

        .packing-date-filter {
            align-items: center;
            display: flex;
            gap: 8px;
            margin-left: auto;
        }

        .packing-date-filter input[type="date"] {
            border: 1.5px solid #e9ecef;
            border-radius: 8px;
            color: #1a1d29;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 14px;
        }

        .packing-date-filter button {
            background: #e85d24;
            border: none;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 14px;
        }

        .packing-table th {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            padding: 14px 16px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .packing-table td {
            padding: 14px 16px;
            vertical-align: middle;
        }

        .packing-table tr:hover td {
            background: #fbfdff;
        }

        .packing-action-btn {
            align-items: center;
            border: none;
            border-radius: 6px;
            color: #fff;
            display: inline-flex;
            font-size: 13px;
            font-weight: 600;
            justify-content: center;
            margin-right: 6px;
            padding: 6px 12px;
            text-decoration: none;
        }

        .packing-action-btn.dark {
            background: #1a1d29;
        }

        .packing-action-btn.dark:hover {
            background: #111827;
            color: #fff;
        }

        .packing-action-btn.primary {
            background: #e85d24;
        }

        .packing-action-btn.primary:hover {
            background: #d94a10;
            color: #fff;
        }

        .pager-wrap {
            margin-top: 16px;
            display: flex;
            justify-content: center;
        }

        .pager-wrap .pagination {
            margin-bottom: 0;
            flex-wrap: wrap;
            row-gap: 6px;
            column-gap: 4px;
        }

        .pager-wrap .page-link {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            color: #e85d24;
            padding: 6px 12px;
            font-size: 13px;
        }

        .pager-wrap .page-link:hover,
        .pager-wrap .page-link:focus {
            background: #fef1eb;
            border-color: #e85d24;
            color: #e85d24;
            box-shadow: none;
        }

        .pager-wrap .page-item.active .page-link {
            background: #e85d24;
            border-color: #e85d24;
            color: #fff;
        }

        .pager-wrap .page-item.disabled .page-link {
            color: #cbd5e1;
            background: #f8fafc;
            border-color: #e5e7eb;
        }

        @media (max-width: 576px) {
            .pager-wrap .page-link {
                padding: 5px 9px;
                font-size: 12px;
            }
        }

        @media (max-width: 768px) {

            .packing-filters,
            .packing-date-filter {
                width: 100%;
            }

            .packing-date-filter {
                margin-left: 0;
            }

            .packing-filter {
                flex: 1;
                justify-content: center;
            }
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

                    <div class="packing-filters">
                        <a href="{{ route('packing.index', ['period' => 'today']) }}"
                            class="packing-filter {{ request('period') === 'today' ? 'active' : '' }}">
                            <i class="fas fa-calendar-day"></i> ថ្ងៃនេះ
                        </a>
                        <a href="{{ route('packing.index', ['period' => 'yesterday']) }}"
                            class="packing-filter {{ request('period') === 'yesterday' ? 'active' : '' }}">
                            <i class="fas fa-calendar-minus"></i> ម្សិលមិញ
                        </a>
                        <a href="{{ route('packing.index', ['period' => 'month']) }}"
                            class="packing-filter {{ request('period') === 'month' ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i> ខែនេះ
                        </a>
                    </div>

                    <div class="packing-date-filter">
                        <input type="date" name="date" value="{{ request('date') }}">
                        <button type="submit">
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
                                <table class="table packing-table">
                                    <thead>
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
                                                <td class="text-end">
                                                    <div class="dropdown d-inline-block">
                                                        <button type="button" class="packing-action-btn dark dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            បិតលើកេស
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('packing.delivery_pizza', $invoice) }}" target="_blank">
                                                                    ភីហ្សា គ្រួសាររីករាយ
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('packing.delivery_mayo', $invoice) }}" target="_blank">
                                                                    ម៉ាយូនេស បន្ទាយឆ្មា
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('packing.delivery_tamon', $invoice) }}" target="_blank">
                                                                    ម៉ាយូនេស តាម៉ាន់មានជ័យ
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <a href="{{ route('packing.prep', $invoice) }}" target="_blank" class="packing-action-btn dark">
                                                        រៀបចំទំនិញ
                                                    </a>
                                                    <div class="dropdown d-inline-block">
                                                        <button type="button" class="packing-action-btn primary dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            វិក្ក័យបត្រភ្ញៀវ
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('packing.customer', $invoice) }}" target="_blank">
                                                                    ភីហ្សា គ្រួសាររីករាយ
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('packing.customer_mayo', $invoice) }}" target="_blank">
                                                                    ម៉ាយូនេស បន្ទាយឆ្មា
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pager-wrap">
                                {{ $invoices->links('pagination::bootstrap-5') }}
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
