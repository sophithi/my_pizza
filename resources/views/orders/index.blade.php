@extends('layouts.app')

@section('title', 'Orders - Transaction Dashboard')

@push('styles')
<style>
    :root {
        --accent: #e85d24;
        --bg: #f4f5f7;
        --surface: #ffffff;
        --border: #e9ecef;
        --text: #1a1d29;
        --text-muted: #6c757d;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --info: #0d6efd;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
        animation: slideUp 0.6s ease-out;
    }

    .stat-card {
        background: var(--surface);
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid var(--border);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 12px;
    }

    .stat-icon.orders { background: rgba(232, 93, 36, 0.1); color: var(--accent); }
    .stat-icon.revenue { background: rgba(40, 167, 69, 0.1); color: var(--success); }
    .stat-icon.pending { background: rgba(255, 193, 7, 0.1); color: var(--warning); }
    .stat-icon.completed { background: rgba(13, 110, 253, 0.1); color: var(--info); }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .stat-change {
        font-size: 13px;
        color: var(--text-muted);
    }

    .filter-section {
        display: grid;
        grid-template-columns: 1fr 200px 200px 150px auto;
        gap: 12px;
        margin-bottom: 24px;
        align-items: center;
    }

    @media (max-width: 1024px) {
        .filter-section {
            grid-template-columns: 1fr;
        }
    }

    .search-input, .filter-select {
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: var(--surface);
        color: var(--text);
    }

    .search-input:focus, .filter-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(232, 93, 36, 0.1);
    }

    .search-input::placeholder {
        color: var(--text-muted);
    }

    .btn-export {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-export:hover {
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        transform: translateY(-1px);
    }

    .table-container {
        background: var(--surface);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }

    .table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .table thead th {
        padding: 14px 16px;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text);
        border-bottom: 2px solid var(--border);
        cursor: pointer;
        user-select: none;
        transition: background 0.3s ease;
    }

    .table thead th:hover {
        background: #dfe0e5;
    }

    .table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.2s ease;
        animation: slideDown 0.4s ease-out;
    }

    .table tbody tr:hover {
        background: rgba(232, 93, 36, 0.02);
    }

    .table tbody td {
        padding: 14px 16px;
        color: var(--text);
        font-size: 14px;
    }

    .order-id {
        color: var(--accent);
        font-weight: 700;
        font-size: 13px;
    }

    .amount {
        font-weight: 700;
        font-size: 15px;
        color: var(--text);
    }

    .amount.positive { color: var(--success); }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
    }

    .badge.completed { background: rgba(40, 167, 69, 0.15); color: var(--success); }
    .badge.pending { background: rgba(255, 193, 7, 0.15); color: #856404; }
    .badge.cancelled { background: rgba(220, 53, 69, 0.15); color: var(--danger); }
    .badge.paid { background: rgba(40, 167, 69, 0.15); color: var(--success); }
    .badge.unpaid { background: rgba(255, 193, 7, 0.15); color: #856404; }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
        text-decoration: none;
        color: white;
    }

    .btn-icon.view {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    }

    .btn-icon.edit {
        background: linear-gradient(135deg, #6c757d 0%, #5c636a 100%);
    }

    .btn-icon.delete {
        background: linear-gradient(135deg, #dc3545 0%, #bb2d3b 100%);
    }

    .btn-icon:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        transform: translateY(-2px);
    }

    .empty-state {
        padding: 48px 24px;
        text-align: center;
        color: var(--text-muted);
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.6;
    }

    .empty-state-text {
        font-size: 16px;
        margin-bottom: 12px;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .alert-success {
        background: rgba(40, 167, 69, 0.1);
        border: 1px solid rgba(40, 167, 69, 0.3);
        color: #155724;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 20px;
    }

    .alert-success .btn-close {
        opacity: 0.5;
    }
</style>
@endpush

@section('content')

<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="font-size: 28px; font-weight: 700; color: var(--text); margin: 0;">Orders</h2>
    <a href="{{ route('orders.create') }}" class="btn" style="background: linear-gradient(135deg, var(--accent) 0%, #d94a10 100%); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; gap: 8px; align-items: center;">
        បង្កើតការកាម្មង់
    </a>
</div>

@if($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats Cards -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon orders"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-label">Total Orders</div>
        <div class="stat-value">{{ $orders->total() }}</div>
        <div class="stat-change">All time</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon revenue"><i class="fas fa-dollar-sign"></i></div>
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">${{ number_format($orders->sum('total_amount'), 2) }}</div>
        <div class="stat-change">All orders</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon pending"><i class="fas fa-clock"></i></div>
        <div class="stat-label">Pending Orders</div>
        <div class="stat-value">{{ $orders->where('status', 'pending')->count() }}</div>
        <div class="stat-change">Awaiting completion</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon completed"><i class="fas fa-check-circle"></i></div>
        <div class="stat-label">Completed Orders</div>
        <div class="stat-value">{{ $orders->where('status', 'completed')->count() }}</div>
        <div class="stat-change">Successfully processed</div>
    </div>
</div>

<!-- Filters -->
<div class="filter-section">
    <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search by customer, order ID...">
    
    <select id="statusFilter" class="filter-select">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
    </select>

    <select id="paymentFilter" class="filter-select">
        <option value="">All Payment</option>
        <option value="paid">Paid</option>
        <option value="unpaid">Unpaid</option>
    </select>

    <button onclick="exportToCSV()" class="btn-export">
        <i class="fas fa-download"></i> Export
    </button>
</div>

<!-- Orders Table -->
<div class="table-container">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th onclick="sortTable('id')">#Order ID <i class="fas fa-sort" style="margin-left: 6px; opacity: 0.6;"></i></th>
                    <th onclick="sortTable('customer')"> Customer <i class="fas fa-sort" style="margin-left: 6px; opacity: 0.6;"></i></th>
                    <th onclick="sortTable('items')"> Items <i class="fas fa-sort" style="margin-left: 6px; opacity: 0.6;"></i></th>
                    <th onclick="sortTable('date')"> Date <i class="fas fa-sort" style="margin-left: 6px; opacity: 0.6;"></i></th>
                    <th onclick="sortTable('amount')"> Amount <i class="fas fa-sort" style="margin-left: 6px; opacity: 0.6;"></i></th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @forelse($orders as $order)
                <tr data-id="{{ $order->id }}" data-customer="{{ $order->customer->name }}" data-status="{{ strtolower($order->status) }}" data-payment="{{ strtolower($order->payment_status) }}">
                    <td class="order-id">ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td><strong>{{ $order->customer->name }}</strong></td>
                    <td>{{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}</td>
                    <td>{{ $order->order_date->translatedFormat('M d, Y') }}<br><span style="font-size: 12px; color: var(--text-muted);">{{ $order->order_date->translatedFormat('h:i A') }}</span></td>
                    <td class="amount positive">${{ number_format($order->total_amount, 2) }}</td>
                    <td>
                        @if($order->status === 'completed')
                            <span class="badge completed"><i class="fas fa-check"></i> {{ ucfirst($order->status) }}</span>
                        @elseif($order->status === 'pending')
                            <span class="badge pending"><i class="fas fa-hourglass-half"></i> {{ ucfirst($order->status) }}</span>
                        @else
                            <span class="badge cancelled"><i class="fas fa-times"></i> {{ ucfirst($order->status) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($order->payment_status === 'paid')
                            <span class="badge paid"><i class="fas fa-check-circle"></i> {{ ucfirst($order->payment_status) }}</span>
                        @else
                            <span class="badge unpaid"><i class="fas fa-credit-card"></i> {{ ucfirst($order->payment_status) }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('orders.show', $order) }}" class="btn-icon view" title="View Order"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('orders.edit', $order) }}" class="btn-icon edit" title="Edit Order"><i class="fas fa-edit"></i></a>
                            <button onclick="deleteOrder({{ $order->id }}, 'ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}')" class="btn-icon delete" title="Delete Order"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="border: none;">
                        <div class="empty-state">
                            <div class="empty-state-icon">📋</div>
                            <div class="empty-state-text">No orders found</div>
                            <p style="font-size: 13px; margin-bottom: 16px;">Get started by creating your first order.</p>
                            <a href="{{ route('orders.create') }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">Create Order →</a>
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
        const statusFilter = document.getElementById('statusFilter');
        const paymentFilter = document.getElementById('paymentFilter');
        const tableBody = document.getElementById('tableBody');

        if (!searchInput || !statusFilter || !tableBody) return;

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusVal = statusFilter.value.toLowerCase();
            const paymentVal = paymentFilter.value.toLowerCase();
            const rows = tableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const id = row.getAttribute('data-id') || '';
                const customer = row.getAttribute('data-customer') || '';
                const status = row.getAttribute('data-status') || '';
                const payment = row.getAttribute('data-payment') || '';

                const matchesSearch = id.includes(searchTerm) || customer.toLowerCase().includes(searchTerm);
                const matchesStatus = !statusVal || status === statusVal;
                const matchesPayment = !paymentVal || payment === paymentVal;

                row.style.display = (matchesSearch && matchesStatus && matchesPayment) ? '' : 'none';
            });
        }

        searchInput.addEventListener('keyup', filterTable);
        statusFilter.addEventListener('change', filterTable);
        paymentFilter.addEventListener('change', filterTable);
    });

    // Export to CSV
    function exportToCSV() {
        const table = document.getElementById('tableBody');
        if (!table) return;
        
        let csv = "Order ID,Customer,Items,Date,Amount,Status,Payment\n";
        
        table.querySelectorAll('tr').forEach(row => {
            if (row.style.display === 'none') return;
            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                const orderId = cells[0]?.textContent.trim() || '';
                const customer = cells[1]?.textContent.trim() || '';
                const items = cells[2]?.textContent.trim() || '';
                const date = cells[3]?.textContent.trim().split('\n')[0] || '';
                const amount = cells[4]?.textContent.trim() || '';
                const status = cells[5]?.textContent.trim().replace(/[^\w\s]/g, '') || '';
                const payment = cells[6]?.textContent.trim().replace(/[^\w\s]/g, '') || '';
                
                csv += `"${orderId}","${customer}","${items}","${date}","${amount}","${status}","${payment}"\n`;
            }
        });
        
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'orders_' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
        window.URL.revokeObjectURL(url);
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
