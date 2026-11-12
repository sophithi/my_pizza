@push('styles')
    <style>
        .stock-feed {
            padding: 0;
            overflow: hidden;
        }

        .stock-feed-heading {
            padding: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .stock-feed-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stock-feed-title h3 {
            margin-bottom: 5px;
        }

        .stock-feed-title p {
            margin: 0;
        }

        .stock-feed-icon {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            background: #fff1e8;
            color: #e85d24;
            border-radius: 12px;
        }

        .stock-feed-count {
            white-space: nowrap;
            font-size: 11px;
            padding: 5px 9px;
            background: #f1f5f9;
            color: #64748b;
            border-radius: 20px;
        }

        .stock-feed-columns,
        .stock-feed-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 65px 65px 90px;
            gap: 10px;
            align-items: center;
        }

        .stock-feed-columns {
            background: #f8fafc;
            border-block: 1px solid #edf1f5;
            padding: 12px 22px;
            font-size: 11px;
            color: #64748b;
        }

        .stock-feed-columns span:not(:first-child) {
            text-align: center;
        }

        .stock-feed-row {
            padding: 18px 22px;
            border-bottom: 1px solid #edf1f5;
            transition: background .15s;
        }

        .stock-feed-row:last-child {
            border-bottom: 0;
        }

        .stock-feed-row:hover {
            background: #fcfcfd;
        }

        .stock-feed-product strong {
            font-size: 13px;
            color: #0f172a;
            line-height: 1.7;
        }

        .stock-feed-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 3px 9px;
            font-size: 10px;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.7;
        }

        .stock-feed-meta time {
            color: #94a3b8;
        }

        .stock-feed-context {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            font-size: 10px;
            color: #94a3b8;
            margin-top: 7px;
        }

        .stock-feed-order {
            color: #64748b;
            background: #f1f5f9;
            border-radius: 5px;
            padding: 2px 6px;
            font-variant-numeric: tabular-nums;
        }

        .stock-feed-number {
            text-align: center;
            font-size: 14px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        .stock-feed-sold,
        .stock-feed-free {
            display: inline-block;
            padding: 6px 9px;
            border-radius: 8px;
            min-width: 38px;
            background: #fff3e9;
            color: #c2410c;
        }

        .stock-feed-free {
            background: #eff6ff;
            color: #2563eb;
        }

        .stock-feed-na {
            color: #cbd5e1;
            background: transparent;
            font-weight: 400;
        }

        .stock-feed-delta {
            color: #64748b;
        }

        .stock-feed-delta.negative {
            color: #e5484d;
        }

        .stock-feed-delta.positive {
            color: #059669;
        }

        .stock-feed-mobile-label {
            display: none;
        }

        .stock-feed-empty {
            padding: 45px 20px;
            text-align: center;
            color: #94a3b8;
        }

        .stock-feed-empty i {
            font-size: 28px;
            margin-bottom: 12px;
        }

        @media(max-width:650px) {
            .stock-feed-heading {
                padding: 18px 16px;
                align-items: flex-start;
            }

            .stock-feed-count {
                display: none;
            }

            .stock-feed-columns {
                display: none;
            }

            .stock-feed-row {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                padding: 16px;
                gap: 12px;
            }

            .stock-feed-product {
                grid-column: 1/-1;
            }

            .stock-feed-number {
                text-align: left;
            }

            .stock-feed-mobile-label {
                display: block;
                font-size: 10px;
                font-weight: 400;
                color: #94a3b8;
                margin-bottom: 6px;
            }
        }
    </style>
@endpush
<div class="container-fluid inventory-page pb-4">
    <div class="analytics-grid">
        <section class="analytics-card stock-feed">
            <div class="stock-feed-heading">
                <div class="stock-feed-title"><span class="stock-feed-icon"><i
                            class="fas fa-clock-rotate-left"></i></span>
                    <h3>ចលនាស្តុកថ្មីៗ</h3>
                </div>
                <span class="stock-feed-count">{{ $analytics['recentActivities']->count() }}</span>
            </div>
            <div class="stock-feed-columns" aria-hidden="true">
                <span>ទំនិញ</span><span>លក់</span><span>ថែម</span><span>ចលនាស្តុក</span></div>
            @forelse($analytics['recentActivities'] as $activity)
                @php
                    $isOrderMovement = in_array($activity->type, ['order_deduct', 'order_restore', 'order_adjust'], true);
                    $classified = in_array($activity->item_type, ['sale', 'free'], true);
                    $productItems = $activity->order?->items->where('product_id', $activity->product_id);
                    $hasCurrentSplit = !$classified && $isOrderMovement && $productItems?->isNotEmpty();
                    $sold = $classified ? ($activity->item_type === 'sale' ? abs($activity->quantity_change) : null) : ($hasCurrentSplit ? $productItems->where('item_type', 'sale')->sum('quantity') : null);
                    $free = $classified ? ($activity->item_type === 'free' ? abs($activity->quantity_change) : null) : ($hasCurrentSplit ? $productItems->where('item_type', 'free')->sum('quantity') : null);
                    $positive = $activity->quantity_change > 0;
                @endphp
                <div class="stock-feed-row">
                    <div class="stock-feed-product">
                        <strong>{{ $activity->product?->name ?? 'ទំនិញចាស់' }}</strong>
                        <div class="stock-feed-meta"><time>{{ $activity->created_at?->format('d/m/Y · H:i') }}</time></div>
                        @if($isOrderMovement)
                            <div class="stock-feed-context">
                                @if($activity->order_id)<span class="stock-feed-order">#{{ $activity->order_id }}</span>@endif
                                @if($hasCurrentSplit)<span title="ចំនួនលក់ និងថែមពីការបញ្ជាទិញបច្ចុប្បន្ន អាចខុសពីចលនាដើម">តាម
                                    Order បច្ចុប្បន្ន</span>
                                @elseif(!$classified)<span>មិនបានបែងចែក</span>@endif
                            </div>
                        @endif
                    </div>
                    <div class="stock-feed-number"><span class="stock-feed-mobile-label">លក់</span><span
                            class="stock-feed-sold {{ $sold === null ? 'stock-feed-na' : '' }}">{{ $sold === null ? '—' : number_format($sold) }}</span>
                    </div>
                    <div class="stock-feed-number"><span class="stock-feed-mobile-label">ថែម</span><span
                            class="stock-feed-free {{ $free === null ? 'stock-feed-na' : '' }}">{{ $free === null ? '—' : number_format($free) }}</span>
                    </div>
                    <div class="stock-feed-number"><span class="stock-feed-mobile-label">ចលនាស្តុក</span><span
                            class="stock-feed-delta {{ $positive ? 'positive' : ($activity->quantity_change < 0 ? 'negative' : '') }}">{{ $positive ? '+' : '' }}{{ number_format($activity->quantity_change) }}</span>
                    </div>
                </div>
            @empty<div class="stock-feed-empty"><i class="fas fa-box-open"></i>
                <p>មិនទាន់មានចលនាស្តុក</p>
            </div>@endforelse
        </section>
        <section class="analytics-card">
            <h3>ការជូនដំណឹងស្តុកជិតអស់</h3>
            @forelse($analytics['lowStockAlerts'] as $alert)
                <a class="d-flex justify-content-between align-items-center gap-2 py-3 border-bottom text-decoration-none"
                    href="{{ route('inventory.show', $alert) }}">
                    <div><strong class="text-dark">{{ $alert->product?->name ?? 'ទំនិញ' }}</strong>
                        <div class="analytics-muted">កម្រិតជិតអស់: {{ number_format($alert->reorder_level) }}</div>
                    </div>
                    <span
                        class="badge {{ $alert->quantity <= 0 ? 'bg-danger' : 'bg-warning text-dark' }}">{{ number_format($alert->quantity) }}</span>
                </a>
            @empty<div class="py-5 text-center text-success"><i class="fas fa-check-circle fs-3 mb-3"></i>
                <p>គ្រប់មុខទំនិញមានស្តុកគ្រប់គ្រាន់</p>
            </div>@endforelse
        </section>
    </div>
</div>