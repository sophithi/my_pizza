@extends('layouts.app')

@section('title', 'Orders')

@push('styles')
<style>
    /* ===== SUMMARY CARDS ===== */
    .summary-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 28px;
    }
    .summary-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px 22px;
        border: 1px solid #eceef1;
    }
    .summary-card .label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #8a919e;
        margin-bottom: 8px;
    }
    .summary-card .value {
        font-size: 28px;
        font-weight: 800;
        color: #1a1d29;
        line-height: 1;
        margin-bottom: 4px;
    }
    .summary-card .sub {
        font-size: 12px;
        color: #adb5bd;
    }
    .summary-card.accent { border-left: 3px solid #e85d24; }
    .summary-card.green  { border-left: 3px solid #22c55e; }
    .summary-card.amber  { border-left: 3px solid #f59e0b; }
    .summary-card.blue   { border-left: 3px solid #3b82f6; }

    /* ===== STATUS TABS ===== */
    .status-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 20px;
        border-bottom: 2px solid #eceef1;
        padding-bottom: 0;
    }
    .status-tab {
        padding: 10px 20px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        color: #8a919e;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: color 0.15s, border-color 0.15s;
    }
    .status-tab:hover { color: #1a1d29; }
    .status-tab.active {
        color: #e85d24;
        border-bottom-color: #e85d24;
    }

    /* ===== TOOLBAR ===== */
    .toolbar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
        flex-wrap: wrap;
    }
    .toolbar .search-box {
        flex: 1;
        min-width: 200px;
        padding: 9px 14px;
        border: 1px solid #dde1e6;
        border-radius: 8px;
        font-size: 13px;
        background: #fff;
        color: #1a1d29;
        transition: border-color 0.15s;
    }
    .toolbar .search-box:focus {
        outline: none;
        border-color: #e85d24;
        box-shadow: 0 0 0 2px rgba(232,93,36,0.08);
    }
    .toolbar .search-box::placeholder { color: #adb5bd; }
    .toolbar select {
        padding: 9px 12px;
        border: 1px solid #dde1e6;
        border-radius: 8px;
        font-size: 13px;
        background: #fff;
        color: #1a1d29;
        cursor: pointer;
        min-width: 140px;
    }
    .toolbar select:focus {
        outline: none;
        border-color: #e85d24;
    }
    .btn-export {
        padding: 9px 16px;
        border: 1px solid #dde1e6;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        background: #fff;
        color: #1a1d29;
        cursor: pointer;
        transition: all 0.15s;
    }
    .btn-export:hover {
        border-color: #22c55e;
        color: #22c55e;
    }

    /* ===== TABLE ===== */
    .table-wrap {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #eceef1;
        overflow: hidden;
    }
    .orders-table {
        width: 100%;
        border-collapse: collapse;
    }
    .orders-table thead th {
        padding: 12px 16px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #8a919e;
        background: #fafbfc;
        border-bottom: 1px solid #eceef1;
        text-align: left;
        cursor: pointer;
        user-select: none;
    }
    .orders-table thead th:hover { color: #1a1d29; }
    .orders-table tbody tr {
        border-bottom: 1px solid #f2f3f5;
        transition: background 0.1s;
    }
    .orders-table tbody tr:last-child { border-bottom: none; }
    .orders-table tbody tr:hover { background: #fafbfc; }
    .orders-table td {
        padding: 14px 16px;
        font-size: 13px;
        color: #1a1d29;
        vertical-align: middle;
    }

    /* Order ID */
    .oid { color: #e85d24; font-weight: 700; font-size: 13px; }

    /* Customer */
    .cname { font-weight: 600; }

    /* Amount */
    .amt { font-weight: 700; color: #22c55e; font-size: 14px; }

    /* Date secondary */
    .date-sub { font-size: 11px; color: #adb5bd; }

    /* ===== BADGES ===== */
    .s-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.2px;
    }
    .s-badge.completed { background: #ecfdf5; color: #16a34a; }
    .s-badge.pending    { background: #fffbeb; color: #d97706; }
    .s-badge.processing { background: #eff6ff; color: #2563eb; }
    .s-badge.cancelled  { background: #fef2f2; color: #dc2626; }
    .s-badge.paid       { background: #ecfdf5; color: #16a34a; }
    .s-badge.unpaid     { background: #fef2f2; color: #dc2626; }
    .s-badge.partial    { background: #fffbeb; color: #d97706; }

    /* ===== ACTION LINKS ===== */
    .actions {
        display: flex;
        gap: 4px;
    }
    .act-link {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: background 0.12s;
        font-family: inherit;
    }
    .act-link.view  { color: #3b82f6; background: #eff6ff; }
    .act-link.view:hover  { background: #dbeafe; }
    .act-link.edit  { color: #6b7280; background: #f3f4f6; }
    .act-link.edit:hover  { background: #e5e7eb; }
    .act-link.del   { color: #dc2626; background: #fef2f2; }
    .act-link.del:hover   { background: #fee2e2; }

    /* ===== HEADER ===== */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .page-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: #1a1d29;
        margin: 0;
    }
    .btn-create {
        padding: 10px 20px;
        background: #e85d24;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: background 0.15s;
    }
    .btn-create:hover { background: #d94a10; color: #fff; }

    /* ===== EMPTY ===== */
    .empty-box {
        padding: 52px 24px;
        text-align: center;
        color: #adb5bd;
    }
    .empty-box .big { font-size: 36px; margin-bottom: 12px; }
    .empty-box p { font-size: 14px; margin-bottom: 16px; }
    .empty-box a { color: #e85d24; text-decoration: none; font-weight: 600; }

    /* ===== SUCCESS ALERT ===== */
    .alert-ok {
        background: #ecfdf5;
        border: 1px solid #bbf7d0;
        color: #166534;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .alert-ok .close-btn {
        background: none;
        border: none;
        color: #166534;
        font-size: 16px;
        cursor: pointer;
        opacity: 0.5;
    }
    .alert-ok .close-btn:hover { opacity: 1; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 900px) {
        .summary-row { grid-template-columns: repeat(2, 1fr); }
        .toolbar { flex-direction: column; }
    }
    @media (max-width: 600px) {
        .summary-row { grid-template-columns: 1fr; }
        .page-header { flex-direction: column; gap: 12px; align-items: flex-start; }
    }
</style>
@endpush

@section('content')

<!-- Header -->
<div class="page-header">
    <h2>បញ្ជីការកម្មង់ទំនិញទាំងអស់</h2>
    <a href="{{ route('orders.create') }}" class="btn-create">បង្កើតការកាម្មង់</a>
</div>

@if($message = Session::get('success'))
<div class="alert-ok">
    <span>{{ $message }}</span>
    <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
</div>
@endif

<!-- Summary Cards -->
<div class="summary-row">
    <div class="summary-card accent">
        <div class="label">ចំនួនកម្មង់</div>
        <div class="value">{{ $orders->total() }}</div>
        <div class="sub">All time</div>
    </div>
    <div class="summary-card green">
        <div class="label">ចំនួនទឹកប្រាក់</div>
        <div class="value">${{ number_format($orders->sum('total_amount'), 2) }}</div>
        <div class="sub">All orders</div>
    </div>
    <div class="summary-card amber">
        <div class="label">កំពុងដំណើរការ</div>
        <div class="value">{{ $orders->where('status', 'pending')->count() }}</div>
        <div class="sub">Awaiting completion</div>
    </div>
    <div class="summary-card blue">
        <div class="label">រួចរាល់</div>
        <div class="value">{{ $orders->where('status', 'completed')->count() }}</div>
        <div class="sub">Successfully processed</div>
    </div>
</div>

<!-- Status tabs removed: orders no longer use status filter -->

<!-- Toolbar -->
<div class="toolbar">
    <input type="text" id="searchInput" class="search-box" 
           placeholder="Search by customer, order ID...">

           
     <select id="realtime">
        <option value="">time</option>
    </select>
    <!-- status filter removed -->
    <select id="paymentFilter">
        <option value="">All Payment</option>
        <option value="paid">បានបង់ប្រាក់</option>
        <option value="unpaid">មិនទាន់បានបង់ប្រាក់</option>
        <option value="partial">រងចាំ</option>
    </select>
    <button onclick="exportToCSV()" class="btn-export">Export CSV</button>
</div>

<!-- Orders Table -->
<div class="table-wrap">
    <div class="table-responsive">
        <table class="orders-table">
            <thead>
                <tr>
                    <th onclick="sortTable('id')">កូដការកម្មង់</th>
                    <th onclick="sortTable('customer')">ឈ្មោះអតិថិជន</th>
                    <th onclick="sortTable('items')">ចំនួនទំនិញ</th>
                    <th onclick="sortTable('date')">កាលបរិច្ឆេទ</th>
                    <th onclick="sortTable('amount')">ចំនួនទឹកប្រាក់</th>
                    <th>ការបង់ប្រាក់</th>
                    <th>ផ្សេងៗ</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($orders as $order)
                <tr data-id="{{ $order->id }}" 
                    data-customer="{{ $order->customer->name }}" 
                    data-payment="{{ strtolower($order->payment_status) }}">
                    <td class="oid">ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="cname">{{ $order->customer->name }}</td>
                    <td>{{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}</td>
                    <td>
                        {{ $order->order_date->format('d/m/Y') }}
                        <div class="date-sub">{{ $order->order_date->setTimezone('Asia/Phnom_Penh')->format('h:i A') }}</div>
                    </td>
                    <td class="amt">${{ number_format($order->total_amount, 2) }}</td>
                    <td>
                        <span class="s-badge {{ strtolower($order->payment_status) }}">{{ ucfirst($order->payment_status) }}</span>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('orders.show', $order) }}" class="act-link view">View</a>
                            <a href="{{ route('orders.edit', $order) }}" class="act-link edit">Edit</a>
                            <button onclick="deleteOrder({{ $order->id }}, 'ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}')" 
                                    class="act-link del">Del</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="border: none;">
                        <div class="empty-box">
                            <div class="big">No orders yet</div>
                            <p>Get started by creating your first order.</p>
                            <a href="{{ route('orders.create') }}">Create Order →</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    {{ $orders->links() }}
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const paymentFilter = document.getElementById('paymentFilter');
        const tableBody = document.getElementById('tableBody');

        if (!searchInput || !tableBody) return;

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const paymentVal = paymentFilter.value.toLowerCase();
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const id = row.getAttribute('data-id') || '';
                const customer = row.getAttribute('data-customer') || '';
                const payment = row.getAttribute('data-payment') || '';

                const matchesSearch = id.includes(searchTerm) || customer.toLowerCase().includes(searchTerm);
                const matchesPayment = !paymentVal || payment === paymentVal;

                row.style.display = (matchesSearch && matchesPayment) ? '' : 'none';
            });
        }

        searchInput.addEventListener('keyup', filterTable);
        paymentFilter.addEventListener('change', filterTable);
    });

    // Export to CSV
    // function exportToCSV() {
    //     const table = document.getElementById('tableBody');
    //     if (!table) return;

    //     let csv = "Order ID,Customer,Items,Date,Amount,Status,Payment\n";

    //     table.querySelectorAll('tr').forEach(row => {
    //         if (row.style.display === 'none') return;
    //         const cells = row.querySelectorAll('td');
    //         if (cells.length > 0) {
    //             const orderId = cells[0]?.textContent.trim() || '';
    //             const customer = cells[1]?.textContent.trim() || '';
    //             const items = cells[2]?.textContent.trim() || '';
    //             const date = cells[3]?.textContent.trim().split('\n')[0] || '';
    //             const amount = cells[4]?.textContent.trim() || '';
    //             const status = cells[5]?.textContent.trim().replace(/[^\w\s]/g, '') || '';
    //             const payment = cells[6]?.textContent.trim().replace(/[^\w\s]/g, '') || '';

    //             csv += `"${orderId}","${customer}","${items}","${date}","${amount}","${status}","${payment}"\n`;
    //         }
    //     });

    //     const blob = new Blob([csv], { type: 'text/csv' });
    //     const url = window.URL.createObjectURL(blob);
    //     const a = document.createElement('a');
    //     a.href = url;
    //     a.download = 'orders_' + new Date().toISOString().slice(0, 10) + '.csv';
    //     a.click();
    //     window.URL.revokeObjectURL(url);
    // }
  // Export to CSV
    function exportToCSV() {
        const table = document.getElementById('tableBody');
        if (!table) return;

        let csv = "Order ID,Customer,Items,Date,Amount,Payment\n";

        table.querySelectorAll('tr').forEach(row => {
            if (row.style.display === 'none') return;

            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                const orderId = cells[0]?.textContent.trim() || '';
                const customer = cells[1]?.textContent.trim() || '';
                const items = cells[2]?.textContent.trim() || '';
                const date = cells[3]?.textContent.trim().split('\n')[0] || '';
                const amount = cells[4]?.textContent.trim() || '';
                const payment = cells[5]?.textContent.trim().replace(/[^\w\s]/g, '') || '';

                csv += `"${orderId}","${customer}","${items}","${date}","${amount}","${payment}"\n`;
            }
        });

        //  ADD UTF-8 BOM (FIX KHMER)
        const BOM = "\uFEFF";

        //  Create blob with UTF-8
        const blob = new Blob([BOM + csv], { type: 'text/csv;charset=utf-8;' });

        //  Download file
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "orders_" + new Date().toISOString().slice(0,10) + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Sorting
    let sortColumn = null;
    let sortOrder = 'asc';

    function sortTable(column) {
        const table = document.getElementById('tableBody');
        const rows = Array.from(table.querySelectorAll('tr'));

        if (sortColumn === column) {
            sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            sortOrder = 'asc';
            sortColumn = column;
        }

        rows.sort((a, b) => {
            let aVal, bVal;

            if (column === 'id') {
                aVal = parseInt(a.getAttribute('data-id') || 0);
                bVal = parseInt(b.getAttribute('data-id') || 0);
            } else if (column === 'customer') {
                aVal = (a.getAttribute('data-customer') || '').toLowerCase();
                bVal = (b.getAttribute('data-customer') || '').toLowerCase();
            } else if (column === 'items') {
                aVal = parseInt(a.querySelectorAll('td')[2]?.textContent) || 0;
                bVal = parseInt(b.querySelectorAll('td')[2]?.textContent) || 0;
            } else if (column === 'date') {
                aVal = new Date(a.querySelectorAll('td')[3]?.textContent.split('\n')[0]);
                bVal = new Date(b.querySelectorAll('td')[3]?.textContent.split('\n')[0]);
            } else if (column === 'amount') {
                aVal = parseFloat(a.querySelectorAll('td')[4]?.textContent.replace('$', '') || 0);
                bVal = parseFloat(b.querySelectorAll('td')[4]?.textContent.replace('$', '') || 0);
            }

            if (typeof aVal === 'number') {
                return sortOrder === 'asc' ? aVal - bVal : bVal - aVal;
            } else {
                return sortOrder === 'asc' ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            }
        });

        table.innerHTML = '';
        rows.forEach(row => table.appendChild(row));
    }

    // Delete with confirmation
    function deleteOrder(id, orderNum) {
        if (confirm(`Are you sure you want to delete order "${orderNum}"? This will also restore the inventory quantities.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/orders/' + id;
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') + '"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush

@endsection
