@extends('layouts.app')

@section('title', 'Edit User')

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body" style="padding: 28px;">
                <h3 style="font-size: 20px; font-weight: 700; color: #1a1d29; margin-bottom: 24px;">Edit User</h3>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Password (Leave blank to keep current)</label>
                                <input type="password" name="password" class="form-control" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" style="font-weight: 600;">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Role *</label>
                        <select name="role" class="form-control" required style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
                            <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Profile</label>
                        <textarea name="profile" class="form-control" rows="3" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;" placeholder="Brief profile or bio">{{ old('profile', $user->profile) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 600;">Profile Image</label>
                        @if($user->profile_image)
                        <div style="margin-bottom: 12px;">
                            <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" style="width: 100px; height: 100px; border-radius: 8px; object-fit: cover;">
                        </div>
                        @endif
                        <input type="file" name="profile_image" class="form-control" accept="image/*" style="border-radius: 6px; border: 1px solid #e9ecef; padding: 10px 12px;">
                        <div style="color: #6c757d; font-size: 12px; margin-top: 4px;">Accepted formats: JPEG, PNG, JPG, GIF (Max 2MB)</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ old('is_active', $user->is_active) ? 'checked' : '' }} style="cursor: pointer;">
                            <label class="form-check-label" for="is_active" style="font-weight: 600; cursor: pointer;">
                                Active
                            </label>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 28px;">
                        <button type="submit" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-save"></i> Update User
                        </button>
                        <a href="{{ route('users.show', $user) }}" class="btn" style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600;">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
