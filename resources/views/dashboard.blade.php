@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        .dashboard-page {
            box-sizing: border-box;
            max-width: 1240px;
            margin: 0 auto;
            padding: 28px 24px 44px;
            width: 100%;
        }

        .dash-hero {
            background: linear-gradient(135deg, #0f172a 0%, #111827 58%, #c84f20 58%, #7c2d1f 100%);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 10px;
            box-shadow: 0 18px 44px rgba(15, 23, 42, .14);
            color: #fff;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 18px;
            margin-bottom: 16px;
            overflow: hidden;
            padding: 24px 26px;
            position: relative;
        }

        .dash-hero::after {
            background: radial-gradient(circle at 70% 20%, rgba(255,255,255,.18), transparent 32%);
            content: "";
            inset: 0;
            position: absolute;
            pointer-events: none;
        }

        .dash-hero > * {
            position: relative;
            z-index: 1;
        }

        .dash-title {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.1;
            margin: 0;
        }

        .dash-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .dash-brand img {
            height: 44px;
            width: auto;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(15,23,42,.12);
        }

        .dash-subtitle {
            color: #d1d5db;
            margin: 8px 0 0;
        }

        .dash-today {
            align-self: center;
            color: #fff;
            min-width: 180px;
            text-align: right;
        }

        .dash-today strong {
            display: block;
            font-size: 20px;
            margin-bottom: 4px;
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 18px;
        }

        .quick-action {
            align-items: center;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .04);
            color: #0f172a;
            display: inline-flex;
            gap: 8px;
            min-height: 38px;
            padding: 8px 12px;
            text-decoration: none;
            font-weight: 700;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .quick-action.primary {
            background: linear-gradient(135deg, #f97316, #e85d24);
            border-color: #e85d24;
            box-shadow: 0 12px 24px rgba(232, 93, 36, .18);
            color: #fff;
        }

        .quick-action:hover {
            color: #0f172a;
            box-shadow: 0 14px 28px rgba(15, 23, 42, .1);
            transform: translateY(-1px);
        }

        .quick-action.primary:hover {
            color: #fff;
            background: #d94a10;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .metric-card,
        .dash-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
        }

        .metric-card {
            overflow: hidden;
            padding: 20px;
            position: relative;
        }

        .metric-card::before {
            background: linear-gradient(90deg, rgba(232, 93, 36, .9), rgba(249, 115, 22, .14));
            content: "";
            height: 3px;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
        }

        .metric-top {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .metric-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .metric-icon {
            align-items: center;
            border-radius: 9px;
            display: inline-flex;
            height: 40px;
            justify-content: center;
            width: 40px;
        }

        .metric-value {
            color: #0f172a;
            font-size: 30px;
            font-weight: 800;
            line-height: 1.1;
        }

        .metric-sub {
            color: #64748b;
            font-size: 13px;
            margin-top: 5px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .panel-header {
            align-items: center;
            border-bottom: 1px solid #edf0f4;
            display: flex;
            justify-content: space-between;
            padding: 16px 18px;
        }

        .panel-title {
            color: #0f172a;
            font-size: 17px;
            font-weight: 800;
            margin: 0;
        }

        .panel-link {
            color: #e85d24;
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
        }

        .panel-body {
            padding: 16px 18px;
        }

        .sales-panel .panel-header {
            align-items: flex-start;
            background: linear-gradient(180deg, #fff, #fbfcfe);
            padding: 20px 22px;
        }

        .sales-panel .panel-title {
            font-size: 18px;
        }

        .sales-caption {
            color: #64748b;
            font-size: 13px;
            margin-top: 4px;
        }

        .sales-panel .panel-link {
            align-items: center;
            background: #fff4ef;
            border-radius: 999px;
            color: #e85d24;
            display: inline-flex;
            gap: 6px;
            min-height: 32px;
            padding: 7px 12px;
        }

        .sales-panel .panel-body {
            padding: 24px 24px 22px;
        }

        .bar-chart {
            align-items: end;
            background: repeating-linear-gradient(
                to bottom,
                transparent 0,
                transparent 39px,
                #edf1f5 40px
            );
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            min-height: 250px;
            padding: 8px 0 4px;
        }

        .bar-item {
            align-items: end;
            display: grid;
            gap: 9px;
            grid-template-rows: 1fr auto auto;
            min-width: 0;
        }

        .bar-track {
            align-items: end;
            background: transparent;
            border: 0;
            border-radius: 0;
            display: flex;
            height: 165px;
            justify-self: center;
            padding: 0;
            position: relative;
            width: min(100%, 64px);
        }

        .bar-fill {
            background: linear-gradient(180deg, #f97316, #e85d24);
            border-radius: 6px;
            box-shadow: 0 8px 16px rgba(232, 93, 36, .18);
            min-height: 4px;
            width: 100%;
        }

        .bar-value {
            bottom: calc(var(--bar-height) + 6px);
            color: #0f172a;
            font-size: 11px;
            font-weight: 900;
            left: 50%;
            line-height: 1;
            position: absolute;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .bar-money {
            color: #0f172a;
            font-size: 13px;
            font-weight: 800;
            text-align: center;
        }

        .bar-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
        }

        .bar-date {
            color: #94a3b8;
            font-size: 9px;
            margin-top: -5px;
            text-align: center;
        }

        .status-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-top: 18px;
        }

        .status-tile {
            background: linear-gradient(180deg, #fff, #f8fafc);
            border: 1px solid #edf0f4;
            border-radius: 8px;
            padding: 16px;
            position: relative;
        }

        .status-tile::before {
            background: #e85d24;
            border-radius: 999px;
            content: "";
            height: 28px;
            opacity: .12;
            position: absolute;
            right: 14px;
            top: 14px;
            width: 28px;
        }

        .status-tile strong {
            color: #0f172a;
            display: block;
            font-size: 22px;
            line-height: 1;
            margin-bottom: 5px;
        }

        .status-tile span {
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
        }

        .list-row {
            align-items: center;
            border-bottom: 1px solid #edf0f4;
            display: grid;
            gap: 12px;
            grid-template-columns: minmax(0, 1fr) auto;
            padding: 12px 0;
        }

        .list-row:first-child {
            padding-top: 0;
        }

        .list-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .row-title {
            color: #0f172a;
            font-weight: 800;
            line-height: 1.2;
        }

        .row-meta {
            color: #64748b;
            font-size: 13px;
            margin-top: 3px;
        }

        .amount {
            color: #0f172a;
            font-weight: 800;
            text-align: right;
        }

        .pill {
            border-radius: 999px;
            display: inline-flex;
            font-size: 11px;
            font-weight: 800;
            padding: 5px 9px;
            text-transform: capitalize;
        }

        .pill.pending,
        .pill.partial,
        .pill.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .pill.processing {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .pill.completed,
        .pill.paid {
            background: #d1fae5;
            color: #065f46;
        }

        .pill.cancelled,
        .pill.unpaid,
        .pill.critical {
            background: #fee2e2;
            color: #991b1b;
        }

        .side-stack {
            display: grid;
            gap: 16px;
        }

        .empty-note {
            color: #64748b;
            padding: 12px 0;
        }

        @media (max-width: 1180px) {
            .metric-grid,
            .content-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .dashboard-page {
                padding: 16px;
            }

            .dash-hero,
            .metric-grid,
            .status-grid {
                grid-template-columns: 1fr;
            }

            .dash-hero::after {
                width: 100%;
                opacity: .2;
            }

            .dash-today {
                text-align: left;
            }

            .bar-value {
                font-size: 8px;
            }

            .bar-date {
                font-size: 8px;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $fmt = fn($value) => number_format((float) $value, 2);
        $fmtKhr = fn($value) => number_format((float) $value);
    @endphp

    <div class="dashboard-page">
        <section class="dash-hero">
            <div class="dash-brand">
                <img src="{{ asset('assets/logos/logo_pizza.png') }}" alt="Pizza Happy Family Logo">
                <div>
                    <h1 class="dash-title">Pizza Happy Family</h1>
                    <p class="dash-subtitle">Welcome back, {{ auth()->user()->name ?? 'Admin' }}.</p>
                </div>
            </div>
            <div class="dash-today">
                <strong>{{ now('Asia/Phnom_Penh')->format('h:i A') }}</strong>
                <span>{{ now('Asia/Phnom_Penh')->translatedFormat('l, d F Y') }}</span>
            </div>
        </section>

        <nav class="quick-actions" aria-label="Dashboard quick actions">
            <a href="{{ route('orders.create') }}" class="quick-action primary"><i class="fas fa-plus"></i> New Order</a>
            <a href="{{ route('payments.index') }}" class="quick-action"><i class="fas fa-credit-card"></i> ការទូទាត់</a>
            <a href="{{ route('packing.index') }}" class="quick-action"><i class="fas fa-box-open"></i> ការរៀបចំទំនិញ</a>
            <a href="{{ route('purchases.index') }}" class="quick-action"><i class="fas fa-file-invoice"></i> ការចំណាយប្រចាំថ្ងៃ</a>
            <a href="{{ route('inventory.index') }}" class="quick-action"><i class="fas fa-boxes-stacked"></i> ស្តុកទំនិញ</a>
        </nav>



        <section class="content-grid">
            <div class="dash-panel sales-panel">
                <div class="panel-header">
                    <div>
                        <h2 class="panel-title">Sales Last 7 Days</h2>
                        <div class="sales-caption">Daily revenue from non-cancelled orders</div>
                    </div>
                    <a href="{{ route('reports.sales') }}" class="panel-link">
                        View report <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="panel-body">
                    <div class="bar-chart">
                        @foreach($sales_by_day as $day)
                            @php
                                $barHeight = max(4, ($day['total'] / $maxSales) * 100);
                                $barValue = '$' . number_format((float) $day['total'], 2);
                            @endphp
                            <div class="bar-item" title="{{ $day['date'] }}: ${{ $fmt($day['total']) }}">
                                <div class="bar-track">
                                    <div class="bar-fill" style="--bar-height: {{ $barHeight }}%; height: {{ $barHeight }}%;"></div>
                                    <span class="bar-value" style="--bar-height: {{ $barHeight }}%;">{{ $barValue }}</span>
                                </div>
                                <div class="bar-label">{{ $day['label'] }}</div>
                                <div class="bar-date">{{ $day['date'] }}</div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>


        </section>

    </div>
@endsection
