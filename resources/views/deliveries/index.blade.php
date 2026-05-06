@extends('layouts.app')

@section('title', 'Deliveries')

@push('styles')
    <style>
        .delivery-page {
            --accent: #e85d24;
            --accent-dark: #d94a10;
            --surface: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
        }

        .delivery-header {
            align-items: center;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .delivery-title {
            color: var(--text);
            font-size: 28px;
            font-weight: 800;
            margin: 0;
        }

        .delivery-subtitle {
            color: var(--muted);
            margin: 6px 0 0;
        }

        .delivery-btn {
            align-items: center;
            border: 0;
            border-radius: 8px;
            display: inline-flex;
            font-weight: 700;
            gap: 8px;
            justify-content: center;
            min-height: 40px;
            padding: 9px 14px;
            text-decoration: none;
            white-space: nowrap;
        }

        .delivery-btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
        }

        .delivery-btn-primary:hover {
            color: #fff;
            box-shadow: 0 8px 18px rgba(232, 93, 36, 0.22);
            transform: translateY(-1px);
        }

        .delivery-toolbar {
            align-items: center;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex;
            gap: 12px;
            justify-content: flex-start;
            margin-bottom: 16px;
            padding: 14px;
        }

        .search-wrap {
            align-items: center;
            display: flex;
            flex: 1;
            max-width: 420px;
            position: relative;
        }

        .date-filter,
        .date-range {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-btn {
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            color: var(--text);
            cursor: pointer;
            font-weight: 700;
            padding: 8px 12px;
        }

        .filter-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .date-range label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--muted);
        }

        .date-range input[type="date"] {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 10px;
            background: #fff;
            color: var(--text);
        }

        .search-wrap i {
            color: var(--muted);
            left: 13px;
            position: absolute;
        }

        .delivery-search {
            border: 1px solid var(--border);
            border-radius: 8px;
            min-height: 42px;
            padding: 10px 12px 10px 38px;
            width: 100%;
        }

        .delivery-search:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(232, 93, 36, 0.12);
            outline: none;
        }

        .delivery-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .delivery-table {
            margin: 0;
            width: 100%;
        }

        .delivery-table th {
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            padding: 14px 16px;
            text-transform: uppercase;
        }

        .delivery-table td {
            border-bottom: 1px solid #f1f3f5;
            color: var(--text);
            padding: 15px 16px;
            vertical-align: middle;
        }

        .delivery-name {
            font-weight: 800;
        }

        .delivery-desc {
            color: var(--muted);
            font-size: 18px;
            margin-top: 4px;
            max-width: 420px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            justify-content: flex-end;
        }

        .action-btn {
            align-items: center;
            border: 0;
            border-radius: 8px;
            color: #fff;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            min-height: 34px;
            padding: 7px 10px;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s, transform 0.15s;
        }

        .action-view {
            background: #16a34a;
        }

        .action-view:hover {
            background: #0f7a33;
        }

        .action-view.active {
            background: #0f7a33;
        }

        .action-edit {
            background: #2563eb;
        }

        .action-edit:hover {
            background: #1d4ed8;
        }

        .action-delete {
            background: #dc2626;
        }

        .action-delete:hover {
            background: #b91c1c;
        }

        /* ── Inline detail row ── */
        .detail-row {
            display: none;
        }

        .detail-row.open {
            display: table-row;
        }

        .detail-panel {
            background: #fdf8f6;
            border-left: 3px solid var(--accent);
            padding: 16px 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px 24px;
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .detail-item label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: var(--muted);
            display: block;
            margin-bottom: 4px;
        }

        .detail-item span {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
        }

        .detail-item span.accent {
            color: var(--accent);
        }

        .empty-state {
            padding: 56px 20px;
            text-align: center;
        }

        .empty-state i {
            color: #d1d5db;
            font-size: 46px;
            margin-bottom: 14px;
        }

        @media (max-width: 768px) {

            .delivery-header,
            .delivery-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .search-wrap {
                max-width: none;
            }
        }
    </style>
@endpush

@section('content')
<div class="container-fluid py-4 delivery-page">
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="delivery-header">
            <div>
                <h1 class="delivery-title">ការដឹកជញ្ជូន</h1>

            </div>
            <a href="{{ route('deliveries.create') }}" class="delivery-btn delivery-btn-primary">
                <i class="fas fa-plus"></i> បង្កើតថ្មី
            </a>
        </div>

        <form class="delivery-toolbar" method="GET" action="{{ route('deliveries.index') }}">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="delivery-search" placeholder="Search ..."
                    oninput="filterTable()">
            </div>
            <input type="hidden" name="filter" id="deliveryFilterInput" value="{{ $filter ?? '' }}">
            <div class="date-filter">
                <button type="button" class="filter-btn" data-filter="today">ថ្ងៃនេះ</button>
                <button type="button" class="filter-btn" data-filter="yesterday">ម្សិលមិញ</button>
                <button type="button" class="filter-btn" id="clearDateFilter">Clear</button>
            </div>
            <div class="date-range">
                <label>From
                    <input type="date" name="start_date" id="startDate" value="{{ $startDate ?? '' }}">
                </label>
                <label>To
                    <input type="date" name="end_date" id="endDate" value="{{ $endDate ?? '' }}">
                </label>
                <button type="submit" class="delivery-btn delivery-btn-primary">Apply</button>
            </div>

        </form>

        <div class="delivery-card">
            <div class="table-responsive">
                <table class="delivery-table">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>ប្រភេទដឹកជញ្ជូន</th>
                            <th style="width: 150px;">តម្លៃ/កេស</th>
                            <th style="width: 140px;">ចំនួនវិក្ក័យបត្រ</th>
                            <th class="text-end" style="width: 280px;">ផ្សេងៗ</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($deliveries as $delivery)
                            <tr data-search="{{ strtolower($delivery->delivery_name . ' ' . $delivery->delivery_desc) }}">
                                <td style="font-weight: 800; color: var(--accent);">{{ $delivery->id }}</td>
                                <td>
                                    <div class="delivery-name">{{ $delivery->delivery_name }}</div>
                                    <div class="delivery-desc">{{ $delivery->delivery_desc ?: 'No note added' }}</div>
                                </td>
                                <td style="font-weight: 800; color: var(--accent);">
                                    ៛{{ number_format($delivery->delivery_price_khr, 0) }}
                                </td>
                                <td style="text-align: center; font-weight: 700; color: var(--accent);">
                                    {{ $delivery->orders_count ?? 0 }}
                                </td>
                                <td>
                                    <div class="actions">
                                        <button type="button" class="action-btn action-view"
                                            onclick="toggleDetail(this, 'detail-{{ $delivery->id }}')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <a href="{{ route('deliveries.edit', $delivery) }}" class="action-btn action-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST"
                                            data-delete="Delivery" data-item-name="{{ $delivery->delivery_name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn action-delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Inline detail row --}}
                            <tr class="detail-row" id="detail-{{ $delivery->id }}">
                                <td colspan="5" style="padding: 0;">
                                    <div class="detail-panel">
                                        <div class="detail-item">
                                            <label>ប្រភេទដឹកជញ្ជូន</label>
                                            <span>{{ $delivery->delivery_name }}</span>
                                        </div>

                                        <div class="detail-item">
                                            <label>តម្លៃក្នុង ១ កេស</label>
                                            <span class="accent">៛{{ number_format($delivery->delivery_price_khr, 0) }}</span>
                                        </div>

                                        <div class="detail-item">
                                            <label>ចំនួនវិក្ក័យបត្រ</label>
                                            <span class="accent">{{ $delivery->orders_count ?? 0 }}</span>
                                        </div>
                                        <!-- <div class="detail-item">
                                                    <label>Created At</label>
                                                    <span>{{ $delivery->created_at->format('d M Y') }}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <label>Updated At</label>
                                                    <span>{{ $delivery->updated_at->format('d M Y') }}</span>
                                                </div> -->
                                        <div class="detail-item" style="align-self: center;">
                                            <a href="{{ route('deliveries.show', array_merge([$delivery], request()->only('filter', 'start_date', 'end_date'))) }}"
                                                class="delivery-btn delivery-btn-primary"
                                                style="font-size: 12px; min-height: 32px; padding: 6px 12px;">
                                                <i class="fas fa-external-link-alt"></i> ពិនិត្យ
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-truck"></i>
                                        <h5>No delivery options yet</h5>
                                        <p class="text-muted mb-3">Create one so staff can choose delivery during order
                                            creation.</p>
                                        <a href="{{ route('deliveries.create') }}" class="delivery-btn delivery-btn-primary">
                                            បង្កើតថ្មី
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $deliveries->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleDetail(btn, id) {
            const detail = document.getElementById(id);
            const isOpen = detail.classList.toggle('open');
            btn.classList.toggle('active', isOpen);
            btn.innerHTML = isOpen
                ? '<i class="fas fa-eye-slash"></i> Hide'
                : '<i class="fas fa-eye"></i> View';
        }

        function filterTable() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('#tableBody tr[data-search]').forEach(row => {
                const match = row.getAttribute('data-search').includes(search);
                row.style.display = match ? '' : 'none';

                // also hide/close the detail row when filtered out
                const btn = row.querySelector('.action-view');
                const onclickAttr = btn?.getAttribute('onclick') ?? '';
                const idMatch = onclickAttr.match(/'([^']+)'/);
                if (idMatch) {
                    const detailRow = document.getElementById(idMatch[1]);
                    if (detailRow && !match) {
                        detailRow.classList.remove('open');
                        if (btn) {
                            btn.classList.remove('active');
                            btn.innerHTML = '<i class="fas fa-eye"></i> View';
                        }
                    }
                    if (detailRow) detailRow.style.display = match ? '' : 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const todayButton = document.querySelector('.filter-btn[data-filter="today"]');
            const yesterdayButton = document.querySelector('.filter-btn[data-filter="yesterday"]');
            const clearButton = document.getElementById('clearDateFilter');
            const filterInput = document.getElementById('deliveryFilterInput');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');

            todayButton?.addEventListener('click', function () {
                filterInput.value = 'today';
                startDate.value = '';
                endDate.value = '';
                this.closest('form').submit();
            });

            yesterdayButton?.addEventListener('click', function () {
                filterInput.value = 'yesterday';
                startDate.value = '';
                endDate.value = '';
                this.closest('form').submit();
            });

            clearButton?.addEventListener('click', function () {
                filterInput.value = '';
                startDate.value = '';
                endDate.value = '';
                this.closest('form').submit();
            });

            [startDate, endDate].forEach(input => {
                input?.addEventListener('change', function () {
                    filterInput.value = '';
                });
            });
        });
    </script>
@endpush
