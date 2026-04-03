@extends('layouts.app')

@section('title', 'Create Inventory')

@push('styles')
<style>
    .form-card {
        background: #fff;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 28px;
    }

    .form-card__title {
        font-size: 20px;
        font-weight: 700;
        color: #1a1d29;
        margin-bottom: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #1a1d29;
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #e85d24;
        box-shadow: 0 0 0 3px rgba(232,93,36,0.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 28px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%);
        color: #fff;
        padding: 10px 24px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-primary:hover {
        box-shadow: 0 4px 12px rgba(232,93,36,0.3);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #f8f9fa;
        color: #1a1d29;
        padding: 10px 24px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background: #e9ecef;
        border-color: #d9dce0;
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .alert-error ul {
        margin: 0;
        padding-left: 20px;
    }

    .alert-error li {
        margin: 4px 0;
    }

    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
        .btn-primary, .btn-secondary { width: 100%; justify-content: center; }
    }
</style>
@endpush

@section('content')

<div class="container mt-4 mb-4" style="max-width: 600px;">
    <div class="form-card">
        <h3 class="form-card__title">Create Inventory Record</h3>

        @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('inventory.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Product *</label>
                <select name="product_id" class="form-select" required>
                    <option value="">Select a product</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Quantity *</label>
                    <input type="number" name="quantity" class="form-input" value="{{ old('quantity', '0') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Reorder Level *</label>
                    <input type="number" name="reorder_level" class="form-input" value="{{ old('reorder_level', '10') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Warehouse Location</label>
                <input type="text" name="warehouse_location" class="form-input" value="{{ old('warehouse_location') }}" placeholder="e.g., Shelf A2">
            </div>

            <div class="form-group">
                <label class="form-label">Cost Per Unit</label>
                <input type="number" name="cost_per_unit" step="0.01" class="form-input" value="{{ old('cost_per_unit') }}" placeholder="0.00">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Create
                </button>
                <a href="{{ route('inventory.index') }}" class="btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
