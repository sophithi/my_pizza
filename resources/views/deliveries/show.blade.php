@extends('layouts.app')

@section('title', 'Delivery: ' . $delivery->delivery_name)

@push('styles')
    <style>
        :root {
            --accent: #e85d24;
            --bg: #f4f5f7;
            --surface: #ffffff;
            --border: #e9ecef;
            --text: #1a1d29;
            --text-muted: #6c757d;
        }

        body {
            background: var(--bg);
        }

        .detail-container {
            max-width: 700px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 28px;
        }

        .detail-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }

        .btn-primary:hover {
            background: #d94a10;
            color: #fff;
        }

        .btn-danger {
            background: #dc3545;
            color: #fff;
        }

        .btn-danger:hover {
            background: #c82333;
            color: #fff;
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #5a6268;
            color: #fff;
        }

        .card {
            background: var(--surface);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .info-group {
            margin-bottom: 24px;
        }

        .info-group:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
        }

        .price-big {
            font-size: 28px;
            font-weight: 800;
            color: var(--accent);
        }

        .price-khr {
            font-size: 16px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .desc-text {
            font-size: 14px;
            color: var(--text);
            line-height: 1.7;
            white-space: pre-wrap;
        }
    </style>
@endpush

@section('content')
    <div class="detail-container">

        @if(session('success'))
            <div
                style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="detail-header">
            <div>
                <h1 class="detail-title"> {{ $delivery->delivery_name }}</h1>
            </div>
            <div class="header-actions">
                <a href="{{ route('deliveries.edit', $delivery) }}" class="btn btn-primary"> កែប្រែ</a>
                <form action="{{ route('deliveries.destroy', $delivery) }}" method="POST" style="display:inline;"
                    onsubmit="return confirm('លុបការដឹកជញ្ជូននេះ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"> លុប</button>
                </form>
                <a href="{{ route('deliveries.index') }}" class="btn btn-secondary">← ត្រឡប់</a>
            </div>
        </div>

        <div class="card">
            <div class="info-group">
                <div class="info-label">ឈ្មោះដឹកជញ្ជូន</div>
                <div class="info-value">{{ $delivery->delivery_name }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">តម្លៃដឹកជញ្ជូន</div>
                <div class="price-big">៛{{ number_format($delivery->delivery_price_khr, 0) }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">ផ្សេងៗ</div>
                @if($delivery->delivery_desc)
                    <div class="desc-text">{{ $delivery->delivery_desc }}</div>
                @else
                    <div style="color: var(--text-muted);">—</div>
                @endif
            </div>
        </div>
    </div>
@endsection