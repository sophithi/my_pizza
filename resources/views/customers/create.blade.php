@extends('layouts.app')

@section('title', isset($customer) ? 'Edit Customer' : 'Create Customer')

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body" style="padding: 28px;">
                <h3 style="font-size: 20px; font-weight: 700; color: #1a1d29; margin-bottom: 24px;">
                    {{ isset($customer) ? 'Edit Customer' : 'Create New Customer' }}
                </h3>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ isset($customer) ? route('customers.update', $customer) : route('customers.store') }}" method="POST">
                    @csrf
                    @if(isset($customer)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Customer Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name ?? '') }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Customer Type *</label>
                        <select name="type" class="form-control" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            <option value="">Select Type</option>
                            <option value="facebook" {{ old('type', $customer->type ?? '') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                            <option value="telegram" {{ old('type', $customer->type ?? '') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Email *</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email ?? '') }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone ?? '') }}" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Location</label>
                        <textarea name="address" class="form-control" rows="2" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">{{ old('address', $customer->address ?? '') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">City</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city ?? '') }}" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Postal Code</label>
                                <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $customer->postal_code ?? '') }}" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                    </div>


                    <div style="display: flex; gap: 12px; margin-top: 28px;">
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-save"></i> {{ isset($customer) ? 'Update' : 'Create' }} Customer
                        </button>
                        <a href="{{ route('customers.index') }}" class="btn" style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
