@extends('layouts.app')

@section('title', 'ព័ត៌មានផ្ទាល់ខ្លួន')

@section('content')

<style>
    .profile-wrap {
        max-width: 900px;
    }

    .profile-hero {
        background: linear-gradient(135deg, #1a1d29 0%, #2d1f0e 55%, #c44a18 100%);
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .profile-hero-pattern {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle at 80% 50%, rgba(232,93,36,0.18) 0%, transparent 60%);
        pointer-events: none;
    }

    .profile-hero-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 16px;
        object-fit: cover;
        border: 3px solid rgba(255,255,255,0.2);
        box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }

    .profile-avatar-placeholder {
        width: 100px;
        height: 100px;
        border-radius: 16px;
        background: rgba(255,255,255,0.15);
        border: 3px solid rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: 800;
        color: #fff;
    }

    .profile-hero-info h2 {
        font-size: 24px;
        font-weight: 800;
        color: #fff;
        margin: 0 0 6px;
    }

    .profile-hero-info p {
        color: rgba(255,255,255,0.5);
        font-size: 13px;
        margin: 0;
    }

    .profile-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        margin-top: 10px;
    }

    .role-admin { background: rgba(220,38,38,0.2); color: #fca5a5; }
    .role-manager { background: rgba(59,130,246,0.2); color: #93c5fd; }
    .role-staff { background: rgba(168,85,247,0.2); color: #d8b4fe; }

    .section-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .section-header {
        padding: 20px 28px 16px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header i {
        color: #e85d24;
        font-size: 16px;
    }

    .section-header h3 {
        font-size: 16px;
        font-weight: 700;
        color: #1a1d29;
        margin: 0;
    }

    .section-body {
        padding: 24px 28px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1a1d29;
        margin-bottom: 8px;
    }

    .form-group label i {
        color: #e85d24;
        margin-right: 4px;
        width: 14px;
        text-align: center;
    }

    .profile-input {
        width: 100%;
        border: 1.5px solid #e9ecef;
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 14px;
        color: #1a1d29;
        transition: all 0.2s;
        background: #fff;
        font-family: inherit;
    }

    .profile-input:focus {
        outline: none;
        border-color: #e85d24;
        box-shadow: 0 0 0 3px rgba(232,93,36,0.1);
    }

    .profile-input::placeholder {
        color: #b0b8c4;
    }

    textarea.profile-input {
        resize: none;
        min-height: 80px;
    }

    .image-upload-area {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .current-image {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #e9ecef;
    }

    .current-image-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: #f4f5f7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 700;
        color: #b0b8c4;
        border: 2px dashed #d0d5dd;
    }

    .upload-hint {
        font-size: 12px;
        color: #6c757d;
        margin-top: 6px;
    }

    .btn-save {
        background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%);
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-save:hover {
        box-shadow: 0 8px 20px rgba(232,93,36,0.3);
        transform: translateY(-2px);
    }

    .btn-cancel {
        background: #f4f5f7;
        color: #1a1d29;
        border: 1px solid #e9ecef;
        padding: 12px 28px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-cancel:hover {
        background: #e9ecef;
        color: #1a1d29;
    }

    .alert-success-custom {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 14px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 14px;
    }

    .alert-errors {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 14px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .alert-errors ul {
        margin: 0;
        padding-left: 18px;
    }

    .alert-errors li {
        font-size: 13px;
        margin-bottom: 4px;
    }

    .pw-strength {
        font-size: 11px;
        color: #6c757d;
        margin-top: 4px;
    }

    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
        .profile-hero-content { flex-direction: column; text-align: center; }
        .image-upload-area { flex-direction: column; }
    }
</style>

<div class="profile-wrap">

    @if(session('success'))
    <div class="alert-success-custom">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert-errors">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Profile Hero -->
    <div class="profile-hero">
        <div class="profile-hero-pattern"></div>
        <div class="profile-hero-content">
            @if($user->profile_image)
                <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" class="profile-avatar">
            @else
                <div class="profile-avatar-placeholder">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div class="profile-hero-info">
                <h2>{{ $user->name }}</h2>
                <p>{{ $user->email }}</p>
                <span class="profile-role-badge role-{{ $user->role }}">
                    @if($user->role === 'admin')
                        <i class="fas fa-crown"></i> Administrator
                    @elseif($user->role === 'manager')
                        <i class="fas fa-chart-line"></i> Manager
                    @else
                        <i class="fas fa-user"></i> Staff
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-user-edit"></i>
            <h3>ព័ត៌មានផ្ទាល់ខ្លួន</h3>
        </div>
        <div class="section-body">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> ឈ្មោះ</label>
                        <input type="text" name="name" class="profile-input" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> អ៊ីមែល</label>
                        <input type="email" name="email" class="profile-input" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> ព័ត៌មានអំពីខ្លួន</label>
                    <textarea name="profile" class="profile-input" placeholder="បញ្ចូលព័ត៌មានអំពីខ្លួន...">{{ old('profile', $user->profile) }}</textarea>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-camera"></i> រូបភាពប្រវត្តិរូប</label>
                    <div class="image-upload-area">
                        @if($user->profile_image)
                            <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" class="current-image">
                        @else
                            <div class="current-image-placeholder">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                        <div>
                            <input type="file" name="profile_image" class="profile-input" accept="image/*">
                            <div class="upload-hint">JPEG, PNG, JPG, GIF (អតិបរមា 2MB)</div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 8px;">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> រក្សាទុក
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> ត្រលប់ក្រោយ
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="section-card">
        <div class="section-header">
            <i class="fas fa-lock"></i>
            <h3>ផ្លាស់ប្ដូរពាក្យសម្ងាត់</h3>
        </div>
        <div class="section-body">
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label><i class="fas fa-key"></i> ពាក្យសម្ងាត់បច្ចុប្បន្ន</label>
                    <input type="password" name="current_password" class="profile-input" required placeholder="បញ្ចូលពាក្យសម្ងាត់បច្ចុប្បន្ន">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> ពាក្យសម្ងាត់ថ្មី</label>
                        <input type="password" name="password" class="profile-input" required placeholder="យ៉ាងហោចណាស់ 8 តួអក្សរ">
                        <div class="pw-strength">យ៉ាងហោចណាស់ 8 តួអក្សរ</div>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-check-double"></i> បញ្ជាក់ពាក្យសម្ងាត់ថ្មី</label>
                        <input type="password" name="password_confirmation" class="profile-input" required placeholder="បញ្ចូលពាក្យសម្ងាត់ថ្មីម្ដងទៀត">
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-shield-alt"></i> ផ្លាស់ប្ដូរពាក្យសម្ងាត់
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
