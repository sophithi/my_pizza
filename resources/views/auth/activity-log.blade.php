@extends('layouts.app')

@section('title', 'My Activity Log')

@push('styles')
<style>
    :root {
        --accent: #e85d24;
        --bg: #f4f5f7;
        --surface: #ffffff;
        --border: #e9ecef;
        --text: #1a1d29;
        --text-muted: #6c757d;
    }

    body { background: var(--bg); }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: var(--text);
        margin: 0;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: var(--surface);
        padding: 24px;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
    }

    .activity-table {
        width: 100%;
        border-collapse: collapse;
        background: var(--surface);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .activity-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid var(--border);
    }

    .activity-table thead th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .activity-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .activity-table tbody tr:hover {
        background: #f8f9fa;
    }

    .activity-table tbody td {
        padding: 16px;
        color: var(--text);
        font-size: 14px;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-login {
        background: #d1e7dd;
        color: #0f5132;
    }

    .badge-logout {
        background: #f8d7da;
        color: #721c24;
    }

    .time-ago {
        font-size: 12px;
        color: var(--text-muted);
    }

    .ip-info {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        color: var(--text-muted);
    }

    .user-info-card {
        background: var(--surface);
        padding: 24px;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 32px;
    }

    .user-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    .info-group {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .info-value {
        font-size: 15px;
        color: var(--text);
        font-weight: 600;
    }

    .pagination {
        margin-top: 32px;
        display: flex;
        justify-content: center;
        gap: 8px;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--text-muted);
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    .empty-text {
        font-size: 16px;
        font-weight: 600;
        color: var(--text);
    }
</style>
@endpush

@section('content')

<div style="max-width: 1200px; margin: 0 auto; padding: 28px;">

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title"> My Activity Log</h1>
    </div>

    <!-- User Info Card -->
    <div class="user-info-card">
        <h2 style="font-size: 16px; font-weight: 700; margin: 0 0 20px 0; color: var(--text);"> Your Profile</h2>
        <div class="user-info-grid">
            <div class="info-group">
                <div class="info-label">Name</div>
                <div class="info-value">{{ auth()->user()->name }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Email</div>
                <div class="info-value">{{ auth()->user()->email }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Role</div>
                <div class="info-value">{{ auth()->user()->getRoleLabel() }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Last Login</div>
                <div class="info-value">
                    @if(auth()->user()->last_login_at)
                        {{ auth()->user()->last_login_at->translatedFormat('M d, Y H:i:s') }}
                    @else
                        <span style="color: var(--text-muted);">Never</span>
                    @endif
                </div>
            </div>
            <div class="info-group">
                <div class="info-label">Last Login IP</div>
                <div class="info-value ip-info">{{ auth()->user()->last_login_ip ?? '—' }}</div>
            </div>
            <div class="info-group">
                <div class="info-label">Current IP</div>
                <div class="info-value ip-info">{{ request()->ip() }}</div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label">📊 Total Logins</div>
            <div class="stat-value" style="color: #10b981;">{{ $activities->where('activity_type', 'login')->count() ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">⏱️ Total Logouts</div>
            <div class="stat-value" style="color: #ef4444;">{{ $activities->where('activity_type', 'logout')->count() ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">🕐 Active Sessions</div>
            <div class="stat-value" style="color: #0d6efd;">
                @php
                    $logins = $activities->where('activity_type', 'login')->count();
                    $logouts = $activities->where('activity_type', 'logout')->count();
                    $active = max(0, $logins - $logouts);
                @endphp
                {{ $active }}
            </div>
        </div>
    </div>

    <!-- Activity Table -->
    @if($activities->count())
    <table class="activity-table">
        <thead>
            <tr>
                <th>Activity</th>
                <th>Date & Time</th>
                <th>IP Address</th>
                <th>User Agent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
            <tr>
                <td>
                    <span class="badge {{ $activity->activity_type === 'login' ? 'badge-login' : 'badge-logout' }}">
                        {{ $activity->activity_type === 'login' ? '✓ Login' : '✕ Logout' }}
                    </span>
                </td>
                <td>
                    <div>{{ $activity->activity_at->translatedFormat('M d, Y H:i:s') }}</div>
                    <div class="time-ago" title="{{ $activity->activity_at->format('Y-m-d H:i:s') }}">
                        {{ $activity->activity_at->diffForHumans() }}
                    </div>
                </td>
                <td>
                    <div class="ip-info">{{ $activity->ip_address ?? '—' }}</div>
                </td>
                <td>
                    <div style="font-size: 12px; color: var(--text-muted); max-width: 300px; word-break: break-all;">
                        {{ Str::limit($activity->user_agent, 80, '...') ?? '—' }}
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div style="margin-top: 32px;">
        {{ $activities->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">📭</div>
        <div class="empty-text">No activity yet</div>
        <p style="color: var(--text-muted); margin-top: 8px;">Your login/logout history will appear here</p>
    </div>
    @endif

</div>

@endsection
