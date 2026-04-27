@extends('layouts.app')

@section('title', 'ព័ត៌មានចំណាយ')

@push('styles')
    <style>
        .expense-show {
            max-width: 960px;
            margin: 0 auto;
            padding: 28px 20px;
        }

        .expense-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
            padding: 24px;
        }

        .expense-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .expense-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .expense-value {
            color: #0f172a;
            font-size: 18px;
            font-weight: 700;
            margin-top: 5px;
        }

        .expense-money {
            color: #e85d24;
            font-size: 28px;
            font-weight: 800;
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

        .expense-primary {
            background: #e85d24;
            border-color: #e85d24;
            color: #fff;
            font-weight: 700;
        }

        .expense-primary:hover {
            background: #d94a10;
            border-color: #d94a10;
            color: #fff;
        }

        @media (max-width: 800px) {
            .expense-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $exchangeRate = 4000;
        $statusLabels = [
            'pending' => 'មិនទាន់ទូទាត់',
            'received' => 'បានទូទាត់',
            'cancelled' => 'បានលុប',
        ];
        $reference = $purchase->reference_number ?: 'EXP-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT);
    @endphp

    <div class="expense-show">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h1 class="mb-1" style="font-size: 30px; font-weight: 800; color: #0f172a;">ព័ត៌មានចំណាយ</h1>
                <p class="text-muted mb-0">{{ $reference }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('purchases.edit', $purchase) }}" class="btn expense-primary">
                    <i class="fas fa-pen me-1"></i> Edit
                </a>
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                    Back
                </a>
            </div>
        </div>

        <div class="expense-card mb-3">
            <div class="expense-grid">
                <div>
                    <div class="expense-label">លេខយោង</div>
                    <div class="expense-value">{{ $reference }}</div>
                </div>
                <div>
                    <div class="expense-label">ប្រភេទ / អ្នកទទួល</div>
                    <div class="expense-value">{{ $purchase->supplier_name }}</div>
                </div>
                <div>
                    <div class="expense-label">ស្ថានភាព</div>
                    <div class="expense-value">
                        <span class="expense-status {{ $purchase->status }}">
                            {{ $statusLabels[$purchase->status] ?? ucfirst($purchase->status) }}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="expense-label">កាលបរិច្ឆេទ</div>
                    <div class="expense-value">{{ $purchase->purchase_date->translatedFormat('d M Y') }}</div>
                </div>
                <div>
                    <div class="expense-label">ចំនួនទឹកប្រាក់</div>
                    <div class="expense-money">${{ number_format($purchase->total_amount, 2) }}</div>
                    <div class="text-muted">៛{{ number_format($purchase->total_amount * $exchangeRate) }}</div>
                </div>
                <div>
                    <div class="expense-label">Created</div>
                    <div class="expense-value">
                        {{ $purchase->created_at ? $purchase->created_at->setTimezone('Asia/Phnom_Penh')->translatedFormat('d M Y h:i A') : '' }}
                    </div>
                </div>
            </div>
        </div>

        @if($purchase->notes)
            <div class="expense-card mb-3">
                <div class="expense-label mb-2">កំណត់ចំណាំ</div>
                <div style="white-space: pre-line;">{{ $purchase->notes }}</div>
            </div>
        @endif

        <div class="expense-card d-flex flex-wrap gap-2">
            <a href="{{ route('purchases.edit', $purchase) }}" class="btn expense-primary">Edit Expense</a>
            <form action="{{ route('purchases.destroy', $purchase) }}" method="POST"
                onsubmit="return confirm('Delete this daily expense?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete Expense</button>
            </form>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
@endsection
