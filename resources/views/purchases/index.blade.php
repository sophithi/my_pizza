@extends('layouts.app')

@section('title', 'ការចំណាយប្រចាំថ្ងៃ')

@push('styles')
    <style>
        .expense-page {
            max-width: 1280px;
            margin: 0 auto;
            padding: 24px;
        }

        .expense-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }

        .expense-title {
            color: #0f172a;
            font-size: 30px;
            font-weight: 800;
            margin: 0;
        }

        .expense-subtitle {
            color: #64748b;
            margin: 6px 0 0;
        }

        .expense-btn {
            border: 0;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            min-height: 42px;
            padding: 10px 18px;
            text-decoration: none;
        }

        .expense-btn-primary {
            background: #e85d24;
            color: #fff;
        }

        .expense-btn-primary:hover {
            background: #d94a10;
            color: #fff;
        }

        .expense-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        .expense-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .expense-stat {
            padding: 18px;
        }

        .expense-stat-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .expense-stat-value {
            color: #0f172a;
            font-size: 26px;
            font-weight: 800;
            margin-top: 8px;
        }

        .expense-stat-help {
            color: #64748b;
            font-size: 13px;
            margin-top: 2px;
        }

        .expense-filter {
            display: grid;
            grid-template-columns: minmax(220px, 1fr) 180px 180px auto;
            gap: 10px;
            padding: 14px;
            margin-bottom: 16px;
        }

        .expense-filter .form-control,
        .expense-filter .form-select {
            border-color: #d9dee7;
            border-radius: 6px;
            min-height: 42px;
        }

        .expense-table {
            width: 100%;
            border-collapse: collapse;
        }

        .expense-table th {
            border-bottom: 1px solid #e5e7eb;
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            padding: 14px 16px;
            text-align: left;
            text-transform: uppercase;
        }

        .expense-table td {
            border-bottom: 1px solid #edf0f4;
            color: #0f172a;
            padding: 14px 16px;
            vertical-align: middle;
        }

        .expense-money {
            color: #e85d24;
            font-size: 16px;
            font-weight: 800;
        }

        .expense-khr {
            color: #64748b;
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-top: 2px;
        }

        .expense-status {
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            padding: 6px 11px;
        }

        .expense-status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .expense-status.received {
            background: #d1fae5;
            color: #065f46;
        }

        .expense-status.cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .expense-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .expense-action {
            background: transparent;
            border: 0;
            color: #2563eb;
            padding: 0;
            text-decoration: none;
        }

        .expense-action-danger {
            color: #e11d48;
        }

        .expense-empty {
            padding: 54px 20px;
            text-align: center;
        }

        @media (max-width: 900px) {
            .expense-stats,
            .expense-filter {
                grid-template-columns: 1fr;
            }

            .expense-header {
                align-items: stretch;
                flex-direction: column;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $statusLabels = [
            'pending' => 'មិនទាន់ទូទាត់',
            'received' => 'បានទូទាត់',
            'cancelled' => 'បានលុប',
        ];
    @endphp

    <div class="expense-page">
        <div class="expense-header">
            <div>
                <h1 class="expense-title">ការចំណាយប្រចាំថ្ងៃ</h1>
                <p class="expense-subtitle">កត់ត្រាថ្លៃចំណាយប្រចាំថ្ងៃ ដូចជា ទិញសម្ភារៈ ប្រេង សេវា និងចំណាយផ្សេងៗ។</p>
            </div>
            <a href="{{ route('purchases.create') }}" class="expense-btn expense-btn-primary">
                <i class="fas fa-plus"></i> បញ្ចូលចំណាយ
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="border-radius: 8px;">{{ session('success') }}</div>
        @endif

        <div class="expense-stats">
            <div class="expense-card expense-stat">
                <div class="expense-stat-label">ចំនួនប្រតិបត្តិការ</div>
                <div class="expense-stat-value">{{ $stats['total'] }}</div>
            </div>
            <div class="expense-card expense-stat">
                <div class="expense-stat-label">ចំណាយសរុប</div>
                <div class="expense-stat-value">${{ number_format($stats['amount'], 2) }}</div>
                <div class="expense-stat-help">៛{{ number_format($stats['amount'] * $exchangeRate) }}</div>
            </div>
            <div class="expense-card expense-stat">
                <div class="expense-stat-label">មិនទាន់ទូទាត់</div>
                <div class="expense-stat-value" style="color: #d97706;">{{ $stats['pending'] }}</div>
            </div>
            <div class="expense-card expense-stat">
                <div class="expense-stat-label">បានទូទាត់</div>
                <div class="expense-stat-value" style="color: #059669;">{{ $stats['paid'] }}</div>
            </div>
        </div>

        <form method="GET" action="{{ route('purchases.index') }}" class="expense-card expense-filter" autocomplete="off">
            <input type="search" name="search" class="form-control" value="{{ request('search') }}"
                placeholder="ស្វែងរកចំណាយ ឬកំណត់ចំណាំ..." autocomplete="off">
            <select name="status" class="form-select">
                <option value="all">គ្រប់ស្ថានភាព</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>មិនទាន់ទូទាត់</option>
                <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>បានទូទាត់</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>បានលុប</option>
            </select>
            <input type="date" name="date" class="form-control" value="{{ request('date') }}" autocomplete="off">
            <button class="expense-btn expense-btn-primary" type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </form>

        <div class="expense-card">
            @if($purchases->count() > 0)
                <div class="table-responsive">
                    <table class="expense-table">
                        <thead>
                            <tr>
                                <th>លេខយោង</th>
                                <th>ប្រភេទ / អ្នកទទួល</th>
                                <th>កាលបរិច្ឆេទ</th>
                                <th>ចំនួនទឹកប្រាក់</th>
                                <th>ស្ថានភាព</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>
                                        <strong>{{ $purchase->reference_number ?: 'EXP-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</strong>
                                        @if($purchase->notes)
                                            <span class="d-block text-muted small">{{ \Illuminate\Support\Str::limit($purchase->notes, 46) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $purchase->supplier_name }}</td>
                                    <td>{{ $purchase->purchase_date->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <span class="expense-money">${{ number_format($purchase->total_amount, 2) }}</span>
                                        <span class="expense-khr">៛{{ number_format($purchase->total_amount * $exchangeRate) }}</span>
                                    </td>
                                    <td>
                                        <span class="expense-status {{ $purchase->status }}">
                                            {{ $statusLabels[$purchase->status] ?? ucfirst($purchase->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="expense-actions">
                                            <a href="{{ route('purchases.show', $purchase) }}" class="expense-action" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('purchases.edit', $purchase) }}" class="expense-action" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <form action="{{ route('purchases.destroy', $purchase) }}" method="POST"
                                                onsubmit="return confirm('Delete this daily expense?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="expense-action expense-action-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{ $purchases->links() }}
                </div>
            @else
                <div class="expense-empty">
                    <h5 class="mb-2">មិនទាន់មានការចំណាយទេ</h5>
                    <p class="text-muted mb-3">ចាប់ផ្តើមកត់ត្រាចំណាយប្រចាំថ្ងៃរបស់ហាង។</p>
                    <a href="{{ route('purchases.create') }}" class="expense-btn expense-btn-primary">
                        <i class="fas fa-plus"></i> បញ្ចូលចំណាយដំបូង
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
