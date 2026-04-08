@extends('layouts.app')

@section('title', 'ចេញវិក័្កយបត្រជូនអតិថិជន')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    :root {
        --accent: #e85d24;
        --bg: #f4f5f7;
        --surface: #ffffff;
        --border: #e9ecef;
        --text: #1a1d29;
        --text-muted: #6c757d;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
    }

    body { background: var(--bg); }

    .page-header {
        margin-bottom: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .product-section-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 16px;
        max-height: 750px;
        overflow-y: auto;
        padding: 24px;
        background: var(--surface);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid var(--border);
    }

    .product-card {
        background: var(--surface);
        border: 2px solid var(--border);
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .product-card:hover {
        border-color: var(--accent);
        box-shadow: 0 8px 16px rgba(232, 93, 36, 0.2);
        transform: translateY(-6px);
    }

    .product-card.selected {
        border-color: var(--accent);
        background: linear-gradient(135deg, rgba(232, 93, 36, 0.05) 0%, rgba(232, 93, 36, 0.02) 100%);
        box-shadow: 0 6px 12px rgba(232, 93, 36, 0.15);
    }

    .product-card.selected::after {
        content: '✓';
        position: absolute;
        top: 6px;
        right: 6px;
        background: var(--accent);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
    }

    .product-image {
        width: 100%;
        height: 100px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .product-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-price {
        font-size: 13px;
        color: var(--accent);
        font-weight: 700;
    }

    .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .card-body {
        padding: 28px;
    }

    .form-label {
        font-weight: 600;
        color: var(--text);
        margin-bottom: 10px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control, .form-select {
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: var(--surface);
        color: var(--text);
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(232, 93, 36, 0.1);
    }

    .customer-info-card {
        padding: 16px;
        background: linear-gradient(135deg, rgba(232, 93, 36, 0.05) 0%, rgba(232, 93, 36, 0.02) 100%);
        border-radius: 8px;
        border-left: 4px solid var(--accent);
        margin-top: 16px;
        animation: slideDown 0.4s ease-out;
    }

    .customer-info-item {
        margin: 8px 0;
        font-size: 13px;
        color: var(--text-muted);
    }

    .customer-info-item strong {
        color: var(--text);
        font-weight: 600;
    }

    .invoice-items {
        max-height: 450px;
        overflow-y: auto;
        margin-bottom: 20px;
    }

    .invoice-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
        animation: slideDown 0.3s ease-out;
    }

    .invoice-item:hover {
        background: rgba(232, 93, 36, 0.02);
    }

    .invoice-item-info {
        flex: 1;
    }

    .invoice-item-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 4px;
    }

    .invoice-item-qty {
        font-size: 12px;
        color: var(--text-muted);
    }

    .invoice-item-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .qty-input {
        width: 55px;
        padding: 6px 8px;
        border: 1px solid var(--border);
        border-radius: 6px;
        text-align: center;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .qty-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 2px rgba(232, 93, 36, 0.1);
    }

    .btn-remove {
        background: linear-gradient(135deg, var(--danger) 0%, #bb2d3b 100%);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 12px;
        font-weight: 600;
    }

    .btn-remove:hover {
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        transform: translateY(-1px);
    }

    .invoice-summary {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 20px;
        border-radius: 10px;
        border: 1px solid var(--border);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 13px;
        color: var(--text);
    }

    .summary-row.total {
        border-top: 2px solid var(--border);
        padding-top: 14px;
        margin-top: 14px;
        font-weight: 700;
        font-size: 16px;
        color: var(--accent);
    }

    .summary-row input[type="number"] {
        width: 60px;
        padding: 4px 8px;
        border: 1px solid var(--border);
        border-radius: 6px;
        text-align: right;
        font-size: 12px;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.6;
    }

    .empty-state-text {
        font-size: 15px;
        margin-bottom: 8px;
        color: var(--text);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent) 0%, #d94a10 100%);
        color: white;
        padding: 12px 28px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        flex: 1;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary:hover {
        box-shadow: 0 8px 20px rgba(232, 93, 36, 0.3);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: var(--bg);
        color: var(--text);
        padding: 12px 28px;
        border: 1px solid var(--border);
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        flex: 1;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-secondary:hover {
        background: var(--surface);
        border-color: var(--accent);
        color: var(--accent);
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 24px;
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #721c24;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid var(--border);
        border-radius: 8px;
        height: 44px;
        padding: 6px 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text);
        line-height: 32px;
        font-size: 14px;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(232, 93, 36, 0.1);
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .col-lg-7 { animation: slideUp 0.6s ease-out; }
    .col-lg-5 { animation: slideUp 0.6s ease-out 0.1s both; }

    /* Order Details Fields */
    .od-field {
        margin-bottom: 18px;
        position: relative;
    }

    .od-label {
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .od-label i {
        color: var(--accent);
        font-size: 13px;
        width: 16px;
        text-align: center;
    }

    .od-select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236c757d' d='M6 8.825L0.375 3.175 1.275 2.275 6 7 10.725 2.275 11.625 3.175z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
    }

    @media (max-width: 992px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }
    }

    /* Toast Notification */
    .toast-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.4);
        z-index: 9998;
        opacity: 0;
        transition: opacity 0.3s;
        display: none;
    }
    .toast-overlay.show { display: block; opacity: 1; }

    .toast-box {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        background: var(--surface);
        border-radius: 16px;
        padding: 32px 36px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        z-index: 9999;
        text-align: center;
        min-width: 320px;
        max-width: 400px;
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: none;
    }
    .toast-box.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }

    .toast-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 24px;
    }
    .toast-icon.warning { background: #fff3cd; color: #d97706; }
    .toast-icon.error { background: #fee2e2; color: #dc2626; }
    .toast-icon.success { background: #d1fae5; color: #059669; }

    .toast-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 8px;
    }
    .toast-message {
        font-size: 14px;
        color: var(--text-muted);
        margin-bottom: 20px;
        line-height: 1.5;
    }
    .toast-btn {
        background: linear-gradient(135deg, var(--accent) 0%, #d94a10 100%);
        color: #fff;
        border: none;
        padding: 10px 32px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }
    .toast-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(232,93,36,0.3); }
</style>
@endpush

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <ul style="margin: 0; padding-left: 20px;">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    
    <!-- Left Section: Products -->
    <div class="col-lg-7">

        <div class="card">
            
            <!-- Customer Selection (Inside Form!) -->
            <div class="card">
                <div class="card-body">
                   <h4 class="product-section-title">
                        សូមជ្រើសរើសអតិថិជន
                    </h4>

                    <div class="mb-3">
                        <select name="customer_id" id="customer_id" class="form-control select2-customer" required>
                            <option value="">សូមស្វែងរកអតិថិជន</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    data-name="{{ $customer->name }}"
                                    data-phone="{{ $customer->phone }}"
                                    data-location="{{ $customer->location }}"
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="customer_info_card" class="customer-info-card" style="display: none;">
                        <div class="customer-info-item">
                            <strong>ឈ្មោះ:</strong> <span id="customer_name">-</span>
                        </div>
                        <div class="customer-info-item">
                            <strong>លេខទំនាក់ទំនង:</strong> <span id="customer_phone">-</span>
                        </div>
                        <div class="customer-info-item">
                            <strong>ទីតាំង:</strong> <span id="customer_location">-</span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card-body">
                 <!-- Products Section -->
                <h4 class="product-section-title">
                    <div class="mb-6">សូមជ្រើសរើសទំនិញ</div>
                </h4>

        <div class="products-grid" id="productsGrid">
                    @forelse($products as $product)
                    <div class="product-card" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price_usd }}, {{ $product->price_khr }}, '{{ asset('storage/' . $product->image) }}')">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                        @else
                            <div style="width: 100%; height: 100px; background: var(--bg); border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                                <i class="fas fa-image" style="font-size: 24px; color: var(--text-muted);"></i>
                            </div>
                        @endif
                        <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
                        <div style="font-size: 12px; margin-bottom: 6px;">
                            <div style="color: var(--accent); font-weight: 700;">${{ number_format($product->price_usd, 2) }}</div>
                            <div style="color: var(--text-muted); font-weight: 600;">៛{{ number_format($product->price_khr, 0) }}</div>
                        </div>
                    </div>  
                    @empty
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <div class="empty-state-icon"></div>
                        <div class="empty-state-text">មិនមានផលិតផលទេ</div>
                        <a href="{{ route('products.index') }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">បន្ថែមផលិតផល →</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Right Section: Order Form -->
    <div class="col-lg-5">
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf

            <!-- Invoice Section -->
            <div class="card">
                <div class="card-body">
                    <h4 class="product-section-title">
                        វិក្ក័យបត្រ
                    </h4>

                    <div class="invoice-items" id="invoiceItems">
                        <div class="empty-state">
                            <div class="empty-state-icon">🛒</div>
                            <div class="empty-state-text">មិនទាន់បន្ថែមមុខទំនិញនៅឡើយទេ</div>
                        </div>
                    </div>

                    <div class="invoice-summary">
                        <div class="summary-row">
                            <span>សរុប (USD / KHR):</span>
                            <div style="text-align: right;">
                                <div style="font-weight: 600;">$<span id="subtotal">0.00</span></div>
                                <div style="font-weight: 600; color: var(--text-muted); font-size: 12px;">៛<span id="subtotal_khr">0</span></div>
                            </div>
                        </div>

                        <div class="summary-row" id="discountAmountRow">
                            <span style="font-weight: 600;">បញ្ចុះតម្លៃសរុប:</span>
                            <div style="text-align: right;">
                                <div style="font-weight: 600; color: var(--danger);">-$<span id="discountAmount">0.00</span></div>
                                <div style="font-weight: 600; color: var(--text-muted); font-size: 12px;">-៛<span id="discountAmount_khr">0</span></div>
                            </div>
                        </div>

                    
                        <div class="summary-row total">
                            <span>តម្លៃសរុប:</span>
                            <div style="text-align: right;">
                                <div>$<span id="totalAmount">0.00</span></div>
                                <div style="color: var(--accent); font-size: 13px;">៛<span id="totalAmount_khr">0</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden inputs for form submission -->
            <input type="hidden" id="hidden_customer_id" name="customer_id">
            <input type="hidden" id="order_items" name="order_items" value="[]">
            <input type="hidden" id="subtotal_amount" name="subtotal">
            
            <input type="hidden" id="discount_amount" name="discount_amount">
            <input type="hidden" id="total_amount_input" name="total_amount">

            <!-- Order Details Section -->
            <div class="card">
                <div class="card-body">
                    <h4 class="product-section-title">
                        <i class="fas fa-cog" style="color: var(--accent);"></i> ព័ត៌មានការបញ្ជាទិញ
                    </h4>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="od-field">
                                <label class="od-label"><i class="fas fa-calendar-alt"></i> កាលបរិច្ឆេទ *</label>
                                <input type="date" name="order_date" class="form-control" required 
                                    value="{{ old('order_date', now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="od-field">
                                <label class="od-label"><i class="fas fa-info-circle"></i> ស្ថានភាព</label>
                                <select name="status" class="form-control od-select">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>🟡 រង់ចាំ</option>
                                    <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>🔵 កំពុងដំណើរការ</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>🟢 បានបញ្ចប់</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>🔴 បានលុបចោល</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="od-field">
                                <label class="od-label"><i class="fas fa-money-bill-wave"></i> ស្ថានភាពបង់ប្រាក់</label>
                                <select name="payment_status" class="form-control od-select">
                                    <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>🔴 មិនទាន់បង់</option>
                                    <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>🟡 បង់មួយផ្នែក</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>🟢 បានបង់</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="od-field">
                                <label class="od-label"><i class="fas fa-truck"></i> ការដឹកជញ្ជូន</label>
                                <select id="delivery_select" class="form-control od-select">
                                    <option value="">គ្មាន</option>
                                    @foreach($deliveries as $delivery)
                                        <option value="{{ $delivery->id }}" data-name="{{ $delivery->delivery_name }}" data-price="{{ $delivery->delivery_price_khr }}">
                                            🚚 {{ $delivery->delivery_name }} — ៛{{ number_format($delivery->delivery_price_khr, 0) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="od-field">
                        <label class="od-label"><i class="fas fa-sticky-note"></i> កំណត់ចំណាំ</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="បញ្ចូលកំណត់ចំណាំ..." style="resize: none;">{{ old('notes') }}</textarea>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check-circle"></i> បញ្ជាក់
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn-secondary">
                            <i class="fas fa-times"></i> បោះបង់
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let cart = {};

    // Delivery options from server
    const deliveryOptions = @json($deliveries->map(fn($d) => ['id' => $d->id, 'name' => $d->delivery_name, 'price_khr' => $d->delivery_price_khr]));

    $(document).ready(function() {
        // Initialize Select2
        $('.select2-customer').select2({
            placeholder: 'សូមស្វែងរកអតិថិជន',
            allowClear: true,
            width: '100%'
        });

        // Handle customer selection
        $('#customer_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const customerId = selectedOption.val();

            // Sync to hidden input inside form
            $('#hidden_customer_id').val(customerId);

            if (customerId) {
                const name = selectedOption.data('name') || '-';
                const phone = selectedOption.data('phone') || '-';
                const location = selectedOption.data('location') || '-';

                $('#customer_name').text(name);
                $('#customer_phone').text(phone);
                $('#customer_location').text(location);
                $('#customer_info_card').slideDown(300);
            } else {
                $('#customer_info_card').slideUp(300);
            }
        });

        if ($('#customer_id').val()) {
            $('#customer_id').trigger('change');
        }

        // Discount checkbox
        // Tax checkbox
        $('#applyTax').on('change', function() {
            if (this.checked) {
                $('#taxRow').show();
                $('#taxAmountRow').show();
            } else {
                $('#taxRow').hide();
                $('#taxAmountRow').hide();
                $('#taxPercent').val(0);
            }
            calculateTotal();
        });
    });

    function addToCart(productId, productName, price, priceKhr, imageUrl) {
        if (cart[productId]) {
            cart[productId].qty += 1;
        } else {
            cart[productId] = {
                name: productName,
                price: parseFloat(price),
                price_khr: parseFloat(priceKhr),
                qty: 1,
                discount: 0,
                image: imageUrl
            };
        }
        renderInvoice();
    }

    function removeFromCart(productId) {
        const itemName = cart[productId].name;
        
        if (confirm(`Remove "${itemName}" from order?`)) {
            delete cart[productId];
            renderInvoice();
        }
    }

    function updateQuantity(productId, newQty) {
        newQty = parseInt(newQty) || 1;
        if (newQty <= 0) {
            removeFromCart(productId);
        } else {
            cart[productId].qty = newQty;
            renderInvoice();
        }
    }

    function renderInvoice() {
        const invoiceItems = $('#invoiceItems');
        let html = '';

        if (Object.keys(cart).length === 0) {
            invoiceItems.html(`
                <div class="empty-state">
                    <div class="empty-state-icon">🛒</div>  
                    <div class="empty-state-text">No items added yet</div>
                </div>
            `);
        } else {
            Object.entries(cart).forEach(([productId, item]) => {
                // Calculate discounted price per item
                const discountPercent = parseFloat(item.discount || 0);
                const discountedPrice = item.price * (1 - discountPercent / 100);
                const discountedPriceKhr = item.price_khr * (1 - discountPercent / 100);
                
                const itemTotal = discountedPrice * item.qty;
                const itemTotalKhr = discountedPriceKhr * item.qty;
                
                html += `
                    <div class="invoice-item">
                        <div class="invoice-item-info">
                            <div class="invoice-item-name">${item.name}</div>
                            <div class="invoice-item-qty">
                                <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 6px;">
                                    Original: $${item.price.toFixed(2)} / ៛${item.price_khr.toLocaleString()}
                                    ${discountPercent > 0 ? `<span style="color: var(--danger); margin-left: 8px;">-${discountPercent}%</span>` : ''}
                                </div>
                                <div>
                                    $${discountedPrice.toFixed(2)} / ៛${discountedPriceKhr.toLocaleString()} × ${item.qty} = 
                                    <strong>$${itemTotal.toFixed(2)}</strong> / <strong>${itemTotalKhr.toLocaleString()}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-item-actions">
                            <div style="display: flex; gap: 6px; align-items: center; margin-bottom: 8px; flex-wrap: wrap;">
                                <label style="font-size: 11px; white-space: nowrap;">Qty:</label>
                                <input type="number" class="qty-input" value="${item.qty}" onchange="updateQuantity(${productId}, this.value)" min="1" style="width: 50px;">
                                <label style="font-size: 11px; white-space: nowrap; margin-left: 8px;">Discount %:</label>
                                <input type="number" value="${discountPercent}" onchange="updateItemDiscount(${productId}, this.value)" min="0" max="100" step="0.1" style="width: 60px; padding: 4px; border: 1px solid var(--border); border-radius: 4px; font-size: 12px;">
                            </div>
                            <button type="button" class="btn-remove" onclick="removeFromCart(${productId})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            invoiceItems.html(html);
        }

        calculateTotal();
        updateCartData();
    }
    function updateItemDiscount(productId, discountPercent) {
        const percent = parseFloat(discountPercent) || 0;
        if (cart[productId]) {
            cart[productId].discount = Math.min(Math.max(percent, 0), 100);
            renderInvoice();
        }
    }

    // Update cart data when delivery selection changes
    $(document).ready(function() {
        $('#delivery_select').on('change', function() {
            updateCartData();
        });
    });

    function calculateTotal() {
        // Calculate subtotal with per-item discounts already applied
        let subtotal = 0;
        let subtotalKhr = 0;
        let totalDiscount = 0;
        let totalDiscountKhr = 0;
        
        Object.values(cart).forEach(item => {
            const discountPercent = parseFloat(item.discount || 0);
            const discountedPrice = item.price * (1 - discountPercent / 100);
            const discountedPriceKhr = item.price_khr * (1 - discountPercent / 100);
            
            // Calculate discount amount for this item
            const itemDiscount = item.price * item.qty - discountedPrice * item.qty;
            const itemDiscountKhr = item.price_khr * item.qty - discountedPriceKhr * item.qty;
            
            subtotal += discountedPrice * item.qty;
            subtotalKhr += discountedPriceKhr * item.qty;
            totalDiscount += itemDiscount;
            totalDiscountKhr += itemDiscountKhr;
        });

        let tax = 0;
        let taxKhr = 0;
        if ($('#applyTax').is(':checked')) {
            tax = (subtotal * parseInt($('#taxPercent').val() || 0)) / 100;
            taxKhr = (subtotalKhr * parseInt($('#taxPercent').val() || 0)) / 100;
        }

        let total = subtotal + tax;
        let totalKhr = subtotalKhr + taxKhr;

        // Update USD displays
        $('#subtotal').text(subtotal.toFixed(2));
        $('#discountAmount').text(totalDiscount.toFixed(2));
        $('#taxAmount').text(tax.toFixed(2));
        $('#totalAmount').text(total.toFixed(2));

        // Update KHR displays
        $('#subtotal_khr').text(subtotalKhr.toLocaleString());
        $('#discountAmount_khr').text(Math.round(totalDiscountKhr).toLocaleString());
        $('#taxAmount_khr').text(Math.round(taxKhr).toLocaleString());
        $('#totalAmount_khr').text(Math.round(totalKhr).toLocaleString());

        // Update hidden inputs
        $('#subtotal_amount').val(subtotal.toFixed(2));
        $('#tax_amount').val(tax.toFixed(2));
        $('#discount_amount').val(totalDiscount.toFixed(2));
        $('#total_amount_input').val(total.toFixed(2));
    }

    function updateCartData() {
        const deliveryId = $('#delivery_select').val() || null;
        const orderItems = [];
        Object.entries(cart).forEach(([productId, item]) => {
            const discountPercent = parseFloat(item.discount || 0);
            const discountedPrice = item.price * (1 - discountPercent / 100);
            orderItems.push({
                product_id: parseInt(productId),
                quantity: item.qty,
                unit_price: item.price,
                discount_percent: discountPercent,
                total_price: discountedPrice * item.qty,
                delivery_id: deliveryId ? parseInt(deliveryId) : null
            });
        });
        $('#order_items').val(JSON.stringify(orderItems));
    }

    // Form validation
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        if (!document.getElementById('hidden_customer_id').value) {
            e.preventDefault();
            showToast('warning', '⚠️', 'សូមជ្រើសរើសអតិថិជន', 'សូមជ្រើសរើសអតិថិជនមុននឹងបញ្ជាទិញ');
            return false;
        }
        if (Object.keys(cart).length === 0) {
            e.preventDefault();
            showToast('warning', '🛒', 'សូមបន្ថែមទំនិញ', 'សូមជ្រើសរើសផលិតផលអាតិចមួយ');
            return false;
        }
    });

    function showToast(type, icon, title, message) {
        // Remove any existing toast
        $('.toast-overlay, .toast-box').remove();

        const overlay = $('<div class="toast-overlay"></div>');
        const box = $(`
            <div class="toast-box">
                <div class="toast-icon ${type}">${icon}</div>
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
                <button class="toast-btn" onclick="closeToast()">ភ្លាម</button>
            </div>
        `);

        $('body').append(overlay).append(box);
        requestAnimationFrame(() => {
            overlay.addClass('show');
            box.addClass('show');
        });

        overlay.on('click', closeToast);
    }

    function closeToast() {
        $('.toast-overlay').removeClass('show');
        $('.toast-box').removeClass('show');
        setTimeout(() => { $('.toast-overlay, .toast-box').remove(); }, 300);
    }
</script>
@endpush

@endsection
