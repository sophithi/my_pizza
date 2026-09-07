@extends('layouts.app')

@section('title', 'កែសម្រួលវិក្កយបត្រ - ' . $invoice->invoice_number)

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 style="font-size: 26px; font-weight: 800; color: #1e293b; margin: 0;">
                    <i class="fas fa-file-invoice text-warning me-2"></i> កែសម្រួលវិក្កយបត្រ - {{ $invoice->invoice_number }}
                </h2>
                <p class="text-muted small mb-0 mt-1">កែប្រែស្ថានភាពបង់ប្រាក់ ឬបន្ថែមចំណាំផ្សេងៗលើវិក្កយបត្រ</p>
            </div>
            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary px-3 py-2 fw-bold" style="border-radius: 8px;">
                <i class="fas fa-arrow-left me-1"></i> ត្រឡប់ក្រោយ
            </a>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <!-- Invoice Quick Info Header -->
                    <div class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="text-muted small d-block">អតិថិជន</span>
                            <strong class="text-dark">{{ $invoice->order?->customer?->name ?? 'N/A' }}</strong>
                            @if($invoice->order?->customer?->phone)
                                <span class="text-muted small">({{ $invoice->order->customer->phone }})</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-muted small d-block">ការបញ្ជាទិញ</span>
                            <span class="badge bg-secondary">#ORD-{{ str_pad($invoice->order_id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div>
                            <span class="text-muted small d-block">ទឹកប្រាក់សរុប</span>
                            <strong class="text-danger" style="font-size: 16px;">${{ number_format($invoice->total_amount, 2) }}</strong>
                            <small class="text-muted">(៛{{ number_format($invoice->order?->totalKhr() ?? ($invoice->total_amount * 4000)) }})</small>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('invoices.update', $invoice) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Payment Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold text-dark" style="font-size: 13.5px;">
                                    <i class="fas fa-money-check-alt text-primary me-1"></i> ស្ថានភាពការបង់ប្រាក់
                                </label>
                                <select name="status" id="status" class="form-select" style="border-radius: 8px; font-weight: 600; padding: 10px 12px;">
                                    <option value="draft" {{ $invoice->status === 'draft' ? 'selected' : '' }}>
                                        🟡 មិនទាន់ទូទាត់ 
                                    </option>
                                    <option value="paid" {{ $invoice->status === 'paid' ? 'selected' : '' }}>
                                        🟢 បានទូទាត់រួច
                                    </option>
                                    <option value="cancelled" {{ $invoice->status === 'cancelled' ? 'selected' : '' }}>
                                        🔴 មិនទូទាត់ / បោះបង់ 
                                    </option>
                                </select>
                            </div>

                            <!-- Notes / ផ្សេងៗ -->
                            <div class="mb-4">
                                <label for="notes" class="form-label fw-bold text-dark" style="font-size: 13.5px;">
                                    <i class="fas fa-sticky-note text-warning me-1"></i> ផ្សេងៗ (កំណត់ចំណាំ / Note)
                                </label>
                                <textarea name="notes" id="notes" class="form-control" rows="4" style="border-radius: 8px; font-size: 14px; padding: 12px;"
                                    placeholder="បញ្ចូលកំណត់ចំណាំលើវិក្កយបត្រនេះ (ឧទាហរណ៍៖ បានបង់តាម ABA ម៉ោង 2:30 PM, អតិថិជនសុំពន្យារពេលបង់ប្រាក់, ចំណាំបញ្ចុះតម្លៃ...)">{{ $invoice->notes }}</textarea>
                                <small class="text-muted mt-1 d-block" style="font-size: 11.5px;">
                                    💡 <em>អ្នកអាចសរសេរព័ត៌មានបន្ថែមទាក់ទងនឹងការបង់ប្រាក់ ឬការកត់ត្រាផ្សេងៗនៅទីនេះ។</em>
                                </small>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold" style="background: #e85d24; border-color: #e85d24; border-radius: 8px;">
                                        <i class="fas fa-save me-1"></i> រក្សាទុក
                                    </button>
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-light border px-3 py-2 fw-bold text-muted" style="border-radius: 8px;">
                                        បោះបង់ 
                                    </a>
                                </div>

                                @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <button type="button" class="btn btn-outline-danger px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#deleteReasonModal" style="border-radius: 8px;">
                                        <i class="fas fa-trash-alt me-1"></i> លុបវិក្ក័យបត្រ
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <!-- Modal Popup: Ask Why Delete Invoice -->
    <div class="modal fade" id="deleteReasonModal" tabindex="-1" aria-labelledby="deleteReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title fw-bold" id="deleteReasonModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i> លុបវិក្កយបត្រ {{ $invoice->invoice_number }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form id="delete-invoice-form" action="{{ route('invoices.destroy', $invoice) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-0 d-flex align-items-center gap-2 mb-3" style="border-radius: 10px; font-size: 13px;">
                            <i class="fas fa-info-circle fs-5 text-warning flex-shrink-0"></i>
                            <div>វិក្កយបត្រនេះនឹងត្រូវផ្ទេរទៅកាន់ <strong>"ធុងសំរាម "</strong> ប៉ុន្តែអ្នកអាចស្តារឡើងវិញបាន។</div>
                        </div>

                        <label for="delete_reason_input" class="form-label fw-bold text-dark mb-2" style="font-size: 14px;">
                            ❓ តើហេតុអ្វីបានជាអ្នកចង់លុបវិក្កយបត្រនេះ? <span class="text-danger">*</span>
                        </label>

                        <!-- Quick Choice Tags -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-sm btn-light border py-1.5 px-2.5 text-dark fw-medium" onclick="setQuickReason('អតិថិជនសុំបោះបង់ការកុម្ម៉ង់')" style="border-radius: 8px; font-size: 12px; transition: all 0.2s;">
                                ❌ អតិថិជនសុំបោះបង់
                            </button>
                            <button type="button" class="btn btn-sm btn-light border py-1.5 px-2.5 text-dark fw-medium" onclick="setQuickReason('បញ្ចូលទិន្នន័យស្ទួន / ច្រឡំ')" style="border-radius: 8px; font-size: 12px; transition: all 0.2s;">
                                🔄 កុម្ម៉ង់ស្ទួន / ច្រឡំ
                            </button>
                            <button type="button" class="btn btn-sm btn-light border py-1.5 px-2.5 text-dark fw-medium" onclick="setQuickReason('ប្តូរមុខទំនិញ / កែសម្រួលតម្លៃថ្មី')" style="border-radius: 8px; font-size: 12px; transition: all 0.2s;">
                                ✏️ ប្តូរមុខទំនិញ / តម្លៃ
                            </button>
                            <button type="button" class="btn btn-sm btn-light border py-1.5 px-2.5 text-dark fw-medium" onclick="setQuickReason('ការបញ្ជាទិញសាកល្បងប្រព័ន្ធ')" style="border-radius: 8px; font-size: 12px; transition: all 0.2s;">
                                🧪 តេស្តសាកល្បង
                            </button>
                        </div>

                        <textarea name="delete_reason" id="delete_reason_input" class="form-control" rows="3" required
                            placeholder="សូមសរសេរមូលហេតុនៃការលុបវិក្កយបត្រនេះនៅទីនេះ..." style="border-radius: 10px; font-size: 13.5px;"></textarea>
                    </div>

                    <div class="modal-footer bg-light px-4 py-3 border-0 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary px-3 py-2 fw-bold" data-bs-dismiss="modal" style="border-radius: 8px;">
                            បោះបង់ 
                        </button>
                        <button type="submit" class="btn btn-danger px-4 py-2 fw-bold" style="border-radius: 8px;">
                            <i class="fas fa-trash-alt me-1"></i> បញ្ជាក់ការលុប 
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        function setQuickReason(text) {
            const input = document.getElementById('delete_reason_input');
            if (input) {
                input.value = text;
                input.focus();
            }
        }
        window.setQuickReason = setQuickReason;
    </script>
@endpush
