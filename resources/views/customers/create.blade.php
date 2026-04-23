@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')

    <div class="row">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body" style="padding: 36px;">
                    <div style="margin-bottom: 28px;">
                        <h2 style="font-size: 28px; font-weight: 700; color: #1a1d29; margin: 0 0 8px 0;">
                            អតិថិជនថ្មី
                        </h2>
                        <p style="color: #6c757d; margin: 0; font-size: 14px;">បំពេញព័ត៏មានភ្ញៀវខាងក្រោម</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 8px;">
                            <strong>Please fix the following errors:</strong>
                            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf

                        <!-- Basic Information Section -->
                        <div style="margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #e9ecef;">
                            <h5 style="font-size: 16px; font-weight: 700; color: #1a1d29; margin-bottom: 20px;">
                                <i class="fas fa-info-circle" style="color: #e85d24; margin-right: 8px;"></i>Basic
                                Information
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" style="font-weight: 600; color: #1a1d29;">ឈ្មោះអតិថិជន
                                            <span style="color: #dc3545;">*</span></label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                            required placeholder="បំពេញឈ្មោះ"
                                            style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" style="font-weight: 600; color: #1a1d29;">ប្រភេទអតិថិជន
                                            <span style="color: #dc3545;">*</span></label>
                                        <select name="type" class="form-control" required
                                            style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                            <option value="">-- ជ្រើសរើស --</option>
                                            <option value="facebook" {{ old('type') === 'facebook' ? 'selected' : '' }}><i
                                                    class="fab fa-facebook"></i> Facebook</option>
                                            <option value="telegram" {{ old('type') === 'telegram' ? 'selected' : '' }}><i
                                                    class="fab fa-telegram"></i> Telegram</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            style="font-weight: 600; color: #1a1d29;">លេខទំនាក់ទំនង</label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                                            placeholder="+1-234-567-8900"
                                            style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information Section -->
                        <div style="margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #e9ecef;">
                            <h5 style="font-size: 16px; font-weight: 700; color: #1a1d29; margin-bottom: 20px;">
                                <i class="fas fa-map-marker-alt" style="color: #e85d24; margin-right: 8px;"></i>Location
                                Details
                            </h5>

                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1a1d29;">ទីតាំងអតិថិជន</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="បំពេញទីតាំង"
                                    style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">{{ old('address') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"
                                            style="font-weight: 600; color: #1a1d29;">ខេត្ត/ក្រុង</label>
                                        <input type="text" name="city" class="form-control" value="{{ old('city') }}"
                                            placeholder="បំពេញឈ្មោះខេត្ត/ក្រុង"
                                            style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div style="margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #e9ecef;">
                            <h5 style="font-size: 16px; font-weight: 700; color: #1a1d29; margin-bottom: 20px;">
                                <i class="fas fa-sticky-note" style="color: #e85d24; margin-right: 8px;"></i>ផ្សេងៗ
                            </h5>

                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600; color: #1a1d29;">Status Notes</label>
                                <textarea name="notes" class="form-control" rows="3"
                                    placeholder="Add notes about this customer's status, e.g., 'input data by user' or 'note customer'"
                                    style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">{{ old('notes') }}</textarea>
                                <small style="color: #6c757d; margin-top: 4px; display: block;">These notes will appear as
                                    status descriptions when hovering over the status badge</small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div
                            style="display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e9ecef;">
                            <button type="submit" class="btn"
                                style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 12px 28px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-plus"></i> Create Customer
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn"
                                style="background: #f8f9fa; color: #1a1d29; padding: 12px 28px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection