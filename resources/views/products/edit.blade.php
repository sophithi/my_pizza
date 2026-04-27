@extends('layouts.app')

@section('title', isset($product) ? 'Edit Product' : 'Create Product')

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius:12px">
            <div class="card-body" style="padding:28px">
                <h3 style="font-size:20px;font-weight:700;color:#1a1d29;margin-bottom:24px">
                    {{ isset($product) ? 'Edit Product' : 'Create New Product' }}
                </h3>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin:0;padding-left:20px">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($product)) @method('PUT') @endif

                    {{-- Product Name --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $product->name ?? '') }}" placeholder="e.g. Mozzarella Cheese 1kg">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- SKU --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">SKU <span class="text-danger">*</span></label>
                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                            value="{{ old('sku', $product->sku ?? '') }}" placeholder="e.g. MOZ-1KG">
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Price USD + KHR --}}
                    <div class="row">
                         <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Price (KHR)</label>
                                <div class="input-group">
                                    <span class="input-group-text">៛</span>
                                    <input type="number" name="price_khr" id="price_khr"
                                        step="1" min="0"
                                        class="form-control"
                                        value="{{ old('price_khr', isset($product) && $product->price_khr ? (int)round($product->price_khr) : '') }}"
                                        placeholder="0"
                                        oninput="onKhrInput(this.value)">
                                </div>
                                <small class="text-muted">អត្រាប្តូរប្រាក់: 1 USD = <span id="exchangeRateText">{{ $exchangeRate ?? 4000 }}</span> KHR</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Price (USD) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price_usd" id="price_usd"
                                        step="0.001" min="0"
                                        class="form-control @error('price_usd') is-invalid @enderror"
                                        value="{{ old('price_usd', isset($product) && $product->price_usd ? rtrim(rtrim(number_format($product->price_usd, 3, '.', ''), '0'), '.') : '') }}"
                                        placeholder="0.000"
                                        oninput="onUsdInput(this.value)">
                                </div>
                                @error('price_usd') <div class="text-danger" style="font-size:12px">{{ $message }}</div> @enderror
                            </div>
                        </div>
                       
                    </div>

                    {{-- Category + Unit --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <input type="text" name="category"
                                    class="form-control @error('category') is-invalid @enderror"
                                    value="{{ old('category', $product->category ?? '') }}"
                                    placeholder="e.g. Cheese, Flour, Sauce">
                                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">ខ្នាត <span class="text-danger">*</span></label>
                                <select name="unit" class="form-select kh @error('unit') is-invalid @enderror">
                                    <option value="" disabled {{ old('unit', $product->unit ?? '') ? '' : 'selected' }}>— ជ្រើសរើស —</option>
                                    @php
                                        $units = [
                                            'kg'  => 'គីឡូក្រាម',
                                            'g'   => 'ក្រាម',
                                            'L'   => 'លីត្រ',
                                            'ml'  => 'មីលីលីត្រ',
                                            'pcs' => 'បន្ទះ',
                                            'box1' => 'កេស',
                                            'box2' => 'ប្រអប់',
                                            'pack'=> 'កញ្ចប់',
                                        ];
                                    @endphp
                                    @foreach($units as $code => $label)
                                    <option value="{{ $code }}" {{ old('unit', $product->unit ?? '') == $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Supplier --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Supplier</label>
                        <input type="text" name="supplier" class="form-control"
                            value="{{ old('supplier', $product->supplier ?? '') }}"
                            placeholder="e.g. Dairy Fresh Ltd.">
                    </div>

                    {{-- Image --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        @if(isset($product) && $product->image)
                        <small class="text-muted mt-1 d-block">Current: {{ basename($product->image) }}</small>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Optional product description...">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn" style="background:#e85d24;color:#fff;padding:10px 24px;border-radius:6px;font-weight:600">
                            {{ isset($product) ? 'Update Product' : 'Create Product' }}
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" style="padding:10px 24px;border-radius:6px;font-weight:600">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

{{-- Auto-calculate USD/KHR --}}
<script>
let currentExchangeRate = Number({{ $exchangeRate ?? 4000 }});

function formatRate(n) {
    const num = Number(n);
    if (isNaN(num)) return String(n);
    return num.toLocaleString(undefined, { maximumFractionDigits: 4 });
}

function onUsdInput(val) {
    const usd = parseFloat(val);
    if (isNaN(usd)) {
        document.getElementById('price_khr').value = '';
        return;
    }
    document.getElementById('price_khr').value = Math.round(usd * currentExchangeRate);
}

function onKhrInput(val) {
    const khr = parseFloat(val);
    if (isNaN(khr)) {
        document.getElementById('price_usd').value = '';
        return;
    }
    document.getElementById('price_usd').value = parseFloat((khr / currentExchangeRate).toFixed(3));
}

document.addEventListener('DOMContentLoaded', function () {
    const usdEl = document.getElementById('price_usd');
    const khrEl = document.getElementById('price_khr');

    // Trim USD trailing zeros on page load
    if (usdEl.value) {
        usdEl.value = parseFloat(parseFloat(usdEl.value).toFixed(3));
    }

    // If only KHR has value (create page), calculate USD
    if (khrEl.value && !usdEl.value) {
        onKhrInput(khrEl.value);
    }

    // Format exchange rate display
    const rateTextEl = document.getElementById('exchangeRateText');
    if (rateTextEl) {
        rateTextEl.textContent = formatRate(rateTextEl.textContent || currentExchangeRate);
    }
});
</script>

@endsection
