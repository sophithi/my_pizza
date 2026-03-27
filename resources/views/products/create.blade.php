@extends('layouts.app')

@section('title', isset($product) ? 'Edit Product' : 'Create Product')

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body" style="padding: 28px;">
                <h3 style="font-size: 20px; font-weight: 700; color: #1a1d29; margin-bottom: 24px;">
                    {{ isset($product) ? 'Edit Product' : 'Create New Product' }}
                </h3>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($product)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #1a1d29; margin-bottom: 8px;">Product Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #1a1d29; margin-bottom: 8px;">SKU (Stock Keeping Unit) *</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku ?? '') }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1a1d29; margin-bottom: 8px;">Price (PHP) *</label>
                                <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price', $product->price ?? '') }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1a1d29; margin-bottom: 8px;">Unit *</label>
                                <select name="unit" class="form-control" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                    <option value="">Select Unit</option>
                                    <option value="kg" {{ (old('unit', $product->unit ?? '') == 'kg') ? 'selected' : '' }}>Kilogram (kg)</option>
                                    <option value="ltr" {{ (old('unit', $product->unit ?? '') == 'ltr') ? 'selected' : '' }}>Litre (ltr)</option>
                                    <option value="pcs" {{ (old('unit', $product->unit ?? '') == 'pcs') ? 'selected' : '' }}>Pieces (pcs)</option>
                                    <option value="box" {{ (old('unit', $product->unit ?? '') == 'box') ? 'selected' : '' }}>Box</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1a1d29; margin-bottom: 8px;">Price (USD)</label>
                                <input type="number" name="price_usd" step="0.01" class="form-control" value="{{ old('price_usd', $product->price_usd ?? '') }}" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1a1d29; margin-bottom: 8px;">Price (KHR)</label>
                                <input type="number" name="price_khr" step="0.01" class="form-control" value="{{ old('price_khr', $product->price_khr ?? '') }}" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1a1d29; margin-bottom: 8px;">Category *</label>
                                <input type="text" name="category" class="form-control" value="{{ old('category', $product->category ?? '') }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1a1d29; margin-bottom: 8px;">Supplier</label>
                                <input type="text" name="supplier" class="form-control" value="{{ old('supplier', $product->supplier ?? '') }}" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #1a1d29; margin-bottom: 8px;">Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                        @if(isset($product) && $product->image)
                        <small style="color: #666; margin-top: 8px; display: block;">
                            <i class="fas fa-check-circle" style="color: #28a745;"></i> Current image: {{ basename($product->image) }}
                        </small>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600; color: #1a1d29; margin-bottom: 8px;">Description</label>
                        <textarea name="description" class="form-control" rows="3" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 28px;">
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-save"></i> {{ isset($product) ? 'Update' : 'Create' }} Product
                        </button>
                        <a href="{{ route('products.index') }}" class="btn" style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
