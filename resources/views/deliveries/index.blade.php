@extends('layouts.app')

@section('title', 'Deliveries')

@push('styles')
    <style>
        :root {
            --accent: #e85d24;
            --accent-light: #fff5f0;
            --bg: #f4f5f7;
            --surface: #ffffff;
            --border: #e9ecef;
            --text: #1a1d29;
            --text-muted: #6c757d;
        }

        body {
            background: var(--bg);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
            margin: 0;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 4px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .btn-add {
            background: linear-gradient(135deg, var(--accent) 0%, #d94a10 100%);
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            border: none;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(232, 93, 36, 0.3);
            color: #fff;
        }

        .search-bar {
            margin-bottom: 24px;
        }

        .search-bar input {
            padding: 10px 16px;
            border-radius: 8px;
            border: 1px solid var(--border);
            font-size: 14px;
            width: 300px;
            background: var(--surface);
            color: var(--text);
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .table-card {
            background: var(--surface);
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .table-card table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-card th {
            padding: 14px 16px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            background: #fafbfc;
            border-bottom: 2px solid var(--border);
        }

        .table-card td {
            padding: 14px 16px;
            font-size: 14px;
            color: var(--text);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .table-card tr:hover {
            background: #fafbfc;
        }

        .price-tag {
            font-weight: 700;
            color: var(--accent);
            font-size: 15px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            gap: 4px;
        }

        .btn-view {
            background: #28a745;
            color: #fff;
        }

        .btn-view:hover {
            background: #218838;
            color: #fff;
        }

        .btn-edit {
            background: #0d6efd;
            color: #fff;
        }

        .btn-edit:hover {
            background: #0a58ca;
            color: #fff;
        }

        .btn-delete {
            background: #dc3545;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .btn-delete:hover {
            background: #b02a37;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            color: #dee2e6;
        }
    </style>
@endpush

@section('content')
    <div style="max-width: 1000px; margin: 0 auto; padding: 24px;">

        @if(session('success'))
            <div
                style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="page-header">
            <div>
                <p class="page-subtitle">ការដឹកជញ្ជូន</p>

            </div>
            <a href="{{ route('deliveries.create') }}" class="btn-add">
                <i class="fas fa-plus"></i> បង្កើតថ្មី
            </a>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder=" ស្វែងរកការដឹកជញ្ជូន..." onkeyup="filterTable()">
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ឈ្មោះដឹកជញ្ជូន</th>
                        <th>តម្លៃ</th>
                        <th>ផ្សេងៗ</th>
                        <th>សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($deliveries as $delivery)
                        <tr data-search="{{ strtolower($delivery->delivery_name . ' ' . $delivery->delivery_desc) }}">
                            <td style="font-weight: 700; color: var(--accent);">{{ $delivery->id }}</td>
                            <td style="font-weight: 700;">{{ $delivery->delivery_name }}</td>
                            <td>
                                <span class="price-tag">៛{{ number_format($delivery->delivery_price_khr, 0) }}</span>
                            </td>
                            <td style="color: var(--text-muted); max-width: 300px;">
                                {{ Str::limit($delivery->delivery_desc, 60) ?? '—' }}</td>
                            <td>
                                <div style="display: flex; gap: 6px;">
                                    <a href="{{ route('deliveries.show', $delivery) }}" class="action-btn btn-view"><i
                                            class="fas fa-eye"></i> View</a>
                                    <a href="{{ route('deliveries.edit', $delivery) }}" class="action-btn btn-edit"><i
                                            class="fas fa-edit"></i> Edit</a>
                                    <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST"
                                        style="display:inline;" data-delete="Delivery"
                                        data-item-name="{{ $delivery->delivery_name }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action-btn btn-delete"><i class="fas fa-trash"></i>
                                            Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-truck"></i>
                                    <p style="font-size: 16px; font-weight: 600;">មិនមានការដឹកជញ្ជូន</p>
                                    <p>ចុច "បង្កើតថ្មី" ដើម្បីចាប់ផ្តើម</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
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