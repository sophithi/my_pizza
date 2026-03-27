@extends('layouts.app')

@section('title', 'Products')

@section('content')

<style>
    .product-card {
        background: #fff;
        border-radius: 8px;
        padding: 16px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .product-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .btn-action {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 4px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-edit {
        background: #0d6efd;
        color: #fff;
    }
    .btn-delete {
        background: #dc3545;
        color: #fff;
    }
</style>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <h2 style="font-size: 24px; font-weight: 700; color: #1a1d29; margin: 0;">Products</h2>
    <a href="{{ route('products.create') }}" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
        <i class="fas fa-plus"></i> Add Product
    </a>
</div>

@if($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body" style="padding: 24px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: #f8f9fa; border-top: 1px solid #e9ecef; border-bottom: 2px solid #e9ecef;">
                    <tr>
                        <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">SKU</th>
                        <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">Name</th>
                        <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">Category</th>
                        <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">Price</th>
                        <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">Stock</th>
                        <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr style="border-bottom: 1px solid #e9ecef;">
                        <td style="padding: 12px; color: #e85d24; font-weight: 600;">{{ $product->sku }}</td>
                        <td style="padding: 12px; color: #1a1d29;">{{ $product->name }}</td>
                        <td style="padding: 12px; color: #6c757d;">{{ $product->category }}</td>
                        <td style="padding: 12px; color: #1a1d29; font-weight: 600;">${{ number_format($product->price, 2) }}</td>
                        <td style="padding: 12px;">
                            @if($product->inventory)
                                <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; 
                                    background: {{ $product->inventory->status === 'out_of_stock' ? '#f8d7da' : ($product->inventory->status === 'low_stock' ? '#fff3cd' : '#d4edda') }};
                                    color: {{ $product->inventory->status === 'out_of_stock' ? '#721c24' : ($product->inventory->status === 'low_stock' ? '#856404' : '#155724') }};">
                                    {{ $product->inventory->quantity }} units
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            <a href="{{ route('products.show', $product) }}" class="btn-action btn-edit" style="margin-right: 4px;"><i class="fas fa-eye"></i> View</a>
                            <a href="{{ route('products.edit', $product) }}" class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" style="border: none; cursor: pointer;" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 32px; text-align: center; color: #6c757d;">No products found. <a href="{{ route('products.create') }}">Create one now</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $products->links() }}
</div>

@endsection
