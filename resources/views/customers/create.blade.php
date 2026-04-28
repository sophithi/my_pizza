@extends('layouts.app')

@section('title', 'Create Customer')

@push('styles')
<style>
    .form-card {
        max-width: 980px;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .form-header {
        padding: 20px 24px 16px;
        border-bottom: 1px solid #edf0f4;
        background: #fff;
    }

    .form-title {
        font-size: 26px;
        font-weight: 900;
        color: #111827;
        margin: 0;
    }

    .form-subtitle {
        color: #64748b;
        margin-top: 6px;
        font-size: 14px;
    }

    .form-body {
        padding: 22px 24px 24px;
    }

    .form-section {
        background: #fff;
        border-bottom: 1px solid #edf0f4;
        padding-bottom: 18px;
        margin-bottom: 18px;
    }

    .form-section:last-of-type {
        margin-bottom: 12px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 900;
        color: #111827;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title i {
        color: #e85d24;
    }

    .form-label {
        font-size: 14px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 6px;
    }

    .required {
        color: #dc3545;
    }

    .form-control,
    .form-select {
        border-radius: 6px;
        border: 1px solid #dbe3ef;
        min-height: 44px;
        padding: 10px 12px;
        font-size: 14px;
        color: #111827;
        box-shadow: none;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #e85d24;
        box-shadow: 0 0 0 4px rgba(232, 93, 36, .12);
    }

    .hint-text {
        color: #64748b;
        font-size: 12px;
        margin-top: 6px;
        display: block;
    }

    .action-bar {
        display: flex;
        gap: 10px;
        justify-content: flex-start;
        padding-top: 2px;
    }

    .btn-orange {
        background: linear-gradient(135deg, #ff6b35, #e85d24);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 800;
    }

    .btn-orange:hover {
        color: #fff;
        opacity: .9;
    }

    .btn-cancel {
        background: #fff;
        color: #475569;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 800;
        text-decoration: none;
    }

    .btn-cancel:hover {
        background: #f8fafc;
        color: #111827;
    }

    @media(max-width: 768px) {
        .form-header,
        .form-body {
            padding: 20px;
        }

        .action-bar {
            flex-direction: column;
        }

        .btn-orange,
        .btn-cancel {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')

<div class="form-card">

    <div class="form-header">
        <h2 class="form-title">អតិថិជនថ្មី</h2>
        <div class="form-subtitle">បំពេញព័ត៌មានអតិថិជនខាងក្រោម ដើម្បីបង្កើតអតិថិជនថ្មី</div>
    </div>

    <div class="form-body">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 12px;">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('customers.store') }}" method="POST">
            @csrf

            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    ព័ត៌មានទូទៅ
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            ឈ្មោះអតិថិជន <span class="required">*</span>
                        </label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}" required
                               placeholder="បញ្ចូលឈ្មោះអតិថិជន">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            ប្រភេទអតិថិជន <span class="required">*</span>
                        </label>
                        <select name="type" class="form-select" required>
                            <option value="">-- ជ្រើសរើសប្រភេទ --</option>
                            <option value="facebook" {{ old('type') === 'facebook' ? 'selected' : '' }}>
                                Facebook
                            </option>
                            <option value="telegram" {{ old('type') === 'telegram' ? 'selected' : '' }}>
                                Telegram
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">លេខទំនាក់ទំនង</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone') }}"
                               placeholder="បញ្ចូលលេខទូរស័ព្ទ">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    ព័ត៌មានទីតាំង
                </div>

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">ទីតាំងអតិថិជន</label>
                        <textarea name="address" class="form-control" rows="2"
                                  placeholder="បញ្ចូលទីតាំងអតិថិជន">{{ old('address') }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">ខេត្ត/ក្រុង</label>
                        <input type="text" name="city" class="form-control"
                               value="{{ old('city') }}"
                               placeholder="បញ្ចូលខេត្ត ឬ ក្រុង">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-sticky-note"></i>
                    កំណត់ចំណាំ
                </div>

                <label class="form-label">Status Notes</label>
                <textarea name="notes" class="form-control" rows="2"
                          placeholder="បញ្ចូលកំណត់ចំណាំអំពីអតិថិជន">{{ old('notes') }}</textarea>
                <small class="hint-text">
                    កំណត់ចំណាំនេះនឹងបង្ហាញក្នុងព័ត៌មានលម្អិតរបស់អតិថិជន
                </small>
            </div>

            <div class="action-bar">
                <a href="{{ route('customers.index') }}" class="btn btn-cancel">
                     បោះបង់
                </a>

                <button type="submit" class="btn btn-orange">
                     រួចរាល់
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
