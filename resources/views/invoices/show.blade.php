@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">{{ $invoice->invoice_number }}</h2>
            <small class="text-muted">Invoice Detail</small>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('invoices.index') }}" class="btn btn-light">
                ← Back
            </a>

            <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-dark">
                🖨 Print
            </a>
        </div>
    </div>

    {{-- INFO --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 p-3">
                <h6 class="text-muted">Customer</h6>

                @if($invoice->order && $invoice->order->customer)
                    <h5>{{ $invoice->order->customer->name }}</h5>
                    <p class="mb-1">{{ $invoice->order->customer->email }}</p>
                    <p class="mb-0">{{ $invoice->order->customer->phone }}</p>
                @else
                    <p class="text-muted">No customer</p>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-3 p-3">
                <h6 class="text-muted">Invoice Details</h6>

                <p><strong>Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
                <p><strong>Due:</strong> {{ $invoice->due_date?->format('d M Y') ?? 'N/A' }}</p>

                <span class="badge bg-success">
                    {{ $invoice->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- ITEMS --}}
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">

            <h5 class="mb-3">Order Items</h5>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach(($invoice->order->items ?? []) as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>

                            <td class="text-center">{{ $item->quantity }}</td>

                            <td class="text-end">
                                ${{ number_format($item->unit_price, 2) }}
                                <br>
                                <small class="text-muted">
                                    ៛{{ number_format($item->unit_price * 4000) }}
                                </small>
                            </td>

                            <td class="text-end fw-bold">
                                ${{ number_format($item->total_price, 2) }}
                                <br>
                                <small class="text-muted">
                                    ៛{{ number_format($item->total_price * 4000) }}
                                </small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- TOTAL --}}
            <div class="d-flex justify-content-end mt-4">
                <div style="width: 300px">

                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>Subtotal</span>
                        <strong>${{ number_format($invoice->subtotal, 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>Discount</span>
                        <strong>${{ number_format($invoice->discount_amount, 2) }}</strong>
                    </div>

                    @if((float) $invoice->delivery_fee_khr > 0)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span>Delivery {{ $invoice->order?->delivery ? '(' . $invoice->order->delivery->delivery_name . ')' : '' }}</span>
                        <strong>
                            ${{ number_format($invoice->delivery_fee_usd, 2) }}
                            <small class="text-muted d-block text-end">៛{{ number_format($invoice->delivery_fee_khr, 0) }}</small>
                        </strong>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between bg-light p-3 rounded mt-2">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold text-danger fs-5">
                            ${{ number_format($invoice->total_amount, 2) }}
                        </span>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- NOTES --}}
    @if($invoice->notes)
    <div class="card shadow-sm border-0 rounded-3 mt-3 p-3">
        <h6 class="text-muted">Notes</h6>
        <p class="mb-0">{{ $invoice->notes }}</p>
    </div>
    @endif

</div>
@endsection
