@extends('layouts.app')

@section('title', 'Delivery: ' . $delivery->delivery_name)

@push('styles')
    <style>
        .show-page {
            --accent: #e85d24;
            --accent-dark: #d94a10;
            --surface: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
        }

        .show-container {
            max-width: 1080px;
            margin: 24px auto;
            padding: 0 24px 48px;
        }

        /* ── Back link ── */
        .show-back {
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }

        .show-back:hover {
            color: var(--accent);
        }

        /* ── Header ── */
        .show-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .show-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
            margin: 0;
        }

        .show-subtitle {
            font-size: 14px;
            color: var(--muted);
            margin: 4px 0 0;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ── Buttons ── */
        .btn {
            align-items: center;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            font-size: 14px;
            font-weight: 700;
            gap: 7px;
            min-height: 40px;
            padding: 9px 20px;
            text-decoration: none;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
        }

        .btn-primary:hover {
            color: #fff;
            box-shadow: 0 6px 16px rgba(232, 93, 36, 0.3);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: #dc2626;
            color: #fff;
        }

        .btn-danger:hover {
            background: #b91c1c;
            color: #fff;
        }

        /* ── Two-column layout ── */
        .show-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 18px;
            align-items: start;
        }

        .show-left {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .show-right {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* ── Card ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .card-header {
            border-bottom: 1px solid var(--border);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f9fafb;
        }

        .card-header-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #fff5f0;
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .card-header-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--text);
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        /* ── Info grid ── */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }

        .info-item label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--muted);
            margin-bottom: 6px;
        }

        .info-item span {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
        }

        .info-item span.accent {
            color: var(--accent);
            font-size: 22px;
            font-weight: 800;
        }

        /* ── Stats ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .stat-card {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 12px;
            text-align: center;
        }

        .stat-val {
            font-size: 20px;
            font-weight: 800;
            color: var(--accent);
            line-height: 1.2;
        }

        .stat-lbl {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--muted);
            margin-top: 4px;
            letter-spacing: 0.4px;
        }

        /* ── Case selector ── */
        .case-selector {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .case-option-card {
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
        }

        .case-option-card:hover {
            border-color: var(--accent);
        }

        .case-option-card.selected {
            border-color: var(--accent);
            background: #fff8f5;
        }

        .case-option-card input[type="radio"] {
            accent-color: var(--accent);
            width: 16px;
            height: 16px;
        }

        .case-label {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }

        .case-price {
            font-size: 13px;
            color: var(--muted);
            margin-top: 2px;
        }

        .selected-price-display {
            margin-top: 12px;
            padding: 14px 16px;
            background: #fff5f0;
            border: 1px solid rgba(232, 93, 36, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .price-label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 600;
        }

        .price-value {
            font-size: 22px;
            font-weight: 800;
            color: var(--accent);
        }

        /* ── Orders table ── */
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .orders-table th {
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            padding: 12px 16px;
            text-transform: uppercase;
            text-align: left;
            letter-spacing: 0.4px;
        }

        .orders-table td {
            border-bottom: 1px solid #f1f3f5;
            color: var(--text);
            padding: 13px 16px;
            vertical-align: middle;
        }

        .orders-table tr:last-child td {
            border-bottom: none;
        }

        .orders-table tr:hover td {
            background: #fafafa;
        }

        .badge-inv {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 800;
            padding: 3px 9px;
        }

        .empty-orders {
            padding: 40px 20px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
        }

        .empty-orders i {
            font-size: 32px;
            color: #d1d5db;
            display: block;
            margin-bottom: 10px;
        }

        @media (max-width: 800px) {
            .show-grid {
                grid-template-columns: 1fr;
            }

            .show-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-3 show-page">
        <div class="show-container">

            @if(session('success'))
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <a href="{{ route('deliveries.index') }}" class="show-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>

            <div class="show-header">
                <div>
                    <h1 class="show-title">{{ $delivery->delivery_name }}</h1>
                </div>
                <div class="header-actions">
                    <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> កែប្រែ
                    </a>
                    <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" style="display:inline;"
                        onsubmit="return confirm('លុបការដឹកជញ្ជូននេះ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> លុប
                        </button>
                    </form>
                </div>
            </div>

            @php
                $caseUnit = 5000;
                $oneCasePrice = $caseUnit;
                $twoCasePrice = $caseUnit * 2;
                $totalFee = $delivery->orders->sum('delivery_fee_khr');
                $totalAmount = $delivery->orders->sum(fn($o) => $o->invoice?->total_amount ?? $o->total_amount ?? 0);
                $orderCount = $delivery->orders_count ?? $delivery->orders->count();
            @endphp

            <div class="show-grid">

                {{-- LEFT --}}
                <div class="show-left">

                    {{-- Delivery Info --}}
                    <!-- <div class="card">
                        <div class="card-header">
                            <div class="card-header-icon"><i class="fas fa-truck"></i></div>
                            <p class="card-header-title">Delivery Information</p>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <label>ឈ្មោះដឹកជញ្ជូន</label>
                                    <span>{{ $delivery->delivery_name }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Invoice Qty</label>
                                    <span class="accent">{{ $orderCount }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Created At</label>
                                    <span>{{ $delivery->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Updated At</label>
                                    <span>{{ $delivery->updated_at->format('d M Y') }}</span>
                                </div>
                                @if($delivery->delivery_desc)
                                    <div class="info-item" style="grid-column: 1 / -1;">
                                        <label>ផ្សេងៗ</label>
                                        <span
                                            style="font-size: 14px; font-weight: 400; line-height: 1.7; white-space: pre-wrap;">{{ $delivery->delivery_desc }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div> -->

                    {{-- Linked Invoices --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-icon"><i class="fas fa-file-invoice"></i></div>
                            <p class="card-header-title">វិក្ក័យបត្រ</p>
                        </div>
                        <div class="table-responsive">
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>ឈ្មោះអតិថិជន</th>
                                        <th>ទំនាក់ទំនង</th>
                                        <th style="text-align:center;">ចំនួនកេស</th>
                                        <th style="text-align:right;">ថ្លៃដឹក</th>
                                        <th style="text-align:right;">តម្លៃសរុប</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($delivery->orders as $order)
                                        @php
                                            $invoice = $order->invoice;
                                            $customer = $order->customer;
                                            $deliveryFee = (float) $order->delivery_fee_khr;
                                            $caseCount = $deliveryFee > 0 ? $deliveryFee / $caseUnit : 0;
                                            $caseLabel = $caseCount > 0
                                                ? ($caseCount == floor($caseCount) ? number_format($caseCount, 0) : number_format($caseCount, 2))
                                                : '0';
                                            $customerName = $customer?->customer_name ?? 'N/A';
                                            $customerPhone = $customer?->customer_phone ?? '—';
                                            $totalPrice = $invoice?->total_amount ?? $order->total_amount ?? 0;
                                        @endphp
                                        <tr>
                                            <td><span class="badge-inv">{{ $invoice?->invoice_number ?? 'N/A' }}</span></td>
                                            <td>{{ $customerName }}</td>
                                            <td>{{ $customerPhone }}</td>
                                            <td style="text-align:center; font-weight:700;">{{ $caseLabel }}</td>
                                            <td style="text-align:right; font-weight:700; color:var(--accent);">
                                                ៛{{ number_format($deliveryFee, 0) }}
                                            </td>
                                            <td style="text-align:right; font-weight:700;">
                                                ${{ number_format($totalPrice, 0) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-orders">
                                                    <i class="fas fa-file-invoice"></i>
                                                    មិនមានវិក្ក័យបត្រ.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- RIGHT --}}
                <div class="show-right">

                    {{-- Summary --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="card-header-icon"><i class="fas fa-chart-bar"></i></div>
                            <p class="card-header-title">Summary</p>
                        </div>
                        <div class="card-body">
                            <div class="stats-row">
                                <div class="stat-card">
                                    <div class="stat-val">{{ $orderCount }}</div>
                                    <div class="stat-lbl">ចំនួនវិក្ក័យបត្រ</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-val" style="font-size: 15px;">៛{{ number_format($totalFee, 0) }}</div>
                                    <div class="stat-lbl">សរុបចំនួនថ្លៃដឹក</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-val" style="font-size: 15px;">${{ number_format($totalAmount, 0) }}
                                    </div>
                                    <div class="stat-lbl">សរុប</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Case / Price selector --}}
                    <!-- <div class="card">
                        <div class="card-header">
                            <div class="card-header-icon"><i class="fas fa-money-bill-wave"></i></div>
                            <p class="card-header-title">តម្លៃ</p>
                        </div>
                        <div class="card-body">
                            <div class="case-selector">
                                <label class="case-option-card selected" id="card-case1">
                                    <input type="radio" name="case" value="1" checked onchange="selectCase(1)">
                                    <div>
                                        <div class="case-label">1 កេស</div>
                                        <div class="case-price">៛{{ number_format($oneCasePrice, 0) }}</div>
                                    </div>
                                </label>
                                <label class="case-option-card" id="card-case2">
                                    <input type="radio" name="case" value="2" onchange="selectCase(2)">
                                    <div>
                                        <div class="case-label">2 កេស</div>
                                        <div class="case-price">៛{{ number_format($twoCasePrice, 0) }}</div>
                                    </div>
                                </label>
                            </div>
                            <div class="selected-price-display">
                                <span class="price-label" id="selectedCaseText">Selected: 1 កេស</span>
                                <span class="price-value" id="deliveryPrice">៛{{ number_format($oneCasePrice, 0) }}</span>
                            </div>
                        </div>
                    </div> -->

                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const prices = { 1: {{ $oneCasePrice }}, 2: {{ $twoCasePrice }} };

        function selectCase(num) {
            document.getElementById('card-case1').classList.toggle('selected', num === 1);
            document.getElementById('card-case2').classList.toggle('selected', num === 2);
            document.getElementById('deliveryPrice').textContent = '៛' + prices[num].toLocaleString();
            document.getElementById('selectedCaseText').textContent = 'Selected: ' + num + ' កេស';
        }
    </script>
@endpush
