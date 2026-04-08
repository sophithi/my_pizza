@extends('layouts.app')

@section('title', 'Edit Order')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')

<div class="row">
    <div class="col-md-10">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body" style="padding: 28px;">
                <h3 style="font-size: 20px; font-weight: 700; color: #1a1d29; margin-bottom: 24px;">Edit Order</h3>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Customer *</label>
                        <select name="customer_id" id="customer_id" class="form-control select2-customer" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px; width: 100%;">
                            <option value="">Search and select a customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" 
                                data-name="{{ $customer->name }}" 
                                data-phone="{{ $customer->phone }}" 
                                data-location="{{ $customer->location }}"
                                {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="customer_info_card" style="margin-bottom: 24px;">
                        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #f8f9fa;">
                            <div class="card-body" style="padding: 20px;">
                                <h5 style="font-size: 16px; font-weight: 700; color: #1a1d29; margin-bottom: 16px;">
                                    <i class="fas fa-user-circle"></i> ព័ត៌មានអតិថិជន
                                </h5>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <div>
                                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px; font-size: 12px;">ឈ្មោះ</p>
                                        <p style="color: #1a1d29; margin: 0; font-weight: 600;" id="customer_name">-</p>
                                    </div>
                                    <div>
                                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px; font-size: 12px;">លេខទំនាក់ទំនង</p>
                                        <p style="color: #1a1d29; margin: 0;" id="customer_phone">-</p>
                                    </div>
                                </div>
                                <div style="margin-top: 12px;">
                                    <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px; font-size: 12px;">ទីតាំង</p>
                                    <p style="color: #1a1d29; margin: 0;" id="customer_location">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">កាលបរិច្ឆេទកម្មង់ *</label>
                                <input type="datetime-local" name="order_date" class="form-control" value="{{ old('order_date', $order->order_date->format('Y-m-d\TH:i')) }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">សរុបតម្លៃ *</label>
                                <input type="number" name="total_amount" step="0.01" class="form-control" value="{{ old('total_amount', $order->total_amount) }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Status</label>
                                <select name="status" class="form-control" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                    <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Payment Status</label>
                                <select name="payment_status" class="form-control" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                    <option value="unpaid" {{ old('payment_status', $order->payment_status) == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    <option value="partial" {{ old('payment_status', $order->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">ផ្សេងៗ</label>
                        <textarea name="notes" class="form-control" rows="3" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 28px;">
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-save"></i> Update Order
                        </button>
                        <a href="{{ route('orders.show', $order) }}" class="btn" style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
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
            const customerInfoCard = $('#customer_info_card');

            if (customerId) {
                // Get data from option attributes
                const name = selectedOption.data('name') || '-';
                const phone = selectedOption.data('phone') || '-';
                const location = selectedOption.data('location') || '-';

                // Update display
                $('#customer_name').text(name);
                $('#customer_phone').text(phone);
                $('#customer_location').text(location);

                // Show the card
                customerInfoCard.slideDown(300);
            } else {
                // Hide the card if no selection
                customerInfoCard.slideUp(300);
            }
        });

        // Show customer info on page load if customer is selected
        if ($('#customer_id').val()) {
            $('#customer_id').trigger('change');
        }
    });
</script>
@endpush

@endsection
