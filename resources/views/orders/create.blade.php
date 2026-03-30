@extends('layouts.app')

@section('title', 'Create Order')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 20px;
        max-height: 600px;
        overflow-y: auto;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
    }

    .product-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .product-card:hover {
        border-color: #e85d24;
        box-shadow: 0 4px 12px rgba(232, 93, 36, 0.15);
        transform: translateY(-5px);
    }

    .product-card.selected {
        border-color: #e85d24;
        background: #fff5f0;
    }

    .product-image {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .product-name {
        font-size: 13px;
        font-weight: 600;
        color: #1a1d29;
        margin-bottom: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-price {
        font-size: 12px;
        color: #e85d24;
        font-weight: 700;
    }

    .invoice-items {
        max-height: 400px;
        overflow-y: auto;
    }

    .invoice-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .invoice-item-info {
        flex: 1;
    }

    .invoice-item-name {
        font-size: 13px;
        font-weight: 600;
        color: #1a1d29;
    }

    .invoice-item-qty {
        font-size: 12px;
        color: #6c757d;
    }

    .invoice-item-actions {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .qty-input {
        width: 50px;
        padding: 4px;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        text-align: center;
        font-size: 12px;
    }

    .invoice-summary {
        background: #f8f9fa;
        padding: 16px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .summary-row.total {
        border-top: 2px solid #e9ecef;
        padding-top: 12px;
        font-weight: 700;
        font-size: 15px;
        color: #e85d24;
    }

    .empty-cart {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }
</style>
@endpush

@section('content')

<div class="row">
    <div class="col-lg-12">
        @if ($errors->any())
        <div class="alert alert-danger" style="border-radius: 12px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <!-- Left Section: Products -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body" style="padding: 28px;">
                <h3 style="font-size: 20px; font-weight: 700; color: #1a1d29; margin-bottom: 24px;">
                    <i class="fas fa-pizza-slice"></i> Select Products
                </h3>
                
                <div class="products-grid" id="productsGrid">
                    @forelse($products as $product)
                    <div class="product-card" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price_usd }}, '{{ asset('storage/' . $product->image) }}')">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                        @else
                            <div style="width: 100%; height: 120px; background: #e9ecef; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                                <i class="fas fa-image" style="font-size: 24px; color: #adb5bd;"></i>
                            </div>
                        @endif
                        <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
                        <div class="product-price">${{ number_format($product->price_usd, 2) }}</div>
                    </div>
                    @empty
                    <div class="empty-cart">
                        <p><i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 10px;"></i></p>
                        <p>No products available</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Section: Invoice/Order Form -->
    <div class="col-lg-5">
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf

            <!-- Customer Section -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
                <div class="card-body" style="padding: 28px;">
                    <h4 style="font-size: 16px; font-weight: 700; color: #1a1d29; margin-bottom: 20px;">
                        <i class="fas fa-user"></i> Customer
                    </h4>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Select Customer *</label>
                        <select name="customer_id" id="customer_id" class="form-control select2-customer" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px; width: 100%;">
                            <option value="">Search and select a customer</option>
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

                    <div id="customer_info_card" style="display: none; padding: 12px; background: #e7f3ff; border-radius: 6px; border-left: 4px solid #e85d24;">
                        <p style="margin: 4px 0; font-size: 12px; color: #6c757d;">
                            <strong>Name:</strong> <span id="customer_name">-</span>
                        </p>
                        <p style="margin: 4px 0; font-size: 12px; color: #6c757d;">
                            <strong>Phone:</strong> <span id="customer_phone">-</span>
                        </p>
                        <p style="margin: 4px 0; font-size: 12px; color: #6c757d;">
                            <strong>Location:</strong> <span id="customer_location">-</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Invoice Section -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
                <div class="card-body" style="padding: 28px;">
                    <h4 style="font-size: 16px; font-weight: 700; color: #1a1d29; margin-bottom: 20px;">
                        <i class="fas fa-receipt"></i> Invoice
                    </h4>

                    <div class="invoice-items" id="invoiceItems">
                        <div class="empty-cart">
                            <p><i class="fas fa-shopping-cart" style="font-size: 32px; margin-bottom: 10px;"></i></p>
                            <p>No items added yet</p>
                        </div>
                    </div>

                    <div class="invoice-summary">
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span id="subtotal">$0.00</span>
                        </div>
                        <div class="summary-row">
                            <label style="font-weight: 600;">
                                <input type="checkbox" id="applyDiscount" style="margin-right: 5px;"> Apply Discount
                            </label>
                        </div>
                        <div class="summary-row" id="discountRow" style="display: none;">
                            <span>Discount (%):</span>
                            <input type="number" id="discountPercent" value="0" min="0" max="100" style="width: 60px; padding: 4px; border: 1px solid #e9ecef; border-radius: 4px;" onchange="calculateTotal()">
                        </div>
                        <div class="summary-row" id="discountAmountRow" style="display: none; color: #dc3545;">
                            <span>Discount Amount:</span>
                            <span id="discountAmount">-$0.00</span>
                        </div>
                        <div class="summary-row">
                            <label style="font-weight: 600;">
                                <input type="checkbox" id="applyTax" style="margin-right: 5px;" checked> Apply Tax
                            </label>
                        </div>
                        <div class="summary-row" id="taxRow">
                            <span>Tax (%):</span>
                            <input type="number" id="taxPercent" value="10" min="0" max="100" style="width: 60px; padding: 4px; border: 1px solid #e9ecef; border-radius: 4px;" onchange="calculateTotal()">
                        </div>
                        <div class="summary-row" id="taxAmountRow">
                            <span>Tax Amount:</span>
                            <span id="taxAmount">$0.00</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span id="totalAmount">$0.00</span>
                        </div>
                    </div>

                    <!-- Hidden inputs for form submission -->
                    <input type="hidden" id="order_items" name="order_items" value="[]">
                    <input type="hidden" id="subtotal_amount" name="subtotal">
                    <input type="hidden" id="tax_amount" name="tax_amount">
                    <input type="hidden" id="discount_amount" name="discount_amount">
                    <input type="hidden" id="total_amount_input" name="total_amount">
                </div>
            </div>

            <!-- Order Details Section -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
                <div class="card-body" style="padding: 28px;">
                    <h4 style="font-size: 16px; font-weight: 700; color: #1a1d29; margin-bottom: 20px;">
                        <i class="fas fa-cogs"></i> Order Details
                    </h4>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Order Date *</label>
                        <input type="datetime-local" name="order_date" class="form-control" value="{{ old('order_date', now()->format('Y-m-d\TH:i')) }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Status</label>
                                <select name="status" class="form-control" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Payment Status</label>
                                <select name="payment_status" class="form-control" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                    <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">{{ old('notes') }}</textarea>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 20px;">
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; flex: 1;">
                            <i class="fas fa-save"></i> Create Order
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn" style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600; flex: 1; text-align: center;">
                            <i class="fas fa-times"></i> Cancel
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

    $(document).ready(function() {
        // Initialize Select2
        $('.select2-customer').select2({
            placeholder: 'Search and select a customer',
            allowClear: true,
            width: '100%'
        });

        // Handle customer selection
        $('#customer_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const customerId = selectedOption.val();

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
        $('#applyDiscount').on('change', function() {
            if (this.checked) {
                $('#discountRow').show();
                $('#discountAmountRow').show();
            } else {
                $('#discountRow').hide();
                $('#discountAmountRow').hide();
                $('#discountPercent').val(0);
            }
            calculateTotal();
        });

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

    function addToCart(productId, productName, price, imageUrl) {
        if (cart[productId]) {
            cart[productId].qty += 1;
        } else {
            cart[productId] = {
                name: productName,
                price: parseFloat(price),
                qty: 1,
                image: imageUrl
            };
        }
        renderInvoice();
    }

    function removeFromCart(productId) {
        const itemName = cart[productId].name;
        
        Swal.fire({
            title: 'Remove Item?',
            html: `<div style="text-align: left;">
                <p style="margin-bottom: 15px;">Are you sure you want to remove <strong>${itemName}</strong> from the order?</p>
            </div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Remove',
            cancelButtonText: '<i class="fas fa-times"></i> Cancel',
            allowOutsideClick: false,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                delete cart[productId];
                renderInvoice();
            }
        });
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
                <div class="empty-cart">
                    <p><i class="fas fa-shopping-cart" style="font-size: 32px; margin-bottom: 10px;"></i></p>
                    <p>No items added yet</p>
                </div>
            `);
        } else {
            Object.entries(cart).forEach(([productId, item]) => {
                const itemTotal = item.price * item.qty;
                html += `
                    <div class="invoice-item">
                        <div class="invoice-item-info">
                            <div class="invoice-item-name">${item.name}</div>
                            <div class="invoice-item-qty">$${item.price.toFixed(2)} × <span id="qty_${productId}">${item.qty}</span></div>
                        </div>
                        <div class="invoice-item-actions">
                            <input type="number" class="qty-input" value="${item.qty}" onchange="updateQuantity(${productId}, this.value)" min="1">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart(${productId})">
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

    function calculateTotal() {
        let subtotal = 0;
        Object.values(cart).forEach(item => {
            subtotal += item.price * item.qty;
        });

        let discount = 0;
        if ($('#applyDiscount').is(':checked')) {
            discount = (subtotal * parseInt($('#discountPercent').val() || 0)) / 100;
        }

        let tax = 0;
        let subtotalAfterDiscount = subtotal - discount;
        if ($('#applyTax').is(':checked')) {
            tax = (subtotalAfterDiscount * parseInt($('#taxPercent').val() || 0)) / 100;
        }

        let total = subtotalAfterDiscount + tax;

        $('#subtotal').text('$' + subtotal.toFixed(2));
        $('#discountAmount').text('-$' + discount.toFixed(2));
        $('#taxAmount').text('$' + tax.toFixed(2));
        $('#totalAmount').text('$' + total.toFixed(2));

        // Update hidden inputs
        $('#subtotal_amount').val(subtotal.toFixed(2));
        $('#tax_amount').val(tax.toFixed(2));
        $('#discount_amount').val(discount.toFixed(2));
        $('#total_amount_input').val(total.toFixed(2));
    }

    function updateCartData() {
        const orderItems = [];
        Object.entries(cart).forEach(([productId, item]) => {
            orderItems.push({
                product_id: parseInt(productId),
                quantity: item.qty,
                unit_price: item.price,
                total_price: item.price * item.qty
            });
        });
        $('#order_items').val(JSON.stringify(orderItems));
    }

    // Form validation
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        if (!document.getElementById('customer_id').value) {
            e.preventDefault();
            alert('Please select a customer');
            return false;
        }
        if (Object.keys(cart).length === 0) {
            e.preventDefault();
            alert('Please add at least one product');
            return false;
        }
    });
</script>
@endpush

@endsection
