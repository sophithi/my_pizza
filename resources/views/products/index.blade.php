@extends('layouts.app')

@section('title', 'ទំនិញ')

@section('content')

    @include('products.partials.catalog-style')
    @if($errors->any())
        <div class="alert alert-danger mx-3 mt-3" role="alert">{{ $errors->first() }}</div>
    @endif
    <div class="product-catalog">

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

        .pager-wrap {
            margin-top: 16px;
        }
    </style>

    <div class="mb-4 d-flex justify-content-between align-items-center" style="gap: 8px; flex-wrap: wrap;">
        <h2 style="font-size: 24px; font-weight: 700; color: #1a1d29; margin: 0;">ទំនិញទាំងអស់</h2>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('products.export.excel') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-file-excel"></i> Excel
            </a>
            <a href="{{ route('products.export.pdf') }}" class="btn btn-outline-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('products.create') }}" class="btn"
                style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
                <i class="fas fa-plus"></i> បង្កើតទំនិញថ្មី
            </a>
        </div>
    </div>

    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="catalog-metrics">
        @foreach([['fa-box','ទំនិញសរុប',$productStats->total],['fa-layer-group','ប្រភេទទំនិញ',$categories->count()],['fa-tag','អនុញ្ញាតកែតម្លៃ',$productStats->custom_count],['fa-gift','អនុញ្ញាតថែម',$productStats->free_count]] as [$icon,$label,$count])
            <div class="catalog-metric"><i class="fas {{ $icon }}"></i><div><span>{{ $label }}</span><strong>{{ number_format($count ?? 0) }}</strong></div></div>
        @endforeach
    </div>
    <form method="GET" action="{{ route('products.index') }}" class="catalog-filters">
        <div class="catalog-search"><i class="fas fa-search"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="ស្វែងរកឈ្មោះ ឬកូដទំនិញ..." aria-label="ស្វែងរកទំនិញ"></div>
        <select name="category" aria-label="ប្រភេទទំនិញ"><option value="">គ្រប់ប្រភេទ</option>@foreach($categories as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>@endforeach</select>
        <button type="submit" class="catalog-search-button">ស្វែងរក</button>
        @if(request()->filled('search') || request()->filled('category'))<a href="{{ route('products.index') }}" class="text-secondary small">សម្អាត</a>@endif
    </form>
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body" style="padding: 24px;">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #f8f9fa; border-top: 1px solid #e9ecef; border-bottom: 2px solid #e9ecef;">
                        <tr>
                            <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">
                                កូដ</th>
                            <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">
                                ឈ្មោះ</th>
                            <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">
                                ប្រភេទ</th>
                            <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">
                                ខ្នាត</th>
                            <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">
                                តម្លៃ (KHR / USD)</th>
                            <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">
                                ស្តុក</th>
                            <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">
                                កែតម្លៃ</th>
                            <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">
                                ថែម</th>
                            <th style="padding: 12px; font-weight: 600; color: #1a1d29; font-size: 12px; text-transform: uppercase;">
                                សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td style="padding: 12px; color: #e85d24; font-weight: 600;">{{ $product->sku }}</td>
                                <td style="padding: 12px; color: #1a1d29;">{{ $product->name }}</td>
                                <td style="padding: 12px; color: #6c757d;">{{ $product->category }}</td>

                                @php
                                    $unitLabels = [
                                        'kg'   => 'គីឡូក្រាម',
                                        'g'    => 'ក្រាម',
                                        'L'    => 'លីត្រ',
                                        'ml'   => 'កំប៉ុង',
                                        'pcs'  => 'បន្ទះ',
                                        'bag'  => 'ដើម',
                                        'box1' => 'កេស',
                                        'box2' => 'ប្រអប់',
                                        'pack' => 'កញ្ចប់',
                                    ];
                                    $usd = $product->price_usd ?? 0;
                                    $khr = $product->price_khr ?? 0;
                                @endphp

                                <td style="padding: 12px; color: #6c757d;">
                                    {{ $product->unitLabel() }}
                                </td>

                                <td style="padding: 12px; color: #1a1d29; font-weight: 600;">
                                    ៛{{ number_format((int) round($khr)) }}<small class="catalog-usd">${{ rtrim(rtrim(number_format($usd, 3, '.', ''), '0'), '.') }}</small>
                                </td>

                                <td style="padding: 12px;">
                                    @if($product->inventory)
                                        <span style="padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                                            background: {{ $product->inventory->status === 'out_of_stock' ? '#f8d7da' : ($product->inventory->status === 'low_stock' ? '#fff3cd' : '#d4edda') }};
                                            color: {{ $product->inventory->status === 'out_of_stock' ? '#721c24' : ($product->inventory->status === 'low_stock' ? '#856404' : '#155724') }};">
                                            {{ number_format($product->inventory->quantity) }} {{ $product->unitLabel() }}
                                        </span>
                                    @else
                                        <span style="color: #6c757d; font-size: 12px;">-</span>
                                    @endif
                                </td>

                                <td style="padding: 12px;">
                                    @if($product->allow_custom_price)
                                        <span style="background: #fff3cd; color: #856404; padding: 4px 12px;
                                            border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap;">
                                            <i class="fas fa-tag"></i> បើក
                                        </span>
                                    @else
                                        <span style="background: #e9ecef; color: #6c757d; padding: 4px 12px;
                                            border-radius: 999px; font-size: 11px; font-weight: 600;">
                                            បិទ
                                        </span>
                                    @endif
                                </td>

                                <td style="padding: 12px;">
                                    @if($product->allow_free)
                                        <span style="background: #fff3cd; color: #24e8069f; padding: 4px 12px;
                                            border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap;">
                                            <i class="fas fa-tag"></i> បើក
                                        </span>
                                    @else
                                        <span style="background: #e9ecef; color: #6c757d; padding: 4px 12px;
                                            border-radius: 999px; font-size: 11px; font-weight: 600;">
                                            បិទ
                                        </span>
                                    @endif
                                </td>


                                <td style="padding: 12px;">
                                    <a href="{{ route('products.show', $product) }}" class="btn-action btn-edit"
                                        style="margin-right: 4px;">
                                        <i class="fas fa-eye"></i> មើល
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn-action btn-edit"
                                        style="margin-right: 4px;">
                                        <i class="fas fa-edit"></i> កែប្រែ
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                        style="display: inline;"
                                        data-delete="Product"
                                        data-item-name="{{ $product->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete"
                                            style="border: none; cursor: pointer;">
                                            <i class="fas fa-trash"></i> លុប
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="padding: 32px; text-align: center; color: #6c757d;">
                                    រកមិនឃើញទំនិញ.
                                    <a href="{{ route('products.create') }}">បង្កើតទំនិញ</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="pager-wrap">
        <span class="text-secondary small">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} / {{ number_format($products->total()) }} មុខ</span>
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    </div>

@endsection
