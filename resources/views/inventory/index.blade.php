@extends('layouts.app')

@section('title', 'ស្តុកទំនិញ')

@push('styles')
    <style>
        .inventory-page {
            --accent: #e85d24;
            --accent-dark: #d94a10;
            --surface: #fff;
            --border: #e5e7eb;
            --text: #0f172a;
            --muted: #64748b;
            --success: #059669;
            --warning: #d97706;
            --danger: #dc2626;
        }

        .inventory-header {
            align-items: center;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .inventory-title {
            color: var(--text);
            font-size: 30px;
            font-weight: 800;
            margin: 0;
        }

        .inventory-subtitle {
            color: var(--muted);
            margin: 6px 0 0;
        }

        .inventory-btn {
            align-items: center;
            border: 0;
            border-radius: 8px;
            display: inline-flex;
            font-weight: 800;
            gap: 8px;
            justify-content: center;
            min-height: 42px;
            padding: 10px 15px;
            text-decoration: none;
            white-space: nowrap;
        }

        .inventory-btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            box-shadow: 0 10px 22px rgba(232, 93, 36, .18);
        }

        .inventory-btn-primary:hover {
            color: #fff;
            background: linear-gradient(135deg, var(--accent-dark), #b83a0a);
        }

        .inventory-stats {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
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
            font-size: 26px;
            font-weight: 900;
            margin-top: 6px;
        }

        .filter-card,
        .inventory-table-card,
        .empty-state {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
        }

        .filter-card {
            display: grid;
            gap: 10px;
            grid-template-columns: minmax(260px, 1fr) 180px 180px auto;
            margin-bottom: 16px;
            padding: 14px;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            border-color: #d9dee7;
            border-radius: 6px;
            min-height: 42px;
        }

        .inventory-table-card {
            overflow: hidden;
        }

        .inventory-table th {
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            padding: 14px 16px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .inventory-table td {
            border-bottom: 1px solid #edf0f4;
            color: var(--text);
            padding: 14px 16px;
            vertical-align: middle;
        }

        .product-name {
            font-weight: 800;
        }

        .product-meta {
            color: var(--muted);
            font-size: 12px;
            margin-top: 3px;
        }

        .stock-number {
            cursor: pointer;
            font-size: 18px;
            font-weight: 900;
            text-decoration: underline;
            text-decoration-color: rgba(232, 93, 36, .3);
            text-underline-offset: 4px;
        }

        .status-pill {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 900;
            gap: 6px;
            padding: 6px 10px;
            white-space: nowrap;
        }

        .status-good {
            background: #d1fae5;
            color: #065f46;
        }

        .status-low {
            background: #fef3c7;
            color: #92400e;
        }

        .status-out {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-row {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
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

        .icon-danger {
            color: var(--danger);
        }

        .empty-state {
            padding: 64px 20px;
            text-align: center;
        }

        .empty-state i {
            color: var(--accent);
            font-size: 42px;
            margin-bottom: 14px;
        }

        .empty-state h3 {
            color: var(--text);
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--muted);
            margin-bottom: 18px;
        }

        @media (max-width: 1000px) {
            .inventory-stats,
            .filter-card {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .inventory-header {
                align-items: stretch;
                flex-direction: column;
            }

            .inventory-stats,
            .filter-card {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $items = $inventories->getCollection();
    @endphp

    <div class="container-fluid py-4 inventory-page">
        <div class="inventory-header">
            <div>
                <h2 class="inventory-title">ស្តុកទំនិញ</h2>
                <p class="inventory-subtitle">តាមដានចំនួនទំនិញ កម្រិតស្តុក និងទីតាំងស្តុកសម្រាប់ការរៀបចំទំនិញ។</p>
            </div>
            <a href="{{ route('inventory.create') }}" class="inventory-btn inventory-btn-primary">
                <i class="fas fa-plus"></i> បន្ថែមស្តុក
            </a>
        </div>

        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="inventory-stats">
            <div class="stat-card">
                <div class="stat-label">មុខទំនិញសរុប</div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">មានក្នុងស្តុក</div>
                <div class="stat-value text-success">{{ number_format($stats['in_stock']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">ជិតអស់</div>
                <div class="stat-value" style="color:#d97706;">{{ number_format($stats['low_stock']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">អស់ស្តុក</div>
                <div class="stat-value text-danger">{{ number_format($stats['out_stock']) }}</div>
            </div>
        </div>

        @if($inventories->count())
            <div class="filter-card">
                <input type="search" id="inventorySearch" class="form-control" placeholder="ស្វែងរកទំនិញ ឬប្រភេទ..." autocomplete="off">
                <select id="statusFilter" class="form-select">
                    <option value="">គ្រប់ស្ថានភាព</option>
                    <option value="in">មានក្នុងស្តុក</option>
                    <option value="low">ជិតអស់</option>
                    <option value="out">អស់ស្តុក</option>
                </select>
                <select id="warehouseFilter" class="form-select">
                    <option value="">គ្រប់ទីតាំង</option>
                    @foreach($items->pluck('warehouse_location')->unique()->reject(fn($x) => !$x) as $warehouse)
                        <option value="{{ strtolower($warehouse) }}">{{ $warehouse }}</option>
                    @endforeach
                </select>
                <button type="button" class="inventory-btn inventory-btn-primary" onclick="exportInventoryCsv()">
                    <i class="fas fa-download"></i> ទាញយក
                </button>
            </div>

            <div class="inventory-table-card">
                <div class="table-responsive">
                    <table class="table inventory-table mb-0">
                        <thead>
                            <tr>
                                <th>ទំនិញ</th>
                                <th>ប្រភេទ</th>
                                <th>ទីតាំងស្តុក</th>
                                <th class="text-center">ចំនួន</th>
                                <th class="text-center">កម្រិតត្រូវបំពេញ</th>
                                <th class="text-center">ស្ថានភាព</th>
                                <th class="text-end">សកម្មភាព</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            @foreach($inventories as $inv)
                                @php
                                    $isOut = $inv->quantity <= 0;
                                    $isLow = !$isOut && $inv->quantity <= $inv->reorder_level;
                                    $status = $isOut ? 'out' : ($isLow ? 'low' : 'in');
                                    $statusClass = $isOut ? 'status-out' : ($isLow ? 'status-low' : 'status-good');
                                    $statusLabel = $isOut ? 'អស់ស្តុក' : ($isLow ? 'ជិតអស់' : 'មានក្នុងស្តុក');
                                @endphp
                                <tr data-name="{{ strtolower($inv->product?->name ?? '') }}"
                                    data-category="{{ strtolower($inv->product?->category ?? '') }}"
                                    data-status="{{ $status }}"
                                    data-warehouse="{{ strtolower($inv->warehouse_location ?? '') }}">
                                    <td>
                                        <div class="product-name">{{ $inv->product?->name ?? 'មិនមានឈ្មោះ' }}</div>
                                        <div class="product-meta">{{ $inv->product?->sku ?? 'SKU មិនមាន' }}</div>
                                    </td>
                                    <td>{{ $inv->product?->category ?? 'មិនមាន' }}</td>
                                    <td>{{ $inv->warehouse_location ?? 'មិនមាន' }}</td>
                                    <td class="text-center">
                                        <span class="stock-number" onclick="openQuickUpdate({{ $inv->id }}, {{ $inv->quantity }})">
                                            {{ number_format($inv->quantity) }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ number_format($inv->reorder_level) }}</td>
                                    <td class="text-center">
                                        <span class="status-pill {{ $statusClass }}">
                                            <i class="fas fa-circle" style="font-size:7px;"></i> {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-row">
                                            <a href="{{ route('inventory.show', $inv) }}" class="icon-action" title="មើល">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('inventory.edit', $inv) }}" class="icon-action" title="កែប្រែ">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="icon-action icon-danger"
                                                onclick="deleteInventory({{ $inv->id }}, @js($inv->product?->name ?? 'ទំនិញ'))"
                                                title="លុប">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $inventories->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h3>មិនទាន់មានស្តុកទំនិញ</h3>
                <p>ចាប់ផ្តើមបង្កើតស្តុក ដើម្បីតាមដានចំនួនទំនិញក្នុងហាង។</p>
                <a href="{{ route('inventory.create') }}" class="inventory-btn inventory-btn-primary">
                    <i class="fas fa-plus"></i> បន្ថែមស្តុកដំបូង
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('inventorySearch');
            const statusFilter = document.getElementById('statusFilter');
            const warehouseFilter = document.getElementById('warehouseFilter');
            const tableBody = document.getElementById('inventoryTableBody');

            if (!searchInput || !statusFilter || !warehouseFilter || !tableBody) return;

            function filterTable() {
                const search = searchInput.value.toLowerCase();
                const status = statusFilter.value;
                const warehouse = warehouseFilter.value;

                tableBody.querySelectorAll('tr').forEach(row => {
                    const rowName = row.dataset.name || '';
                    const rowCategory = row.dataset.category || '';
                    const rowStatus = row.dataset.status || '';
                    const rowWarehouse = row.dataset.warehouse || '';
                    const matchesSearch = !search || rowName.includes(search) || rowCategory.includes(search);
                    const matchesStatus = !status || rowStatus === status;
                    const matchesWarehouse = !warehouse || rowWarehouse === warehouse;

                    row.style.display = matchesSearch && matchesStatus && matchesWarehouse ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterTable);
            statusFilter.addEventListener('change', filterTable);
            warehouseFilter.addEventListener('change', filterTable);
        });

        function exportInventoryCsv() {
            const tableBody = document.getElementById('inventoryTableBody');
            if (!tableBody) return;

            let csv = 'ទំនិញ,ប្រភេទ,ទីតាំង,ចំនួន,កម្រិតត្រូវបំពេញ,ស្ថានភាព\n';

            tableBody.querySelectorAll('tr').forEach(row => {
                if (row.style.display === 'none') return;
                const cells = row.querySelectorAll('td');
                const values = [
                    cells[0]?.innerText.trim() || '',
                    cells[1]?.innerText.trim() || '',
                    cells[2]?.innerText.trim() || '',
                    cells[3]?.innerText.trim() || '',
                    cells[4]?.innerText.trim() || '',
                    cells[5]?.innerText.trim() || '',
                ];
                csv += values.map(value => `"${value.replaceAll('"', '""')}"`).join(',') + '\n';
            });

            const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'stock.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function deleteInventory(id, name) {
            if (!confirm(`តើអ្នកពិតជាចង់លុបស្តុក "${name}" មែនទេ?`)) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/inventory/' + id;
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') + '"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }

        function openQuickUpdate(id, currentQty) {
            const newQty = prompt(`បញ្ចូលចំនួនស្តុកថ្មី\n\nចំនួនបច្ចុប្បន្ន: ${currentQty}`, currentQty);
            if (newQty === null || newQty === '' || newQty == currentQty) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/inventory/' + id + '/quick-update';
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') + '"><input type="hidden" name="quantity" value="' + newQty + '">';
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush
