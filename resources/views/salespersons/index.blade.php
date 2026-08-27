@extends('layouts.app')

@section('title', 'Salespeople')

@push('styles')
    <style>
        .sales-page {
            max-width: 1400px;
            margin: 0 auto;
        }

        .sales-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 16px;
        }

        .sales-title {
            font-size: 28px;
            font-weight: 900;
            color: #111827;
            margin: 0;
        }

        .sales-subtitle {
            color: #64748b;
            margin-top: 4px;
            font-size: 14px;
        }

        .sales-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            transition: all .2s;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .sales-btn-primary {
            background: linear-gradient(135deg, #ff6b35, #e85d24);
            color: #fff;
        }

        .sales-btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(232, 93, 36, .2);
            color: #fff;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .05);
        }

        .stat-label {
            font-size: 13px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 900;
            color: #111827;
            margin-top: 6px;
        }

        .filter-card {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .05);
        }

        .filter-form {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input-wrap {
            flex: 1;
            min-width: 260px;
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-control {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border-radius: 6px;
            border: 1px solid #dbe3ef;
            font-size: 14px;
            min-height: 42px;
        }

        .search-control:focus {
            border-color: #e85d24;
            outline: none;
            box-shadow: 0 0 0 3px rgba(232, 93, 36, .1);
        }

        .filter-select {
            min-width: 160px;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #dbe3ef;
            font-size: 14px;
            background-color: #fff;
            min-height: 42px;
        }

        .filter-select:focus {
            border-color: #e85d24;
            outline: none;
        }

        .sales-table-card {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .05);
        }

        .sales-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sales-table th {
            background: #f8fafc;
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 800;
            color: #475569;
            text-align: left;
            border-bottom: 1px solid #edf2f7;
        }

        .sales-table td {
            padding: 14px 16px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #edf2f7;
        }

        .sales-table tr:last-child td {
            border-bottom: none;
        }

        .sales-table tr:hover td {
            background-color: #f8fafc;
        }

        .status-pill {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            padding: 4px 10px;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-row {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            transition: all .2s;
            text-decoration: none;
        }

        .btn-action:hover {
            background: #f1f5f9;
            color: #334155;
        }

        .btn-view:hover {
            color: #2563eb;
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .btn-edit:hover {
            color: #059669;
            border-color: #a7f3d0;
            background: #ecfdf5;
        }

        .btn-delete:hover {
            color: #dc2626;
            border-color: #fecaca;
            background: #fef2f2;
        }

        @media (max-width: 768px) {
            .sales-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .sales-btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 sales-page">
        <div class="sales-header">
            <div>
                <h2 class="sales-title">ភ្នាក់ងារលក់ (Salespeople)</h2>
                <p class="sales-subtitle">គ្រប់គ្រងគណនីភ្នាក់ងារលក់ និងតាមដានចំនួនអតិថិជនដែលពួកគេគ្រប់គ្រង។</p>
            </div>
            <div>
                <a href="{{ route('salespersons.create') }}" class="sales-btn sales-btn-primary">
                    <i class="fas fa-plus"></i> បន្ថែមភ្នាក់ងារលក់ថ្មី
                </a>
            </div>
        </div>

        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">ភ្នាក់ងារលក់សរុប</div>
                <div class="stat-value">{{ number_format(\App\Models\Salesperson::count()) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">ភ្នាក់ងារសកម្ម</div>
                <div class="stat-value text-success">{{ number_format(\App\Models\Salesperson::where('status', 'active')->count()) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">ភ្នាក់ងារអសកម្ម</div>
                <div class="stat-value text-danger">{{ number_format(\App\Models\Salesperson::where('status', 'inactive')->count()) }}</div>
            </div>
        </div>

        <div class="filter-card">
            <form action="{{ route('salespersons.index') }}" method="GET" class="filter-form">
                <div class="search-input-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="search-control" placeholder="ស្វែងរកឈ្មោះ ឬលេខទូរស័ព្ទ..." value="{{ request('search') }}">
                </div>
                <div>
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>គ្រប់ស្ថានភាព</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>សកម្ម</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>អសកម្ម</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="sales-btn sales-btn-primary" style="padding: 9px 16px;">
                        ស្វែងរក
                    </button>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('salespersons.index') }}" class="sales-btn sales-btn-soft" style="background: #e2e8f0; color: #475569; margin-left: 6px;">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="sales-table-card">
            <div class="table-responsive">
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>ឈ្មោះភ្នាក់ងារ</th>
                            <th>លេខទូរស័ព្ទ</th>
                            <th class="text-center" style="text-align: center;">អតិថិជនគ្រប់គ្រង</th>
                            <th>ស្ថានភាព</th>
                            <th>សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salespersons as $sp)
                            <tr>
                                <td class="fw-bold" style="font-weight: 800;">
                                    <a href="{{ route('salespersons.show', $sp) }}" style="color: #e85d24; text-decoration: none;">
                                        {{ $sp->name }}
                                    </a>
                                </td>
                                <td>{{ $sp->phone ?? '—' }}</td>
                                <td class="text-center" style="text-align: center; font-weight: 800;">
                                    <span class="badge bg-secondary text-white px-2 py-1 rounded">
                                        {{ number_format($sp->customers_count) }}
                                    </span>
                                </td>
                                <td>
                                    @if($sp->status == 'active')
                                        <span class="status-pill status-active"><i class="fas fa-check-circle"></i> សកម្ម</span>
                                    @else
                                        <span class="status-pill status-inactive"><i class="fas fa-times-circle"></i> អសកម្ម</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-row">
                                        <a href="{{ route('salespersons.show', $sp) }}" class="btn-action btn-view" title="មើលលម្អិត">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('salespersons.edit', $sp) }}" class="btn-action btn-edit" title="កែប្រែ">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('salespersons.destroy', $sp) }}" method="POST" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបភ្នាក់ងារលក់នេះមែនទេ? អតិថិជនទាំងអស់របស់គាត់នឹងត្រូវដកចេញ។')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="លុប">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    មិនមានទិន្នន័យភ្នាក់ងារលក់ឡើយ។
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($salespersons->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4">
                    {{ $salespersons->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

