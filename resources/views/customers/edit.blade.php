@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
    <style>
        .customer-form-card {
            max-width: 980px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        }

        .customer-form-card .form-control,
        .customer-form-card .form-select {
            border-color: #d9dee7;
            border-radius: 6px;
            min-height: 44px;
        }

        .customer-form-card .form-control:focus,
        .customer-form-card .form-select:focus {
            border-color: #e85d24;
            box-shadow: 0 0 0 .2rem rgba(232, 93, 36, .14);
        }

        .customer-section-title {
            color: #101827;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .customer-section {
            border-bottom: 1px solid #edf0f4;
            padding-bottom: 24px;
            margin-bottom: 24px;
        }

        .btn-orange {
            background: #e85d24;
            border-color: #e85d24;
            color: #fff;
            font-weight: 700;
        }

        .btn-orange:hover {
            background: #d94a10;
            border-color: #d94a10;
            color: #fff;
        }
    </style>

    <div class="customer-form-card p-4 p-lg-5">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h2 class="mb-1" style="font-size: 28px; font-weight: 800; color: #0f172a;">កែប្រែអតិថិជន</h2>
                <p class="text-muted mb-0">Update customer information for {{ $customer->name }}.</p>
            </div>
         
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 8px;">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('customers.update', $customer) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="customer-section">
                <div class="customer-section-title">
                    <i class="fas fa-user me-2" style="color: #e85d24;"></i>Basic Information
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ឈ្មោះអតិថិជន <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}"
                            required placeholder="បំពេញឈ្មោះ" autocomplete="off" spellcheck="false">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ប្រភេទអតិថិជន <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required autocomplete="off">
                            <option value="">-- ជ្រើសរើស --</option>
                            <option value="facebook" {{ old('type', $customer->type) === 'facebook' ? 'selected' : '' }}>
                                Facebook
                            </option>
                            <option value="telegram" {{ old('type', $customer->type) === 'telegram' ? 'selected' : '' }}>
                                Telegram
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">លេខទំនាក់ទំនង</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}"
                            placeholder="បំពេញលេខទូរស័ព្ទ" autocomplete="off" spellcheck="false">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select" autocomplete="off">
                            <option value="active" {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="customer-section">
                <div class="customer-section-title">
                    <i class="fas fa-map-marker-alt me-2" style="color: #e85d24;"></i>Location Details
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">ទីតាំងអតិថិជន</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="បំពេញទីតាំង" autocomplete="off"
                            spellcheck="false">{{ old('address', $customer->address) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ខេត្ត/ក្រុង</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city) }}"
                            placeholder="បំពេញឈ្មោះខេត្ត/ក្រុង" autocomplete="off" spellcheck="false">
                    </div>
                </div>
            </div>

            <div class="customer-section">
                <div class="customer-section-title">
                    <i class="fas fa-sticky-note me-2" style="color: #e85d24;"></i>ផ្សេងៗ
                </div>

                <label class="form-label fw-semibold">កំណត់ចំណាំ</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="បញ្ចូលកំណត់ចំណាំផ្សេងៗ" autocomplete="off"
                    spellcheck="false">{{ old('notes', $customer->notes) }}</textarea>
            </div>

            <div class="d-flex flex-wrap gap-2 pt-1">
                <button type="submit" class="btn btn-orange px-4">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary px-4">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
