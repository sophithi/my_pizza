@extends('layouts.app')

@section('title', 'វិក្ក័យបត្របានលុប')

@push('styles')
    <style>
        .trash-page {
            --accent: #e85d24;
            --accent-dark: #d94a10;
            --border: #e5e7eb;
            --muted: #6b7280;
            --surface: #fff;
            --text: #111827;
        }

        .trash-header {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .trash-title {
            color: var(--text);
            font-size: 28px;
            font-weight: 900;
            margin: 0;
        }

        .trash-subtitle {
            color: var(--muted);
            margin: 4px 0 0;
            font-size: 13.5px;
        }

        .trash-btn {
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
            transition: all 0.2s ease;
        }

        .trash-btn-soft {
            background: #f3f4f6;
            color: #374151;
        }

        .trash-btn-soft:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .trash-btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
        }

        .trash-btn-primary:hover {
            color: #fff;
            box-shadow: 0 4px 12px rgba(232, 93, 36, .25);
        }

        /* Filter Card */
        .filter-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
            margin-bottom: 16px;
            padding: 16px;
        }

        .quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }

        .quick-filter {
            align-items: center;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: #4b5563;
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            gap: 7px;
            min-height: 38px;
            padding: 7px 14px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .quick-filter:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            color: #1e293b;
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
            grid-template-columns: minmax(220px, 1.5fr) 190px 180px 160px auto auto;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            min-height: 42px;
            border-radius: 8px;
            font-size: 13.5px;
        }

        .date-field {
            position: relative;
        }

        .date-field .form-control {
            width: 100%;
        }

        .date-placeholder {
            color: var(--muted);
            font-size: 13px;
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

        .trash-table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
            overflow: hidden;
        }

        .trash-table th {
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            padding: 14px 16px;
            text-transform: uppercase;
        }

        .trash-table td {
            border-bottom: 1px solid #f1f3f5;
            color: var(--text);
            padding: 14px 16px;
            vertical-align: middle;
        }

        .trash-badge {
            background: #fee2e2;
            border-radius: 999px;
            color: #991b1b;
            display: inline-flex;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 8px;
            gap: 4px;
            align-items: center;
            margin-top: 4px;
        }

        .reason-badge {
            display: inline-block;
            max-width: 240px;
            font-size: 12px;
            font-weight: 600;
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 8px;
            padding: 5px 10px;
            line-height: 1.4;
            word-break: break-word;
        }

        .action-row {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-btn {
            align-items: center;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            padding: 7px 12px;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .action-btn-restore {
            background: #d1fae5;
            color: #065f46;
        }

        .action-btn-restore:hover {
            background: #a7f3d0;
            color: #065f46;
        }

        .action-btn-forget {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-btn-forget:hover {
            background: #fecaca;
            color: #991b1b;
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

        @media (max-width: 992px) {
            .filter-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .trash-header {
                align-items: stretch;
                flex-direction: column;
            }
            .trash-btn {
                width: 100%;
            }
            .filter-row {
                grid-template-columns: 1fr;
            }
        }

        .pager-wrap {
            margin-top: 16px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 trash-page">
        <div class="trash-header">
            <div>
                <h2 class="trash-title">
                    <i class="fas fa-trash-alt text-danger me-2"></i> វិក្ក័យបត្របានលុប 
                </h2>
                <p class="trash-subtitle">មើល ស្វែងរក និងស្តារវិក្ក័យបត្រដែលបានលុបឡើងវិញ (Admin / Manager)</p>
            </div>
            <a href="{{ route('invoices.index') }}" class="trash-btn trash-btn-soft">
                <i class="fas fa-arrow-left"></i> ត្រឡប់ទៅវិក្ក័យបត្រ
            </a>
        </div>
        <form method="GET" action="{{ route('invoices.trash') }}" class="filter-card" id="trashFilter">
            <div class="quick-filters">
                <a href="{{ route('invoices.trash', request()->except('period', 'date', 'page')) }}"
                    class="quick-filter {{ !request('period') && !request('date') ? 'active' : '' }}">
                    <i class="fas fa-list"></i> ទាំងអស់
                </a>
                <a href="{{ route('invoices.trash', array_merge(request()->except('period', 'date', 'page'), ['period' => 'today'])) }}"
                    class="quick-filter {{ request('period') === 'today' ? 'active' : '' }}">
                    <i class="fas fa-calendar-day"></i> ថ្ងៃនេះ
                </a>
                <a href="{{ route('invoices.trash', array_merge(request()->except('period', 'date', 'page'), ['period' => 'yesterday'])) }}"
                    class="quick-filter {{ request('period') === 'yesterday' ? 'active' : '' }}">
                    <i class="fas fa-calendar-minus"></i> ម្សិលមិញ
                </a>
                <a href="{{ route('invoices.trash', array_merge(request()->except('period', 'date', 'page'), ['period' => 'month'])) }}"
                    class="quick-filter {{ request('period') === 'month' ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> ខែនេះ
                </a>
            </div>
            <div class="filter-row">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="ស្វែងរកលេខវិក្ក័យបត្រ, អតិថិជន, មូលហេតុ...">

                <select name="reason" class="form-select">
                    <option value="all">គ្រប់មូលហេតុនៃការលុប</option>
                    <option value="បោះបង់" {{ request('reason') === 'បោះបង់' ? 'selected' : '' }}> អតិថិជនសុំបោះបង់</option>
                    <option value="ស្ទួន" {{ request('reason') === 'ស្ទួន' ? 'selected' : '' }}> កុម្ម៉ង់ស្ទួន / ច្រឡំ</option>
                    <option value="ប្តូរមុខទំនិញ" {{ request('reason') === 'ប្តូរមុខទំនិញ' ? 'selected' : '' }}> ប្តូរមុខទំនិញ / តម្លៃ</option>
                    <option value="សាកល្បង" {{ request('reason') === 'សាកល្បង' ? 'selected' : '' }}> តេស្តសាកល្បង</option>
                </select>

                <select name="deleted_by" class="form-select">
                    <option value="all">លុបដោយបុគ្គលិកទាំងអស់</option>
                    @if(isset($users))
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" {{ (string) request('deleted_by') === (string) $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <div class="date-field {{ request('date') ? 'has-value' : '' }}">
                    <input type="date" name="date" value="{{ request('date') }}" class="form-control"
                        title="កាលបរិច្ឆេទលុប">
                    <span class="date-placeholder">កាលបរិច្ឆេទលុប</span>
                </div>
                <button type="submit" class="trash-btn trash-btn-primary">
                    <i class="fas fa-search"></i> ស្វែងរក
                </button>
                @if(request('search') || request('period') || request('date') || (request('deleted_by') && request('deleted_by') !== 'all') || (request('reason') && request('reason') !== 'all'))
                    <a href="{{ route('invoices.trash') }}" class="trash-btn trash-btn-soft" title="សម្អាតតម្រង">
                        <i class="fas fa-rotate-left"></i> សម្អាត
                    </a>
                @endif
            </div>
        </form>

        <div class="trash-table-card">
            <div class="table-responsive">
                <table class="table trash-table mb-0">
                    <thead>
                        <tr>
                            <th>លេខវិក្ក័យបត្រ</th>
                            <th>អតិថិជន</th>
                            <th>បញ្ជាទិញ</th>
                            <th>ទឹកប្រាក់</th>
                            <th>មូលហេតុនៃការលុប</th>
                            <th>បានលុបដោយ</th>
                            <th>បានលុបនៅ</th>
                            <th class="text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td>
                                    <span class="fw-bold fs-6">{{ $invoice->invoice_number }}</span>
                                    <div><span class="trash-badge"><i class="fas fa-trash-alt"></i> បានលុប</span></div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $invoice->order?->customer?->name ?? 'N/A' }}</div>
                                    @if($invoice->order?->customer?->phone)
                                        <div class="text-muted small"><i class="fas fa-phone-alt me-1 text-secondary" style="font-size: 11px;"></i>{{ $invoice->order->customer->phone }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">#ORD-{{ str_pad($invoice->order_id, 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-danger">${{ number_format($invoice->total_amount, 2) }}</div>
                                    <small class="text-muted" style="font-size: 11px;">(៛{{ number_format($invoice->order?->totalKhr() ?? ($invoice->total_amount * 4000)) }})</small>
                                </td>
                                <td>
                                    @if($invoice->delete_reason)
                                        <div class="reason-badge">
                                            <i class="fas fa-comment-dots text-danger me-1"></i> {{ $invoice->delete_reason }}
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic">— មិនមានមូលហេតុ —</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $invoice->deletedBy?->name ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium text-muted" style="font-size: 13px;">{{ $invoice->deleted_at?->format('d/m/Y') }}</div>
                                    <small class="text-muted" style="font-size: 11px;">{{ $invoice->deleted_at?->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="action-row">
                                        <form method="POST" action="{{ route('invoices.restore', $invoice->id) }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="action-btn action-btn-restore"
                                                onclick="return confirm('តើអ្នកពិតជាចង់ស្តារវិក្ក័យប័ត្រ {{ $invoice->invoice_number }} នេះឡើងវិញមែនទេ?');">
                                                <i class="fas fa-rotate-left"></i> ស្តារ
                                            </button>
                                        </form>
                                        @auth
                                            @if(auth()->user()->isAdmin())
                                                <form method="POST" action="{{ route('invoices.force-delete', $invoice->id) }}" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn action-btn-forget"
                                                        onclick="return confirm('⚠️ ការព្រមាន៖ លុបជាអចិន្ត្រៃយ៍? សកម្មភាពនេះមិនអាចត្រឡប់វិញបានទេ!');">
                                                        <i class="fas fa-ban"></i> លុបអចិន្ត្រៃយ៍
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-trash-restore"></i>
                                        </div>
                                        <div class="empty-state-title">មិនមានវិក្ក័យបត្រក្នុងធុងសំរាមទេ</div>
                                        <p class="text-muted small mb-0">មិនមានទិន្នន័យស្របតាមលក្ខខណ្ឌស្វែងរក</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pager-wrap">{{ $invoices->links('pagination::bootstrap-5') }}</div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('trashFilter');
            if (!form) return;

            const search = form.querySelector('input[name="search"]');
            const controls = form.querySelectorAll('select[name="reason"], select[name="deleted_by"], input[name="date"]');
            const dateField = form.querySelector('.date-field');
            const dateInput = form.querySelector('input[name="date"]');
            let timer = null;

            const submit = () => form.submit();

            if (search) {
                search.addEventListener('input', function () {
                    clearTimeout(timer);
                    timer = setTimeout(submit, 500);
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
