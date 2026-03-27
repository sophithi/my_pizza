@extends('layouts.app')

@section('title', $user->name)

@section('content')

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; margin-bottom: 20px;">
            <div class="card-body" style="padding: 28px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 24px;">
                    <div>
                        @if($user->profile_image)
                        <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" style="width: 120px; height: 120px; border-radius: 12px; object-fit: cover; margin-bottom: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        @else
                        <div style="width: 120px; height: 120px; border-radius: 12px; background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 48px; margin-bottom: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        @endif
                        <h2 style="font-size: 28px; font-weight: 700; color: #1a1d29; margin: 0;">{{ $user->name }}</h2>
                        <div style="display: flex; gap: 12px; align-items: center; margin-top: 12px;">
                            @if($user->role == 'admin')
                                <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #ffebee; color: #c62828;">
                                    <i class="fas fa-crown"></i> Administrator
                                </span>
                            @elseif($user->role == 'manager')
                                <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #e3f2fd; color: #1976d2;">
                                    <i class="fas fa-chart-line"></i> Manager
                                </span>
                            @else
                                <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f3e5f5; color: #7b1fa2;">
                                    <i class="fas fa-user"></i> Staff
                                </span>
                            @endif
                            @if($user->is_active)
                                <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #d4edda; color: #155724;">
                                    Active
                                </span>
                            @else
                                <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f8d7da; color: #721c24;">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Email</p>
                        <p style="color: #1a1d29; margin: 0;">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p style="color: #6c757d; font-weight: 600; margin-bottom: 4px;">Role</p>
                        <p style="color: #1a1d29; margin: 0; text-transform: capitalize;">{{ $user->getRoleLabel() }}</p>
                    </div>
                </div>

                @if($user->profile)
                <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e9ecef;">
                    <p style="color: #6c757d; font-weight: 600; margin-bottom: 8px;">Profile</p>
                    <p style="color: #1a1d29; margin: 0;">{{ $user->profile }}</p>
                </div>
                @endif

                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('users.edit', $user) }}" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 24px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('users.index') }}" class="btn" style="background: #f8f9fa; color: #1a1d29; padding: 10px 24px; border-radius: 6px; border: 1px solid #e9ecef; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
