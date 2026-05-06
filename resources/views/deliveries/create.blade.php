@extends('layouts.app')

@section('title', 'Create Delivery')

@push('styles')
    <style>
        :root {
            --accent: #e85d24;
            --bg: #f4f5f7;
            --surface: #ffffff;
            --border: #e9ecef;
            --text: #1a1d29;
            --text-muted: #6c757d;
        }

        body {
            background: var(--bg);
        }

        .form-container {
            max-width: 760px;
            margin: 42px auto;
            padding: 0 20px;
        }

        .form-header {
            align-items: flex-end;
            display: flex;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .form-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
            margin: 0 0 6px;
        }

        .form-subtitle {
            color: var(--text-muted);
            font-size: 13px;
            margin-bottom: 28px;
        }

        .form-card {
            background: var(--surface);
            padding: 28px;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .form-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(220px, 280px);
            gap: 18px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-group label .req {
            color: #dc3545;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text);
            transition: border-color 0.2s;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(232, 93, 36, 0.1);
        }

        .price-preview {
            background: #fff7f3;
            border: 1px solid #fde0d3;
            border-radius: 10px;
            padding: 16px;
        }

        .price-preview-label {
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .price-preview-main {
            color: var(--accent);
            font-size: 26px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 12px;
        }

        .price-preview-line {
            color: var(--text-muted);
            display: flex;
            font-size: 13px;
            justify-content: space-between;
            padding-top: 8px;
        }

        .actions-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 20px;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--accent) 0%, #d94a10 100%);
            color: #fff;
            padding: 12px 28px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(232, 93, 36, 0.3);
        }

        .btn-cancel {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 20px;
        }

        .error-msg {
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
        }

        @media (max-width: 768px) {
            .form-header,
            .actions-row {
                align-items: stretch;
                flex-direction: column;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
@section('content')
    <div class="form-container">
        <div class="form-header">
            <div>
                <h1 class="form-title">ការដឹកជញ្ជូន</h1>
                <p class="form-subtitle">បន្ថែមសេវាដឹកជញ្ជូនថ្មី</p>
            </div>
            <a href="{{ route('deliveries.index') }}" class="btn-cancel">ត្រឡប់ក្រោយ</a>
        </div>
        @if($errors->any())
            <div
                style="background: #f8d7da; border: 1px solid #f5c6cb;
                color: #721c24; padding: 12px 20px; border-radius: 8px;
                margin-bottom: 20px; font-size: 13px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('deliveries.store') }}" method="POST">
            @csrf
            <div class="form-card">
                <div class="form-grid">
                    <div>
                        <div class="form-group">
                            <label>ឈ្មោះប្រភេទដឹកជញ្ជូន <span class="req">*</span></label>
                            <input type="text" name="delivery_name" class="form-control" value="{{ old('delivery_name') }}"
                                placeholder="..." required>
                            @error('delivery_name') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label>តម្លៃក្នុង ១ កេស (៛) <span class="req">*</span></label>
                            <input type="number" name="delivery_price_khr" id="deliveryPriceInput" class="form-control" step="1" min="0"
                                value="{{ old('delivery_price_khr', '0') }}" placeholder="0" required>
                            @error('delivery_price_khr') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label>ផ្សេងៗ</label>
                            <textarea name="delivery_desc" class="form-control" rows="4"
                                placeholder="...">{{ old('delivery_desc') }}</textarea>
                            @error('delivery_desc') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="price-preview">
                        <div class="price-preview-label">តម្លៃដឹកជញ្ជូន</div>
                        <div class="price-preview-main">៛<span id="priceOne">0</span></div>
                        <div class="price-preview-line">
                            <span>1 កេស</span>
                            <strong>៛<span id="priceOneLine">0</span></strong>
                        </div>
                        <div class="price-preview-line">
                            <span>2 កេស</span>
                            <strong>៛<span id="priceTwoLine">0</span></strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions-row">
                <button type="submit" class="btn-submit">បញ្ជាក់</button>
                <a href="{{ route('deliveries.index') }}" class="btn-cancel">បោះបង់</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function updatePricePreview() {
            const price = Math.max(parseInt(document.getElementById('deliveryPriceInput').value || 0, 10) || 0, 0);
            document.getElementById('priceOne').textContent = price.toLocaleString();
            document.getElementById('priceOneLine').textContent = price.toLocaleString();
            document.getElementById('priceTwoLine').textContent = (price * 2).toLocaleString();
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('deliveryPriceInput')?.addEventListener('input', updatePricePreview);
            updatePricePreview();
        });
    </script>
@endpush
