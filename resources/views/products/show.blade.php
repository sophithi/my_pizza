@extends('layouts.app')

@section('title', $product->name)

@section('content')

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
                <div class="card-body" style="padding: 28px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px;">
                        <div>
                            <h2 style="font-size: 28px; font-weight: 700; color: #1a1d29; margin: 0;">{{ $product->name }}
                            </h2>
                            <p style="color: #6c757d; margin: 8px 0 0 0;">កូដ: <strong>{{ $product->sku }}</strong></p>
                        </div>
                        <div style="text-align: right;">
                            <h3 style="font-size: 24px; color: #e85d24; font-weight: 700; margin: 0;">
                                ${{ number_format($product->price_usd, 2) }}</h3>
                            <p style="color: #6c757d; font-size: 12px; margin: 4px 0 0 0;">ខ្នាត: {{ $unitLabels[$product->unit] ?? $product->unit }} </p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                        <div>
                            <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">ប្រភេទ</p>
                            <p style="color: #1a1d29; margin: 0;">{{ $product->category }}</p>
                        </div>
                        <div>
                            <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">អ្នកផ្គត់ផ្គង់</p>
                            <p style="color: #1a1d29; margin: 0;">{{ $product->supplier ?? 'Not specified' }}</p>
                        </div>
                    </div>

                    @if($product->description)
                        <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e9ecef;">
                            <p style="color: #6c757d; font-weight: 600; margin-bottom: 8px;">ផ្សេងៗ</p>
                            <p style="color: #1a1d29; margin: 0; line-height: 1.6;">{{ $product->description }}</p>
                        </div>
                    @endif

                    <div style="display: flex; gap: 12px;">
                        <a href="{{ route('products.edit', $product) }}" class="btn"
                            style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-edit"></i> កែប្រែ
                        </a>
                        <a href="{{ route('products.index') }}" class="btn"
                            style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-arrow-left"></i> ត្រឡប់
                        </a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;"
                            data-delete="Product" data-item-name="{{ $product->name }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn"
                                style="background: #f8d7da; color: #721c24; padding: 10px 24px; border-radius: 6px; border: 1px solid #f5c6cb; font-weight: 600; cursor: pointer;">
                                <i class="fas fa-trash"></i> លុប
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if($product->inventory)
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body" style="padding: 28px;">
                        <h4 style="font-size: 18px; font-weight: 700; color: #1a1d29; margin-bottom: 20px;">ព័ត៌មានឃ្លាំង</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div>
                                <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">ស្តុកបច្ចុប្បន្ន</p>
                                <p style="color: #1a1d29; font-size: 20px; font-weight: 700; margin: 0;">
                                    {{ $product->inventory->quantity }} units</p>
                            </div>
                            <div>
                                <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">កម្រិតនៃស្តុក</p>
                                <p style="color: #1a1d29; font-size: 20px; font-weight: 700; margin: 0;">
                                    {{ $product->inventory->reorder_level }} units</p>
                            </div>
                            <div>
                                <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">ស្ថានភាព</p>
                                <span
                                    style="padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                    background: {{ $product->inventory->status === 'out_of_stock' ? '#f8d7da' : ($product->inventory->status === 'low_stock' ? '#fff3cd' : '#d4edda') }};
                                    color: {{ $product->inventory->status === 'out_of_stock' ? '#721c24' : ($product->inventory->status === 'low_stock' ? '#856404' : '#155724') }};">
                                    {{ ucfirst(str_replace('_', ' ', $product->inventory->status)) }}
                                </span>
                            </div>
                            <div>
                                <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">ទីតាំងឃ្លាំង</p>
                                <p style="color: #1a1d29; margin: 0;">
                                    {{ $product->inventory->warehouse_location ?? 'Not assigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
