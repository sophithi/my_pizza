@extends('layouts.app')

@section('title', 'Salesperson Details')

@push('styles')
    <style>
        .profile-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .profile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 16px;
        }

        .profile-title {
            font-size: 28px;
            font-weight: 900;
            color: #111827;
            margin: 0;
        }

        .btn-action-back {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 700;
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action-back:hover {
            background: #e2e8f0;
            color: #334155;
        }

        .info-card {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .05);
            margin-bottom: 24px;
            display: flex;
            gap: 24px;
            align-items: center;
            flex-wrap: wrap;
        }

        .avatar-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #ff6b35;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 900;
        }

        .profile-details {
            flex: 1;
            min-width: 240px;
        }

        .details-name {
            font-size: 22px;
            font-weight: 900;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .details-meta {
            margin-top: 8px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            font-size: 14px;
            color: #64748b;
        }

        .details-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-pill {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            padding: 4px 10px;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-topgrade {
            background: #fef3c7;
            color: #854d0e;
        }

        .channel-pill {
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            font-weight: 800;
            padding: 3px 8px;
            gap: 4px;
        }

        .channel-facebook {
            background: #e7f3ff;
            color: #0a66c2;
        }

        .channel-telegram {
            background: #e0f7ff;
            color: #0088cc;
        }

        .customers-card {
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, .05);
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #edf0f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 18px;
            font-weight: 900;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title i {
            color: #e85d24;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .customers-table {
            width: 100%;
            border-collapse: collapse;
        }

        .customers-table th {
            background: #f8fafc;
            padding: 14px 20px;
            font-size: 13px;
            font-weight: 800;
            color: #475569;
            text-align: left;
            border-bottom: 1px solid #edf2f7;
        }

        .customers-table td {
            padding: 14px 20px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #edf2f7;
        }

        .customers-table tr:last-child td {
            border-bottom: none;
        }

        .btn-view {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            transition: all .2s;
            text-decoration: none;
        }

        .btn-view:hover {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4 profile-container">
        <div class="profile-header">
            <div>
                <h2 class="profile-title">លម្អិតភ្នាក់ងារលក់</h2>
            </div>
            <div>
                <a href="{{ route('salespersons.index') }}" class="btn-action-back">
                    <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ
                </a>
            </div>
        </div>

        <div class="info-card">
            <div class="avatar-wrap">
                {{ strtoupper(substr($salesperson->name, 0, 1)) }}
            </div>
            <div class="profile-details">
                <h3 class="details-name">
                    {{ $salesperson->name }}
                    @if($salesperson->status == 'active')
                        <span class="status-pill status-active"><i class="fas fa-check-circle"></i> សកម្ម</span>
                    @else
                        <span class="status-pill status-inactive"><i class="fas fa-times-circle"></i> អសកម្ម</span>
                    @endif
                </h3>
                <div class="details-meta">
                    <span>
                        <i class="fas fa-phone-alt"></i> {{ $salesperson->phone ?? 'គ្មានលេខទូរស័ព្ទ' }}
                    </span>
                    <span>
                        <i class="fas fa-users"></i> អតិថិជនសរុប៖ {{ number_format($salesperson->customers()->count()) }} នាក់
                    </span>
                </div>
            </div>
            <div>
                <a href="{{ route('salespersons.edit', $salesperson) }}" class="sales-btn sales-btn-primary" style="padding: 10px 20px;">
                    <i class="fas fa-edit"></i> កែប្រែព័ត៌មាន
                </a>
            </div>
        </div>

        <div class="customers-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users"></i> អតិថិជនដែលគ្រប់គ្រង (Customers Managed)
                </h3>
            </div>

            {{-- Period Filter --}}
            <div class="mb-2 d-flex align-items-center gap-2 flex-wrap" style="padding: 16px 24px 8px 24px;">
                @foreach(['all' => 'គ្រប់ពេល', 'today' => 'ថ្ងៃនេះ', 'month' => 'ខែនេះ', 'custom' => 'កំណត់ថ្ងៃ'] as $key => $label)
                    <a href="{{ route('salespersons.show', [$salesperson, 'period' => $key]) }}"
                        style="font-size: 12px; font-weight: 800; border-radius: 9999px; padding: 5px 14px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; border: 1px solid {{ request('period', 'all') === $key ? '#e85d24' : '#cbd5e1' }}; color: {{ request('period', 'all') === $key ? '#fff' : '#475569' }}; background-color: {{ request('period', 'all') === $key ? '#e85d24' : '#fff' }}; transition: all 0.2s;">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if(request('period') === 'custom')
                <form method="GET" action="{{ route('salespersons.show', $salesperson) }}" class="d-flex gap-2 align-items-center flex-wrap" style="padding: 0 24px 16px 24px;">
                    <input type="hidden" name="period" value="custom">
                    <label class="small text-muted mb-0 fw-bold" style="font-size: 13px;">ពីថ្ងៃទី</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm" style="width:160px; min-height: 34px; padding: 4px 8px; font-size: 13px;">
                    <label class="small text-muted mb-0 fw-bold" style="font-size: 13px;">ដល់ថ្ងៃទី</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm" style="width:160px; min-height: 34px; padding: 4px 8px; font-size: 13px;">
                    <button type="submit" class="sales-btn sales-btn-primary" style="padding: 6px 12px; font-size: 13px; border-radius: 6px;">អនុវត្ត</button>
                </form>
            @endif

            <div class="table-responsive">
                <table class="customers-table">
                    <thead>
                        <tr>
                            <th>អតិថិជន</th>
                            <th>ប្រភេទ</th>
                            <th>លេខទូរស័ព្ទ</th>
                            <th>ទីតាំង/ខេត្ត</th>
                            <th class="text-right" style="text-align: right;">ចំនួនកម្មង់</th>
                            <th>ស្ថានភាព</th>
                            <th>សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td class="fw-bold" style="font-weight: 800;">
                                    <a href="{{ route('customers.show', $customer) }}" style="color: #e85d24; text-decoration: none;">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td>
                                    @if($customer->type == 'facebook')
                                        <span class="channel-pill channel-facebook"><i class="fab fa-facebook-f"></i> Facebook</span>
                                    @elseif($customer->type == 'telegram')
                                        <span class="channel-pill channel-telegram"><i class="fab fa-telegram-plane"></i> Telegram</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $customer->phone ?? '—' }}</td>
                                <td>{{ $customer->city ?? $customer->address ?? '—' }}</td>
                                <td class="text-right" style="text-align: right;">
                                    {{ number_format($customer->orders_count) }} ដង
                                </td>
                                <td>
                                    @if($customer->status == 'active')
                                        <span class="status-pill status-active"><i class="fas fa-check-circle"></i> សកម្ម</span>
                                    @elseif($customer->status == 'inactive')
                                        <span class="status-pill status-inactive"><i class="fas fa-times-circle"></i> អសកម្ម</span>
                                    @elseif($customer->status == 'topgrade')
                                        <span class="status-pill status-topgrade"><i class="fas fa-crown"></i> អតិថិជនកម្មង់ច្រើនបំផុត</span>
                                    @else
                                        <span class="text-muted">{{ $customer->status ?? 'รង់ចាំ' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('customers.show', $customer) }}" class="btn-view" title="មើលលម្អិត">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    មិនទាន់មានអតិថិជនដែលគ្រប់គ្រងដោយភ្នាក់ងារលក់នេះទេ។
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4">
                    {{ $customers->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection

