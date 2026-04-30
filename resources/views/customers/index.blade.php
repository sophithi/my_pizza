@extends('layouts.app')

@section('title', 'អតិថិជន')

@push('styles')
    <style>
        .customer-page {
            --accent: #e85d24;
            --accent-dark: #d94a10;
            --surface: #fff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
        }

        .customer-header {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .customer-title {
            color: var(--text);
            font-size: 28px;
            font-weight: 800;
            margin: 0;
        }

        .customer-subtitle {
            color: var(--muted);
            margin: 6px 0 0;
        }

        .customer-btn {
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

        .customer-btn-soft {
            background: #f3f4f6;
            color: #374151;
        }

        .customer-btn-soft:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .customer-btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
        }

        .customer-btn-primary:hover {
            color: #fff;
            box-shadow: 0 8px 18px rgba(232, 93, 36, .22);
            transform: translateY(-1px);
        }

        .stats-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            min-height: 102px;
            padding: 16px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .stat-value {
            color: var(--text);
            font-size: 24px;
            font-weight: 900;
            margin-top: 6px;
        }

        .filter-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 16px;
            padding: 14px;
        }

        .filter-row {
            align-items: center;
            display: grid;
            gap: 10px;
            grid-template-columns: minmax(320px, 1fr) 170px 170px auto auto;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            min-height: 42px;
        }

        .customer-search {
            display: flex;
            gap: 8px;
            min-width: 0;
        }

        .customer-search .form-control {
            min-width: 0;
        }

        .customer-search-btn {
            min-width: 46px;
            padding-inline: 12px;
        }

        .customer-table-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
            overflow: hidden;
        }

        .customer-table th {
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            padding: 14px 16px;
            text-transform: uppercase;
        }

        .customer-table td {
            border-bottom: 1px solid #f1f3f5;
            color: var(--text);
            padding: 14px 16px;
            vertical-align: middle;
        }

        .channel-pill,
        .status-pill,
        .order-pill {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            padding: 6px 10px;
        }

        .channel-facebook {
            background: #e7f3ff;
            color: #0a66c2;
        }

        .channel-telegram {
            background: #e0f7ff;
            color: #0088cc;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .order-pill {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .action-row {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .icon-action {
            align-items: center;
            border: 0;
            background: transparent;
            color: #2563eb;
            display: inline-flex;
            height: 32px;
            justify-content: center;
            text-decoration: none;
            width: 32px;
        }

        .icon-danger {
            color: #dc2626;
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

        .empty-state-text {
            color: var(--muted);
            margin: 0;
        }

        @media (max-width: 900px) {

            .customer-header {
                align-items: stretch;
                flex-direction: column;
            }

            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .filter-row {
                grid-template-columns: 1fr;
            }

            .customer-search {
                flex-direction: column;
            }

            .customer-btn {
                width: 100%;
            }
        }

        @media (max-width: 560px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .customer-title {
                font-size: 24px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 customer-page">
        <div class="customer-header">
            <div>
                <h2 class="customer-title">អតិថិជន</h2>
                <p class="customer-subtitle">ស្វែងរកអតិថិជន មើលប្រវត្តិបញ្ជាទិញ និងបង្កើតការបញ្ជាទិញថ្មីបានរហ័ស។</p>
            </div>
            <a href="{{ route('customers.create') }}" class="customer-btn customer-btn-primary">
                <i class="fas fa-plus"></i> បន្ថែមអតិថិជនថ្មី
            </a>
        </div>

        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">អតិថិជនសរុប</div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">កំពុងប្រើប្រាស់</div>
                <div class="stat-value text-success">{{ number_format($stats['active']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">មានប្រវត្តិបញ្ជាទិញ</div>
                <div class="stat-value text-primary">{{ number_format($stats['with_orders']) }}</div>
            </div>
        </div>

        <form method="GET" action="{{ route('customers.index') }}" class="filter-card">
            <div class="filter-row">
                <div class="customer-search">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="ស្វែងរកឈ្មោះ លេខទូរស័ព្ទ ទីក្រុង ឬទីតាំង..." autocomplete="off">
                    <button type="submit" class="customer-btn customer-btn-primary customer-search-btn" title="ស្វែងរក">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <select name="type" class="form-select">
                    <option value="all">គ្រប់ប្រភព</option>
                    <option value="facebook" {{ request('type') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                    <option value="telegram" {{ request('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                </select>
                <select name="status" class="form-select">
                    <option value="all">គ្រប់ស្ថានភាព</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>សកម្ម</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>អសកម្ម</option>
                </select>
          
                <a href="{{ route('customers.index') }}" class="customer-btn customer-btn-soft">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>

        <div class="customer-table-card">
            <div class="table-responsive">
                <table class="table customer-table mb-0">
                    <thead>
                        <tr>
                            <th>ប្រភព</th>
                            <th>អតិថិជន</th>
                            <th>ទំនាក់ទំនង</th>
                            <th>ទីតាំង</th>
                            <th>ការបញ្ជាទិញ</th>
                            <th>ចំណាយ</th>
                            <th>បញ្ជាទិញចុងក្រោយ</th>
                            <th>ស្ថានភាព</th>
                            <th class="text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>
                                    @if($customer->type == 'facebook')
                                        <span class="channel-pill channel-facebook"><i class="fab fa-facebook-f"></i>
                                            Facebook</span>
                                    @elseif($customer->type == 'telegram')
                                        <span class="channel-pill channel-telegram"><i class="fab fa-telegram"></i> Telegram</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $customer->name }}</div>
                                    @if($customer->notes)
                                        <div class="text-muted small">{{ Str::limit($customer->notes, 45) }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->phone)
                                        <a href="tel:{{ $customer->phone }}" class="text-decoration-none">{{ $customer->phone }}</a>
                                    @else
                                        <span class="text-muted">មិនមាន</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $customer->city ?? $customer->address ?? 'មិនមាន' }}</td>
                                <td>
                                    @if($customer->orders_count > 0)
                                        <span class="order-pill">{{ $customer->orders_count }} ការបញ្ជាទិញ</span>
                                    @else
                                        <span class="text-muted fst-italic">មិនទាន់មានការបញ្ជាទិញ</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>${{ number_format($customer->total_spent ?? 0, 2) }}</strong>
                                    <div class="text-muted small">៛{{ number_format(($customer->total_spent ?? 0) * 4000, 0) }}
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    {{ $customer->last_order_at ? \Carbon\Carbon::parse($customer->last_order_at)->format('d/m/Y') : '—' }}
                                </td>
                                <td>
                                    @if($customer->status == 'active')
                                        <span class="status-pill status-active"><i class="fas fa-check-circle"></i> សកម្ម</span>
                                    @elseif($customer->status == 'inactive')
                                        <span class="status-pill status-inactive"><i class="fas fa-times-circle"></i> អសកម្ម</span>
                                    @else
                                        <span class="text-muted">{{ $customer->status ?? 'រង់ចាំ' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-row">
                                        <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}"
                                            class="icon-action" title="បង្កើតការបញ្ជាទិញថ្មី">
                                            <i class="fas fa-cart-plus"></i>
                                        </a>
                                        <a href="{{ route('customers.show', $customer) }}" class="icon-action" title="មើល">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customers.edit', $customer) }}" class="icon-action" title="កែប្រែ">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                                            data-delete="អតិថិជន" data-item-name="{{ $customer->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="icon-action icon-danger" title="លុប">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
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

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const form = document.querySelector('form[action="{{ route('customers.index') }}"]');
                if (!form) return;

                const input = form.querySelector('input[name="search"]');
                const selects = form.querySelectorAll('select[name="type"], select[name="status"]');
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
