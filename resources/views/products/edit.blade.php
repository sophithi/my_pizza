@extends('layouts.app')

@section('title', isset($product) ? 'កែប្រែទំនិញ' : 'បន្ថែមទំនិញ')

@section('content')

    @include('products.partials.edit-style')
    <div class="product-editor">
        <div class="pe-heading"><div><a href="{{ isset($product) ? route('products.show', $product) : route('products.index') }}" class="pe-back"><i class="fas fa-arrow-left"></i> ត្រឡប់</a><h2>{{ isset($product) ? 'កែប្រែទំនិញ' : 'បន្ថែមទំនិញ' }}</h2></div><span class="pe-sku">{{ $product->sku ?? 'Pizza Happy Family' }}</span></div>
        <div>
            <div class="card border-0 shadow-sm" style="border-radius:12px">
                <div class="card-body" style="padding:28px">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul style="margin:0;padding-left:20px">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($product)) @method('PUT') @endif

                        <div class="pe-grid"><section class="pe-section">
                        <h3><i class="fas fa-box"></i> ព័ត៌មានទំនិញ</h3>
                        {{-- Product Name --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ឈ្មោះទំនិញ <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $product->name ?? '') }}" placeholder="ឈ្មោះទំនិញ" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- SKU --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">កូដទំនិញ <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                value="{{ old('sku', $product->sku ?? '') }}" placeholder="e.g. MOZ-1KG">
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <h3 class="pe-divider"><i class="fas fa-coins"></i> តម្លៃលក់</h3>
                        {{-- Price USD + KHR --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">តម្លៃរៀល (KHR)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">៛</span>
                                        <input type="number" name="price_khr" id="price_khr" step="1" min="0"
                                            class="form-control"
                                            value="{{ old('price_khr', isset($product) && $product->price_khr ? (int) round($product->price_khr) : '') }}"
                                            placeholder="0" oninput="onKhrInput(this.value)">
                                    </div>
                                    <small class="text-muted">អត្រាប្តូរប្រាក់: 1 USD = <span
                                            id="exchangeRateText">{{ $exchangeRate ?? 4000 }}</span> KHR</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">តម្លៃដុល្លារ (USD) <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price_usd" id="price_usd" step="0.001" min="0"
                                            class="form-control @error('price_usd') is-invalid @enderror"
                                            value="{{ old('price_usd', isset($product) && $product->price_usd ? rtrim(rtrim(number_format($product->price_usd, 3, '.', ''), '0'), '.') : '') }}"
                                            placeholder="0.000" oninput="onUsdInput(this.value)">
                                    </div>
                                    @error('price_usd') <div class="text-danger" style="font-size:12px">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <h3 class="pe-divider"><i class="fas fa-sliders-h"></i> ជម្រើសទំនិញ</h3>
                        <div class="form-group pe-toggle">
                         <label>
                             <input type="hidden" name="allow_custom_price" value="0">
                             <input type="checkbox" name="allow_custom_price" value="1" {{ old('allow_custom_price', $product->allow_custom_price ?? false) ? 'checked' : '' }}>
                            អនុញ្ញាតកែតម្លៃ
                            </label>
                        </div>

                        <div class="form-group pe-toggle">
                         <label>
                             <input type="hidden" name="allow_free" value="0">
                             <input type="checkbox" name="allow_free" value="1" {{ old('allow_free', $product->allow_free ?? false) ? 'checked' : '' }}>
                                អនុញ្ញាតថែម
                             </label>
                        </div>
                       
                        <h3 class="pe-divider"><i class="fas fa-layer-group"></i> ប្រភេទ និងការផ្គត់ផ្គង់</h3>
                        {{-- Category + Unit --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">ប្រភេទ <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="category"
                                        class="form-control @error('category') is-invalid @enderror"
                                        value="{{ old('category', $product->category ?? '') }}"
                                        placeholder="ប្រភេទទំនិញ">
                                    @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">ខ្នាត <span class="text-danger">*</span></label>
                                    <select name="unit" class="form-select kh @error('unit') is-invalid @enderror">
                                        <option value="" disabled {{ old('unit', $product->unit ?? '') ? '' : 'selected' }}>
                                            — ជ្រើសរើស —</option>
                                        @php
                                            $units = [
                                                'kg' => 'គីឡូក្រាម',
                                                'g' => 'ក្រាម',
                                                'L' => 'លីត្រ',
                                                'ml' => 'មីលីលីត្រ',
                                                'pcs' => 'បន្ទះ',
                                                'bag' => 'ដើម',
                                                'box1' => 'កេស',
                                                'box2' => 'ប្រអប់',
                                                'pack' => 'កញ្ចប់',
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
                            <label class="form-label fw-semibold">អ្នកផ្គត់ផ្គង់</label>
                            <input type="text" name="supplier" class="form-control"
                                value="{{ old('supplier', $product->supplier ?? '') }}" placeholder="ឈ្មោះអ្នកផ្គត់ផ្គង់">
                        </div>

                        </section><aside class="pe-section pe-media">
                        <h3><i class="fas fa-image"></i> រូបភាពទំនិញ</h3>
                        <div class="pe-image-preview"><img id="productImagePreview" src="{{ isset($product) ? $product->imageUrl() : '' }}" alt="រូបភាពទំនិញ" @if(empty($product?->image)) hidden @endif><span id="productImagePlaceholder" @if(!empty($product?->image)) hidden @endif><i class="fas fa-image"></i><small id="productImageMessage" class="d-block mt-3">មិនទាន់មានរូបភាព</small></span></div>
                        {{-- Image --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">ជ្រើសរើសរូបភាព</label>
                            <input type="file" id="productImageInput" name="image" class="form-control" accept="image/*">
                            <small class="text-muted d-block mt-2">ទំហំអតិបរមា 2 MB</small>
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">បរិយាយ</label>
                            <textarea name="description" class="form-control" rows="3"
                                placeholder="ព័ត៌មានបន្ថែម...">{{ old('description', $product->description ?? '') }}</textarea>
                        </div>

                        </aside></div>
                        {{-- Buttons --}}
                        <div class="d-flex gap-2 pe-footer">
                            <button type="submit" class="btn"
                                style="background:#e85d24;color:#fff;padding:10px 24px;border-radius:6px;font-weight:600">
                                <i class="fas fa-check me-2"></i> រក្សាទុក
                            </button>
                            <a href="{{ isset($product) ? route('products.show', $product) : route('products.index') }}" class="btn btn-outline-secondary"
                                style="padding:10px 24px;border-radius:6px;font-weight:600">
                                បោះបង់
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

        // Ties (e.g. 1500/4000 = 0.375) round DOWN to the cent below, not up —
        // matches the stored decimal(10,2) column, which can't hold a third decimal.
        function roundHalfDown(value, precision) {
            const factor = Math.pow(10, precision);
            const scaled = value * factor;
            const floor = Math.floor(scaled);
            if (Math.abs(scaled - floor - 0.5) < 1e-6) {
                return floor / factor;
            }
            return Math.round(scaled) / factor;
        }

        function onKhrInput(val) {
            const khr = parseFloat(val);
            if (isNaN(khr)) {
                document.getElementById('price_usd').value = '';
                return;
            }
            document.getElementById('price_usd').value = roundHalfDown(khr / currentExchangeRate, 2);
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
