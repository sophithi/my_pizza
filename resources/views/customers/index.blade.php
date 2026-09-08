@extends('layouts.app')

@section('title', 'អតិថិជន')

@push('styles')
    <style>
        .customer-page {
            --accent: #e85d24;
            --accent-hover: #d04b16;
            --surface: #ffffff;
            --border: #e2e8f0;
            --border-subtle: #f1f5f9;
            --text: #0f172a;
            --text-secondary: #475569;
            --muted: #64748b;
            --bg-soft: #f8fafc;
        }

        /* ===== HEADER ===== */
        .customer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .customer-title-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .customer-title {
            color: var(--text);
            font-size: 24px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.3px;
        }

        .customer-count-badge {
            background: #f1f5f9;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            border: 1px solid var(--border);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .customer-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-weight: 700;
            font-size: 13px;
            height: 38px;
            padding: 0 14px;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .customer-btn-soft {
            background: var(--surface);
            color: var(--text-secondary);
            border-color: var(--border);
        }

        .customer-btn-soft:hover {
            background: #f8fafc;
            color: var(--text);
            border-color: #cbd5e1;
        }

        .customer-btn-map {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .customer-btn-map:hover {
            background: #dbeafe;
            color: #1e40af;
            border-color: #93c5fd;
        }

        .customer-btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 2px 6px rgba(232, 93, 36, 0.2);
        }

        .customer-btn-primary:hover {
            background: var(--accent-hover);
            color: #fff;
            box-shadow: 0 4px 12px rgba(232, 93, 36, 0.3);
            transform: translateY(-1px);
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-bottom: 18px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: transform 0.15s ease, border-color 0.15s ease;
        }

        .stat-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .stat-icon.total {
            background: #eff6ff;
            color: #2563eb;
        }

        .stat-icon.active {
            background: #ecfdf5;
            color: #16a34a;
        }

        .stat-icon.inactive {
            background: #fef2f2;
            color: #dc2626;
        }

        .stat-label {
            color: var(--muted);
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stat-value {
            color: var(--text);
            font-size: 22px;
            font-weight: 800;
            line-height: 1.1;
            margin-top: 3px;
        }

        /* ===== FILTER TOOLBAR ===== */
        .filter-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            margin-bottom: 18px;
            padding: 10px 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .filter-row {
            display: grid;
            align-items: center;
            gap: 10px;
            grid-template-columns: minmax(240px, 1fr) 150px 160px 150px auto;
        }

        .customer-search {
            position: relative;
            display: flex;
            align-items: center;
        }

        .customer-search .search-icon {
            position: absolute;
            left: 12px;
            color: var(--muted);
            font-size: 13px;
            pointer-events: none;
        }

        .customer-search .form-control {
            padding-left: 36px;
            height: 38px;
            font-size: 13px;
            border-radius: 8px;
            border-color: var(--border);
        }

        .customer-search .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(232, 93, 36, 0.12);
        }

        .filter-card .form-select {
            height: 38px;
            font-size: 13px;
            border-radius: 8px;
            border-color: var(--border);
            color: var(--text-secondary);
            font-weight: 500;
        }

        .filter-card .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(232, 93, 36, 0.12);
        }

        .btn-reset {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 38px;
            width: 38px;
            border-radius: 8px;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--muted);
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-reset:hover {
            background: #f1f5f9;
            color: var(--text);
            border-color: #cbd5e1;
        }

        /* ===== TABLE CARD ===== */
        .customer-table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .customer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .customer-table th {
            background: #fafbfc;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 11.5px;
            font-weight: 700;
            padding: 11px 14px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .customer-table td {
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text);
            padding: 10px 14px;
            vertical-align: middle;
        }

        .customer-table tbody tr {
            transition: background 0.12s ease;
        }

        .customer-table tbody tr:hover {
            background: #fafbfc;
        }

        .customer-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ===== BADGES & PILLS ===== */
        .channel-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .channel-fb {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .channel-tg {
            background: #e0f2fe;
            color: #0369a1;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-badge.active {
            background: #ecfdf5;
            color: #15803d;
        }

        .status-badge.inactive {
            background: #fef2f2;
            color: #b91c1c;
        }

        .order-count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 24px;
            padding: 2px 8px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 11.5px;
            font-weight: 700;
            border-radius: 6px;
        }

        .salesperson-tag {
            display: inline-block;
            background: #f8fafc;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            white-space: nowrap;
        }

        /* ===== ROW ACTIONS ===== */
        .action-btn-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .btn-row-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 0;
            background: transparent;
            color: var(--muted);
            text-decoration: none;
            transition: all 0.12s ease;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-row-action:hover {
            background: #f1f5f9;
            color: var(--text);
        }

        .btn-row-action.order-act:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .btn-row-action.view-act:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        .btn-row-action.edit-act:hover {
            background: #fff7ed;
            color: var(--accent);
        }

        .btn-row-action.del-act:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            padding: 40px 16px;
            text-align: center;
        }

        .empty-state-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #fff7ed;
            color: var(--accent);
            font-size: 20px;
            margin-bottom: 10px;
        }

        .empty-state-title {
            color: var(--text);
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .empty-state-text {
            color: var(--muted);
            font-size: 12.5px;
            margin: 0;
        }

        /* ===== PAGER-WRAPPER ===== */
        .pager-wrap {
            display: flex;
            justify-content: center;
            margin-top: 18px;
        }

        .pager-wrap .pagination {
            display: flex;
            gap: 4px;
            margin-bottom: 0;
            flex-wrap: wrap;
            align-items: center;
            list-style: none;
            padding: 0;
        }

        .pager-wrap .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 11px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: #ffffff;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .pager-wrap .page-item .page-link:hover,
        .pager-wrap .page-item .page-link:focus {
            background: #fff7ed;
            border-color: #fdba74;
            color: var(--accent);
            box-shadow: none;
        }

        .pager-wrap .page-item.active .page-link {
            background: var(--accent);
            border-color: var(--accent);
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(232, 93, 36, 0.25);
        }

        .pager-wrap .page-item.disabled .page-link {
            background: #f8fafc;
            border-color: #edf2f7;
            color: #94a3b8;
            pointer-events: none;
            cursor: not-allowed;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .filter-row {
                grid-template-columns: 1fr 1fr;
            }
            .customer-search {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 768px) {
            .customer-header {
                flex-direction: column;
                align-items: stretch;
            }
            .header-actions {
                justify-content: flex-start;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .filter-row {
                grid-template-columns: 1fr;
            }
            .btn-reset {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .pager-wrap .page-item .page-link {
                min-width: 30px;
                height: 30px;
                padding: 0 8px;
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-3 customer-page">
        {{-- Header --}}
        <div class="customer-header">
            <div class="customer-title-group">
                <h2 class="customer-title">អតិថិជន</h2>
                <span class="customer-count-badge">{{ number_format($stats['total']) }}</span>
            </div>
            <div class="header-actions">
                <a href="{{ route('customer-locations.index') }}" class="customer-btn customer-btn-map" title="ផែនទី Google Maps">
                    <i class="fas fa-map-marked-alt"></i> <span>ផែនទី</span>
                </a>
                <a href="{{ route('customers.export.excel', request()->query()) }}" class="customer-btn customer-btn-soft" title="នាំចេញ Excel">
                    <i class="fas fa-file-excel text-success"></i> <span>Excel</span>
                </a>
                <a href="{{ route('customers.export.pdf', request()->query()) }}" class="customer-btn customer-btn-soft" target="_blank" title="នាំចេញ PDF">
                    <i class="fas fa-file-pdf text-danger"></i> <span>PDF</span>
                </a>
                <a href="{{ route('customers.create') }}" class="customer-btn customer-btn-primary">
                    <i class="fas fa-plus"></i> <span>បន្ថែមអតិថិជន</span>
                </a>
            </div>
        </div>

        {{-- Success Alert --}}
        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 px-3 small mb-3 rounded-3" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ $message }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" style="padding: 10px;"></button>
            </div>
        @endif

        {{-- Stats Grid --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-label">អតិថិជនសរុប</div>
                    <div class="stat-value">{{ number_format($stats['total']) }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <div class="stat-label">សកម្ម</div>
                    <div class="stat-value text-success">{{ number_format($stats['active']) }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon inactive">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <div class="stat-label">អសកម្ម</div>
                    <div class="stat-value text-muted">{{ number_format($stats['inactive']) }}</div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('customers.index') }}" class="filter-card">
            <div class="filter-row">
                <div class="customer-search">
                    <i class="fas fa-search search-icon"></i>
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="ស្វែងរកអតិថិជន..." autocomplete="off">
                </div>
                <select name="type" class="form-select">
                    <option value="all">ប្រភពទាំងអស់</option>
                    <option value="facebook" {{ request('type') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                    <option value="telegram" {{ request('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                </select>
                <select name="salesperson_id" class="form-select">
                    <option value="all">អ្នកលក់ទាំងអស់</option>
                    @foreach($salespersons as $s)
                        <option value="{{ $s->id }}" {{ request('salesperson_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="form-select">
                    <option value="all">ស្ថានភាពទាំងអស់</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>សកម្ម</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>អសកម្ម</option>
                </select>
                <a href="{{ route('customers.index') }}" class="btn-reset" title="សម្អាត">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>

        {{-- Customers Table --}}
        <div class="customer-table-card">
            <div class="table-responsive">
                <table class="customer-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 75px;">ប្រភព</th>
                            <th>ឈ្មោះអតិថិជន</th>
                            <th>លេខទូរស័ព្ទ</th>
                            <th>អ្នកលក់</th>
                            <th>ទីតាំង</th>
                            <th class="text-center">កុម្ម៉ង់</th>
                            <th>ចំណាយ</th>
                            <th>កាលបរិច្ឆេទចុងក្រោយ</th>
                            <th>ស្ថានភាព</th>
                            <th class="text-center" style="width: 140px;">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>
                                    @if($customer->type == 'facebook')
                                        <span class="channel-badge channel-fb"><i class="fab fa-facebook-f"></i> FB</span>
                                    @elseif($customer->type == 'telegram')
                                        <span class="channel-badge channel-tg"><i class="fab fa-telegram-plane"></i> TG</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('customers.show', $customer) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $customer->name }}
                                    </a>
                                    @if($customer->notes)
                                        <div class="text-muted small text-truncate" style="max-width: 200px;" title="{{ $customer->notes }}">{{ $customer->notes }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->phone)
                                        <a href="tel:{{ $customer->phone }}" class="text-dark font-monospace text-decoration-none small">
                                            {{ $customer->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->salesperson)
                                        <span class="salesperson-tag">{{ $customer->salesperson->name }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-secondary small text-truncate d-inline-block" style="max-width: 130px;" title="{{ $customer->city ?? $customer->address }}">
                                        {{ $customer->city ?? $customer->address ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($customer->orders_count > 0)
                                        <span class="order-count-badge">{{ $customer->orders_count }}</span>
                                    @else
                                        <span class="text-muted small">0</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-dark">${{ number_format($customer->total_spent ?? 0, 2) }}</strong>
                                </td>
                                <td class="text-muted small">
                                    {{ $customer->last_order_at ? \Carbon\Carbon::parse($customer->last_order_at)->format('d/m/Y') : '—' }}
                                </td>
                                <td>
                                    @if($customer->status == 'active')
                                        <span class="status-badge active"><i class="fas fa-circle" style="font-size: 6px;"></i> សកម្ម</span>
                                    @elseif($customer->status == 'inactive')
                                        <span class="status-badge inactive"><i class="fas fa-circle" style="font-size: 6px;"></i> អសកម្ម</span>
                                    @else
                                        <span class="text-muted small">{{ $customer->status ?? '—' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-btn-group">
                                        <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}"
                                            class="btn-row-action order-act" title="បង្កើតការកុម្ម៉ង់">
                                            <i class="fas fa-cart-plus"></i>
                                        </a>
                                        <a href="{{ route('customers.show', $customer) }}" class="btn-row-action view-act" title="មើល">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customers.edit', $customer) }}" class="btn-row-action edit-act" title="កែប្រែ">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                                            data-delete="អតិថិជន" data-item-name="{{ $customer->name }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-row-action del-act" title="លុប">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="empty-state-title">រកមិនឃើញអតិថិជនទេ</div>
                                        <p class="empty-state-text">សាកល្បងសម្អាតតម្រង ឬបន្ថែមអតិថិជនថ្មី។</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="pager-wrap">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const form = document.querySelector('form[action="{{ route('customers.index') }}"]');
                if (!form) return;

                const input = form.querySelector('input[name="search"]');
                const selects = form.querySelectorAll('select[name="type"], select[name="salesperson_id"], select[name="status"]');
                const submit = () => form.submit();

                if (input) {
                    input.addEventListener('keydown', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            submit();
                        }
                    });
                }

                selects.forEach(function (sel) {
                    sel.addEventListener('change', function () {
                        submit();
                    });
                });
            })();
        </script>
    @endpush
@endsection
