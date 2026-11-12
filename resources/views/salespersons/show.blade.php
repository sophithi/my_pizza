@extends('layouts.app')

@section('title', $salesperson->name . ' - ភ្នាក់ងារលក់')

@push('styles')
    <style>
.page-wrap {

            max-width: 1400px;
            margin: 0 auto;

}

.page-wrap .page-header {

            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;

}

.page-wrap .page-title {

            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;

}

.page-wrap .btn-back {

            background: #fff;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 700;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all .15s;

}

.page-wrap .btn-back:hover {

            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;

}

.page-wrap .person-card {

            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 20px 24px;
            margin-bottom: 18px;
            display: flex;
            gap: 20px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;

}

.page-wrap .person-left {

            display: flex;
            align-items: center;
            gap: 16px;

}

.page-wrap .person-avatar {

            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #e85d24;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;

}

.page-wrap .person-name {

            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;

}

.page-wrap .person-meta {

            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            font-size: 13.5px;
            color: #64748b;

}

.page-wrap .status-badge {

            display: inline-flex;
            align-items: center;
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 999px;

}

.page-wrap .status-badge.active {

            background: #dcfce7;
            color: #166534;

}

.page-wrap .status-badge.inactive {

            background: #fee2e2;
            color: #991b1b;

}

.page-wrap .channel-tag {

            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
            display: inline-block;

}

.page-wrap .channel-tag.facebook {

            background: #e7f3ff;
            color: #0a66c2;

}

.page-wrap .channel-tag.telegram {

            background: #e0f7ff;
            color: #0088cc;

}

.page-wrap .filter-bar {

            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 18px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;

}

.page-wrap .filter-tabs {

            display: flex;
            gap: 6px;
            flex-wrap: wrap;

}

.page-wrap .filter-tab {

            font-size: 12.5px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            color: #475569;
            background: #fff;
            transition: all .15s;

}

.page-wrap .filter-tab:hover {

            background: #f8fafc;
            color: #0f172a;

}

.page-wrap .filter-tab.active {

            background: #e85d24;
            border-color: #e85d24;
            color: #fff;

}

.page-wrap .kpi-row {

            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 18px;

}

@media (max-width: 768px) {
.page-wrap .kpi-row {

                grid-template-columns: 1fr;

}
}

.page-wrap .kpi-box {

            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 18px;

}

.page-wrap .kpi-box-label {

            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;

}

.page-wrap .kpi-box-val {

            font-size: 20px;
            font-weight: 800;
            color: #0f172a;

}

.page-wrap .kpi-box-val.accent {

            color: #e85d24;

}

.page-wrap .panel-card {

            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 20px;

}

.page-wrap .panel-head {

            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;

}

.page-wrap .panel-title {

            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;

}

.page-wrap .btn-toggle-panel {

            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px 10px;
            cursor: pointer;
            text-decoration: none;

}

.page-wrap .btn-toggle-panel:hover {

            color: #0f172a;
            background: #f8fafc;

}

.page-wrap .clean-table {

            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;

}

.page-wrap .clean-table th {

            background: #f8fafc;
            color: #475569;
            font-weight: 800;
            font-size: 12.5px;
            padding: 11px 14px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;

}

.page-wrap .clean-table td {

            padding: 12px 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;

}

.page-wrap .clean-table tbody tr:hover {

            background-color: #f8fafc;

}

.page-wrap .clean-table tbody tr:last-child td {

            border-bottom: none;

}

.page-wrap .size-qty {

            display: inline-block;
            min-width: 28px;
            text-align: center;
            font-weight: 800;
            font-size: 13px;
            padding: 2px 6px;
            border-radius: 6px;

}

.page-wrap .size-qty.s {

            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #dcfce7;

}

.page-wrap .size-qty.m {

            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;

}

.page-wrap .size-qty.l {

            background: #faf5ff;
            color: #7e22ce;
            border: 1px solid #f3e8ff;

}

.page-wrap .size-qty.total {

            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
            font-size: 13.5px;

}

.page-wrap .size-qty.zero {

            color: #94a3b8;
            font-weight: normal;

}

.page-wrap .badge-qty {

            background: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
            font-size: 13px;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-block;

}

.page-wrap .btn-edit-main {

            background: #e85d24;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: all .15s;

}

.page-wrap .btn-edit-main:hover {

            background: #d04b16;
            color: #fff;

}

.page-wrap .btn-view-icon {

            color: #64748b;
            padding: 5px 8px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #fff;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;

}

.page-wrap .btn-view-icon:hover {

            color: #e85d24;
            border-color: #fed7aa;
            background: #fff7ed;

}
</style>
@endpush

@section('content')
    <div class="container-fluid py-4 page-wrap">
        <!-- Header -->
        <div class="page-header">
            <div>
                <h2 class="page-title">លម្អិតភ្នាក់ងារលក់</h2>
            </div>
            <div>
                <a href="{{ route('salespersons.index') }}" class="btn-back">
                    ត្រឡប់ក្រោយ
                </a>
            </div>
        </div>

        <!-- Salesperson Information Card -->
        <div class="person-card">
            <div class="person-left">
                <div class="person-avatar">
                    {{ strtoupper(substr($salesperson->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="person-name">
                        {{ $salesperson->name }}
                        @if($salesperson->status == 'active')
                            <span class="status-badge active">សកម្ម</span>
                        @else
                            <span class="status-badge inactive">អសកម្ម</span>
                        @endif
                    </h3>
                    <div class="person-meta">
                        <span>ទូរស័ព្ទ៖ <strong>{{ $salesperson->phone ?? '—' }}</strong></span>
                        <span>អតិថិជន៖ <strong>{{ number_format($salesperson->customers()->count()) }} នាក់</strong></span>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('salespersons.edit', $salesperson) }}" class="btn-edit-main">
                    កែប្រែ
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-tabs">
                @foreach(['all' => 'គ្រប់ពេល', 'today' => 'ថ្ងៃនេះ', 'month' => 'ខែនេះ', 'custom' => 'កំណត់ថ្ងៃ'] as $key => $label)
                    <a href="{{ route('salespersons.show', [$salesperson, 'period' => $key]) }}"
                       class="filter-tab {{ request('period', 'all') === $key ? 'active' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if(request('period') === 'custom')
                <form method="GET" action="{{ route('salespersons.show', $salesperson) }}" class="d-flex gap-2 align-items-center flex-wrap m-0">
                    <input type="hidden" name="period" value="custom">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm" style="width:140px; font-size:12.5px;">
                    <span class="text-muted small">ដល់</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm" style="width:140px; font-size:12.5px;">
                    <button type="submit" class="btn-edit-main" style="padding: 5px 12px; font-size: 12.5px;">អនុវត្ត</button>
                </form>
            @endif
        </div>

        <!-- KPI Row (3 boxes: Customers, Orders, Pizzas) -->
        <div class="kpi-row">
            <div class="kpi-box">
                <div class="kpi-box-label">អតិថិជនសរុប</div>
                <div class="kpi-box-val">{{ number_format($salesperson->customers()->count()) }} នាក់</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-box-label">ចំនួនកុម្ម៉ង់</div>
                <div class="kpi-box-val">{{ number_format($totalPeriodOrders ?? 0) }} ដង</div>
            </div>
            <div class="kpi-box">
                <div class="kpi-box-label">លក់ / ថែម (S+M+L)</div>
                <div class="kpi-box-val accent">{{ number_format($totalPizzasSold ?? 0) }} នំ</div>
                <div class="small text-primary">ថែម {{ number_format($totalPizzasFree) }} នំ</div>
            </div>
        </div>

        <!-- 1. Pizza Products Summary Panel (S, M, L) -->
        <div class="panel-card">
            <div class="panel-head">
                <h3 class="panel-title">
                    សង្ខេបការលក់នំភីហ្សា
                </h3>
                <button type="button" class="btn-toggle-panel" onclick="toggleProductSummary(this)">
                    លាក់/បង្ហាញ
                </button>
            </div>

            <div id="productSummaryBody">
                @if($productSummary->isEmpty())
                    <div class="p-3 text-center text-muted small">
                        គ្មានការបញ្ជាទិញនំភីហ្សាក្នុងចន្លោះពេលនេះទេ។
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="clean-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>ទំហំខ្នាតនំភីហ្សា (Pizza Size)</th>
                                    <th class="text-center">ចំនួនលក់សរុប</th>
                                    <th class="text-center">ថែម</th>
                                    <th class="text-center">អតិថិជនបានទិញ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productSummary as $index => $item)
                                    <tr>
                                        <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                        <td class="fw-bold">
                                            @if($item->id == 13)
                                                <span class="size-qty s me-1">S</span> នំតូចខ្នាត (S 15cm)
                                            @elseif($item->id == 14)
                                                <span class="size-qty m me-1">M</span> នំកណ្តាលខ្នាត (M 20cm)
                                            @elseif($item->id == 15)
                                                <span class="size-qty l me-1">L</span> នំធំខ្នាត (L 25cm)
                                            @else
                                                {{ $item->name }}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="size-qty total">
                                                {{ number_format($item->total_qty) }} នំ
                                            </span>
                                        </td>
                                        <td class="text-center text-primary">{{ number_format($item->free_qty) }}</td>
                                        <td class="text-center">
                                            <span class="text-muted">{{ number_format($item->customer_count) }} នាក់</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- 2. Customers Table with S, M, L Columns -->
        <div class="panel-card">
            <div class="panel-head">
                <h3 class="panel-title">
                    បញ្ជីអតិថិជន & បរិមាណនំតាមទំហំ
                </h3>
                <span class="text-muted small">សរុប {{ $customers->total() }} នាក់</span>
            </div>

            <div class="table-responsive">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>អតិថិជន</th>
                            <th>ប្រភេទ</th>
                            <!-- <th>លេខទូរស័ព្ទ</th>
                            <th>ទីតាំង</th> -->
                            <th class="text-center">កុម្ម៉ង់</th>
                            <th class="text-center" title="នំតូចខ្នាត (S 15cm)">នំតូច (S)</th>
                            <th class="text-center" title="នំកណ្តាលខ្នាត (M 20cm)">នំកណ្តាល (M)</th>
                            <th class="text-center" title="នំធំខ្នាត (L 25cm)">នំធំ (L)</th>
                            <th class="text-center" title="នំសរុបទាំងអស់">នំសរុប</th>
                            <!-- <th class="text-center">ស្ថានភាព</th> -->
                            <th class="text-center" style="width: 50px;">មើល</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            @php
                                $qtyS = $customerPizzas[$customer->id][13] ?? 0;
                                $qtyM = $customerPizzas[$customer->id][14] ?? 0;
                                $qtyL = $customerPizzas[$customer->id][15] ?? 0;
                                $totalPizza = $qtyS + $qtyM + $qtyL;
                                $freeS = $customerFreePizzas[$customer->id][13] ?? 0;
                                $freeM = $customerFreePizzas[$customer->id][14] ?? 0;
                                $freeL = $customerFreePizzas[$customer->id][15] ?? 0;
                            @endphp
                            <tr>
                                <td class="fw-bold">
                                    <a href="{{ route('customers.show', $customer) }}" style="color: #0f172a; text-decoration: none;">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($customer->type == 'facebook')
                                        <span class="channel-tag facebook">Facebook</span>
                                    @elseif($customer->type == 'telegram')
                                        <span class="channel-tag telegram">Telegram</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <!-- <td><span class="text-muted">{{ $customer->phone ?? '—' }}</span></td> -->
                                <!-- <td><span class="text-muted small">{{ $customer->city ?? $customer->address ?? '—' }}</span></td> -->
                                <td class="text-center">
                                    <span class="badge-qty">{{ number_format($customer->orders_count) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($qtyS > 0)
                                        <span class="size-qty s">{{ number_format($qtyS) }}</span>
                                    @else
                                        <span class="size-qty zero">—</span>
                                    @endif
                                    <small class="d-block text-primary">ថែម {{ number_format($freeS) }}</small>
                                </td>
                                <td class="text-center">
                                    @if($qtyM > 0)
                                        <span class="size-qty m">{{ number_format($qtyM) }}</span>
                                    @else
                                        <span class="size-qty zero">—</span>
                                    @endif
                                    <small class="d-block text-primary">ថែម {{ number_format($freeM) }}</small>
                                </td>
                                <td class="text-center">
                                    @if($qtyL > 0)
                                        <span class="size-qty l">{{ number_format($qtyL) }}</span>
                                    @else
                                        <span class="size-qty zero">—</span>
                                    @endif
                                    <small class="d-block text-primary">ថែម {{ number_format($freeL) }}</small>
                                </td>
                                <td class="text-center">
                                    @if($totalPizza > 0)
                                        <span class="size-qty total">{{ number_format($totalPizza) }}</span>
                                    @else
                                        <span class="size-qty zero">0</span>
                                    @endif
                                    <small class="d-block text-primary">ថែម {{ number_format($freeS + $freeM + $freeL) }}</small>
                                </td>
                                <!-- <td class="text-center">
                                    @if($customer->status == 'active')
                                        <span class="status-badge active">សកម្ម</span>
                                    @elseif($customer->status == 'inactive')
                                        <span class="status-badge inactive">អសកម្ម</span>
                                    @else
                                        <span class="text-muted small">{{ $customer->status ?? '—' }}</span>
                                    @endif
                                </td> -->
                                <td class="text-center">
                                    <a href="{{ route('customers.show', $customer) }}" class="btn-view-icon" title="មើលព័ត៌មាន">
                                        មើល
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted small">
                                    មិនទាន់មានអតិថិជនដែលគ្រប់គ្រងដោយភ្នាក់ងារលក់នេះទេ។
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4">
                    {{ $customers->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleProductSummary(btn) {
            const body = document.getElementById('productSummaryBody');
            if (body.style.display === 'none') {
                body.style.display = 'block';
            } else {
                body.style.display = 'none';
            }
        }
    </script>
@endpush
