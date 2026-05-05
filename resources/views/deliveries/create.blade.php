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
            max-width: 600px;
            margin: 30px auto;
            padding: 0 20px;
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
            padding: 32px;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
    </style>
@endpush
@section('content')
    <div class="form-container">
        <h1 class="form-title"> ការដឹកជញ្ជូន</h1>
        <p class="form-subtitle">បន្ថែមសេវាដឹកជញ្ជូនថ្មី</p>
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
                <div class="form-group">
                    <label>ឈ្មោះប្រភេទដឹកជញ្ជូន <span class="req">*</span></label>
                    <input type="text" name="delivery_name" class="form-control" value="{{ old('delivery_name') }}"
                        placeholder="..." required>
                    @error('delivery_name') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>តម្លៃដឹកជញ្ជូន (៛) <span class="req">*</span></label>
                    <input type="number" name="delivery_price_khr" class="form-control" step="1" min="0"
                        value="{{ old('delivery_price_khr', '0') }}" placeholder="0" required>
                   

                    @error('delivery_price_khr') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>ផ្សេងៗ</label>
                    <textarea name="delivery_desc" class="form-control" rows="4"
                        placeholder="...">{{ old('delivery_desc') }}</textarea>
                    @error('delivery_desc') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; align-items: center; gap: 12px;">
                <button type="submit" class="btn-submit">បញ្ជាក់</button>
                <a href="{{ route('deliveries.index') }}" class="btn-cancel">បោះបង់</a>
            </div>
        </form>
    </div>
@endsection
