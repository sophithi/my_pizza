@extends('layouts.app')

@section('title', 'វិក្ក័យប័ត្រ')

@push('styles')
    <style>
        .invoice-page {
            --accent: #e85d24;
            --accent-dark: #d94a10;
            --border: #e5e7eb;
            --muted: #6b7280;
            --surface: #fff;
            --text: #111827;
        }

        .invoice-header {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .invoice-title {
            color: var(--text);
            font-size: 30px;
            font-weight: 900;
            margin: 0;
        }

        .invoice-subtitle {
            color: var(--muted);
            margin: 6px 0 0;
        }

        .invoice-btn {
            align-items: center;
            border: 0;
            border-radius: 8px;
            display: inline-flex;
            font-weight: 800;
            gap: 8px;
            justify-content: center;
            min-height: 40px;
            padding: 9px 14px;
            text-decoration: none;
            white-space: nowrap;
        }

        .invoice-btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
        }

        .invoice-btn-primary:hover {
            color: #fff;
            box-shadow: 0 8px 18px rgba(232, 93, 36, .22);
            transform: translateY(-1px);
        }

        .invoice-btn-soft {
            background: #f3f4f6;
            color: #374151;
        }

        .invoice-btn-soft:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .invoice-btn-export {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #047857;
        }

        .invoice-btn-export:hover {
            background: #d1fae5;
            color: #065f46;
        }

        .stats-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            min-height: 96px;
            padding: 16px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .stat-value {
            color: var(--text);
            font-size: 24px;
            font-weight: 900;
            margin-top: 6px;
        }

        .filter-card,
        .invoice-table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
        }

        .filter-card {
            margin-bottom: 16px;
            padding: 14px;
        }

        .quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .quick-filter {
            align-items: center;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: #4b5563;
            display: inline-flex;
            font-size: 13px;
            font-weight: 800;
            gap: 7px;
            min-height: 38px;
            padding: 8px 13px;
            text-decoration: none;
        }

        .quick-filter.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .filter-row {
            align-items: center;
            display: grid;
            gap: 10px;
            grid-template-columns: minmax(320px, 1fr) 180px 180px auto auto;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            min-height: 42px;
        }

        .date-field {
            position: relative;
        }

        .date-field .form-control {
            width: 100%;
        }

        .date-placeholder {
            color: var(--muted);
            font-size: 14px;
            left: 13px;
            pointer-events: none;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }

        .date-field.has-value .date-placeholder,
        .date-field:focus-within .date-placeholder {
            display: none;
        }

        .invoice-table-card {
            overflow: hidden;
        }

        .invoice-table th {
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            padding: 14px 16px;
            text-transform: uppercase;
        }

        .invoice-table td {
            border-bottom: 1px solid #f1f3f5;
            color: var(--text);
            padding: 14px 16px;
            vertical-align: middle;
        }

        .invoice-number {
            color: var(--text);
            font-weight: 900;
            text-decoration: none;
        }

        .invoice-number:hover {
            color: var(--accent);
        }

        .status-pill {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            padding: 6px 10px;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-sent {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-other {
            background: #e5e7eb;
            color: #374151;
        }

        .action-row {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .icon-action {
            align-items: center;
            background: transparent;
            border: 0;
            color: #2563eb;
            display: inline-flex;
            height: 32px;
            justify-content: center;
            text-decoration: none;
            width: 32px;
        }

        .icon-print {
            color: #4b5563;
        }

        .icon-edit {
            color: #e85d24;
        }

        .empty-state {
            padding: 46px 16px;
            text-align: center;
        }

        .empty-state-icon {
            align-items: center;
            background: #fff7ed;
            border-radius: 999px;
            color: var(--accent);
            display: inline-flex;
            font-size: 24px;
            height: 54px;
            justify-content: center;
            margin-bottom: 12px;
            width: 54px;
        }

        .empty-state-title {
            color: var(--text);
            font-weight: 900;
            margin-bottom: 4px;
        }

        .empty-state-text {
            color: var(--muted);
            margin: 0 0 14px;
        }

        @media (max-width: 980px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .invoice-header {
                align-items: stretch;
                flex-direction: column;
            }

            .invoice-btn {
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 invoice-page">
        <div class="invoice-header">
            <div>
                <h2 class="invoice-title">វិក្ក័យប័ត្រ</h2>
                <p class="invoice-subtitle">គ្រប់គ្រងវិក្ក័យប័ត្រ ស្វែងរកតាមអតិថិជន និងបោះពុម្ពបានរហ័ស។</p>
            </div>
            <a href="{{ route('orders.create') }}" class="invoice-btn invoice-btn-primary">
                <i class="fas fa-plus"></i> បង្កើតបញ្ជាទិញ
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">វិក្ក័យប័ត្រសរុប</div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">បានទូទាត់</div>
                <div class="stat-value text-success">{{ number_format($stats['paid']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">មិនទាន់បង់</div>
                <div class="stat-value text-warning">{{ number_format($stats['unpaid']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">ចំនួនទឹកប្រាក់</div>
                <div class="stat-value">៛{{ number_format($stats['amount_khr'], 0) }}</div>
                <div class="text-muted small fw-bold">${{ number_format($stats['amount_usd'], 2) }}</div>
            </div>
        </div>

        <form method="GET" action="{{ route('invoices.index') }}" class="filter-card" id="invoiceFilter">
            <div class="quick-filters">
                <a href="{{ route('invoices.index', ['period' => 'today']) }}"
                    class="quick-filter {{ request('period') === 'today' ? 'active' : '' }}">
                    <i class="fas fa-calendar-day"></i> ថ្ងៃនេះ
                </a>
                <a href="{{ route('invoices.index', ['period' => 'yesterday']) }}"
                    class="quick-filter {{ request('period') === 'yesterday' ? 'active' : '' }}">
                    <i class="fas fa-calendar-minus"></i> ម្សិលមិញ
                </a>
                <a href="{{ route('invoices.index', ['period' => 'month']) }}"
                    class="quick-filter {{ request('period') === 'month' ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> ខែនេះ
                </a>
                <a href="{{ route('invoices.index', ['period' => 'year']) }}"
                    class="quick-filter {{ request('period') === 'year' ? 'active' : '' }}">
                    <i class="fas fa-calendar"></i> ឆ្នាំនេះ
                </a>
            </div>

            <div class="filter-row">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="ស្វែងរកលេខវិក្ក័យប័ត្រ ឈ្មោះអតិថិជន លេខទូរស័ព្ទ ឬលេខបញ្ជាទិញ...">

                <select name="status" class="form-select">
                    <option value="all">គ្រប់ស្ថានភាព</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>មិនទាន់ទូទាត់</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>មិនទាន់ទូទាត់</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>បានទូទាត់</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>មិនទូទាត់</option>
                </select>

                <div class="date-field {{ request('date') ? 'has-value' : '' }}">
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control"
                        title="ជ្រើសរើសកាលបរិច្ឆេទ">
                    <span class="date-placeholder">ជ្រើសរើសកាលបរិច្ឆេទ</span>
                </div>

                <a href="{{ route('invoices.index') }}" class="invoice-btn invoice-btn-soft">
                    <i class="fas fa-rotate-left"></i> សម្អាត
                </a>

                <a href="{{ route('invoices.export', request()->query()) }}" class="invoice-btn invoice-btn-export">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </form>

        <div class="invoice-table-card">
            <div class="table-responsive">
                <table class="table invoice-table mb-0">
                    <thead>
                        <tr>
                            <th>លេខវិក្ក័យប័ត្រ</th>
                            <th>អតិថិជន</th>
                            <th>បញ្ជាទិញ</th>
                            <th>ចំនួនទំនិញ</th>
                            <th>ទឹកប្រាក់</th>
                            <th>កាលបរិច្ឆេទ</th>
                            <th>ស្ថានភាព</th>
                            <th class="text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="invoice-number">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $invoice->order?->customer?->name ?? 'N/A' }}</div>
                                    @if($invoice->order?->customer?->phone)
                                        <div class="text-muted small">{{ $invoice->order->customer->phone }}</div>
                                    @endif
                                </td>
                                <td class="text-muted">#{{ $invoice->order?->id ?? 'N/A' }}</td>
                                <td>{{ number_format($invoice->items_count) }}</td>
                                <td>
                                    <div class="fw-bold">៛{{ number_format($invoice->total_khr, 0) }}</div>
                                    <div class="text-muted small fw-bold">${{ number_format($invoice->total_amount, 2) }}</div>
                                </td>
                                <td class="text-muted">{{ $invoice->invoice_date?->format('d/m/Y') ?? 'N/A' }}
                                </td>
                                <td>
                                    @if($invoice->status === 'paid')
                                        <span class="status-pill status-paid">
                                            <i class="fas fa-check-circle"></i> បានទូទាត់
                                        </span>
                                    @elseif($invoice->status === 'sent')
                                        <span class="status-pill status-sent">
                                            <i class="fas fa-clock"></i> មិនទាន់ទូទាត់
                                        </span>
                                    @elseif($invoice->status === 'cancelled')
                                        <span class="status-pill status-cancelled">
                                            <i class="fas fa-times-circle"></i> មិនទូទាត់
                                        </span>
                                    @elseif($invoice->status === 'draft')
                                        <span class="status-pill status-draft">
                                            <i class="fas fa-clock"></i> មិនទាន់ទូទាត់
                                        </span>
                                    @else
                                        <span class="status-pill status-other">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-row">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="icon-action" title="មើល">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('invoices.print', $invoice) }}" class="icon-action icon-print"
                                            title="ព្រីន ">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if ($invoice->status !== 'paid')
                                            <a href="{{ route('invoices.edit', $invoice) }}" class="icon-action icon-edit"
                                                title="កែប្រែ">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <div class="empty-state-title">មិនមានវិក្ក័យប័ត្រ</div>
                                        <p class="empty-state-text">សាកល្បងសម្អាតតម្រង ឬបង្កើតបញ្ជាទិញថ្មី។</p>
                                        <a href="{{ route('orders.create') }}" class="invoice-btn invoice-btn-primary">
                                            <i class="fas fa-plus"></i> បង្កើតបញ្ជាទិញ
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const form = document.getElementById('invoiceFilter');
                if (!form) return;

                const search = form.querySelector('input[name="search"]');
                const controls = form.querySelectorAll('select[name="status"], input[name="date"]');
                const dateField = form.querySelector('.date-field');
                const dateInput = form.querySelector('input[name="date"]');
                let timer = null;

                const submit = () => form.submit();

                if (search) {
                    search.addEventListener('input', function () {
                        clearTimeout(timer);
                        timer = setTimeout(submit, 400);
                    });

                    search.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            submit();
                        }
                    });
                }

                controls.forEach(function (control) {
                    control.addEventListener('change', submit);
                });

                if (dateField && dateInput) {
                    const syncDatePlaceholder = () => {
                        dateField.classList.toggle('has-value', Boolean(dateInput.value));
                    };

                    dateInput.addEventListener('input', syncDatePlaceholder);
                    dateInput.addEventListener('change', syncDatePlaceholder);
                    syncDatePlaceholder();
                }
            })();
        </script>
    @endpush
@endsection
