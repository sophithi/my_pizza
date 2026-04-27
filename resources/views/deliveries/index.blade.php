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
        font-size: 12px;
        margin-top: 4px;
        max-width: 420px;
    }

    .price-badge {
        background: #fff5f0;
        border: 1px solid rgba(232, 93, 36, 0.2);
        border-radius: 999px;
        color: var(--accent);
        display: inline-flex;
        font-weight: 800;
        padding: 7px 11px;
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
    }

    .action-view { background: #16a34a; }
    .action-edit { background: #2563eb; }
    .action-delete { background: #dc2626; }

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
            <p class="delivery-subtitle">Manage delivery options that staff can choose when creating an order.</p>
        </div>
        <a href="{{ route('deliveries.create') }}" class="delivery-btn delivery-btn-primary">
            <i class="fas fa-plus"></i> បង្កើតថ្មី
        </a>
    </div>

    <div class="delivery-toolbar">
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" class="delivery-search" placeholder="Search delivery name or note..." oninput="filterTable()">
        </div>
    </div>

    <div class="delivery-card">
        <div class="table-responsive">
            <table class="delivery-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th>Delivery Option</th>
                        <th style="width: 180px;">Fee</th>
                        <th class="text-end" style="width: 280px;">Actions</th>
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
                            <td>
                                <span class="price-badge">៛{{ number_format($delivery->delivery_price_khr, 0) }}</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('deliveries.show', $delivery) }}" class="action-btn action-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('deliveries.edit', $delivery) }}" class="action-btn action-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" data-delete="Delivery" data-item-name="{{ $delivery->delivery_name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="fas fa-truck"></i>
                                    <h5>No delivery options yet</h5>
                                    <p class="text-muted mb-3">Create one so staff can choose delivery during order creation.</p>
                                    <a href="{{ route('deliveries.create') }}" class="delivery-btn delivery-btn-primary">
                                        <i class="fas fa-plus"></i> បង្កើតថ្មី
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
    function filterTable() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        document.querySelectorAll('#tableBody tr[data-search]').forEach(row => {
            row.style.display = row.getAttribute('data-search').includes(search) ? '' : 'none';
        });
    }
</script>
@endpush
