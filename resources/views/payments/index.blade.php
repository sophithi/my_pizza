@extends('layouts.app')

@section('title', 'ចំណូលពីការទូទាត់')

@push('styles')
    <style>
.payment-side-panel {
    position: fixed;
    right: 0;
    top: 0;
    margin: 0;
    width: 520px;
    max-width: 95%;
    height: 100vh;
}

#paymentModal {
    background: transparent;
}

#paymentModal .modal-dialog {
    margin: 0 0 0 auto;
}

.payment-side-panel .modal-content {
    height: 100vh;
    border-radius: 18px 0 0 18px;
    border: none;
    box-shadow: -10px 0 30px rgba(0,0,0,.18);
}

.payment-side-panel .modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #eee;
}

.payment-side-panel .modal-body {
    padding: 24px;
    overflow-y: auto;
}

.payment-side-panel .modal-footer {
    padding: 18px 24px;
    border-top: 1px solid #eee;
    background: #fff;
}

.badge-paid {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
    font-weight: 700;
}

.badge-partial {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fbbf24;
    font-weight: 700;
}

.badge-pending {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #f87171;
    font-weight: 700;
}

.modal.fade .payment-side-panel {
    transform: translateX(100%);
    transition: transform .25s ease-out;
}

.modal.show .payment-side-panel {
    transform: translateX(0);
}

@media (max-width: 576px) {
    .payment-side-panel {
        width: 100%;
    }

    .payment-side-panel .modal-content {
        border-radius: 0;
    }
}
</style>
@endpush

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <h4 class="mb-0 fw-semibold">
                <i class="bi bi-credit-card me-2"></i> ប្រតិបត្តិការទូទាត់ពីអតិថិជន
            </h4>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('payments.export.excel', request()->query()) }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> នាំចេញ CSV
                </a>
                <a href="{{ route('payments.export.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm"
                    target="_blank">
                    <i class="bi bi-file-earmark-pdf me-1"></i> នាំចេញ PDF
                </a>
                <button type="button" class="btn btn-sm text-white" style="background:#D85A30" data-bs-toggle="modal"
                    data-bs-target="#paymentModal">
                    + កត់ត្រាការទូទាត់
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small text-uppercase mb-1">បានប្រមូល</div>
                        <div class="fs-5 fw-bold text-primary">${{ number_format($stats['collected'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small text-uppercase mb-1">នៅសល់</div>
                        <div class="fs-5 fw-bold text-danger">${{ number_format($stats['outstanding'], 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small text-uppercase mb-1">ការបញ្ជាទិញ</div>
                        <div class="fs-5 fw-bold">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small text-uppercase mb-1">បានបង់គ្រប់</div>
                        <div class="fs-5 fw-bold text-success">{{ $stats['paid'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small text-uppercase mb-1">បង់ខ្លះ</div>
                        <div class="fs-5 fw-bold text-warning">{{ $stats['partial'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="card border-0 bg-light h-100">
                    <div class="card-body py-3">
                        <div class="text-muted small text-uppercase mb-1">មិនទាន់បង់</div>
                        <div class="fs-5 fw-bold text-danger">{{ $stats['unpaid'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Date Quick Filters --}}
        <div class="d-flex gap-2 flex-wrap mb-3">
            @foreach(['all' => 'គ្រប់ពេល', 'today' => 'ថ្ងៃនេះ', 'week' => 'សប្តាហ៍នេះ', 'month' => 'ខែនេះ', 'custom' => 'កំណត់ថ្ងៃ'] as $key => $label)
                <a href="{{ route('payments.index', array_merge(request()->query(), ['period' => $key])) }}"
                    class="btn btn-sm btn-outline-secondary rounded-pill quick-pill {{ request('period', 'all') === $key ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Custom Date Range --}}
        @if(request('period') === 'custom')
            <form method="GET" action="{{ route('payments.index') }}" class="d-flex gap-2 align-items-center mb-3 flex-wrap">
                <input type="hidden" name="period" value="custom">
                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                <label class="small text-muted mb-0">ពីថ្ងៃទី</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm"
                    style="width:160px">
                <label class="small text-muted mb-0">ដល់ថ្ងៃទី</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm"
                    style="width:160px">
                <button type="submit" class="btn btn-sm btn-secondary">អនុវត្ត</button>
            </form>
        @endif

        {{-- Search + Status Tabs --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <ul class="nav border-bottom w-auto">
                @foreach(['all' => 'ទាំងអស់', 'paid' => 'បានបង់', 'partial' => 'បង់ខ្លះ', 'pending' => 'មិនទាន់បង់'] as $key => $label)
                    <li class="nav-item">
                        <a class="nav-link tab-link px-3 py-2 {{ request('status', 'all') === $key ? 'active' : 'text-muted' }}"
                            href="{{ route('payments.index', array_merge(request()->query(), ['status' => $key])) }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <form method="GET" action="{{ route('payments.index') }}" class="d-flex gap-2">
                @foreach(request()->except('search') as $k => $v)
                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                @endforeach
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ស្វែងរកអតិថិជន ឬលេខការបញ្ជាទិញ..."
                    class="form-control form-control-sm" style="width:220px">
                <button class="btn btn-sm btn-outline-secondary">ស្វែងរក</button>
            </form>
        </div>

        {{-- Payments Table --}}
        <div class="card border shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>អតិថិជន</th>
                            <th>កាលបរិច្ឆេទ</th>
                            <th>សរុបការបញ្ជាទិញ</th>
                            <th>បានបង់</th>
                            <th>នៅសល់</th>
                            <th>វិធីបង់</th>
                            <th>ស្ថានភាព</th>
                            <th class="text-center">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $payment->customer_name }}</div>
                                    <div class="text-muted small">{{ $payment->order_id }}</div>
                                </td>
                                <td class="text-muted small">{{ \Carbon\Carbon::parse($payment->order_date)->format('d M Y') }}
                                </td>
                                <td>${{ number_format($payment->total_amount, 2) }}</td>
                                <td class="text-success fw-semibold">${{ number_format($payment->paid_amount, 2) }}</td>
                                <td>
                                    @if($payment->balance > 0)
                                        <span
                                            class="text-danger small">${{ number_format($payment->balance, 2) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $payment->method }}</td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-1 badge-{{ $payment->status }}">
                                        {{ $payment->status === 'pending' ? 'មិនទាន់បង់' : ($payment->status === 'partial' ? 'បង់ខ្លះ' : 'បានបង់') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-secondary"
                                        onclick="openPaymentForm(@js($payment))" data-bs-toggle="modal"
                                        data-bs-target="#paymentModal">
                                        {{ $payment->payment_id ? 'កែប្រែ' : 'កត់ត្រា' }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    មិនមានទិន្នន័យការទូទាត់សម្រាប់រយៈពេលនេះទេ។
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $payments->withQueryString()->links() }}
        </div>

    </div>

    {{-- Record / Edit Payment Modal --}}
   <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true" data-bs-backdrop="false">
        <div class="modal-dialog payment-side-panel">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">កត់ត្រាការទូទាត់អតិថិជន</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="paymentForm" method="POST" action="{{ route('payments.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="form_method" value="POST">
                    <input type="hidden" name="payment_id" id="form_payment_id">
                    <input type="hidden" name="source_order_id" id="form_source_order_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted">ឈ្មោះអតិថិជន</label>
                            <input type="text" name="customer_name" id="f_customer_name" class="form-control"
                                placeholder="ឧ. សុភា ចាន់" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">លេខការបញ្ជាទិញ</label>
                            <input type="text" name="order_id" id="f_order_id" class="form-control"
                                placeholder="ឧ. ORD-0007">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">កាលបរិច្ឆេទបញ្ជាទិញ</label>
                            <input type="date" name="order_date" id="f_order_date" class="form-control" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col">
                                <label class="form-label small text-muted">សរុបការបញ្ជាទិញ ($)</label>
                                <input type="number" name="total_amount" id="f_total" class="form-control" step="0.01"
                                    min="0" placeholder="0.00" oninput="updateStatusBadge()" required>
                            </div>
                            <div class="col">
                                <label class="form-label small text-muted">ចំនួនបានបង់ ($)</label>
                                <input type="number" name="paid_amount" id="f_paid" class="form-control" step="0.01" min="0"
                                    placeholder="0.00" oninput="updateStatusBadge()">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">វិធីបង់ប្រាក់</label>
                            <div class="d-flex gap-2 flex-wrap" id="methodPills">
                                @foreach(['Cash', 'ABA', 'ACLEDA', 'Wing', 'Other'] as $m)
                                    <span
                                        class="badge rounded-pill border px-3 py-2 method-pill {{ $m === 'Cash' ? 'bg-danger text-white border-danger' : 'bg-white text-secondary' }}"
                                        style="cursor:pointer;font-size:13px" onclick="pickMethod(this, '{{ $m }}')">
                                        {{ $m }}
                                    </span>
                                @endforeach
                            </div>
                            <input type="hidden" name="method" id="f_method" value="Cash">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted">កំណត់ចំណាំ (បើមាន)</label>
                            <textarea name="notes" id="f_notes" class="form-control" rows="2"
                                placeholder="ឧ. ចំនួននៅសល់នឹងបង់ថ្ងៃស្អែក"></textarea>
                        </div>
                        <div class="d-flex align-items-center justify-content-between bg-light rounded px-3 py-2">
                            <span class="text-muted small">ស្ថានភាពការទូទាត់</span>
                            <span id="statusBadge" class="badge rounded-pill px-3 badge-paid">បានបង់</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">បោះបង់</button>
                        <button type="submit" class="btn text-white" style="background:#D85A30">រក្សាទុក</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const paymentModal = document.getElementById('paymentModal');

        function updateStatusBadge() {
            const total = parseFloat(document.getElementById('f_total').value) || 0;
            const paid = parseFloat(document.getElementById('f_paid').value) || 0;
            const badge = document.getElementById('statusBadge');
            badge.className = 'badge rounded-pill px-3 ';
            if (paid <= 0) { badge.classList.add('badge-pending'); badge.textContent = 'មិនទាន់បង់'; }
            else if (paid >= total) { badge.classList.add('badge-paid'); badge.textContent = 'បានបង់'; }
            else { badge.classList.add('badge-partial'); badge.textContent = 'បង់ខ្លះ'; }
        }

        function pickMethod(el, method) {
            document.querySelectorAll('.method-pill').forEach(p => {
                p.className = 'badge rounded-pill border px-3 py-2 method-pill bg-white text-secondary';
                p.style.cursor = 'pointer';
                p.style.fontSize = '13px';
            });
            el.className = 'badge rounded-pill border px-3 py-2 method-pill bg-danger text-white border-danger';
            el.style.cursor = 'pointer';
            el.style.fontSize = '13px';
            document.getElementById('f_method').value = method;
        }

        function resetModal() {
            document.getElementById('paymentModalLabel').textContent = 'កត់ត្រាការទូទាត់អតិថិជន';
            document.getElementById('paymentForm').action = '{{ route("payments.store") }}';
            document.getElementById('form_method').value = 'POST';
            document.getElementById('form_payment_id').value = '';
            document.getElementById('form_source_order_id').value = '';
            document.getElementById('f_customer_name').value = '';
            document.getElementById('f_order_id').value = '';
            document.getElementById('f_order_date').value = new Date().toISOString().slice(0, 10);
            document.getElementById('f_total').value = '';
            document.getElementById('f_paid').value = '';
            document.getElementById('f_notes').value = '';
            document.getElementById('f_method').value = 'Cash';
            document.querySelectorAll('.method-pill').forEach((p, i) => {
                p.className = 'badge rounded-pill border px-3 py-2 method-pill ' + (i === 0 ? 'bg-danger text-white border-danger' : 'bg-white text-secondary');
                p.style.cursor = 'pointer'; p.style.fontSize = '13px';
            });
            updateStatusBadge();
        }

        function openPaymentForm(payment) {
            const isEdit = !!payment.payment_id;

            document.getElementById('paymentModalLabel').textContent = (isEdit ? 'កែប្រែការទូទាត់ - ' : 'កត់ត្រាការទូទាត់ - ') + payment.order_id;
            document.getElementById('paymentForm').action = isEdit
                ? '{{ url('/payments') }}/' + payment.payment_id
                : '{{ route("payments.store") }}';
            document.getElementById('form_method').value = isEdit ? 'PUT' : 'POST';
            document.getElementById('form_payment_id').value = payment.payment_id || '';
            document.getElementById('form_source_order_id').value = payment.source_order_id || '';
            document.getElementById('f_customer_name').value = payment.customer_name;
            document.getElementById('f_order_id').value = payment.order_id;
            document.getElementById('f_order_date').value = String(payment.order_date).slice(0, 10);
            document.getElementById('f_total').value = payment.total_amount;
            document.getElementById('f_paid').value = payment.paid_amount;
            document.getElementById('f_notes').value = payment.notes || '';
            document.getElementById('f_method').value = payment.method === '—' ? 'Cash' : payment.method;
            document.querySelectorAll('.method-pill').forEach(p => {
                const isMatch = p.textContent.trim() === (payment.method === '—' ? 'Cash' : payment.method);
                p.className = 'badge rounded-pill border px-3 py-2 method-pill ' + (isMatch ? 'bg-danger text-white border-danger' : 'bg-white text-secondary');
                p.style.cursor = 'pointer'; p.style.fontSize = '13px';
            });
            updateStatusBadge();
        }

        paymentModal?.addEventListener('show.bs.modal', event => {
            const trigger = event.relatedTarget;

            if (!trigger?.hasAttribute('onclick')) {
                resetModal();
            }

            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
        });

        paymentModal?.addEventListener('hidden.bs.modal', () => {
            resetModal();
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
        });
    </script>
@endpush
