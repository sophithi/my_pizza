@extends('layouts.app')

@section('title', 'រៀបចំទំនិញ')

@push('styles')
    <style>
        .packing-new-badge {
            align-items: center;
            background: #dcfce7;
            border: 1px solid #86efac;
            border-radius: 999px;
            color: #047857;
            display: inline-flex;
            font-size: 11px;
            font-weight: 800;
            gap: 5px;
            margin-left: 8px;
            padding: 3px 8px;
            vertical-align: middle;
        }

        .packing-edited-badge {
            align-items: center;
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 999px;
            color: #b45309;
            display: inline-flex;
            font-size: 11px;
            font-weight: 800;
            gap: 5px;
            margin-left: 8px;
            padding: 3px 8px;
            vertical-align: middle;
        }

        .packing-sent-time {
            color: #6b7280;
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-top: 3px;
        }

        .packing-complete-btn,
        .packing-complete-done {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            gap: 7px;
            min-height: 32px;
            padding: 6px 12px;
            white-space: nowrap;
        }

        .packing-complete-btn {
            background: #fff;
            border: 1px solid #d1d5db;
            color: #374151;
            cursor: pointer;
        }

        .packing-complete-btn:hover {
            background: #ecfdf5;
            border-color: #86efac;
            color: #047857;
        }

        .packing-complete-done {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #047857;
        }

        .packing-row-completed {
            background: #f9fafb;
            opacity: .78;
        }

        .packing-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .packing-filter {
            align-items: center;
            background: #fff;
            border: 1.5px solid #e9ecef;
            border-radius: 8px;
            color: #6c757d;
            display: inline-flex;
            font-size: 13px;
            font-weight: 600;
            gap: 7px;
            padding: 8px 16px;
            text-decoration: none;
            transition: all .2s;
        }

        .packing-filter.active {
            background: #e85d24;
            border-color: #e85d24;
            color: #fff;
        }

        .packing-date-filter {
            align-items: center;
            display: flex;
            gap: 8px;
            margin-left: auto;
        }

        .packing-date-filter input[type="date"] {
            border: 1.5px solid #e9ecef;
            border-radius: 8px;
            color: #1a1d29;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 14px;
        }

        .packing-date-filter button {
            background: #e85d24;
            border: none;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 14px;
        }

        .packing-table th {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
            padding: 14px 16px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .packing-table td {
            padding: 14px 16px;
            vertical-align: middle;
        }

        .packing-table tr:hover td {
            background: #fbfdff;
        }

        .packing-action-btn {
            align-items: center;
            border: none;
            border-radius: 6px;
            color: #fff;
            display: inline-flex;
            font-size: 13px;
            font-weight: 600;
            justify-content: center;
            margin-right: 6px;
            padding: 6px 12px;
            text-decoration: none;
        }

        .packing-action-btn.dark {
            background: #1a1d29;
        }

        .packing-action-btn.dark:hover {
            background: #111827;
            color: #fff;
        }

        .packing-action-btn.primary {
            background: #e85d24;
        }

        .packing-action-btn.primary:hover {
            background: #d94a10;
            color: #fff;
        }

        .pager-wrap {
            margin-top: 16px;
            display: flex;
            justify-content: center;
        }

        .pager-wrap .pagination {
            margin-bottom: 0;
            flex-wrap: wrap;
            row-gap: 6px;
            column-gap: 4px;
        }

        .pager-wrap .page-link {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            color: #e85d24;
            padding: 6px 12px;
            font-size: 13px;
        }

        .pager-wrap .page-link:hover,
        .pager-wrap .page-link:focus {
            background: #fef1eb;
            border-color: #e85d24;
            color: #e85d24;
            box-shadow: none;
        }

        .pager-wrap .page-item.active .page-link {
            background: #e85d24;
            border-color: #e85d24;
            color: #fff;
        }

        .pager-wrap .page-item.disabled .page-link {
            color: #cbd5e1;
            background: #f8fafc;
            border-color: #e5e7eb;
        }

        @media (max-width: 576px) {
            .pager-wrap .page-link {
                padding: 5px 9px;
                font-size: 12px;
            }
        }

        @media (max-width: 768px) {

            .packing-filters,
            .packing-date-filter {
                width: 100%;
            }

            .packing-date-filter {
                margin-left: 0;
            }

            .packing-filter {
                flex: 1;
                justify-content: center;
            }
        }

        /* Force this modal/backdrop above everything on the page (sidebar,
           dropdowns, etc.) so nothing can end up stacked on top of it and
           silently eat clicks. */
        #stickerModal {
            z-index: 1075 !important;
        }

        #stickerModal ~ .modal-backdrop,
        .modal-backdrop {
            z-index: 1070 !important;
        }

        /* ===== STICKER MODAL DESIGN ===== */
        .sticker-modal-content {
            height: 85vh;
            border-radius: 14px;
            overflow: hidden;
            border: none;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .28);
        }

        .sticker-modal-header {
            align-items: center;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 18px;
        }

        .sticker-modal-title-wrap {
            align-items: center;
            display: flex;
            gap: 10px;
            min-width: 0;
        }

        .sticker-modal-title-wrap i {
            color: #e85d24;
            font-size: 15px;
            flex-shrink: 0;
        }

        .sticker-modal-header .modal-title {
            color: #1a1d29;
            font-size: 14px;
            font-weight: 800;
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sticker-modal-actions {
            align-items: center;
            display: flex;
            flex-shrink: 0;
            gap: 8px;
        }

        .sticker-modal-btn {
            align-items: center;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            color: #374151;
            cursor: pointer;
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            gap: 6px;
            height: 36px;
            justify-content: center;
            padding: 0 14px;
            text-decoration: none;
            transition: all .15s ease;
        }

        .sticker-modal-btn:hover {
            background: #f4f5f7;
            border-color: #d1d5db;
            color: #1a1d29;
        }

        .sticker-modal-btn.primary {
            background: linear-gradient(135deg, #e85d24, #d94a10);
            border-color: transparent;
            color: #fff;
        }

        .sticker-modal-btn.primary:hover {
            box-shadow: 0 6px 14px rgba(232, 93, 36, .28);
            color: #fff;
        }

        .sticker-modal-close {
            align-items: center;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 50%;
            color: #6b7280;
            cursor: pointer;
            display: inline-flex;
            font-size: 14px;
            height: 36px;
            justify-content: center;
            transition: all .15s ease;
            width: 36px;
        }

        .sticker-modal-close:hover {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }

        .sticker-modal-body {
            background: #eef1f5;
            overflow: hidden;
            position: relative;
        }

        .sticker-modal-iframe {
            border: 0;
            display: block;
            height: 100%;
            opacity: 0;
            transition: opacity .2s ease;
            width: 100%;
        }

        .sticker-modal-iframe.loaded {
            opacity: 1;
        }

        .sticker-modal-loading {
            align-items: center;
            background: #eef1f5;
            display: flex;
            inset: 0;
            justify-content: center;
            position: absolute;
            z-index: 1;
        }

        .sticker-modal-spinner {
            animation: sticker-modal-spin .7s linear infinite;
            border: 3px solid #dbe0e6;
            border-radius: 50%;
            border-top-color: #e85d24;
            height: 34px;
            width: 34px;
        }

        @keyframes sticker-modal-spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm" style="border-radius: 10px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <h2 style="font-size:28px;font-weight:600;color:#333;margin:0;">រៀបចំទំនិញ</h2>
                </div>
            </div>
        </div>
        <!-- Date Filter -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body" style="padding: 16px 24px;">
                <form method="GET" action="{{ route('packing.index') }}" id="packingFilter"
                    style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">

                    <input type="hidden" name="period" id="periodInput" value="{{ request('period') }}">

                    <div class="packing-filters">
                        <a href="{{ route('packing.index', ['period' => 'today']) }}"
                            class="packing-filter {{ request('period') === 'today' ? 'active' : '' }}">
                            <i class="fas fa-calendar-day"></i> ថ្ងៃនេះ
                        </a>
                        <a href="{{ route('packing.index', ['period' => 'yesterday']) }}"
                            class="packing-filter {{ request('period') === 'yesterday' ? 'active' : '' }}">
                            <i class="fas fa-calendar-minus"></i> ម្សិលមិញ
                        </a>
                        <a href="{{ route('packing.index', ['period' => 'month']) }}"
                            class="packing-filter {{ request('period') === 'month' ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i> ខែនេះ
                        </a>
                    </div>

                    <div class="packing-date-filter">
                        <input type="date" name="date" value="{{ request('date') }}" placeholder="សូមជ្រើសរើសថ្ងៃខែ">
                        <button type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:12px;">
                    <div class="card-body" style="padding:24px;">
                        @if($invoices->count())
                            <div class="table-responsive">
                                <table class="table packing-table">
                                    <thead>
                                        <tr>
                                            <th>វិក័្កយបត្រ</th>
                                            <th>ឈ្មោះអតិថិជន</th>
                                            <th>កាលបរិច្ឆេទ</th>
                                            <th>ផ្ញើរមក</th>
                                            <th>រៀបចំរួច</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $invoice)
                                            <tr class="{{ $invoice->packing_completed_at ? 'packing-row-completed' : '' }}">
                                                <td>
                                                    {{ $invoice->invoice_number }}
                                                    @php
                                                        // A resend after an order edit: packing_first_sent_at is set once
                                                        // and never touched again, so once it stops matching the current
                                                        // packing_sent_at we know this row isn't the invoice's first trip
                                                        // through packing.
                                                        $isEditedResend = $invoice->packing_first_sent_at
                                                            && $invoice->packing_sent_at
                                                            && !$invoice->packing_first_sent_at->equalTo($invoice->packing_sent_at);
                                                    @endphp
                                                    @if(!$invoice->packing_completed_at && $isEditedResend)
                                                        <span class="packing-edited-badge">
                                                            <i class="fas fa-pen"></i> បានកែវិក្ក័យបត្រ
                                                        </span>
                                                    @elseif(!$invoice->packing_completed_at && $invoice->packing_sent_at && $invoice->packing_sent_at->gt(now()->subMinutes(30)))
                                                        <span class="packing-new-badge">
                                                            <i class="fas fa-circle"></i> ថ្មី
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $invoice->order?->customer?->name ?? 'N/A' }}</td>
                                                <td>{{ $invoice->invoice_date->translatedFormat('M d, Y') }}</td>
                                                <td>
                                                    {{ $invoice->packing_sent_at?->setTimezone('Asia/Phnom_Penh')->format('h:i A') ?? 'N/A' }}
                                                    <span
                                                        class="packing-sent-time">{{ $invoice->packing_sent_at?->diffForHumans() }}</span>
                                                </td>
                                                <td>
                                                    @if($invoice->packing_completed_at)
                                                        <span class="packing-complete-done">
                                                            <i class="fas fa-check-circle"></i> រួចរាល់
                                                        </span>
                                                        <span class="packing-sent-time">
                                                            {{ $invoice->packing_completed_at->setTimezone('Asia/Phnom_Penh')->format('h:i A') }}
                                                        </span>
                                                    @else
                                                        <form method="POST" action="{{ route('packing.complete', $invoice) }}"
                                                            class="m-0">
                                                            @csrf
                                                            <button type="submit" class="packing-complete-btn">
                                                                <i class="far fa-square"></i> បញ្ជាក់
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown d-inline-block">
                                                        <button type="button" class="packing-action-btn dark dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            បិតលើកេស
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item sticker-modal-link" href="{{ route('packing.delivery_pizza', $invoice) }}" data-title="បិតលើកេស — {{ $invoice->invoice_number }} — ភីហ្សា គ្រួសាររីករាយ">
                                                                    ភីហ្សា គ្រួសាររីករាយ
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item sticker-modal-link" href="{{ route('packing.delivery_mayo', $invoice) }}" data-title="បិតលើកេស — {{ $invoice->invoice_number }} — ម៉ាយូនេស បន្ទាយឆ្មា">
                                                                    ម៉ាយូនេស បន្ទាយឆ្មា
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item sticker-modal-link" href="{{ route('packing.delivery_tamon', $invoice) }}" data-title="បិតលើកេស — {{ $invoice->invoice_number }} — ម៉ាយូនេស តាមាន់មានជ័យ">
                                                                    ម៉ាយូនេស តាមាន់មានជ័យ
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <a href="{{ route('packing.prep', $invoice) }}" class="packing-action-btn dark sticker-modal-link" data-title="រៀបចំទំនិញ — {{ $invoice->invoice_number }}">
                                                        រៀបចំទំនិញ
                                                    </a>
                                                    <div class="dropdown d-inline-block">
                                                        <button type="button" class="packing-action-btn primary dropdown-toggle"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            វិក្ក័យបត្រភ្ញៀវ
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item sticker-modal-link" href="{{ route('packing.customer', $invoice) }}" data-title="វិក្ក័យបត្រភ្ញៀវ — {{ $invoice->invoice_number }} — ភីហ្សា គ្រួសាររីករាយ">
                                                                    ភីហ្សា គ្រួសាររីករាយ
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item sticker-modal-link" href="{{ route('packing.customer_mayo', $invoice) }}" data-title="វិក្ក័យបត្រភ្ញៀវ — {{ $invoice->invoice_number }} — ម៉ាយូនេស បន្ទាយឆ្មា">
                                                                    ម៉ាយូនេស បន្ទាយឆ្មា
                                                                </a>
                                                            </li>
                                                               <li>
                                                                <a class="dropdown-item sticker-modal-link" href="{{ route('packing.customer_tamon', $invoice) }}" data-title="វិក្ក័យបត្រភ្ញៀវ — {{ $invoice->invoice_number }} — តាមាន់មានជ័យ">
                                                                    តាមាន់មានជ័យ
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="pager-wrap">
                                {{ $invoices->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <p style="color:#666;">មិនទាន់មានវិក្ក័យបត្រ.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('modals')
  
    <div class="modal fade" id="stickerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content sticker-modal-content">
                <div class="modal-header sticker-modal-header">
                    <div class="sticker-modal-title-wrap">
                        <i class="fas fa-receipt"></i>
                        <h6 class="modal-title" id="stickerModalTitle"></h6>
                    </div>
                    <div class="sticker-modal-actions">
                        <!-- <button type="button" class="sticker-modal-btn primary" id="stickerPrintBtn">
                            <i class="fas fa-print"></i> បោះពុម្ព
                        </button> -->
                        <a href="#" target="_blank" id="stickerOpenNewTab" class="sticker-modal-btn" title="បើកទំព័រថ្មី">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                        <!-- <button type="button" class="sticker-modal-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-xmark"></i>
                        </button> -->
                    </div>
                </div>
                <div class="modal-body p-0 sticker-modal-body">
                    <div class="sticker-modal-loading" id="stickerModalLoading">
                        <div class="sticker-modal-spinner"></div>
                    </div>
                    <iframe id="stickerModalIframe" src="about:blank" class="sticker-modal-iframe"></iframe>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        (() => {
            const refreshKey = 'packingRefresh';
            const seenKey = 'packingRefreshSeenAt';

            function isStickerModalOpen() {
                return document.getElementById('stickerModal')?.classList.contains('show') ?? false;
            }

            function refreshPackingPage(payload) {
                if (!payload?.time || sessionStorage.getItem(seenKey) === String(payload.time)) {
                    return;
                }

                if (Date.now() - payload.time > 120000) {
                    return;
                }

                if (isStickerModalOpen()) {
                    return;
                }

                sessionStorage.setItem(seenKey, String(payload.time));
                const targetUrl = payload.url || @json(route('packing.index', ['period' => 'today']));

                if (window.location.href === targetUrl) {
                    window.location.reload();
                    return;
                }

                window.location.href = targetUrl;
            }

            function readRefreshSignal() {
                try {
                    return JSON.parse(localStorage.getItem(refreshKey) || 'null');
                } catch (error) {
                    return null;
                }
            }

            window.addEventListener('storage', (event) => {
                if (event.key === refreshKey) {
                    refreshPackingPage(readRefreshSignal());
                }
            });

            refreshPackingPage(readRefreshSignal());

            setInterval(() => {
                if (document.hidden) {
                    return;
                }

                if (isStickerModalOpen()) {
                    return;
                }

                const activeTag = document.activeElement?.tagName;
                if (activeTag === 'INPUT' || activeTag === 'SELECT' || activeTag === 'TEXTAREA') {
                    return;
                }

                window.location.reload();
            }, 60000);

            // ===== STICKER MODAL (opens sticker/prep pages in-page instead of a new tab) =====
            const stickerModalEl = document.getElementById('stickerModal');
            const stickerModal = stickerModalEl ? bootstrap.Modal.getOrCreateInstance(stickerModalEl) : null;
            const stickerIframe = document.getElementById('stickerModalIframe');
            const stickerLoading = document.getElementById('stickerModalLoading');
            const stickerTitle = document.getElementById('stickerModalTitle');
            const stickerOpenNewTab = document.getElementById('stickerOpenNewTab');

            stickerIframe?.addEventListener('load', () => {
                stickerLoading.style.display = 'none';
                stickerIframe.classList.add('loaded');
            });

            function loadSticker(url, title) {
                stickerIframe.classList.remove('loaded');
                stickerLoading.style.display = 'flex';
                stickerIframe.src = url;
                if (title) {
                    stickerTitle.textContent = title;
                }
                stickerOpenNewTab.href = url;
            }

            document.querySelectorAll('.sticker-modal-link').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    e.stopPropagation();

                    // Defensive cleanup: a leftover backdrop/body-lock from a
                    // dropdown or a previous open that didn't tear down
                    // cleanly is the classic cause of a modal that shows but
                    // eats every click. Clear that state before opening.
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.removeProperty('overflow');
                    document.body.style.removeProperty('padding-right');

                    loadSticker(link.getAttribute('href'), link.dataset.title || link.textContent.trim());
                    stickerModal?.show();
                });
            });

            document.getElementById('stickerPrintBtn')?.addEventListener('click', () => {
                stickerIframe.contentWindow?.focus();
                stickerIframe.contentWindow?.print();
            });

            stickerModalEl?.addEventListener('hidden.bs.modal', () => {
                stickerIframe.src = 'about:blank';
                stickerIframe.classList.remove('loaded');
                stickerLoading.style.display = 'flex';
            });

            // Sticker pages (brand-switch chips, "back" links) run inside the
            // iframe and can't reach the modal directly, so they postMessage
            // instead of navigating themselves — keeps everything inside the
            // popup rather than breaking out to a bare page in the iframe.
            window.addEventListener('message', event => {
                if (event.source !== stickerIframe.contentWindow || !event.data || event.data.source !== 'sticker-page') {
                    return;
                }

                if (event.data.action === 'navigate' && event.data.url) {
                    loadSticker(event.data.url, event.data.title);
                } else if (event.data.action === 'close') {
                    stickerModal?.hide();
                }
            });
        })();
    </script>
@endpush
