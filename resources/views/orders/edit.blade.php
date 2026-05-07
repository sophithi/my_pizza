@extends('layouts.app')

@section('title', 'កែបញ្ជាទិញ')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .order-edit {
        --accent: #e85d24;
        --accent-dark: #d94a10;
        --border: #e5e7eb;
        --muted: #6b7280;
        --surface: #fff;
        --text: #111827;
        --bg: #f5f7fa;
        --danger: #dc2626;
        --shadow: 0 12px 32px rgba(15, 23, 42, .07);
    }

    .edit-header {
        align-items: flex-start;
        display: flex;
        gap: 16px;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .edit-title {
        color: var(--text);
        font-size: 28px;
        font-weight: 900;
        margin: 0;
    }

    .edit-subtitle {
        color: var(--muted);
        margin: 6px 0 0;
    }

    .edit-shell {
        display: grid;
        gap: 16px;
        grid-template-columns: minmax(0, 1.25fr) minmax(360px, .75fr);
    }

    .edit-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: var(--shadow);
        padding: 16px;
    }

    .section-title {
        align-items: center;
        color: var(--text);
        display: flex;
        font-size: 17px;
        font-weight: 900;
        gap: 9px;
        margin: 0 0 14px;
    }

    .section-title i {
        align-items: center;
        background: #fff7ed;
        border-radius: 8px;
        color: var(--accent);
        display: inline-flex;
        height: 30px;
        justify-content: center;
        width: 30px;
    }

    .form-label {
        color: var(--text);
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .form-control,
    .form-select {
        border: 1px solid var(--border);
        border-radius: 8px;
        min-height: 42px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(232, 93, 36, .12);
    }

    .customer-card {
        background: #f9fafb;
        border: 1px solid #eef2f7;
        border-radius: 8px;
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-top: 12px;
        padding: 12px;
    }

    .customer-label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .customer-value {
        color: var(--text);
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .products-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fill, minmax(145px, 1fr));
        max-height: 560px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .product-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 8px;
        cursor: pointer;
        min-height: 154px;
        padding: 10px;
        text-align: center;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }

    .product-card:hover {
        border-color: var(--accent);
        box-shadow: 0 10px 20px rgba(232, 93, 36, .13);
        transform: translateY(-2px);
    }

    .product-image {
        aspect-ratio: 1.25 / 1;
        background: var(--bg);
        border-radius: 6px;
        object-fit: cover;
        width: 100%;
    }

    .product-name {
        color: var(--text);
        font-size: 13px;
        font-weight: 800;
        margin-top: 9px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .product-price {
        color: var(--accent);
        font-size: 12px;
        font-weight: 900;
        margin-top: 3px;
    }

    .cart-panel {
        position: sticky;
        top: 88px;
    }

    .cart-items {
        display: grid;
        gap: 10px;
        max-height: 360px;
        overflow-y: auto;
    }

    .cart-item {
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px;
    }

    .cart-item-top {
        align-items: flex-start;
        display: flex;
        gap: 10px;
        justify-content: space-between;
    }

    .cart-item-name {
        color: var(--text);
        font-weight: 900;
    }

    .cart-item-price {
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
        margin-top: 3px;
    }

    .cart-controls {
        align-items: center;
        display: grid;
        gap: 8px;
        grid-template-columns: 76px 92px 36px;
        margin-top: 10px;
    }

    .cart-controls input {
        border: 1px solid var(--border);
        border-radius: 7px;
        min-height: 34px;
        padding: 5px 8px;
        width: 100%;
    }

    .remove-btn {
        align-items: center;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 7px;
        color: var(--danger);
        display: inline-flex;
        height: 34px;
        justify-content: center;
        width: 36px;
    }

    .summary-box {
        background: #fbfdff;
        border: 1px solid var(--border);
        border-radius: 8px;
        margin-top: 14px;
        padding: 14px;
    }

    .summary-row {
        align-items: center;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
    }

    .summary-row:last-child {
        border-bottom: 0;
    }

    .summary-total {
        background: linear-gradient(135deg, #fff7ed, #ffedd5);
        border: 1px solid #fed7aa;
        border-radius: 8px;
        color: var(--accent-dark);
        font-size: 20px;
        font-weight: 900;
        margin-top: 10px;
        padding: 12px;
    }

    .action-row {
        display: flex;
        gap: 10px;
        margin-top: 14px;
    }

    .edit-btn {
        align-items: center;
        border: 0;
        border-radius: 8px;
        display: inline-flex;
        font-weight: 900;
        gap: 8px;
        justify-content: center;
        min-height: 42px;
        padding: 10px 14px;
        text-decoration: none;
        width: 100%;
    }

    .edit-btn-primary {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #fff;
    }

    .edit-btn-primary:hover {
        color: #fff;
    }

    .edit-btn-soft {
        background: #f3f4f6;
        color: #374151;
    }

    .empty-cart {
        color: var(--muted);
        padding: 26px 12px;
        text-align: center;
    }

    @media (max-width: 1100px) {
        .edit-shell {
            grid-template-columns: 1fr;
        }

        .cart-panel {
            position: static;
        }
    }

    @media (max-width: 700px) {
        .edit-header,
        .action-row {
            flex-direction: column;
        }

        .customer-card {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $initialCart = $order->items->mapWithKeys(function ($item) {
        $product = $item->product;
        $unitPrice = (float) $item->unit_price;
        $lineTotal = (float) $item->total_price;
        $qty = max((int) $item->quantity, 1);
        $effectiveUnit = $lineTotal / $qty;

        return [
            (string) $item->product_id => [
                'name' => $product?->name ?? 'N/A',
                'price' => $unitPrice,
                'price_khr' => (float) ($product?->price_khr ?? $unitPrice * 4000),
                'qty' => $qty,
                'discount' => $unitPrice > 0 ? round((1 - ($effectiveUnit / $unitPrice)) * 100, 2) : 0,
                'image' => $product?->imageUrl(),
            ],
        ];
    });
@endphp

<div class="container-fluid py-4 order-edit">
    <div class="edit-header">
        <div>
            <h2 class="edit-title">កែបញ្ជាទិញ #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h2>
            <p class="edit-subtitle">កែអតិថិជន ទំនិញ ចំនួន ការដឹកជញ្ជូន និងស្ថានភាពបង់ប្រាក់</p>
        </div>
        <a href="{{ $order->invoice ? route('invoices.show', $order->invoice) : route('orders.show', $order) }}" class="edit-btn edit-btn-soft" style="width:auto;">
            <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('orders.update', $order) }}" method="POST" id="orderEditForm">
        @csrf
        @method('PUT')

        <div class="edit-shell">
            <div>
                <div class="edit-card mb-3">
                    <h3 class="section-title"><i class="fas fa-user"></i> អតិថិជន</h3>
                    <select name="customer_id" id="customer_id" class="form-control select2-customer" required>
                        <option value="">សូមស្វែងរកអតិថិជន</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                data-name="{{ $customer->name }}"
                                data-phone="{{ $customer->phone }}"
                                data-location="{{ $customer->location ?? $customer->city ?? $customer->address }}"
                                {{ (string) old('customer_id', $order->customer_id) === (string) $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="customer-card" id="customerInfo">
                        <div>
                            <div class="customer-label">ឈ្មោះ</div>
                            <div class="customer-value" id="customerName">-</div>
                        </div>
                        <div>
                            <div class="customer-label">លេខទូរស័ព្ទ</div>
                            <div class="customer-value" id="customerPhone">-</div>
                        </div>
                        <div>
                            <div class="customer-label">ទីតាំង</div>
                            <div class="customer-value" id="customerLocation">-</div>
                        </div>
                    </div>
                </div>

                <div class="edit-card">
                    <h3 class="section-title"><i class="fas fa-pizza-slice"></i> ទំនិញ</h3>
                    <div class="products-grid">
                        @foreach($products as $product)
                            @php($productImageUrl = $product->imageUrl())
                            <div class="product-card" onclick="addToCart({{ $product->id }}, @js($product->name), {{ (float) $product->price_usd }}, {{ (float) $product->price_khr }}, @js($productImageUrl))">
                                @if($productImageUrl)
                                    <img src="{{ $productImageUrl }}" alt="{{ $product->name }}" class="product-image">
                                @else
                                    <div class="product-image d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                                <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
                                <div class="product-price">${{ number_format($product->price_usd, 2) }}</div>
                                <div class="text-muted small fw-bold">៛{{ number_format($product->price_khr, 0) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="cart-panel">
                <div class="edit-card">
                    <h3 class="section-title"><i class="fas fa-receipt"></i> មុខទំនិញក្នុងបញ្ជាទិញ</h3>

                    <div class="cart-items" id="cartItems"></div>

                    <div class="summary-box">
                        <div class="summary-row">
                            <span class="text-muted fw-bold">Subtotal</span>
                            <strong>$<span id="subtotalText">0.00</span></strong>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted fw-bold">Discount</span>
                            <strong class="text-danger">-$<span id="discountText">0.00</span></strong>
                        </div>
                        <div class="summary-row">
                            <span class="text-muted fw-bold">Delivery</span>
                            <strong>
                                $<span id="deliveryUsdText">0.00</span>
                                <span class="text-muted small d-block text-end">៛<span id="deliveryKhrText">0</span></span>
                            </strong>
                        </div>
                        <div class="summary-total d-flex justify-content-between align-items-center">
                            <span>សរុប</span>
                            <span>$<span id="totalText">0.00</span></span>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">កាលបរិច្ឆេទ</label>
                            <input type="datetime-local" name="order_date" class="form-control"
                                value="{{ old('order_date', $order->order_date->setTimezone('Asia/Phnom_Penh')->format('Y-m-d\TH:i')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ស្ថានភាពបង់ប្រាក់</label>
                            <select name="payment_status" class="form-select">
                                <option value="unpaid" {{ old('payment_status', $order->payment_status) === 'unpaid' ? 'selected' : '' }}>មិនទាន់បង់</option>
                                <option value="partial" {{ old('payment_status', $order->payment_status) === 'partial' ? 'selected' : '' }}>បង់មួយផ្នែក</option>
                                <option value="paid" {{ old('payment_status', $order->payment_status) === 'paid' ? 'selected' : '' }}>បានបង់</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">ការដឹកជញ្ជូន</label>
                        <select name="delivery_id" id="delivery_id" class="form-select">
                            <option value="" data-price="0">គ្មាន</option>
                            @foreach($deliveries as $delivery)
                                <option value="{{ $delivery->id }}" data-price="{{ $delivery->delivery_price_khr }}"
                                    {{ (string) old('delivery_id', $order->delivery_id) === (string) $delivery->id ? 'selected' : '' }}>
                                    {{ $delivery->delivery_name }}
                                </option>
                            @endforeach

                        </select>

                    </div>
                      <div class="col-md-6">
                            <div class="od-field">
                                <label class="od-label">ចំនួនកេស</label>
                                <input type="number" name="box_qty" id="box_qty" class="form-control" min="1" value="{{ old('box_qty', $order->box_qty ?? 1) }}">
                            </div>
                        </div>

                    <div class="mt-3">
                        <label class="form-label">កំណត់ចំណាំ</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    <input type="hidden" name="order_items" id="order_items">
                    <input type="hidden" name="subtotal" id="subtotalInput">
                    <input type="hidden" name="discount_amount" id="discountInput">
                    <input type="hidden" name="delivery_fee_khr" id="deliveryFeeInput">
                    <input type="hidden" name="total_amount" id="totalInput">

                    <div class="action-row">
                        <button type="submit" class="edit-btn edit-btn-primary">
                            <i class="fas fa-save"></i> រក្សាទុក
                        </button>
                        <a href="{{ $order->invoice ? route('invoices.show', $order->invoice) : route('orders.show', $order) }}" class="edit-btn edit-btn-soft">
                            <i class="fas fa-times"></i> បោះបង់
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const exchangeRate = 4000;
    let cart = @json($initialCart);
    const originalDeliveryId = @json((string) $order->delivery_id);
    const originalBoxQty = Math.max(parseInt(@json($order->box_qty ?? 1), 10) || 1, 1);
    const originalDeliveryFeeKhr = parseFloat(@json((float) $order->delivery_fee_khr)) || 0;
    const originalDeliveryUnitKhr = originalBoxQty > 0 ? originalDeliveryFeeKhr / originalBoxQty : 0;
    let deliverySelectionChanged = false;

    $(document).ready(function () {
        $('.select2-customer').select2({
            placeholder: 'សូមស្វែងរកអតិថិជន',
            allowClear: true,
            width: '100%'
        });

        $('#customer_id').on('change', updateCustomerInfo).trigger('change');
        $('#delivery_id').on('change', function () {
            deliverySelectionChanged = true;
            renderCart();
        });
        $('#box_qty').on('change input', renderCart);
        renderCart();
    });

    function updateCustomerInfo() {
        const option = $('#customer_id option:selected');
        $('#customerName').text(option.data('name') || '-');
        $('#customerPhone').text(option.data('phone') || '-');
        $('#customerLocation').text(option.data('location') || '-');
    }

    function addToCart(productId, name, price, priceKhr, image) {
        if (cart[productId]) {
            cart[productId].qty += 1;
        } else {
            cart[productId] = {
                name,
                price: parseFloat(price),
                price_khr: parseFloat(priceKhr),
                qty: 1,
                discount: 0,
                image
            };
        }

        renderCart();
    }

    function updateQty(productId, value) {
        const qty = parseInt(value, 10);
        if (!cart[productId]) return;
        cart[productId].qty = Math.max(qty || 1, 1);
        renderCart();
    }

    function updateDiscount(productId, value) {
        if (!cart[productId]) return;
        cart[productId].discount = Math.min(Math.max(parseFloat(value) || 0, 0), 100);
        renderCart();
    }

    function removeItem(productId) {
        delete cart[productId];
        renderCart();
    }

    function getDeliveryFeeKhr() {
        const selectedDeliveryId = String($('#delivery_id').val() || '');
        const isOriginalDelivery = selectedDeliveryId === originalDeliveryId;
        const deliveryPriceKhr = isOriginalDelivery && !deliverySelectionChanged
            ? originalDeliveryUnitKhr
            : (parseFloat($('#delivery_id option:selected').data('price') || 0) || 0);
        const boxQty = Math.max(parseInt($('#box_qty').val() || 1, 10) || 1, 1);
        return deliveryPriceKhr * boxQty;
    }

    function renderCart() {
        const box = $('#cartItems');
        const entries = Object.entries(cart);

        if (entries.length === 0) {
            box.html('<div class="empty-cart">មិនមានទំនិញក្នុងបញ្ជាទិញ</div>');
        } else {
            box.html(entries.map(([productId, item]) => {
                const discount = parseFloat(item.discount || 0);
                const unit = item.price * (1 - discount / 100);
                const line = unit * item.qty;

                return `
                    <div class="cart-item">
                        <div class="cart-item-top">
                            <div>
                                <div class="cart-item-name">${escapeHtml(item.name)}</div>
                                <div class="cart-item-price">$${unit.toFixed(2)} x ${item.qty} = $${line.toFixed(2)}</div>
                            </div>
                            <strong>$${line.toFixed(2)}</strong>
                        </div>
                        <div class="cart-controls">
                            <input type="number" min="1" value="${item.qty}" onchange="updateQty(${productId}, this.value)" title="Quantity">
                            <input type="number" min="0" max="100" step="0.1" value="${discount}" onchange="updateDiscount(${productId}, this.value)" title="Discount percent">
                            <button type="button" class="remove-btn" onclick="removeItem(${productId})" title="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join(''));
        }

        updateTotals();
    }

    function updateTotals() {
        let subtotal = 0;
        let discountTotal = 0;
        const orderItems = [];

        Object.entries(cart).forEach(([productId, item]) => {
            const qty = parseInt(item.qty, 10) || 1;
            const discount = parseFloat(item.discount || 0);
            const originalLine = item.price * qty;
            const discountedUnit = item.price * (1 - discount / 100);
            const lineTotal = discountedUnit * qty;

            subtotal += lineTotal;
            discountTotal += originalLine - lineTotal;

            orderItems.push({
                product_id: parseInt(productId, 10),
                quantity: qty,
                unit_price: parseFloat(item.price),
                total_price: lineTotal
            });
        });

        const deliveryFeeKhr = getDeliveryFeeKhr();
        const deliveryFeeUsd = deliveryFeeKhr / exchangeRate;
        const total = subtotal + deliveryFeeUsd;

        $('#subtotalText').text(subtotal.toFixed(2));
        $('#discountText').text(discountTotal.toFixed(2));
        $('#deliveryUsdText').text(deliveryFeeUsd.toFixed(2));
        $('#deliveryKhrText').text(Math.round(deliveryFeeKhr).toLocaleString());
        $('#totalText').text(total.toFixed(2));

        $('#order_items').val(JSON.stringify(orderItems));
        $('#subtotalInput').val(subtotal.toFixed(2));
        $('#discountInput').val(discountTotal.toFixed(2));
        $('#deliveryFeeInput').val(deliveryFeeKhr.toFixed(2));
        $('#totalInput').val(total.toFixed(2));
    }

    document.getElementById('orderEditForm').addEventListener('submit', function (event) {
        if (Object.keys(cart).length === 0) {
            event.preventDefault();
            alert('សូមបន្ថែមទំនិញយ៉ាងហោចណាស់ ១ មុខ');
        }
    });

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
</script>
@endpush
