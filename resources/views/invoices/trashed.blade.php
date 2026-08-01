@extends('layouts.app')

@section('title', 'វិក្ក័យប័ត្របានលុប')

@push('styles')
    <style>
        .trash-page {
            --accent: #e85d24;
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
            font-size: 30px;
            font-weight: 900;
            margin: 0;
        }

        .trash-subtitle {
            color: var(--muted);
            margin: 6px 0 0;
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
        }

        .trash-btn-soft {
            background: #f3f4f6;
            color: #374151;
        }

        .trash-btn-soft:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .trash-search {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
            margin-bottom: 16px;
            padding: 14px;
        }

        .trash-table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
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
            font-size: 12px;
            font-weight: 800;
            padding: 6px 10px;
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

        @media (max-width: 640px) {
            .trash-header {
                align-items: stretch;
                flex-direction: column;
            }

            .trash-btn {
                width: 100%;
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
                <h2 class="trash-title">វិក្ក័យប័ត្របានលុប</h2>
                <p class="trash-subtitle">មើល និងស្តារវិក្ក័យប័ត្រដែលបានលុប (admin/manager)</p>
            </div>
            <a href="{{ route('invoices.index') }}" class="trash-btn trash-btn-soft">
                <i class="fas fa-arrow-left"></i> ត្រឡប់ទៅវិក្ក័យប័ត្រ
            </a>
        </div>

        <form method="GET" action="{{ route('invoices.trash') }}" class="trash-search">
            <div style="display:flex; gap:10px;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="ស្វែងរកលេខវិក្ក័យប័ត្រ ឬឈ្មោះអតិថិជន...">
                <button type="submit" class="trash-btn trash-btn-soft">
                    <i class="fas fa-search"></i> ស្វែងរក
                </button>
                @if(request('search'))
                    <a href="{{ route('invoices.trash') }}" class="trash-btn trash-btn-soft">
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
                            <th>លេខវិក្ក័យប័ត្រ</th>
                            <th>អតិថិជន</th>
                            <th>បញ្ជាទិញ</th>
                            <th>ទឹកប្រាក់</th>
                            <th>បានលុបដោយ</th>
                            <th>បានលុបនៅ</th>
                            <th class="text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $invoice->invoice_number }}</span>
                                    <div><span class="trash-badge"><i class="fas fa-trash"></i> បានលុប</span></div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $invoice->order?->customer?->name ?? 'N/A' }}</div>
                                    @if($invoice->order?->customer?->phone)
                                        <div class="text-muted small">{{ $invoice->order->customer->phone }}</div>
                                    @endif
                                </td>
                                <td class="text-muted">#{{ $invoice->order?->id ?? 'N/A' }}</td>
                                <td>
                                    <div class="text-muted small fw-bold">${{ number_format($invoice->total_amount, 2) }}</div>
                                </td>
                                <td class="text-muted">{{ $invoice->deletedBy?->name ?? 'N/A' }}</td>
                                <td class="text-muted">{{ $invoice->deleted_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="action-row">
                                        <form method="POST" action="{{ route('invoices.restore', $invoice->id) }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="action-btn action-btn-restore"
                                                onclick="return confirm('ស្តារវិក្ក័យប័ត្រនេះឡើងវិញ?');">
                                                <i class="fas fa-rotate-left"></i> ស្តារ
                                            </button>
                                        </form>
                                        @auth
                                            @if(auth()->user()->isAdmin())
                                                <form method="POST" action="{{ route('invoices.force-delete', $invoice->id) }}" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn action-btn-forget"
                                                        onclick="return confirm('លុបជាអចិន្ត្រៃយ៍? សកម្មភាពនេះមិនអាចត្រឡប់វិញបានទេ!');">
                                                        <i class="fas fa-ban"></i> លុបជាអចិន្ត្រៃយ៍
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fas fa-trash"></i>
                                        </div>
                                        <div class="empty-state-title">មិនមានវិក្ក័យប័ត្របានលុបទេ</div>
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
