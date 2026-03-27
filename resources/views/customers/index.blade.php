@extends('layouts.app')

@section('title', 'Customers')

@section('content')

<div class="mb-4 d-flex justify-content-between align-items-center">
    <h2 style="font-size: 24px; font-weight: 700; color: #1a1d29; margin: 0;">Customers</h2>
    <a href="{{ route('customers.create') }}" class="btn" style="background: linear-gradient(135deg, #e85d24 0%, #d94a10 100%); color: #fff; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
        <i class="fas fa-plus"></i> Add Customer
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
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Type</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Name</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Phone</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Location</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Orders History</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Status</th>
                        <th style="padding: 12px; font-weight: 600; font-size: 12px; text-transform: uppercase;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr style="border-bottom: 1px solid #e9ecef;">
                        <td style="padding: 12px;">
                            @if($customer->type == 'facebook')
                                <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #e7f3ff; color: #0a66c2; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fab fa-facebook-f"></i> Facebook
                                </span>
                            @elseif($customer->type == 'telegram')
                                <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #e0f7ff; color: #0088cc; display: inline-flex; align-items: center; gap: 6px;">
                                    <i class="fab fa-telegram"></i> Telegram
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px; color: #1a1d29; font-weight: 600;">{{ $customer->name }}</td>
                        <td style="padding: 12px; color: #6c757d;">{{ $customer->phone ?? 'N/A' }}</td>
                        <td style="padding: 12px; color: #6c757d;">{{ $customer->city ?? $customer->address ?? 'N/A' }}</td>
                        <td style="padding: 12px; color: #1a1d29;">
                            @if($customer->orders_count > 0)
                                <span style="padding: 4px 8px; border-radius: 4px; background: #e3f2fd; color: #1976d2; font-weight: 600; font-size: 12px;">
                                    {{ $customer->orders_count }} orders
                                </span>
                            @else
                                <span style="color: #999; font-style: italic;">No orders yet</span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            @if($customer->status == 'active')
                                <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #d4edda; color: #155724;">
                                    Active
                                </span>
                            @elseif($customer->status == 'inactive')
                                <span style="padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f8d7da; color: #721c24;">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px;">
                            <a href="{{ route('customers.show', $customer) }}" class="btn-action" title="View" style="color: #0d6efd; text-decoration: none; font-size: 12px; margin-right: 10px;"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn-action" title="Edit" style="color: #0d6efd; text-decoration: none; font-size: 12px; margin-right: 10px;"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete" style="background: none; border: none; color: #dc3545; cursor: pointer; font-size: 12px;" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 32px; text-align: center; color: #6c757d;">No customers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $customers->links() }}
</div>

@endsection
