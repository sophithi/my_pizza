@extends('layouts.app')

@section('title', 'Users')

@section('content')

<div class="mb-4 d-flex justify-content-between align-items-center">
    <h2 style="font-size: 24px; font-weight: 700; color: #1a1d29; margin: 0;">Users</h2>
    <a href="{{ route('users.create') }}" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
        <i class="fas fa-plus"></i> Add User
    </a>
</div>

@if($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body" style="padding: 24px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Avatar</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Name</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Email</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Role</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Status</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr style="border-bottom: 1px solid #e9ecef;">
                        <td style="padding: 12px;">
                            @if($user->profile_image)
                                <img src="{{ asset($user->profile_image) }}" alt="{{ $user->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td style="padding: 12px; color: #1a1d29; font-weight: 600;">{{ $user->name }}</td>
                        <td style="padding: 12px; color: #6c757d;">{{ $user->email }}</td>
                        <td style="padding: 12px;">
                            @if($user->role == 'admin')
                                <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #ffebee; color: #c62828;">
                                    <i class="fas fa-crown"></i> Admin
                                </span>
                            @elseif($user->role == 'manager')
                                <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #e3f2fd; color: #1976d2;">
                                    <i class="fas fa-chart-line"></i> Manager
                                </span>
                            @else
                                <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f3e5f5; color: #7b1fa2;">
                                    <i class="fas fa-user"></i> Staff
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            @if($user->is_active)
                                <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #d4edda; color: #155724;">
                                    Active
                                </span>
                            @else
                                <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f8d7da; color: #721c24;">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            <a href="{{ route('users.show', $user) }}" class="btn-action" style="color: #0d6efd; text-decoration: none; font-size: 12px; margin-right: 10px;"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('users.edit', $user) }}" class="btn-action" style="color: #0d6efd; text-decoration: none; font-size: 12px; margin-right: 10px;"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #dc3545; cursor: pointer; font-size: 12px;" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 32px; text-align: center; color: #6c757d;">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>

@endsection
