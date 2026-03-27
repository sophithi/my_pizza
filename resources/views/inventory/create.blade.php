@extends('layouts.app')

@section('title', 'Create Inventory')

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body" style="padding: 28px;">
                <h3 style="font-size: 20px; font-weight: 700; color: #1a1d29; margin-bottom: 24px;">Create Inventory Record</h3>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Product *</label>
                        <select name="product_id" class="form-control" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            <option value="">Select a product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Quantity *</label>
                                <input type="number" name="quantity" class="form-control" value="{{ old('quantity', '0') }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Reorder Level *</label>
                                <input type="number" name="reorder_level" class="form-control" value="{{ old('reorder_level', '10') }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Warehouse Location</label>
                        <input type="text" name="warehouse_location" class="form-control" value="{{ old('warehouse_location') }}" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Cost Per Unit</label>
                        <input type="number" name="cost_per_unit" step="0.01" class="form-control" value="{{ old('cost_per_unit') }}" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 28px;">
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-save"></i> Create
                        </button>
                        <a href="{{ route('inventory.index') }}" class="btn" style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
