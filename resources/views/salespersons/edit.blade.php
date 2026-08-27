@extends('layouts.app')

@section('title', 'Edit Salesperson')

@push('styles')
    <style>
        .form-card {
            max-width: 800px;
            margin: 0 auto;
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
            font-size: 24px;
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
            padding: 24px;
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

        .action-bar {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #edf0f4;
        }

        .btn-submit {
            background: linear-gradient(135deg, #ff6b35, #e85d24);
            border: none;
            color: #fff;
            font-weight: 700;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            transition: all .2s;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(232, 93, 36, .2);
        }

        .btn-cancel {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 700;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 14px;
            text-decoration: none;
            transition: all .2s;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
            color: #334155;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="form-card">
            <div class="form-header">
                <h2 class="form-title">កែប្រែព័ត៌មានភ្នាក់ងារលក់</h2>
                <p class="form-subtitle">ធ្វើបច្ចុប្បន្នភាពព័ត៌មានលម្អិតរបស់ភ្នាក់ងារលក់ខាងក្រោម។</p>
            </div>

            <form action="{{ route('salespersons.update', $salesperson) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ឈ្មោះភ្នាក់ងារលក់ <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $salesperson->name) }}" placeholder="បញ្ចូលឈ្មោះភ្នាក់ងារលក់" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">លេខទូរស័ព្ទ</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $salesperson->phone) }}" placeholder="បញ្ចូលលេខទូរស័ព្ទ">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ស្ថានភាព <span class="required">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $salesperson->status) == 'active' ? 'selected' : '' }}>សកម្ម</option>
                                <option value="inactive" {{ old('status', $salesperson->status) == 'inactive' ? 'selected' : '' }}>អសកម្ម</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="action-bar">
                        <a href="{{ route('salespersons.index') }}" class="btn-cancel">បោះបង់</a>
                        <button type="submit" class="btn-submit">រក្សាទុក</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

