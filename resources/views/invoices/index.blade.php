@extends('layouts.app')

@section('title', 'វិក្ក័យបត្រ')

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

        .invoice-btn-warning {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #c2410c;
        }

        .invoice-btn-warning:hover {
            background: #ffedd5;
            color: #9a3412;
        }

        .invoice-btn-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .invoice-btn-info:hover {
            background: #dbeafe;
            color: #1e40af;
        }

        .invoice-btn-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        .invoice-btn-danger:hover {
            background: #fee2e2;
            color: #b91c1c;
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
            grid-template-columns: minmax(240px, 1fr) 160px 160px 160px 160px auto auto;
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

        .printed-checkbox {
            align-items: center;
            cursor: pointer;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            margin: 0;
            white-space: nowrap;
        }

        .printed-checkbox input {
            cursor: pointer;
            height: 15px;
            width: 15px;
        }

        .printed-checkbox .printed-label {
            color: #92400e;
        }

        .printed-checkbox input:checked ~ .printed-label {
            color: #047857;
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

        .pager-wrap {
            margin-top: 16px;
        }

        .bulk-print-bar {
            align-items: center;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            display: flex;
            gap: 12px;
            justify-content: space-between;
            margin-bottom: 16px;
            padding: 12px 16px;
        }

        .bulk-print-info {
            color: #9a3412;
            font-weight: 800;
            font-size: 14px;
        }

        .bulk-print-info strong {
            color: #e85d24;
        }

        .invoice-btn:disabled {
            cursor: not-allowed;
            opacity: .5;
        }

        .invoice-btn:disabled:hover {
            box-shadow: none;
            transform: none;
        }

        .invoice-select-cell {
            text-align: center;
            width: 36px;
        }

        .invoice-select,
        .invoice-select-all {
            cursor: pointer;
            height: 16px;
            width: 16px;
        }
    </style>
@endpush

@section('content')
    @php
        $calcTotalKhr = function ($invoice) {
            return $invoice->order?->totalKhr() ?? 0;
        };
    @endphp
    <div class="container-fluid py-4 invoice-page">
        <div class="invoice-header">
            <div>
                <h2 class="invoice-title">វិក្ក័យបត្រ</h2>
                <p class="invoice-subtitle">គ្រប់គ្រងវិក្ក័យបត្រ</p>
            </div>
            <div style="display:flex; gap:8px;">
                @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                    <a href="{{ route('invoices.trash') }}" class="invoice-btn invoice-btn-soft">
                        <i class="fas fa-trash"></i> វិក្ក័យបត្របានលុប
                    </a>
                    @if($canUndoClosePeriod ?? false)
                        <form method="POST" action="{{ route('invoices.undo-close-period') }}"
                            data-confirm="លេខវិក្ក័យបត្រនឹងបន្តដូចមុន។"
                            data-confirm-title="លុបចោលការបិទបញ្ជីវិក្ក័យបត្រចុងក្រោយ?"
                            data-confirm-icon="question"
                            data-confirm-color="#2563eb">
                            @csrf
                            <button type="submit" class="invoice-btn invoice-btn-info">
                                <i class="fas fa-rotate-left"></i> លុបចោលការបិទបញ្ជី
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('invoices.close-period') }}"
                            data-confirm="វិក្ក័យបត្របន្ទាប់នឹងចាប់ផ្តើមពី INV-000001 ។"
                            data-confirm-title="បិទបញ្ជីវិក្ក័យបត្រខែនេះ?"
                            data-confirm-icon="warning">
                            @csrf
                            <button type="submit" class="invoice-btn invoice-btn-warning">
                                <i class="fas fa-lock"></i> បិទបញ្ជីវិក្ក័យបត្រក្នុងខែនេះ
                            </button>
                        </form>
                    @endif

                    @if(!empty($mergeBackPreview))
                        @php
                            $mergeBackCount = count($mergeBackPreview);
                            $mergeBackRows = collect($mergeBackPreview)
                                ->map(fn($m) => '<div style="font-family:monospace;font-size:13px;padding:3px 0">'
                                    . '<span style="color:#6b7280">' . e($m['old_number']) . '</span>'
                                    . ' <i class="fas fa-arrow-right" style="font-size:10px;color:#9ca3af"></i> '
                                    . '<span style="color:#e85d24;font-weight:700">' . e($m['new_number']) . '</span>'
                                    . '</div>')
                                ->implode('');
                            $mergeBackHtml = 'លេខនឹងផ្លាស់ប្តូរ៖'
                                . '<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;margin:10px 0;text-align:left;max-height:160px;overflow-y:auto">'
                                . $mergeBackRows . '</div>'
                                . '<strong style="color:#dc2626">សកម្មភាពនេះមិនអាចលុបចោលវិញបានទេ។</strong>';
                        @endphp
                        <form method="POST" action="{{ route('invoices.merge-back-period') }}"
                            data-confirm="{{ $mergeBackHtml }}"
                            data-confirm-html="1"
                            data-confirm-title="បញ្ចូលវិក្ក័យបត្រ {{ $mergeBackCount }} ត្រឡប់ទៅខែមុន?"
                            data-confirm-icon="warning"
                            data-confirm-color="#dc2626">
                            @csrf
                            <button type="submit" class="invoice-btn invoice-btn-danger">
                                <i class="fas fa-code-merge"></i> បញ្ចូលមកវិញក្នុងខែមុន
                            </button>
                        </form>
                    @endif
                @endif
                <a href="{{ route('orders.create') }}" class="invoice-btn invoice-btn-primary">
                    <i class="fas fa-plus"></i> បង្កើតបញ្ជាទិញ
                </a>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">វិក្ក័យបត្រសរុប</div>
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
                <div class="stat-value">{{ number_format($stats['amount_khr'] ?? 0, 0) }}</div>
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
            </div>

            <div class="filter-row">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="...">
                <select name="status" class="form-select">
                    <option value="all">គ្រប់ស្ថានភាព</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>មិនទាន់ទូទាត់</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>បានទូទាត់</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>មិនទូទាត់</option>
                </select>
                <select name="user_id" class="form-select">
                    <option value="all">បុគ្គលិកទាំងអស់</option>
                    @foreach ($staffUsers as $staffUser)
                        <option value="{{ $staffUser->id }}" {{ (string) request('user_id') === (string) $staffUser->id ? 'selected' : '' }}>
                            {{ $staffUser->name }}
                        </option>
                    @endforeach
                </select>
                <select name="printed" class="form-select">
                    <option value="all">ទាំងអស់</option>
                    <option value="printed" {{ request('printed') === 'printed' ? 'selected' : '' }}>បានព្រីន</option>
                    <option value="unprinted" {{ request('printed') === 'unprinted' ? 'selected' : '' }}>មិនទាន់បានព្រីន</option>
                </select>

                <div class="date-field {{ request('date') ? 'has-value' : '' }}">
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control"
                        title="ជ្រើសរើសកាលបរិច្ឆេទ">
                    <span class="date-placeholder">ជ្រើសរើសកាលបរិច្ឆេទ</span>
                </div>

                <!-- <a href="{{ route('invoices.index') }}" class="invoice-btn invoice-btn-soft">
                    <i class="fas fa-rotate-left"></i> សម្អាត
                </a> -->

                <a href="{{ route('invoices.export', request()->query()) }}" class="invoice-btn invoice-btn-export">
                    <i class="fas fa-file-excel"></i> ទាញយក
                </a>
            </div>
        </form>

        <div class="bulk-print-bar">
            <span class="bulk-print-info">
                
                បានជ្រើសរើស <strong id="selectedCount">0</strong> វិក្ក័យបត្រ
            </span>
            <button type="button" id="printSelectedBtn" class="invoice-btn invoice-btn-primary" disabled>
                <i class="fas fa-print"></i> ព្រីនដែលបានជ្រើសរើស
            </button>
        </div>

        <div class="invoice-table-card">
            <div class="table-responsive">
                <table class="table invoice-table mb-0">
                    <thead>
                        <tr>
                            <th class="invoice-select-cell">
                                <input type="checkbox" id="selectAllInvoices" class="invoice-select-all" title="ជ្រើសរើសទាំងអស់">
                            </th>
                            <th>លេខវិក្ក័យបត្រ</th>
                            <th>អតិថិជន</th>
                            <th>បញ្ជាទិញ</th>
                            <th>ទឹកប្រាក់</th>
                            <th>កាលបរិច្ឆេទ</th>
                            <th>បង្កើតដោយ</th>
                            <th>ការបង់ប្រាក់</th>
                            <th class="text-center">ព្រីន</th>
                            <th class="text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $returnParam = 'return=' . urlencode(request()->fullUrl()); @endphp
                        @forelse ($invoices as $invoice)
                            @php $rowTotalKhr = $calcTotalKhr($invoice); @endphp
                            <tr>
                                <td class="invoice-select-cell">
                                    <input type="checkbox" class="invoice-select" value="{{ $invoice->id }}">
                                </td>
                                <td>
                                    <a href="{{ route('invoices.show', $invoice) }}?{{ $returnParam }}" class="invoice-number">
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
                                <td>
                                    <div class="fw-bold">៛{{ number_format($rowTotalKhr, 0) }}</div>
                                    <div class="text-muted small fw-bold">${{ number_format($invoice->total_amount, 2) }}</div>
                                </td>
                                <td class="text-muted">{{ $invoice->invoice_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                <td class="text-muted">{{ $invoice->order?->user?->name ?? 'N/A' }}</td>
                                <td>
                                    @if($invoice->order?->payment_status === 'paid')
                                        <span class="status-pill status-paid">
                                            <i class="fas fa-check-circle"></i> បានទូទាត់
                                        </span>
                                    @elseif($invoice->order?->payment_status === 'partial')
                                        <span class="status-pill status-draft">
                                            <i class="fas fa-adjust"></i> បង់មួយផ្នែក
                                        </span>
                                    @else
                                        <span class="status-pill status-draft">
                                            <i class="fas fa-clock"></i> មិនទាន់ទូទាត់
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(auth()->user()->isAuditor())
                                        <label class="printed-checkbox">
                                            <input type="checkbox" disabled {{ $invoice->printed_at ? 'checked' : '' }}>
                                            <span class="printed-label">{{ $invoice->printed_at ? 'បានព្រីន' : 'មិនទាន់បានព្រីន' }}</span>
                                        </label>
                                    @else
                                        <form method="POST" action="{{ route('invoices.toggle-printed', $invoice) }}" class="m-0">
                                            @csrf
                                            <label class="printed-checkbox">
                                                <input type="checkbox" onchange="this.form.submit()"
                                                    {{ $invoice->printed_at ? 'checked' : '' }}>
                                                <span class="printed-label">{{ $invoice->printed_at ? 'បានព្រីន' : 'មិនទាន់បានព្រីន' }}</span>
                                            </label>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-row">
                                        <a href="{{ route('invoices.show', $invoice) }}?{{ $returnParam }}" class="icon-action" title="មើល">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('invoices.print', $invoice) }}?{{ $returnParam }}" class="icon-action icon-print"
                                            title="ព្រីន ">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if ($invoice->status !== 'paid' && !auth()->user()->isAuditor())
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
                                <td colspan="10">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <div class="empty-state-title">មិនមានវិក្ក័យបត្រ</div>

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
    </div>
    <div class="pager-wrap">{{ $invoices->links('pagination::bootstrap-5') }}
    </div>
    @push('scripts')
        <script>
            (function () {
                const form = document.getElementById('invoiceFilter');
                if (!form) return;

                const search = form.querySelector('input[name="search"]');
                const controls = form.querySelectorAll('select[name="status"], select[name="user_id"], select[name="printed"], input[name="date"]');
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

            (function () {
                const selectAll = document.getElementById('selectAllInvoices');
                const printBtn = document.getElementById('printSelectedBtn');
                const countLabel = document.getElementById('selectedCount');
                const getCheckboxes = () => Array.from(document.querySelectorAll('.invoice-select'));

                function refreshState() {
                    const checkboxes = getCheckboxes();
                    const checked = checkboxes.filter((cb) => cb.checked);

                    countLabel.textContent = checked.length;
                    printBtn.disabled = checked.length === 0;

                    if (selectAll) {
                        selectAll.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
                        selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
                    }
                }

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        getCheckboxes().forEach((cb) => { cb.checked = selectAll.checked; });
                        refreshState();
                    });
                }

                getCheckboxes().forEach((cb) => cb.addEventListener('change', refreshState));

                printBtn.addEventListener('click', function () {
                    const ids = getCheckboxes().filter((cb) => cb.checked).map((cb) => cb.value);
                    if (!ids.length) return;

                    const params = new URLSearchParams();
                    ids.forEach((id) => params.append('ids[]', id));
                    params.append('return', window.location.href);

                    window.location.href = '{{ route('invoices.print-bulk') }}?' + params.toString();
                });

                refreshState();
            })();

        </script>
    @endpush
@endsection