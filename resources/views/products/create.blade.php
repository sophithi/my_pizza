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
                                <label class="form-label fw-semibold">Price (USD) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price_usd" id="price_usd"
                                        step="0.01" min="0"
                                        class="form-control @error('price_usd') is-invalid @enderror"
                                        value="{{ old('price_usd', $product->price_usd ?? '') }}"
                                        placeholder="0.00"
                                        oninput="calcKHR(this.value)">
                                </div>
                                @error('price_usd') <div class="text-danger" style="font-size:12px">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Price (KHR)</label>
                                <div class="input-group">
                                    <span class="input-group-text">៛</span>
                                    <input type="number" name="price_khr" id="price_khr"
                                        step="1" min="0"
                                        class="form-control"
                                        value="{{ old('price_khr', $product->price_khr ?? '') }}"
                                        placeholder="Auto-calculated">
                                </div>
                                <small class="text-muted">Auto-fills from USD × 4,100</small>
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
                                <label class="form-label fw-semibold">Unit <span class="text-danger">*</span></label>
                                <select name="unit" class="form-select @error('unit') is-invalid @enderror">
                                    <option value="">-- Select unit --</option>
                                    @foreach(['kg','g','L','ml','pcs','box','pack','bag'] as $u)
                                    <option value="{{ $u }}" {{ old('unit', $product->unit ?? '') == $u ? 'selected' : '' }}>
                                        {{ $u }}
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

{{-- Auto-calculate KHR --}}
<script>
function calcKHR(usd) {
    const rate = 4100;
    const khr = Math.round(parseFloat(usd) * rate);
    document.getElementById('price_khr').value = isNaN(khr) ? '' : khr;
}
</script>

@endsection