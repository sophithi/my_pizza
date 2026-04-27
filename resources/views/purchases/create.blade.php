@extends('layouts.app')

@section('title', 'បញ្ចូលចំណាយប្រចាំថ្ងៃ')

@push('styles')
    <style>
        .expense-form-wrap {
            max-width: 760px;
            margin: 0 auto;
            padding: 28px 20px;
        }

        .expense-form-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
            padding: 28px;
        }

        .expense-form-card .form-control,
        .expense-form-card .form-select {
            border-color: #d9dee7;
            border-radius: 6px;
            min-height: 44px;
        }

        .expense-form-card .form-control:focus,
        .expense-form-card .form-select:focus {
            border-color: #e85d24;
            box-shadow: 0 0 0 .2rem rgba(232, 93, 36, .14);
        }

        .expense-submit {
            background: #e85d24;
            border: 0;
            border-radius: 7px;
            color: #fff;
            font-weight: 700;
            min-height: 44px;
            padding: 10px 22px;
        }

        .expense-submit:hover {
            background: #d94a10;
            color: #fff;
        }
    </style>
@endpush

@section('content')
    <div class="expense-form-wrap">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="mb-1" style="font-size: 30px; font-weight: 800; color: #0f172a;">បញ្ចូលចំណាយប្រចាំថ្ងៃ</h1>
                <p class="text-muted mb-0">កត់ត្រាចំណាយដូចជា ទិញសម្ភារៈ ប្រេង សេវា ឬចំណាយផ្សេងៗ។</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="border-radius: 8px;">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="expense-form-card">
            <form action="{{ route('purchases.store') }}" method="POST" autocomplete="off">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">លេខយោង</label>
                        <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number') }}"
                            placeholder="EXP-001" autocomplete="off" spellcheck="false">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ប្រភេទ / អ្នកទទួល <span class="text-danger">*</span></label>
                        <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name') }}"
                            placeholder="ឧ. ប្រេង, សម្ភារៈ, បុគ្គលិក" required autocomplete="off" spellcheck="false">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">កាលបរិច្ឆេទ <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" class="form-control"
                            value="{{ old('purchase_date', now('Asia/Phnom_Penh')->format('Y-m-d')) }}" required autocomplete="off">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">រូបិយប័ណ្ណ</label>
                        <select name="amount_currency" class="form-select" autocomplete="off">
                            <option value="USD" {{ old('amount_currency', 'USD') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="KHR" {{ old('amount_currency') === 'KHR' ? 'selected' : '' }}>KHR (៛)</option>
                        </select>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">ចំនួនទឹកប្រាក់ <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" class="form-control" min="0.01" step="0.01"
                            value="{{ old('total_amount') }}" placeholder="0.00" required autocomplete="off">
                        <small class="text-muted">If KHR is selected, the system converts it using ៛4,000 = $1.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ស្ថានភាព <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required autocomplete="off">
                            <option value="pending" {{ old('status', 'received') === 'pending' ? 'selected' : '' }}>មិនទាន់ទូទាត់</option>
                            <option value="received" {{ old('status', 'received') === 'received' ? 'selected' : '' }}>បានទូទាត់</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>បានលុប</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">កំណត់ចំណាំ</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="ព័ត៌មានបន្ថែម..." autocomplete="off"
                            spellcheck="false">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="submit" class="expense-submit">
                        <i class="fas fa-save me-1"></i> រក្សាទុក
                    </button>
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
