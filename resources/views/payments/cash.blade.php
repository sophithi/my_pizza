@extends('layouts.app')

@section('title', 'រាប់លុយក្រៅក្នុងថត (Cash Counter)')

@push('styles')
<style>
/* Cash Count Container */
.cash-count-wrapper {
    max-width: 1400px;
    margin: 0 auto;
}

/* Currency Headers */
.currency-header-khr {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: #fff;
    border-radius: 14px 14px 0 0;
    padding: 16px 20px;
}

.currency-header-usd {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: #fff;
    border-radius: 14px 14px 0 0;
    padding: 16px 20px;
}

/* Denomination Cards */
.denom-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 14px;
    transition: all 0.2s ease;
    margin-bottom: 10px;
}

.denom-card:hover, .denom-card:focus-within {
    border-color: #D85A30;
    box-shadow: 0 4px 14px rgba(216, 90, 48, 0.12);
    transform: translateY(-1px);
}

.denom-badge {
    min-width: 90px;
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    letter-spacing: 0.3px;
}

/* KHR Denomination Visual Badges */
.badge-khr-100k { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }
.badge-khr-50k  { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
.badge-khr-20k  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.badge-khr-10k  { background: #ede9fe; color: #5b21b6; border: 1px solid #c4b5fd; }
.badge-khr-5k   { background: #fae8ff; color: #86198f; border: 1px solid #f0abfc; }
.badge-khr-2k   { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
.badge-khr-1k   { background: #e0e7ff; color: #3730a3; border: 1px solid #a5b4fc; }
.badge-khr-500  { background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }
.badge-khr-100  { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }

/* USD Denomination Visual Badges */
.badge-usd-100  { background: #ecfdf5; color: #047857; border: 1px solid #6ee7b7; }
.badge-usd-50   { background: #f0fdf4; color: #15803d; border: 1px solid #86efac; }
.badge-usd-20   { background: #f0fdfa; color: #0f766e; border: 1px solid #5eead4; }
.badge-usd-10   { background: #fefce8; color: #a16207; border: 1px solid #fde047; }
.badge-usd-5    { background: #fdf4ff; color: #a21caf; border: 1px solid #f0abfc; }
.badge-usd-2    { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.badge-usd-1    { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }

/* Quantity Controls */
.qty-input {
    width: 90px;
    font-weight: 700;
    font-size: 16px;
    text-align: center;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
}

.qty-input:focus {
    border-color: #D85A30;
    box-shadow: 0 0 0 0.2rem rgba(216, 90, 48, 0.2);
}

.qty-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    font-size: 13px;
    font-weight: 700;
}

.quick-pill-btn {
    font-size: 11px;
    padding: 2px 7px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    transition: all 0.15s ease;
}

.quick-pill-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}

/* Subtotal text */
.denom-subtotal {
    min-width: 120px;
    text-align: right;
    font-weight: 700;
    font-size: 15px;
}

/* Summary Card */
.summary-box {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
    position: sticky;
    top: 80px;
}

.summary-metric {
    background: #f8fafc;
    border-radius: 10px;
    padding: 12px 14px;
    border: 1px solid #edf2f7;
}

.summary-metric.highlight-khr {
    background: #eff6ff;
    border-color: #bfdbfe;
}

.summary-metric.highlight-usd {
    background: #ecfdf5;
    border-color: #a7f3d0;
}

.summary-metric.highlight-grand {
    background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
    border-color: #fed7aa;
}

.recon-box {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

/* Print Styles */
@page {
    size: auto;
    margin: 5mm 8mm;
}

@media print {
    html, body {
        background: #fff !important;
        margin: 0 !important;
        padding: 0 !important;
        font-size: 11px !important;
        color: #0f172a !important;
        height: auto !important;
        min-height: 0 !important;
        overflow: visible !important;
    }

    .sidebar, .topbar, .btn, .no-print, nav, header, footer, .sidebar-overlay, .modal {
        display: none !important;
    }

    .cash-count-wrapper {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
    }

    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        min-height: 0 !important;
    }

    #printArea {
        display: block !important;
        visibility: visible !important;
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    #printArea * {
        visibility: visible !important;
    }

    .print-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 4px;
    }

    .print-table th, .print-table td {
        padding: 2px 5px !important;
        font-size: 10px !important;
        border: 1px solid #94a3b8 !important;
    }

    .print-table th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .print-summary-box {
        background-color: #f8fafc !important;
        border: 1px solid #94a3b8 !important;
        border-radius: 6px !important;
        padding: 6px 10px !important;
        margin-top: 4px !important;
        margin-bottom: 6px !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .print-signatures {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        margin-top: 10px !important;
        padding-top: 4px !important;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid py-3 cash-count-wrapper">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2 no-print">
        <div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm" title="ត្រឡប់ទៅការទូទាត់">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-calculator text-primary me-2"></i> រាប់លុយក្រៅក្នុងថត (Cash Counter)
                </h4>
            </div>
            <p class="text-muted small mb-0 ms-4 ps-2">ផ្ទៀងផ្ទាត់និងរាប់លុយក្រៅសុទ្ធប្រចាំថ្ងៃជាប្រាក់រៀល (KHR) និងប្រាក់ដុល្លារ (USD)</p>
        </div>

        {{-- Action Bar --}}
        <div class="d-flex gap-2 flex-wrap align-items-center">
            {{-- Date selector form --}}
            <form method="GET" action="{{ route('payments.cash') }}" class="d-flex align-items-center gap-1">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-muted"></i></span>
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" onchange="this.form.submit()">
                </div>
            </form>

            {{-- Reset Button --}}
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetAllCounts()" title="សម្អាតទិន្នន័យរាប់">
                <i class="fas fa-redo-alt me-1"></i> សម្អាត
            </button>

            {{-- Copy to Telegram / Chat --}}
            <button type="button" class="btn btn-outline-info btn-sm text-dark" onclick="copyCashSummary()" title="ចម្លងសេចក្តីសង្ខេបសម្រាប់ផ្ញើ Telegram">
                <i class="fab fa-telegram-plane text-info me-1"></i> ចម្លងរបាយការណ៍
            </button>

            {{-- Print Slip Button --}}
            <button type="button" class="btn btn-sm text-white" style="background:#D85A30" onclick="window.print()" title="បោះពុម្ពបង្កាន់ដៃ">
                <i class="fas fa-print me-1"></i> ព្រីន (Print)
            </button>
        </div>
    </div>

    {{-- Top Setting & Rate Bar --}}
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-exchange-alt"></i> អត្រាប្តូរប្រាក់</span>
                        <div class="input-group input-group-sm" style="max-width: 200px;">
                            <span class="input-group-text bg-light fw-bold">1$ =</span>
                            <input type="number" id="exchangeRateInput" class="form-control form-control-sm fw-bold text-end"
                                value="{{ $exchangeRate }}" min="1000" max="10000" step="10" oninput="updateCalculations()">
                            <span class="input-group-text bg-light">៛</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-8 text-md-end">
                    <div class="d-inline-flex align-items-center gap-3 flex-wrap">
                        <span class="small text-muted">
                            <i class="fas fa-calendar-day me-1"></i> កាលបរិច្ឆេទ៖ <strong>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</strong>
                        </span>
                        <span class="small text-muted">
                            <i class="fas fa-user-circle me-1"></i> អ្នករាប់៖ <strong>{{ auth()->user()->name }}</strong>
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="fas fa-check-circle me-1"></i> ប្រព័ន្ធ (Live Autosave)
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Counting Area --}}
    <div class="row g-4 no-print">

        {{-- LEFT COLUMN: Khmer Riel (KHR ៛) & MIDDLE COLUMN: US Dollar (USD $) --}}
        <div class="col-12 col-lg-8">
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="currency-header-khr d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 fw-bold"><i class="fas fa-money-bill-wave me-2"></i> ប្រាក់រៀល (KHR)</h5>
                                <small class="opacity-75">រាប់ចំនួនសន្លឹកតាមប្រភេទក្រដាសប្រាក់</small>
                            </div>
                            <div class="text-end">
                                <div class="fs-5 fw-bold" id="khrHeaderTotal">0 ៛</div>
                                <small class="opacity-75" id="khrHeaderBillsCount">0 សន្លឹក</small>
                            </div>
                        </div>

                        <div class="card-body p-3">
                            <div id="khrDynamicRows">
                                {{-- Dynamic KHR rows loaded here --}}
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-2" onclick="addDynamicRow('khr')">
                                <i class="fas fa-plus-circle me-1"></i> បន្ថែមប្រភេទក្រដាសប្រាក់ (Add KHR)
                            </button>
                        </div>

                        {{-- KHR Footer Total --}}
                        <div class="card-footer bg-light p-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted">សរុបប្រាក់រៀល (Total KHR):</span>
                                <div class="text-end">
                                    <h5 class="mb-0 fw-bold text-primary" id="khrFooterTotal">0 ៛</h5>
                                    <small class="text-muted" id="khrAsUsd">≈ $0.00</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="currency-header-usd d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 fw-bold"><i class="fas fa-dollar-sign me-2"></i> ប្រាក់ដុល្លារ (USD)</h5>
                                <small class="opacity-75">រាប់ចំនួនសន្លឹកតាមប្រភេទក្រដាសប្រាក់</small>
                            </div>
                            <div class="text-end">
                                <div class="fs-5 fw-bold" id="usdHeaderTotal">$0.00</div>
                                <small class="opacity-75" id="usdHeaderBillsCount">0 សន្លឹក</small>
                            </div>
                        </div>

                        <div class="card-body p-3">
                            <div id="usdDynamicRows">
                                {{-- Dynamic USD rows loaded here --}}
                            </div>
                            <button type="button" class="btn btn-outline-success btn-sm w-100 mt-2" onclick="addDynamicRow('usd')">
                                <i class="fas fa-plus-circle me-1"></i> បន្ថែមប្រភេទក្រដាសប្រាក់ (Add USD)
                            </button>
                        </div>

                        {{-- USD Footer Total --}}
                        <div class="card-footer bg-light p-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted">សរុបប្រាក់ដុល្លារ (Total USD):</span>
                                <div class="text-end">
                                    <h5 class="mb-0 fw-bold text-success" id="usdFooterTotal">$0.00</h5>
                                    <small class="text-muted" id="usdAsKhr">≈ 0 ៛</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Grand Total & Drawer Reconciliation --}}
        <div class="col-12 col-lg-4">
            <div class="summary-box p-3 mb-4">
                <h5 class="fw-bold mb-3 text-dark">
                    <i class="fas fa-receipt text-warning me-2"></i> សរុបលទ្ធផលរាប់លុយក្រៅ
                </h5>

                {{-- Grand Total Counted --}}
                <div class="summary-metric highlight-grand mb-3">
                    <div class="small fw-bold text-uppercase text-secondary mb-1">សរុបលុយក្រៅរាប់ជាក់ស្តែង</div>
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <span class="fs-4 fw-bold text-danger" id="grandTotalUsd">$0.00</span>
                        <span class="fs-5 fw-bold text-primary" id="grandTotalKhr">0 ៛</span>
                    </div>
                    <div class="small text-muted border-top pt-2 mt-1">
                        <div class="d-flex justify-content-between">
                            <span>លុយរៀលរាប់បាន៖</span>
                            <strong id="summaryCountedKhr">0 ៛</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>លុយដុល្លាររាប់បាន៖</span>
                            <strong id="summaryCountedUsd">$0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span>ចំនួនសន្លឹកសរុប៖</span>
                            <strong id="summaryTotalBills">0 សន្លឹក</strong>
                        </div>
                    </div>
                </div>

                {{-- Drawer Reconciliation (Compare with System Sales & Float) --}}
                <div class="recon-box p-3 mb-3">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="fas fa-balance-scale text-primary me-2"></i> ផ្ទៀងផ្ទាត់ថតលុយក្រៅ 
                    </h6>

                    {{-- Opening Float --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted mb-1">លុយតម្កល់ប្រចាំថ្ងៃ ៖</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="input-group input-group-sm border rounded shadow-sm">
                                    <span class="input-group-text bg-light border-0 fw-bold text-secondary">$</span>
                                    <input type="number" id="floatUsd" class="form-control form-control-sm text-end border-0 fw-bold"
                                           value="0.00" min="0" step="1" onfocus="this.select()" oninput="updateCalculations()">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm border rounded shadow-sm">
                                    <input type="number" id="floatKhr" class="form-control form-control-sm text-end border-0 fw-bold"
                                           value="0" min="0" step="1000" onfocus="this.select()" oninput="updateCalculations()">
                                    <span class="input-group-text bg-light border-0 fw-bold text-secondary">៛</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- System Cash Sales (From DB) --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-muted mb-0">លុយក្រៅបានពីការលក់ (Sales)៖</label>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10px; padding: 3px 6px;">
                                {{ $cashTransactionsCount }} ប្រតិបត្តិការ
                            </span>
                        </div>
                        <div class="p-2 rounded bg-light border border-light-subtle">
                            <div class="d-flex justify-content-between small">
                                <span class="text-secondary">ប្រាក់ដុល្លារ ($)៖</span>
                                <span class="fw-bold text-dark">${{ number_format($systemCashUsd, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between small mt-1">
                                <span class="text-secondary">ប្រាក់រៀល (៛)៖</span>
                                <span class="fw-bold text-dark">{{ number_format($systemCashKhr, 0) }} ៛</span>
                            </div>
                            <input type="hidden" id="systemCashUsd" value="{{ $systemCashUsd }}">
                            <input type="hidden" id="systemCashKhr" value="{{ $systemCashKhr }}">
                        </div>
                    </div>

                    {{-- System Cash Expenses/Purchases (From DB) --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-muted mb-0">ចំណាយលុយក្រៅ (Cash Expenses)៖</label>
                        </div>
                        <div class="p-2 rounded bg-danger-subtle border border-danger-subtle" style="background-color: #fff5f5 !important;">
                            <div class="d-flex justify-content-between small text-danger">
                                <span>ប្រាក់ដុល្លារ ($)៖</span>
                                <strong class="fw-bold">-${{ number_format($systemCashPurchaseUsd, 2) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between small mt-1 text-danger">
                                <span>ប្រាក់រៀល (៛)៖</span>
                                <strong class="fw-bold">-{{ number_format($systemCashPurchaseKhr, 0) }} ៛</strong>
                            </div>
                            <input type="hidden" id="systemCashPurchaseUsd" value="{{ $systemCashPurchaseUsd }}">
                            <input type="hidden" id="systemCashPurchaseKhr" value="{{ $systemCashPurchaseKhr }}">
                        </div>
                    </div>

                    {{-- Expected Cash in Drawer --}}
                    <div class="mb-3 p-3 rounded bg-light border">
                        <div class="small fw-bold text-muted mb-2">លុយក្រៅដែលត្រូវមានក្នុងថត៖</div>
                        
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>ប្រាក់ដុល្លារ ($)៖</span>
                                <strong id="expectedUsd" class="fs-6">$0.00</strong>
                            </div>
                            <div class="text-end text-muted mt-1" style="font-size: 10px;" id="expectedUsdFormula">
                                (តម្កល់ $0.00 + លក់ $0.00 - ចំណាយ $0.00)
                            </div>
                        </div>

                        <div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>ប្រាក់រៀល (៛)៖</span>
                                <strong id="expectedKhr" class="fs-6">0 ៛</strong>
                            </div>
                            <div class="text-end text-muted mt-1" style="font-size: 10px;" id="expectedKhrFormula">
                                (តម្កល់ 0 ៛ + លក់ 0 ៛ - ចំណាយ 0 ៛)
                            </div>
                        </div>
                    </div>

                    {{-- Discrepancy / Over-Short --}}
                    <div id="varianceBox" class="p-3 rounded text-center border shadow-sm">
                        <div class="small fw-bold mb-1" id="varianceTitle">លទ្ធផលផ្ទៀងផ្ទាត់ </div>
                        <div class="fs-5 fw-bold" id="varianceAmount">$0.00</div>
                        <div class="small mt-1" id="varianceSubtext">ស្មើគ្នាត្រឹមត្រូវ</div>
                    </div>
                </div>

                {{-- Quick Notes for Closing --}}
                <div class="mb-2">
                    <label class="form-label small fw-bold text-muted mb-1">កំណត់ចំណាំបន្ថែម (Notes):</label>
                    <textarea id="cashNotes" class="form-control form-control-sm" rows="2" placeholder="បញ្ជាក់មូលហេតុលើស/ខ្វះ ឬចំណាំផ្សេងៗ..." oninput="saveStateToLocal()"></textarea>
                </div>
            </div>
        </div>

    </div>

    {{-- PRINT SLIP AREA (Rendered cleanly when printing) --}}
    <div id="printArea" class="d-none d-print-block">
        {{-- Slip Header --}}
        <div class="text-center pb-2 mb-2 border-bottom" style="border-color: #cbd5e1 !important;">
            <h4 class="fw-bold mb-0" style="font-size: 16px; color: #0f172a; letter-spacing: 0.3px;">Pizza Happy Family</h4>
            <h6 class="fw-bold mb-1" style="font-size: 12.5px; color: #334155;">បង្កាន់ដៃរាប់លុយក្រៅប្រចាំថ្ងៃ </h6>
            <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap text-muted" style="font-size: 10px;">
                <span><i class="far fa-calendar-alt me-1"></i> <strong>កាលបរិច្ឆេទ៖</strong> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span>
                <span><i class="far fa-clock me-1"></i> <strong>ពេលវេលា៖</strong> {{ now()->format('h:i A') }}</span>
                <span><i class="far fa-user me-1"></i> <strong>អ្នករាប់៖</strong> {{ auth()->user()->name }}</span>
                <span><i class="fas fa-exchange-alt me-1"></i> <strong>អត្រាប្តូរប្រាក់៖</strong> 1$ = <span id="printRate">4,000</span> ៛</span>
            </div>
        </div>

        {{-- Denomination Tables Row --}}
        <div class="row g-2 mb-2">
            {{-- KHR Breakdown --}}
            <div class="col-6">
                <div class="fw-bold pb-1 text-primary d-flex justify-content-between align-items-center" style="font-size: 10.5px; border-bottom: 1.5px solid #1e3c72;">
                    <span><i class="fas fa-money-bill-wave me-1"></i> ក្រដាសប្រាក់រៀល (KHR)</span>
                    <span id="printKhrBillsCount" class="badge bg-light text-primary border" style="font-size: 9px;">0 សន្លឹក</span>
                </div>
                <table class="table print-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 45%;">ប្រភេទក្រដាស</th>
                            <th class="text-center" style="width: 25%;">សន្លឹក</th>
                            <th class="text-end" style="width: 30%;">ទឹកប្រាក់ (៛)</th>
                        </tr>
                    </thead>
                    <tbody id="printKhrTable">
                        {{-- Injected via JS --}}
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background: #f1f5f9;">
                            <td colspan="2">សរុបប្រាក់រៀល៖</td>
                            <td class="text-end text-primary" id="printKhrTotal">0 ៛</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- USD Breakdown --}}
            <div class="col-6">
                <div class="fw-bold pb-1 text-success d-flex justify-content-between align-items-center" style="font-size: 10.5px; border-bottom: 1.5px solid #11998e;">
                    <span><i class="fas fa-dollar-sign me-1"></i> ក្រដាសប្រាក់ដុល្លារ (USD)</span>
                    <span id="printUsdBillsCount" class="badge bg-light text-success border" style="font-size: 9px;">0 សន្លឹក</span>
                </div>
                <table class="table print-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 45%;">ប្រភេទក្រដាស</th>
                            <th class="text-center" style="width: 25%;">សន្លឹក</th>
                            <th class="text-end" style="width: 30%;">ទឹកប្រាក់ ($)</th>
                        </tr>
                    </thead>
                    <tbody id="printUsdTable">
                        {{-- Injected via JS --}}
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold" style="background: #f1f5f9;">
                            <td colspan="2">សរុបប្រាក់ដុល្លារ៖</td>
                            <td class="text-end text-success" id="printUsdTotal">$0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Grand Total & Reconciliation Section --}}
        <div class="print-summary-box">
            <div class="row g-2 align-items-center">
                <div class="col-6">
                    <div class="fw-bold text-uppercase mb-1" style="font-size: 10px; color: #475569;">
                        សរុបលុយក្រៅរាប់ជាក់ស្តែង 
                    </div>
                    <div class="fw-bold mb-1" style="font-size: 13.5px; line-height: 1.2;">
                        <span class="text-danger" id="printGrandUsd">$0.00</span>
                        <span class="text-muted mx-1">/</span>
                        <span class="text-primary" id="printGrandKhr">0 ៛</span>
                    </div>
                    <div class="text-muted" style="font-size: 9.5px;">
                    </div>
                </div>

                <div class="col-6 border-start ps-2" style="border-color: #cbd5e1 !important;">
                    <div class="fw-bold text-uppercase mb-1" style="font-size: 10px; color: #475569;">
                        ផ្ទៀងផ្ទាត់ថតលុយក្រៅ 
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 9.5px; margin-bottom: 2px;">
                        <span class="text-muted">លុយតម្កល់ប្រចាំថ្ងៃ៖</span>
                        <strong id="printFloat">$0.00 / 0 ៛</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 9.5px; margin-bottom: 2px;">
                        <span class="text-muted">លុយក្រៅបានពីការលក់៖</span>
                        <strong>${{ number_format($systemCashUsd, 2) }} / {{ number_format($systemCashKhr, 0) }} ៛</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 9.5px; margin-bottom: 2px; color: #b91c1c;">
                        <span class="text-muted">ចំណាយលុយក្រៅ ៖</span>
                        <strong id="printExpenses">-$0.00 / -0 ៛</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-1 border-top" style="font-size: 10px; border-color: #cbd5e1 !important;">
                        <span class="fw-bold">លើស / ខ្វះ ៖</span>
                        <span class="fw-bold" id="printVariance">$0.00</span>
                    </div>
                </div>
            </div>

            {{-- Notes (if any) --}}
            <div id="printNotesArea" class="mt-1 pt-1 border-top text-muted" style="font-size: 9px; display: none; border-color: #e2e8f0 !important;">
                <strong class="text-dark">ចំណាំ (Notes)៖</strong> <span id="printNotesText"></span>
            </div>
        </div>

        {{-- Signatures (3 Columns Side-by-Side: Prepared By, Verified By, Approved By) --}}
        <div class="row print-signatures text-center" style="margin-top: 16px;">
            {{-- Column 1: អ្នកធ្វើរបាយការណ៍ --}}
            <div class="col-4">
                <div class="fw-bold text-dark mb-1" style="font-size: 10.5px;">អ្នកធ្វើរបាយការណ៍</div>
                <div style="height: 35px;"></div>
                <div style="border-bottom: 1px dashed #475569; width: 80%; margin: 0 auto 5px;"></div>
                <div class="fw-semibold text-dark" style="font-size: 10px;">{{ auth()->user()->name }}</div>
                <div class="text-muted" style="font-size: 8.5px;">កាលបរិច្ឆេទ៖ {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</div>
            </div>

            {{-- Column 2: អ្នកត្រួតពិនិត្យ --}}
            <div class="col-4">
                <div class="fw-bold text-dark mb-1" style="font-size: 10.5px;">អ្នកត្រួតពិនិត្យ</div>
                <div style="height: 35px;"></div>
                <div style="border-bottom: 1px dashed #475569; width: 80%; margin: 0 auto 5px;"></div>
                <div class="text-muted" style="font-size: 9.5px;">ឈ្មោះ៖ ...........................</div>
                <div class="text-muted" style="font-size: 8.5px;">កាលបរិច្ឆេទ៖ ____/____/202___</div>
            </div>

            {{-- Column 3: ហត្ថលេខាប្រធាន --}}
            <div class="col-4">
                <div class="fw-bold text-dark mb-1" style="font-size: 10.5px;">ហត្ថលេខាប្រធាន</div>
                <div style="height: 35px;"></div>
                <div style="border-bottom: 1px dashed #475569; width: 80%; margin: 0 auto 5px;"></div>
                <div class="text-muted" style="font-size: 9.5px;">ឈ្មោះ៖ ...........................</div>
                <div class="text-muted" style="font-size: 8.5px;">កាលបរិច្ឆេទ៖ ____/____/202___</div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Format Currency Helper
function formatMoney(amount, decimals = 2) {
    return parseFloat(amount || 0).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function formatKhr(amount) {
    return Math.round(amount || 0).toLocaleString('en-US') + ' ៛';
}

function formatUsd(amount) {
    return '$' + formatMoney(amount, 2);
}

function formatSignedUsd(amount) {
    if (Math.abs(amount) < 0.005) return '$0.00';
    const sign = amount > 0 ? '+' : '-';
    return sign + '$' + formatMoney(Math.abs(amount), 2);
}

function formatSignedKhr(amount) {
    if (Math.abs(amount) < 1) return '0 ៛';
    const sign = amount > 0 ? '+' : '-';
    return sign + Math.round(Math.abs(amount)).toLocaleString('en-US') + ' ៛';
}

// Step quantity button handler for dynamic rows
function stepDynamicQty(button, step) {
    const input = button.closest('.input-group').querySelector('.input-qty');
    if (!input) return;
    let currentVal = parseFloat(input.value) || 0;
    let newVal = Math.max(0, currentVal + step);
    input.value = newVal;
    updateCalculations();
}

// Add dynamic denomination row
function addDynamicRow(currency, denomVal = null, qty = 0) {
    const container = document.getElementById(currency + 'DynamicRows');
    if (!container) return;

    const row = document.createElement('div');
    row.className = 'dynamic-denom-row border-bottom py-1 mb-1';
    row.setAttribute('data-currency', currency);

    let selectOptions = '';
    if (currency === 'khr') {
        const options = [
            { val: '100000', label: '100,000 ៛' },
            { val: '50000', label: '50,000 ៛' },
            { val: '20000', label: '20,000 ៛' },
            { val: '10000', label: '10,000 ៛' },
            { val: '5000', label: '5,000 ៛' },
            { val: '2000', label: '2,000 ៛' },
            { val: '1000', label: '1,000 ៛' },
            { val: '500', label: '500 ៛' },
            { val: '100', label: '100 ៛' },
            { val: 'loose', label: 'កាក់ / សេស (Loose)' }
        ];

        options.forEach(opt => {
            const selected = denomVal === opt.val ? 'selected' : '';
            selectOptions += `<option value="${opt.val}" ${selected}>${opt.label}</option>`;
        });
    } else {
        const options = [
            { val: '100', label: '$100' },
            { val: '50', label: '$50' },
            { val: '20', label: '$20' },
            { val: '10', label: '$10' },
            { val: '5', label: '$5' },
            { val: '2', label: '$2' },
            { val: '1', label: '$1' },
            { val: 'loose', label: 'កាក់សេស (Loose)' }
        ];
        options.forEach(opt => {
            const selected = denomVal === opt.val ? 'selected' : '';
            selectOptions += `<option value="${opt.val}" ${selected}>${opt.label}</option>`;
        });
    }

    row.innerHTML = `
        <div class="row g-2 align-items-center">
            <div class="col-4">
                <select class="form-select form-select-sm select-denom" onchange="updateCalculations()">
                    ${selectOptions}
                </select>
            </div>
            <div class="col-4">
                <div class="input-group input-group-sm">
                    <button type="button" class="btn btn-light border btn-sm" onclick="stepDynamicQty(this, -1)">-</button>
                    <input type="number" class="form-control text-center input-qty" value="${qty}" min="0" step="any" oninput="updateCalculations()" onfocus="this.select()">
                    <button type="button" class="btn btn-light border btn-sm" onclick="stepDynamicQty(this, 1)">+</button>
                </div>
            </div>
            <div class="col-3 text-end fw-bold ${currency === 'khr' ? 'text-primary' : 'text-success'} show-subtotal" style="font-size: 11px; white-space: nowrap;">
                0
            </div>
            <div class="col-1 text-end">
                <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeDynamicRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(row);
    updateCalculations();
}

// Remove dynamic row
function removeDynamicRow(button) {
    const row = button.closest('.dynamic-denom-row');
    if (row) {
        row.remove();
        updateCalculations();
    }
}

// Reset all counts
function resetAllCounts() {
    if (confirm('តើអ្នកពិតជាចង់សម្អាតទិន្នន័យរាប់ទាំងអស់មែនទេ? (Clear all counts?)')) {
        document.getElementById('khrDynamicRows').innerHTML = '';
        document.getElementById('usdDynamicRows').innerHTML = '';
        const notes = document.getElementById('cashNotes');
        if (notes) notes.value = '';
        
        localStorage.removeItem('cash_count_draft_{{ $date }}');
        
        // Add one default empty row for each
        addDynamicRow('khr');
        addDynamicRow('usd');
    }
}

// Main Calculation Function
function updateCalculations() {
    const rate = parseFloat(document.getElementById('exchangeRateInput').value) || 4000;
    
    let totalKhr = 0;
    let totalKhrBills = 0;
    let totalUsd = 0;
    let totalUsdBills = 0;

    let khrBreakdown = [];
    let usdBreakdown = [];

    // Calculate KHR Dynamic Rows
    document.querySelectorAll('#khrDynamicRows .dynamic-denom-row').forEach(row => {
        const select = row.querySelector('.select-denom');
        const input = row.querySelector('.input-qty');
        const subtotalEl = row.querySelector('.show-subtotal');
        
        const val = select.value;
        const qty = parseFloat(input.value) || 0;
        
        let subtotal = 0;
        if (val === 'loose') {
            subtotal = qty;
            if (qty > 0) {
                khrBreakdown.push({ val: 'Loose', qty: '-', subtotal: subtotal });
            }
        } else {
            const denomVal = parseInt(val) || 0;
            subtotal = denomVal * qty;
            totalKhrBills += qty;
            if (qty > 0) {
                khrBreakdown.push({ val: denomVal, qty: qty, subtotal: subtotal });
            }
        }
        
        totalKhr += subtotal;
        if (subtotalEl) {
            subtotalEl.innerText = formatKhr(subtotal);
        }
    });

    // Calculate USD Dynamic Rows
    document.querySelectorAll('#usdDynamicRows .dynamic-denom-row').forEach(row => {
        const select = row.querySelector('.select-denom');
        const input = row.querySelector('.input-qty');
        const subtotalEl = row.querySelector('.show-subtotal');
        
        const val = select.value;
        const qty = parseFloat(input.value) || 0;
        
        let subtotal = 0;
        if (val === 'loose') {
            subtotal = qty;
            if (qty > 0) {
                usdBreakdown.push({ val: 'Loose', qty: '-', subtotal: subtotal });
            }
        } else {
            const denomVal = parseInt(val) || 0;
            subtotal = denomVal * qty;
            totalUsdBills += qty;
            if (qty > 0) {
                usdBreakdown.push({ val: denomVal, qty: qty, subtotal: subtotal });
            }
        }
        
        totalUsd += subtotal;
        if (subtotalEl) {
            subtotalEl.innerText = formatUsd(subtotal);
        }
    });

    // Update KHR Section Summaries
    document.getElementById('khrHeaderTotal').innerText = formatKhr(totalKhr);
    document.getElementById('khrHeaderBillsCount').innerText = totalKhrBills + ' សន្លឹក';
    document.getElementById('khrFooterTotal').innerText = formatKhr(totalKhr);
    document.getElementById('khrAsUsd').innerText = '≈ ' + formatUsd(totalKhr / rate);

    // Update USD Section Summaries
    document.getElementById('usdHeaderTotal').innerText = formatUsd(totalUsd);
    document.getElementById('usdHeaderBillsCount').innerText = totalUsdBills + ' សន្លឹក';
    document.getElementById('usdFooterTotal').innerText = formatUsd(totalUsd);
    document.getElementById('usdAsKhr').innerText = '≈ ' + formatKhr(totalUsd * rate);

    // Combined Grand Totals
    const grandUsd = totalUsd + (totalKhr / rate);
    const grandKhr = totalKhr + (totalUsd * rate);
    const totalBills = totalKhrBills + totalUsdBills;

    document.getElementById('grandTotalUsd').innerText = formatUsd(grandUsd);
    document.getElementById('grandTotalKhr').innerText = formatKhr(grandKhr);
    document.getElementById('summaryCountedKhr').innerText = formatKhr(totalKhr);
    document.getElementById('summaryCountedUsd').innerText = formatUsd(totalUsd);
    document.getElementById('summaryTotalBills').innerText = totalBills + ' សន្លឹក';

    // Drawer Reconciliation
    const floatUsd = parseFloat(document.getElementById('floatUsd').value) || 0;
    const floatKhr = parseFloat(document.getElementById('floatKhr').value) || 0;
    const systemCashUsd = parseFloat(document.getElementById('systemCashUsd').value) || 0;
    const systemCashKhr = parseFloat(document.getElementById('systemCashKhr').value) || 0;
    const systemCashPurchaseUsd = parseFloat(document.getElementById('systemCashPurchaseUsd').value) || 0;
    const systemCashPurchaseKhr = parseFloat(document.getElementById('systemCashPurchaseKhr').value) || 0;

    const expectedUsd = floatUsd + systemCashUsd - systemCashPurchaseUsd;
    const expectedKhr = floatKhr + systemCashKhr - systemCashPurchaseKhr;
    const expectedCombinedUsd = expectedUsd + (expectedKhr / rate);

    document.getElementById('expectedUsd').innerText = formatUsd(expectedUsd);
    document.getElementById('expectedUsdFormula').innerText = '(តម្កល់ $' + formatMoney(floatUsd, 2) + ' + លក់ $' + formatMoney(systemCashUsd, 2) + ' - ចំណាយ $' + formatMoney(systemCashPurchaseUsd, 2) + ')';
    document.getElementById('expectedKhr').innerText = formatKhr(expectedKhr);
    document.getElementById('expectedKhrFormula').innerText = '(តម្កល់ ' + formatMoney(floatKhr, 0) + ' ៛ + លក់ ' + formatMoney(systemCashKhr, 0) + ' ៛ - ចំណាយ ' + formatMoney(systemCashPurchaseKhr, 0) + ' ៛)';

    // Variance = Actual Counted Combined USD - Expected Combined USD
    const varianceUsd = grandUsd - expectedCombinedUsd;
    const varianceKhr = varianceUsd * rate;

    const varianceBox = document.getElementById('varianceBox');
    const varianceTitle = document.getElementById('varianceTitle');
    const varianceAmount = document.getElementById('varianceAmount');
    const varianceSubtext = document.getElementById('varianceSubtext');

    if (Math.abs(varianceUsd) < 0.05) {
        varianceBox.className = 'p-3 rounded text-center border bg-success-subtle border-success text-success';
        varianceTitle.innerText = 'ផ្ទៀងផ្ទាត់៖ ស្មើគ្នាត្រឹមត្រូវ (Balanced)';
        varianceAmount.innerText = '$0.00';
        varianceSubtext.innerText = 'សាច់ប្រាក់ក្នុងថតគ្រប់ចំនួន 100%';
    } else if (varianceUsd > 0) {
        varianceBox.className = 'p-3 rounded text-center border bg-info-subtle border-info text-info-emphasis';
        varianceTitle.innerText = 'ផ្ទៀងផ្ទាត់៖ លើសសាច់ប្រាក់ (Over)';
        varianceAmount.innerText = formatSignedUsd(varianceUsd) + ' (' + formatSignedKhr(varianceKhr) + ')';
        varianceSubtext.innerText = 'សាច់ប្រាក់ក្នុងថតលើសប្រព័ន្ធលក់';
    } else {
        varianceBox.className = 'p-3 rounded text-center border bg-danger-subtle border-danger text-danger';
        varianceTitle.innerText = 'ផ្ទៀងផ្ទាត់៖ ខ្វះសាច់ប្រាក់ (Short)';
        varianceAmount.innerText = formatSignedUsd(varianceUsd) + ' (' + formatSignedKhr(varianceKhr) + ')';
        varianceSubtext.innerText = 'សាច់ប្រាក់ក្នុងថតខ្វះមិនគ្រប់ចំនួន!';
    }

    // Populate Print Slip Elements
    populatePrintSlip(khrBreakdown, usdBreakdown, totalKhr, totalKhrBills, totalUsd, totalUsdBills, grandUsd, grandKhr, totalBills, floatUsd, floatKhr, varianceUsd, rate, systemCashPurchaseUsd, systemCashPurchaseKhr);

    // Save to LocalStorage
    saveStateToLocal();
}

// Populate Print Slip Table
function populatePrintSlip(khrBreakdown, usdBreakdown, totalKhr, totalKhrBills, totalUsd, totalUsdBills, grandUsd, grandKhr, totalBills, floatUsd, floatKhr, varianceUsd, rate, systemCashPurchaseUsd, systemCashPurchaseKhr) {
    document.getElementById('printRate').innerText = formatMoney(rate, 0);
    document.getElementById('printKhrTotal').innerText = formatKhr(totalKhr);
    document.getElementById('printKhrBillsCount').innerText = totalKhrBills + ' សន្លឹក';
    document.getElementById('printUsdTotal').innerText = formatUsd(totalUsd);
    document.getElementById('printUsdBillsCount').innerText = totalUsdBills + ' សន្លឹក';
    document.getElementById('printGrandUsd').innerText = formatUsd(grandUsd);
    document.getElementById('printGrandKhr').innerText = formatKhr(grandKhr);
    document.getElementById('printFloat').innerText = '$' + formatMoney(floatUsd, 2) + ' / ' + formatMoney(floatKhr, 0) + ' ៛';
    document.getElementById('printExpenses').innerText = '-$' + formatMoney(systemCashPurchaseUsd, 2) + ' / -' + formatMoney(systemCashPurchaseKhr, 0) + ' ៛';

    const varianceKhr = varianceUsd * rate;
    const varianceEl = document.getElementById('printVariance');
    if (Math.abs(varianceUsd) < 0.05) {
        varianceEl.className = 'fw-bold text-success';
        varianceEl.innerText = '$0.00 (Balanced)';
    } else if (varianceUsd > 0) {
        varianceEl.className = 'fw-bold text-info-emphasis';
        varianceEl.innerText = formatSignedUsd(varianceUsd) + ' (' + formatSignedKhr(varianceKhr) + ') លើស';
    } else {
        varianceEl.className = 'fw-bold text-danger';
        varianceEl.innerText = formatSignedUsd(varianceUsd) + ' (' + formatSignedKhr(varianceKhr) + ') ខ្វះ';
    }

    const notes = document.getElementById('cashNotes').value;
    const printNotesArea = document.getElementById('printNotesArea');
    if (notes && notes.trim() !== '') {
        printNotesArea.style.display = 'block';
        document.getElementById('printNotesText').innerText = notes;
    } else {
        printNotesArea.style.display = 'none';
    }

    // KHR Table
    let khrHtml = '';
    khrBreakdown.forEach(item => {
        khrHtml += `<tr>
            <td>${item.val === 'Loose' ? 'កាក់ / សេស' : formatMoney(item.val, 0) + ' ៛'}</td>
            <td class="text-center">${item.qty}</td>
            <td class="text-end">${formatKhr(item.subtotal)}</td>
        </tr>`;
    });
    if (khrBreakdown.length === 0) {
        khrHtml = '<tr><td colspan="3" class="text-center text-muted py-1" style="font-size:9.5px;">គ្មានទិន្នន័យ</td></tr>';
    }
    document.getElementById('printKhrTable').innerHTML = khrHtml;

    // USD Table
    let usdHtml = '';
    usdBreakdown.forEach(item => {
        usdHtml += `<tr>
            <td>${item.val === 'Loose' ? 'កាក់សេស' : '$' + item.val}</td>
            <td class="text-center">${item.qty}</td>
            <td class="text-end">${formatUsd(item.subtotal)}</td>
        </tr>`;
    });
    if (usdBreakdown.length === 0) {
        usdHtml = '<tr><td colspan="3" class="text-center text-muted py-1" style="font-size:9.5px;">គ្មានទិន្នន័យ</td></tr>';
    }
    document.getElementById('printUsdTable').innerHTML = usdHtml;
}

// Save Current State to LocalStorage
function saveStateToLocal() {
    const data = {
        rate: document.getElementById('exchangeRateInput').value,
        floatUsd: document.getElementById('floatUsd').value,
        floatKhr: document.getElementById('floatKhr').value,
        notes: document.getElementById('cashNotes').value,
        khrRows: [],
        usdRows: []
    };

    document.querySelectorAll('#khrDynamicRows .dynamic-denom-row').forEach(row => {
        const select = row.querySelector('.select-denom');
        const input = row.querySelector('.input-qty');
        data.khrRows.push({ val: select.value, qty: input.value });
    });

    document.querySelectorAll('#usdDynamicRows .dynamic-denom-row').forEach(row => {
        const select = row.querySelector('.select-denom');
        const input = row.querySelector('.input-qty');
        data.usdRows.push({ val: select.value, qty: input.value });
    });

    localStorage.setItem('cash_count_draft_{{ $date }}', JSON.stringify(data));
}

// Restore State from LocalStorage
function restoreStateFromLocal() {
    const raw = localStorage.getItem('cash_count_draft_{{ $date }}');
    if (!raw) {
        // By default, add one KHR and one USD row for initial use
        addDynamicRow('khr');
        addDynamicRow('usd');
        return;
    }

    try {
        const data = JSON.parse(raw);
        if (data.rate) document.getElementById('exchangeRateInput').value = data.rate;
        if (data.floatUsd) document.getElementById('floatUsd').value = data.floatUsd;
        if (data.floatKhr) document.getElementById('floatKhr').value = data.floatKhr;
        if (data.notes) document.getElementById('cashNotes').value = data.notes;

        // Clear containers
        document.getElementById('khrDynamicRows').innerHTML = '';
        document.getElementById('usdDynamicRows').innerHTML = '';

        if (data.khrRows && data.khrRows.length > 0) {
            data.khrRows.forEach(row => {
                addDynamicRow('khr', row.val, row.qty);
            });
        } else {
            addDynamicRow('khr');
        }

        if (data.usdRows && data.usdRows.length > 0) {
            data.usdRows.forEach(row => {
                addDynamicRow('usd', row.val, row.qty);
            });
        } else {
            addDynamicRow('usd');
        }
    } catch (e) {
        console.error('Failed to restore draft', e);
        addDynamicRow('khr');
        addDynamicRow('usd');
    }

    updateCalculations();
}

// Copy Formatted Summary for Telegram/Chat
function copyCashSummary() {
    const rate = parseFloat(document.getElementById('exchangeRateInput').value) || 4000;
    const khrTotal = document.getElementById('khrHeaderTotal').innerText;
    const usdTotal = document.getElementById('usdHeaderTotal').innerText;
    const grandUsd = document.getElementById('grandTotalUsd').innerText;
    const grandKhr = document.getElementById('grandTotalKhr').innerText;
    const variance = document.getElementById('varianceAmount').innerText;
    const varianceTitle = document.getElementById('varianceTitle').innerText;
    const notes = document.getElementById('cashNotes').value;

    const floatUsd = parseFloat(document.getElementById('floatUsd').value) || 0;
    const floatKhr = parseFloat(document.getElementById('floatKhr').value) || 0;
    const systemCashUsd = parseFloat(document.getElementById('systemCashUsd').value) || 0;
    const systemCashKhr = parseFloat(document.getElementById('systemCashKhr').value) || 0;
    const systemCashPurchaseUsd = parseFloat(document.getElementById('systemCashPurchaseUsd').value) || 0;
    const systemCashPurchaseKhr = parseFloat(document.getElementById('systemCashPurchaseKhr').value) || 0;

    let text = `🍕 *PIZZA HAPPY FAMILY - របាយការណ៍រាប់លុយក្រៅ*\n`;
    text += `📅 កាលបរិច្ឆេទ៖ {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}\n`;
    text += `👤 អ្នករាប់៖ {{ auth()->user()->name }}\n`;
    text += `💱 អត្រាប្តូរប្រាក់៖ 1$ = ${formatMoney(rate, 0)} ៛\n`;
    text += `------------------------------------\n`;
    text += `💵 *សរុបប្រាក់រៀល (KHR)*៖ ${khrTotal}\n`;
    text += `💵 *សរុបប្រាក់ដុល្លារ (USD)*៖ ${usdTotal}\n`;
    text += `⭐ *សរុបលុយក្រៅរាប់ជាក់ស្តែង*៖ ${grandUsd} (${grandKhr})\n`;
    text += `------------------------------------\n`;
    text += `🏪 *លុយតម្កល់ប្រចាំថ្ងៃ (Float)*៖ $${formatMoney(floatUsd, 2)} / ${formatMoney(floatKhr, 0)} ៛\n`;
    text += `🧾 *លុយក្រៅបានពីការលក់ (Cash Sales)*៖ $${formatMoney(systemCashUsd, 2)} / ${formatMoney(systemCashKhr, 0)} ៛\n`;
    if (systemCashPurchaseUsd > 0 || systemCashPurchaseKhr > 0) {
        text += `💸 *ចំណាយលុយក្រៅ (Cash Expenses)*៖ -$${formatMoney(systemCashPurchaseUsd, 2)} / -${formatMoney(systemCashPurchaseKhr, 0)} ៛\n`;
    }
    text += `📊 *ផ្ទៀងផ្ទាត់ថតលុយក្រៅ*៖ ${varianceTitle}\n`;
    text += `⚖️ *លើស/ខ្វះ (Variance)*៖ ${variance}\n`;
    if (notes && notes.trim() !== '') {
        text += `📝 *ចំណាំ*៖ ${notes.trim()}\n`;
    }
    text += `------------------------------------`;

    navigator.clipboard.writeText(text).then(() => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'បានចម្លងជោគជ័យ!',
                text: 'ទិន្នន័យត្រូវបានចម្លងទៅ Clipboard រួចរាល់សម្រាប់ផ្ញើក្នុង Telegram ឬ Chat។',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert('បានចម្លងជោគជ័យ (Copied to clipboard)!');
        }
    }).catch(err => {
        alert('មិនអាចចម្លងបាន៖ ' + err);
    });
}

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    restoreStateFromLocal();
});
</script>
@endpush