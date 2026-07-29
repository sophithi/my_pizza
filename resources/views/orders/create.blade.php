@extends('layouts.app')

@section('title', 'ចេញវិក័្កយបត្រជូនអតិថិជន')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    :root {
        --accent: #e85d24;
        --bg: #f4f5f7;
        --surface: #ffffff;
        --border: #e9ecef;
        --text: #1a1d29;
        --text-muted: #6c757d;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
    }

    body { background: var(--bg); }

    .page-header {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .product-section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .product-section-title i {
        color: var(--accent);
        font-size: 20px;
    }

    .product-tools {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        margin-bottom: 14px;
    }

    .product-search {
        position: relative;
    }

    .product-search i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 13px;
        pointer-events: none;
    }

    .product-search input {
        padding-left: 38px;
    }

    .product-count {
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(128px, 1fr));
        gap: 12px;
        max-height: 560px;
        overflow-y: auto;
        padding: 14px;
        background: var(--surface);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid var(--border);
    }

    .product-card {
        background: var(--surface);
        border: 2px solid var(--border);
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        min-height: 184px;
    }

    .product-card:hover {
        border-color: var(--accent);
        box-shadow: 0 6px 14px rgba(232, 93, 36, 0.16);
        transform: translateY(-2px);
    }

    .product-card.selected {
        border-color: var(--accent);
        background: linear-gradient(135deg, rgba(232, 93, 36, 0.05) 0%, rgba(232, 93, 36, 0.02) 100%);
        box-shadow: 0 6px 12px rgba(232, 93, 36, 0.15);
    }

    .product-card.selected::after {
        content: '✓';
        position: absolute;
        top: 6px;
        right: 6px;
        background: var(--accent);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
    }

    .product-image {
        width: 100%;
        height: 88px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 10px;
    }

    .product-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-price {
        font-size: 13px;
        color: var(--accent);
        font-weight: 700;
    }

    .product-card.is-hidden {
        display: none;
    }

    .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .card-body {
        padding: 20px;
    }

    .form-label {
        font-weight: 600;
        color: var(--text);
        margin-bottom: 10px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control, .form-select {
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: var(--surface);
        color: var(--text);
        width: 100%;
        font-family: inherit;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(232, 93, 36, 0.1);
    }

    textarea.form-control {
        resize: none;
        min-height: 100px;
    }

    .customer-info-card {
        padding: 18px;
        background: linear-gradient(135deg, rgba(232, 93, 36, 0.06) 0%, rgba(255, 255, 255, 0.95) 100%);
        border-radius: 14px;
        border: 1px solid rgba(232, 93, 36, 0.18);
        margin-top: 18px;
        animation: slideDown 0.4s ease-out;
    }

    .customer-info-grid {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 16px;
        align-items: start;
    }

    .customer-info-meta {
        display: grid;
        gap: 10px;
    }

    .customer-info-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 13px;
        color: var(--text-muted);
    }

    .customer-info-row strong {
        color: var(--text);
        font-weight: 700;
    }

    .customer-info-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-active {
        background: #dcfce7;
        color: #166534;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #b91c1c;
    }

    .badge-default {
        background: #e2e8f0;
        color: #334155;
    }

    .customer-notes {
        font-size: 13px;
        color: var(--text);
        line-height: 1.5;
        padding: 12px 14px;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 10px;
    }

    .invoice-items {
        max-height: 260px;
        overflow-y: auto;
        margin-bottom: 12px;
    }

    .invoice-item {
        display: grid;
        grid-template-columns: 54px 1fr;
        gap: 10px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        background: var(--surface);
        border-radius: 8px;
        margin-bottom: 6px;
        transition: all 0.2s ease;
        animation: slideDown 0.3s ease-out;
    }

    .invoice-item-thumb {
        width: 54px;
        height: 54px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: var(--text-muted);
        font-size: 18px;
    }

    .invoice-item-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .invoice-item-content {
        min-width: 0;
    }

    .invoice-item:hover {
        background: rgba(232, 93, 36, 0.02);
    }

    .invoice-item-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
    }

    .invoice-item-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 3px;
        line-height: 1.45;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .invoice-item-qty {
        font-size: 12px;
        color: var(--text-muted);
    }

    .invoice-item-total {
        text-align: right;
        color: var(--accent);
        font-weight: 800;
        white-space: nowrap;
    }

    .invoice-item-total span {
        display: block;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 600;
        margin-top: 2px;
    }

    .invoice-item-actions {
        display: grid;
        grid-template-columns: 112px minmax(76px, 92px) 36px;
        gap: 8px;
        align-items: center;
    }

    .control-label {
        display: block;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .qty-stepper {
        display: grid;
        grid-template-columns: 32px 48px 32px;
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        background: var(--surface);
    }

    .qty-stepper button {
        border: 0;
        background: var(--bg);
        color: var(--text);
        font-weight: 800;
        cursor: pointer;
        min-height: 34px;
    }

    .qty-stepper button:hover {
        color: var(--accent);
        background: rgba(232, 93, 36, 0.08);
    }

    .qty-input,
    .discount-input {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid var(--border);
        border-radius: 8px;
        text-align: center;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    .qty-stepper .qty-input {
        border: 0;
        border-left: 1px solid var(--border);
        border-right: 1px solid var(--border);
        border-radius: 0;
        min-height: 34px;
    }

    .discount-inline {
        position: relative;
    }

    .discount-inline .discount-input {
        height: 36px;
        padding-right: 20px;
    }

    .discount-inline span {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 800;
        pointer-events: none;
    }

    .qty-input:focus,
    .discount-input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 2px rgba(232, 93, 36, 0.1);
    }

    .btn-remove {
        background: linear-gradient(135deg, var(--danger) 0%, #bb2d3b 100%);
        color: white;
        border: none;
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 12px;
        font-weight: 600;
    }

    .btn-remove:hover {
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        transform: translateY(-1px);
    }

    .invoice-summary {
        background: #f7f8fa;
        padding: 14px 16px;
        border-radius: 10px;
        border: 1px solid var(--border);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
        color: var(--text);
    }

    .summary-row.total {
        border-top: 2px solid var(--border);
        padding-top: 14px;
        margin-top: 14px;
        font-weight: 700;
        font-size: 16px;
        color: var(--accent);
    }

    .empty-state {
        text-align: center;
        padding: 24px 12px;
        color: var(--text-muted);
    }

    .empty-state-icon {
        font-size: 42px;
        margin-bottom: 12px;
        opacity: 0.6;
    }

    .empty-state-text {
        font-size: 15px;
        margin-bottom: 8px;
        color: var(--text);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent) 0%, #d94a10 100%);
        color: white;
        padding: 12px 28px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        flex: 1;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-primary:hover {
        box-shadow: 0 8px 20px rgba(232, 93, 36, 0.3);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: var(--bg);
        color: var(--text);
        padding: 12px 28px;
        border: 1px solid var(--border);
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        flex: 1;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-secondary:hover {
        background: var(--surface);
        border-color: var(--accent);
        color: var(--accent);
    }

    .customer-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        border-radius: 10px;
        padding: 10px 16px;
        font-weight: 700;
        transition: all 0.2s ease;
        min-height: 44px;
    }

    .customer-action-btn-primary {
        background: var(--accent);
        color: #fff;
        box-shadow: 0 8px 20px rgba(232, 93, 36, .16);
    }

    .customer-action-btn-secondary {
        background: #fff;
        color: var(--text);
        border: 1px solid rgba(0,0,0,0.08);
    }

    .customer-action-btn-secondary:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .customer-action-btn.disabled,
    .customer-action-btn[aria-disabled="true"] {
        opacity: 0.55;
        pointer-events: none;
    }

    .button-group {
        display: flex;
        gap: 12px;
        margin-top: 0;
    }

    .button-group .btn-primary,
    .button-group .btn-secondary {
        font-size: 14px;
        padding: 12px 24px;
        border-radius: 8px;
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #721c24;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid var(--border);
        border-radius: 8px;
        height: 44px;
        padding: 6px 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: var(--text);
        line-height: 32px;
        font-size: 14px;
    }

    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(232, 93, 36, 0.1);
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .col-lg-7 { animation: slideUp 0.6s ease-out; }
    .col-lg-5 { animation: slideUp 0.6s ease-out 0.1s both; }

    .order-side {
        position: sticky;
        top: 92px;
        align-self: flex-start;
    }

    .checkout-card {
        max-height: calc(100vh - 112px);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .checkout-card .card-body {
        padding: 0;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .checkout-header {
        padding: 18px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .checkout-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        color: var(--text);
        font-size: 18px;
        font-weight: 800;
    }

    .checkout-title i {
        color: var(--accent);
    }

    .cart-count-pill {
        background: rgba(232, 93, 36, 0.1);
        color: var(--accent);
        border: 1px solid rgba(232, 93, 36, 0.24);
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .checkout-scroll {
        overflow-y: auto;
        padding: 18px 20px;
        min-height: 0;
    }

    .checkout-section {
        margin-bottom: 18px;
    }

    .checkout-section-title {
        font-size: 13px;
        font-weight: 800;
        color: var(--text);
        margin: 0 0 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .checkout-section-title i {
        color: var(--accent);
    }

    .order-details-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .order-details-grid .od-field {
        margin-bottom: 0;
    }

    .order-details-grid .full-span {
        grid-column: 1 / -1;
    }

    .checkout-actions {
        padding: 12px 20px 16px;
        border-top: 1px solid var(--border);
        background: var(--surface);
        box-shadow: 0 -8px 18px rgba(26, 29, 41, 0.06);
    }

    .od-field {
        margin-bottom: 16px;
        position: relative;
    }

    .od-label {
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .od-label i {
        color: var(--accent);
        font-size: 13px;
        width: 16px;
        text-align: center;
    }

    .od-select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236c757d' d='M6 8.825L0.375 3.175 1.275 2.275 6 7 10.725 2.275 11.625 3.175z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
    }

    .delivery-combo {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 76px 76px;
        gap: 8px;
        align-items: stretch;
    }

    .box-inline {
        position: relative;
    }

    .box-inline input {
        height: 100%;
        padding-top: 18px;
        text-align: center;
        font-weight: 700;
    }

    .box-inline span {
        position: absolute;
        top: 5px;
        left: 0;
        right: 0;
        color: var(--text-muted);
        font-size: 10px;
        font-weight: 800;
        text-align: center;
        pointer-events: none;
    }

    /* Toast Notification */
    .toast-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.4);
        z-index: 9998;
        opacity: 0;
        transition: opacity 0.3s;
        display: none;
    }
    .toast-overlay.show { display: block; opacity: 1; }

    .toast-box {
        position: fixed;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        background: var(--surface);
        border-radius: 16px;
        padding: 32px 36px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        z-index: 9999;
        text-align: center;
        min-width: 320px;
        max-width: 400px;
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: none;
    }
    .toast-box.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }

    .toast-icon {
        width: 56px; height: 56px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        font-size: 24px;
    }
    .toast-icon.warning { background: #fff3cd; color: #d97706; }
    .toast-icon.error   { background: #fee2e2; color: #dc2626; }
    .toast-icon.success { background: #d1fae5; color: #059669; }

    .toast-title   { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
    .toast-message { font-size: 14px; color: var(--text-muted); margin-bottom: 20px; line-height: 1.5; }
    .toast-btn {
        background: linear-gradient(135deg, var(--accent) 0%, #d94a10 100%);
        color: #fff; border: none; padding: 10px 32px;
        border-radius: 10px; font-size: 14px; font-weight: 700;
        cursor: pointer; transition: all 0.2s;
    }
    .toast-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(232,93,36,0.3); }

    /* Free Products Section */
    .free-product-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
        padding: 12px;
        background: rgba(232, 93, 36, 0.03);
        border: 1px solid var(--border);
        border-radius: 8px;
        animation: slideDown 0.3s ease-out;
    }

    .free-product-row select,
    .free-product-row input { flex: 1; }

    .free-product-row input[type="number"] { flex: 0 0 80px; }

    .btn-remove-free {
        background: var(--danger);
        color: white; border: none;
        padding: 8px 12px; border-radius: 6px;
        cursor: pointer; font-size: 12px;
        transition: all 0.2s ease; flex: 0 0 auto;
    }
    .btn-remove-free:hover { box-shadow: 0 4px 12px rgba(220,53,69,0.3); transform: translateY(-1px); }

    .btn-outline-primary {
        background: transparent; color: var(--accent);
        border: 1px solid var(--accent); padding: 8px 16px;
        border-radius: 6px; cursor: pointer; font-weight: 600;
        font-size: 13px; transition: all 0.2s ease;
    }
    .btn-outline-primary:hover { background: var(--accent); color: white; }

    .btn-sm   { padding: 8px 14px; font-size: 12px; }
    .mt-2     { margin-top: 12px; }

    /* Custom Price Modal */
    .cpm-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.48);
        z-index: 9500;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .cpm-overlay.show { display: flex; }

    .cpm-modal {
        background: var(--surface);
        border-radius: 16px;
        border: 1px solid var(--border);
        width: 100%;
        max-width: 420px;
        overflow: hidden;
        animation: slideDown 0.25s ease-out;
        box-shadow: 0 24px 64px rgba(0,0,0,0.22);
    }

    .cpm-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 20px 14px;
        border-bottom: 1px solid var(--border);
    }

    .cpm-thumb {
        width: 48px; height: 48px;
        border-radius: 10px;
        background: rgba(232,93,36,.1);
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; flex-shrink: 0;
    }

    .cpm-thumb img { width: 100%; height: 100%; object-fit: cover; }

    .cpm-body { padding: 18px 20px; }

    .cpm-orig-prices {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 16px;
    }

    .cpm-orig-box {
        border-radius: 8px;
        padding: 10px 12px;
        border: 1px solid;
    }

    .cpm-orig-box.usd {
        background: rgba(40,167,69,.08);
        border-color: rgba(40,167,69,.22);
    }

    .cpm-orig-box.khr {
        background: var(--bg);
        border-color: var(--border);
    }

    .cpm-orig-label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }

    .cpm-orig-box.usd .cpm-orig-label { color: #166534; }
    .cpm-orig-box.khr .cpm-orig-label { color: var(--text-muted); }

    .cpm-orig-val { font-size: 17px; font-weight: 700; }
    .cpm-orig-box.usd .cpm-orig-val { color: #166534; }
    .cpm-orig-box.khr .cpm-orig-val { color: var(--text); }

    .cpm-field { margin-bottom: 14px; }

    .cpm-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .cpm-label i { color: var(--accent); margin-right: 4px; }

    .cpm-prefix-wrap { position: relative; }

    .cpm-prefix {
        position: absolute;
        left: 11px; top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-weight: 700;
        font-size: 14px;
        pointer-events: none;
    }

    .cpm-prefix-wrap .form-control { padding-left: 26px; }
    .cpm-prefix-wrap.khr .form-control { padding-left: 30px; }

    .cpm-qty-stepper {
        display: grid;
        grid-template-columns: 38px 1fr 38px;
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
    }

    .cpm-qty-stepper button {
        background: var(--bg); border: none;
        cursor: pointer; font-size: 20px;
        font-weight: 700; color: var(--text);
        min-height: 40px; transition: all 0.2s;
    }
    .cpm-qty-stepper button:hover { color: var(--accent); background: rgba(232,93,36,.08); }

    .cpm-qty-stepper input {
        border: none;
        border-left: 1px solid var(--border);
        border-right: 1px solid var(--border);
        border-radius: 0;
        text-align: center;
        font-size: 15px;
        font-weight: 700;
        min-height: 40px;
        width: 100%;
        background: var(--surface);
        color: var(--text);
        font-family: inherit;
    }
    .cpm-qty-stepper input:focus { outline: none; }

    .cpm-total-bar {
        background: rgba(232,93,36,.07);
        border: 1px solid rgba(232,93,36,.2);
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .cpm-total-label { font-size: 13px; color: var(--accent); font-weight: 700; }

    .cpm-total-usd { font-size: 17px; font-weight: 700; color: var(--accent); text-align: right; }
    .cpm-total-khr { font-size: 12px; color: var(--accent); opacity: .8; text-align: right; }

    .cpm-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    /* Custom price badge on cart item */
    .custom-price-badge {
        display: inline-block;
        background: rgba(232,93,36,.12);
        color: var(--accent);
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 999px;
        margin-left: 6px;
        vertical-align: middle;
    }

    /* Payment Methods Grid */
    .payment-methods-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 12px;
        margin-top: 12px;
    }

    .payment-methods-grid.payment-multi-select .payment-method-btn {
        position: relative;
    }

    .payment-method-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 16px 12px;
        border: 2px solid var(--border);
        border-radius: 12px;
        background: var(--surface);
        color: var(--text);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 13px;
        font-weight: 600;
        min-height: 110px;
    }

    .payment-method-btn i {
        font-size: 24px;
        color: var(--text-muted);
        transition: color 0.3s ease;
    }

    .payment-method-btn:hover {
        border-color: var(--accent);
        background: rgba(232, 93, 36, 0.04);
        box-shadow: 0 4px 12px rgba(232, 93, 36, 0.15);
        transform: translateY(-2px);
    }

    .payment-method-btn:hover i {
        color: var(--accent);
    }

    .payment-method-btn.active {
        border-color: var(--accent);
        background: linear-gradient(135deg, rgba(232, 93, 36, 0.1) 0%, rgba(232, 93, 36, 0.05) 100%);
        color: var(--accent);
        box-shadow: 0 6px 16px rgba(232, 93, 36, 0.2);
    }

    .payment-method-btn.active i {
        color: var(--accent);
    }

    /* Multi-select Checkmark */
    .payment-method-btn .method-check {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 28px;
        height: 28px;
        background: var(--accent);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(232, 93, 36, 0.3);
        animation: popIn 0.3s ease;
    }

    @keyframes popIn {
        0% { transform: scale(0); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    /* Selected Method Tags */
    .method-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: linear-gradient(135deg, rgba(232, 93, 36, 0.15) 0%, rgba(232, 93, 36, 0.08) 100%);
        border: 1.5px solid var(--accent);
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: var(--accent);
        animation: slideInLeft 0.3s ease;
    }

    .method-tag i {
        font-size: 12px;
    }

    .method-tag-close {
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .method-tag-close:hover {
        opacity: 1;
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @media (max-width: 992px) {
        .products-grid { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
        .order-side { position: static; }
        .checkout-card { max-height: none; }
    }

    @media (max-width: 576px) {
        .product-tools { grid-template-columns: 1fr; }
        .product-count { text-align: right; }
        .products-grid { grid-template-columns: repeat(auto-fill, minmax(112px, 1fr)); max-height: 420px; }
        .invoice-item-actions,
        .order-details-grid,
        .button-group { grid-template-columns: 1fr; display: grid; }
        .btn-remove { width: 100%; }
        .delivery-combo { grid-template-columns: 1fr 64px 64px; }
        .cpm-orig-prices { grid-template-columns: 1fr; }
        .cpm-actions { grid-template-columns: 1fr; }
    }

    .row { margin-left: -8px; margin-right: -8px; }
    [class*='col-'] { padding-left: 8px; padding-right: 8px; margin-bottom: 8px; }
</style>
@endpush

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <ul style="margin: 0; padding-left: 20px;">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">

    <!-- Left Section: Products -->
    <div class="col-lg-7">

        <!-- Customer Selection -->
        <div class="card">
            <div class="card-body">
                <h4 class="product-section-title">
                    <i class="fas fa-user-circle"></i> សូមជ្រើសរើសអតិថិជន
                </h4>

                <div class="od-field" style="display:grid; gap: 14px;">
                    <div style="display:flex; flex-wrap:wrap; gap: 14px; align-items:flex-end; justify-content:space-between;">
                        <div style="flex:1; min-width:220px;">
                            <label for="customer_id" class="form-label">សូមជ្រើសរើសអតិថិជន</label>
                            <select name="customer_id" id="customer_id" class="form-control select2-customer" required>
                                <option value="">សូមស្វែងរកឈ្មោះ / ទូរសព្ទ / ទីតាំង</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        data-name="{{ $customer->name }}"
                                        data-phone="{{ $customer->phone }}"
                                        data-address="{{ $customer->address ?? $customer->location }}"
                                        data-type="{{ $customer->type ?? 'N/A' }}"
                                        data-status="{{ $customer->status ?? 'Unknown' }}"
                                        data-notes="{{ $customer->notes ?? '' }}"
                                        {{ (string) old('customer_id', $selectedCustomerId ?? '') === (string) $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="display:flex; flex-wrap:wrap; gap: 10px;">
                            <a href="{{ route('customers.create') }}" target="_blank" class="customer-action-btn customer-action-btn-primary">
                                <i class="fas fa-user-plus"></i> អតិថិជនថ្មី
                            </a>
                            <a href="#" id="editCustomerBtn" class="customer-action-btn customer-action-btn-secondary disabled" aria-disabled="true">
                                <i class="fas fa-user-edit"></i> កែប្រែ
                            </a>
                        </div>
                    </div>

                    <div id="customer_info_card" class="customer-info-card" style="display: none;">
                        <div class="customer-info-grid">
                            <div class="customer-info-meta">
                                <div class="customer-info-row" style="justify-content:space-between; gap:12px; border-bottom:1px solid rgba(0,0,0,0.08); padding-bottom:8px;">
                                    <strong id="customer_name">-</strong>
                                    <span id="customer_type" class="customer-info-badge badge-default">-</span>
                                </div>
                                <div class="customer-info-row">
                                    <strong>ទូរសព្ទ:</strong>
                                    <span id="customer_phone">-</span>
                                </div>
                                <div class="customer-info-row">
                                    <strong>អាសយដ្ឋាន:</strong>
                                    <span id="customer_address">-</span>
                                </div>
                                <div class="customer-info-row">
                                    <strong>ស្ថានភាព:</strong>
                                    <span id="customer_status" class="customer-info-badge badge-default">-</span>
                                </div>
                            </div>
                            <div id="notes_section" style="display:none;">
                                <div class="customer-notes" id="customer_notes">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="card">
            <div class="card-body">
                <h4 class="product-section-title">
                    សូមជ្រើសរើសទំនិញ
                </h4>

                <div class="product-tools">
                    <div class="product-search">
                        <i class="fas fa-search"></i>
                        <input type="search" id="productSearch" class="form-control" placeholder="ស្វែងរកទំនិញ">
                    </div>
                    <div class="product-count">
                        <span id="visibleProductCount">{{ $products->count() }}</span> / {{ $products->count() }}
                    </div>
                </div>

                <div class="products-grid" id="productsGrid">
                    @forelse($products as $product)
                    @php($productImageUrl = $product->imageUrl())
                    <div class="product-card"
                        data-product-name="{{ Str::lower($product->name) }}"
                        onclick="addToCart(
                            {{ $product->id }},
                            @js($product->name),
                            {{ $product->price_usd }},
                            {{ $product->price_khr }},
                            {{ $product->allow_custom_price ? 'true' : 'false' }},
                            @js($productImageUrl)
                        )">
                        
                        @if($productImageUrl)
                            <img src="{{ $productImageUrl }}" alt="{{ $product->name }}" class="product-image">
                        @else
                            <div style="width: 100%; height: 88px; background: var(--bg); border-radius: 6px;
                                display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                                <i class="fas fa-image" style="font-size: 24px; color: var(--text-muted);"></i>
                            </div>
                        @endif
                        <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
                        <div style="font-size: 12px; margin-bottom: 6px;">
                            <div style="color: var(--accent); font-weight: 700;">${{ number_format($product->price_usd, 2) }}</div>
                            <div style="color: var(--text-muted); font-weight: 600;">៛{{ number_format($product->price_khr, 0) }}</div>
                        </div>
                        @if($product->allow_custom_price)
                            <div style="font-size: 10px; color: var(--accent); font-weight: 700; margin-top: 4px;">
                                <i class="fas fa-tag" style="font-size:9px;"></i> តម្លៃកំណត់ផ្ទាល់
                            </div>
                        @endif
                    </div>
                    @empty
                    <div class="empty-state" style="grid-column: 1 / -1;">
                        <div class="empty-state-icon"></div>
                        <div class="empty-state-text">មិនមានទំនិញទេ</div>
                        <a href="{{ route('products.index') }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">បន្ថែមទំនិញ →</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Right Section: Order Form -->
    <div class="col-lg-5 order-side">
        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
            @csrf

            <div class="card checkout-card">
                <div class="card-body">
                    <div class="checkout-header">
                        <h4 class="checkout-title">
                            បញ្ជាទិញ
                        </h4>
                        
                    </div>

                    <div class="checkout-scroll">
                        <div class="checkout-section">
                            <div class="invoice-items" id="invoiceItems">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fas fa-shopping-cart"></i></div>
                                    <div class="empty-state-text">សូមជ្រើសរើសមុខទំនិញ</div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" id="hidden_customer_id" name="customer_id">
                        <input type="hidden" id="order_items" name="order_items" value="[]">
                        <input type="hidden" id="subtotal_amount" name="subtotal">
                        <input type="hidden" id="discount_amount" name="discount_amount">
                        <input type="hidden" id="delivery_fee_khr" name="delivery_fee_khr" value="0">
                        <input type="hidden" id="total_amount_input" name="total_amount">
                        <input type="hidden" name="order_date" value="{{ old('order_date', now()->setTimezone('Asia/Phnom_Penh')->format('Y-m-d\TH:i')) }}">

                        <div class="checkout-section">
                            <div class="order-details-grid">

                                <div class="od-field">
                                    <label class="od-label"><i class="fas fa-money-bill-wave"></i> ការបង់ប្រាក់</label>
                                    <select id="payment_status" name="payment_status" class="form-control od-select">
                                        <option value="unpaid"  {{ old('payment_status') == 'unpaid'  ? 'selected' : '' }}>មិនទាន់បង់</option>
                                        <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>បង់មួយផ្នែក</option>
                                        <option value="paid"    {{ old('payment_status') == 'paid'    ? 'selected' : '' }}>បានបង់</option>
                                    </select>
                                </div>

                                <div class="od-field" id="payment_method_field" style="display:none;">
                                    <label class="od-label"><i class="fas fa-credit-card"></i> វិធីបង់ប្រាក់</label>

                                    <!-- Selected Methods Display -->
                                    <div id="selected_methods_display" style="margin-bottom: 12px; min-height: 40px;">
                                        <div id="selected_methods_tags" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
                                    </div>

                                    <!-- Hidden inputs for payment methods -->
                                    <input type="hidden" name="payment_method" id="payment_method" value="{{ old('payment_method', '') }}">
                                    <input type="hidden" id="payment_methods_array" value="[]">

                                    <!-- Payment Method Selection Grid -->
                                    <div class="payment-methods-grid payment-multi-select">
                                        <button type="button" class="payment-method-btn" data-method="Cash" title="ប្រាក់សម្រាប់">
                                            <i class="fas fa-money-bill-wave"></i>
                                            <span>លុយក្រៅ</span>
                                            <span class="method-check" style="display:none;">✓</span>
                                        </button>
                                        <button type="button" class="payment-method-btn" data-method="ABA" title="ធនាគារ ABA">
                                            <i class="fas fa-university"></i>
                                            <span>ABA</span>
                                            <span class="method-check" style="display:none;">✓</span>
                                        </button>
                                        <button type="button" class="payment-method-btn" data-method="ACLEDA" title="ធនាគារ ACLEDA">
                                            <i class="fas fa-university"></i>
                                            <span>ACLEDA</span>
                                            <span class="method-check" style="display:none;">✓</span>
                                        </button>
                                        <button type="button" class="payment-method-btn" data-method="Wing" title="Wing Money">
                                            <i class="fas fa-mobile-alt"></i>
                                            <span>Wing</span>
                                            <span class="method-check" style="display:none;">✓</span>
                                        </button>
                                    </div>

                                    <div style="font-size: 12px; color: #6b7280; margin-top: 8px; font-style: italic;">
                                        💡 ជ្រើសរើសវិធីបង់មួយ ឬច្រើន (ឧ. Bank + Cash)
                                    </div>
                                </div>

                                <div class="od-field">
                                    <label class="od-label"><i class="fas fa-truck"></i> ការដឹកជញ្ជូន</label>
                                    <div class="delivery-combo">
                                        <select id="delivery_select" name="delivery_id" class="form-control od-select">
                                            <option value="">សូមជ្រើសរើស</option>
                                            @foreach($deliveries as $delivery)
                                                <option value="{{ $delivery->id }}"
                                                    data-name="{{ $delivery->delivery_name }}"
                                                    data-price="{{ $delivery->delivery_price_khr }}"
                                                    data-price-big="{{ $delivery->delivery_price_khr_big }}">
                                                    {{ $delivery->delivery_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="box-inline">
                                            <span>តូច</span>
                                            <input type="number" id="small_pack_qty" name="small_pack_qty" class="form-control" min="0"
                                            value="{{ old('small_pack_qty', 1) }}">
                                        </div>
                                        <div class="box-inline">
                                            <span>ធំ</span>
                                            <input type="number" id="big_pack_qty" name="big_pack_qty" class="form-control" min="0"
                                            value="{{ old('big_pack_qty', 0) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="od-field" id="taxi_phone_field" style="display:none;">
                                    <label class="od-label"><i class="fas fa-phone"></i> លេខទូរស័ព្ទតាក់ស៊ី</label>
                                    <input type="text" id="taxi_phone" name="taxi_phone" class="form-control"
                                        value="{{ old('taxi_phone') }}" placeholder="បញ្ចូលលេខទូរស័ព្ទ taxi">
                                </div>

                                <div class="od-field full-span">
                                    <label class="od-label"><i class="fas fa-gift"></i> Free ជូនអតិថិជន</label>
                                    <div id="freeProductsContainer"></div>
                                    <button type="button" id="addFreeProductBtn" class="btn btn-sm btn-outline-primary mt-2">
                                        + Add Free
                                    </button>
                                </div>

                                <div class="od-field full-span">
                                    <label class="od-label"><i class="fas fa-sticky-note"></i> ផ្សេងៗ</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="...">{{ old('notes') }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="checkout-section" style="padding: 0 20px;">
                        <div class="invoice-summary">
                            <div class="summary-row">
                                <span>សរុប (USD / KHR):</span>
                                <div style="text-align: right;">
                                    <div style="font-weight: 600;">$<span id="subtotal">0.00</span></div>
                                    <div style="font-weight: 600; color: var(--text-muted); font-size: 12px;">៛<span id="subtotal_khr">0</span></div>
                                </div>
                            </div>

                            <div class="summary-row">
                                <span style="font-weight: 600;">បញ្ចុះតម្លៃសរុប:</span>
                                <div style="text-align: right;">
                                    <div style="font-weight: 600; color: var(--danger);">-$<span id="discountAmount">0.00</span></div>
                                    <div style="font-weight: 600; color: var(--text-muted); font-size: 12px;">-៛<span id="discountAmount_khr">0</span></div>
                                </div>
                            </div>

                            <div class="summary-row">
                                <span style="font-weight: 600;">ការដឹកជញ្ជូន:</span>
                                <div style="text-align: right;">
                                    <div style="font-weight: 600;">$<span id="deliveryFeeUsd">0.00</span></div>
                                    <div style="font-weight: 600; color: var(--text-muted); font-size: 12px;">៛<span id="deliveryFeeKhr">0</span></div>
                                </div>
                            </div>

                            <div class="summary-row total">
                                <span>តម្លៃសរុប:</span>
                                <div style="text-align: right;">
                                    <div>$<span id="totalAmount">0.00</span></div>
                                    <div style="color: var(--accent); font-size: 13px;">៛<span id="totalAmount_khr">0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-actions button-group">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check-circle"></i> បញ្ជាក់
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn-secondary">
                            <i class="fas fa-times"></i> បោះបង់
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<!-- ===== Custom Price Modal ===== -->
<div id="cpmOverlay" class="cpm-overlay" onclick="cpmOverlayClick(event)">
    <div class="cpm-modal">

        <div class="cpm-header">
            <div class="cpm-thumb" id="cpm_thumb">
                <i class="fas fa-tag" style="color:var(--accent); font-size:18px;"></i>
            </div>
            <div style="min-width:0; flex:1;">
                <div id="cpm_name" style="font-size:15px; font-weight:700; color:var(--text);
                    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"></div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">កំណត់តម្លៃតាមចង់បាន</div>
            </div>
            <button type="button" onclick="cpmClose()"
                style="background:none; border:none; cursor:pointer; color:var(--text-muted);
                    font-size:22px; padding:4px 6px; border-radius:6px; line-height:1; flex-shrink:0;"
                title="បិទ">&times;</button>
        </div>

        <div class="cpm-body">

            <!-- Original prices reference -->
            <div class="cpm-orig-prices">
                <div class="cpm-orig-box usd">
                    <div class="cpm-orig-label">តម្លៃដើម USD</div>
                    <div class="cpm-orig-val" id="cpm_orig_usd">$0.00</div>
                </div>
                <div class="cpm-orig-box khr">
                    <div class="cpm-orig-label">តម្លៃដើម KHR</div>
                    <div class="cpm-orig-val" id="cpm_orig_khr">៛0</div>
                </div>
            </div>

            <!-- Custom KHR price (base currency — type this one) -->
            <div class="cpm-field">
                <label class="cpm-label">
                    <i class="fas fa-coins"></i> តម្លៃថ្មី (KHR)
                </label>
                <div class="cpm-prefix-wrap khr">
                    <span class="cpm-prefix">៛</span>
                    <input type="number" id="cpm_khr" class="form-control"
                        min="0" step="100"
                        oninput="cpmSyncUsd(this.value); cpmUpdateTotal()">
                </div>
            </div>

            <!-- Custom USD price (computed from KHR — for reference/record only) -->
            <div class="cpm-field">
                <label class="cpm-label">
                    <i class="fas fa-dollar-sign"></i> តម្លៃថ្មី (USD)
                </label>
                <div class="cpm-prefix-wrap">
                    <span class="cpm-prefix">$</span>
                    <input type="number" id="cpm_usd" class="form-control"
                        min="0" step="0.01"
                        oninput="cpmSyncKhr(this.value); cpmUpdateTotal()">
                </div>
            </div>

            <!-- Quantity stepper -->
            <div class="cpm-field">
                <label class="cpm-label">
                    <i class="fas fa-layer-group"></i> ចំនួន
                </label>
                <div class="cpm-qty-stepper">
                    <button type="button" onclick="cpmStepQty(-1)">−</button>
                    <input type="number" id="cpm_qty" value="1" min="1"
                        oninput="cpmUpdateTotal()">
                    <button type="button" onclick="cpmStepQty(1)">+</button>
                </div>
            </div>

            <!-- Live total -->
            <div class="cpm-total-bar">
                <span class="cpm-total-label">សរុប</span>
                <div>
                    <div class="cpm-total-usd" id="cpm_total_usd">$0.00</div>
                    <div class="cpm-total-khr" id="cpm_total_khr">៛0</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="cpm-actions">
                <button type="button" onclick="cpmClose()" class="btn-secondary" style="justify-content:center;">
                    <i class="fas fa-times"></i> បោះបង់
                </button>
                <button type="button" onclick="cpmConfirm()" class="btn-primary" style="justify-content:center;">
                    <i class="fas fa-shopping-cart"></i> បញ្ជាក់
                </button>
            </div>

        </div>
    </div>
</div>
<!-- ===== End Custom Price Modal ===== -->

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    /* ─── State ─────────────────────────────────────────────── */
    let cart = {};
    const exchangeRate = 4000;
    let freeProductCount = 0;
    let customProduct = null;   // holds product pending custom-price input

    const customerEditUrlBase  = '{{ url('customers') }}';
    const customerEditReturnUrl = encodeURIComponent(window.location.href);

    /* ─── Select2 + customer card ───────────────────────────── */
    $(document).ready(function () {

        function customerMatcher(params, data) {
            if ($.trim(params.term) === '') return data;
            const term            = params.term.toLowerCase();
            const optionText      = (data.text || '').toLowerCase();
            const phone           = ($(data.element).data('phone') || '').toString().toLowerCase();
            const normalizedTerm  = term.replace(/\D/g, '');
            const normalizedPhone = phone.replace(/\D/g, '');
            const address         = ($(data.element).data('address') || '').toString().toLowerCase();

            if (optionText.indexOf(term) > -1)            return data;
            if (address.indexOf(term) > -1)               return data;
            if (normalizedTerm && normalizedPhone.indexOf(normalizedTerm) > -1) return data;
            if (phone.indexOf(term) > -1)                 return data;
            return null;
        }

        $('.select2-customer').select2({
            placeholder: 'សូមស្វែងរកអតិថិជន',
            allowClear: true,
            width: '100%',
            matcher: customerMatcher,
            templateResult: function (result) {
                if (!result.id) return result.text;
                const phone   = $(result.element).data('phone');
                const address = $(result.element).data('address');
                const display = $('<div></div>');
                display.append($('<div></div>').text(result.text));
                if (phone)   display.append($('<div style="font-size:12px;color:#6c757d;"></div>').text('📞 ' + phone));
                if (address) display.append($('<div style="font-size:12px;color:#6c757d;"></div>').text(address));
                return display;
            }
        });

        $('#customer_id').on('change', function () {
            const opt      = $(this).find('option:selected');
            const custId   = opt.val();
            $('#hidden_customer_id').val(custId);

            if (custId) {
                const name    = opt.data('name')    || '-';
                const phone   = opt.data('phone')   || '-';
                const address = opt.data('address') || '-';
                const type    = opt.data('type')    || 'N/A';
                const status  = opt.data('status')  || 'Unknown';
                const notes   = opt.data('notes')   || '';

                $('#customer_name').text(name);
                $('#customer_phone').text(phone);
                $('#customer_address').text(address);
                $('#customer_type').text(type);
                $('#customer_status').text(status);

                $('#editCustomerBtn')
                    .attr('href', `${customerEditUrlBase}/${custId}/edit?return_url=${customerEditReturnUrl}`)
                    .removeClass('disabled').attr('aria-disabled', 'false');

                const statusLower = status.toLowerCase();
                const badgeClass  = ['active','inactive'].includes(statusLower) ? 'badge-' + statusLower : 'badge-default';

                $('#customer_type').removeClass('badge-active badge-inactive badge-default').addClass(badgeClass);
                $('#customer_status').removeClass('badge-active badge-inactive badge-default').addClass(badgeClass);

                if (notes && notes.trim() !== '') {
                    $('#customer_notes').text(notes);
                    $('#notes_section').slideDown(200);
                } else {
                    $('#notes_section').slideUp(200);
                }
                $('#customer_info_card').slideDown(300);
            } else {
                $('#editCustomerBtn').attr('href','#').addClass('disabled').attr('aria-disabled','true');
                $('#customer_info_card').slideUp(300);
            }
        });

        if ($('#customer_id').val()) $('#customer_id').trigger('change');

        /* ── delivery / pack qty ── */
        $('#delivery_select, #small_pack_qty, #big_pack_qty').on('change input', function () {
            calculateTotal();
            updateCartData();
            updateTaxiPhoneVisibility();
        });
        updateTaxiPhoneVisibility();

        /* ── product search ── */
        $('#productSearch').on('input', function () { filterProducts(this.value); });

        /* ── free products ── */
        $('#addFreeProductBtn').on('click', function (e) { e.preventDefault(); addFreeProductRow(); });
        addFreeProductRow();
    });

    /* ─── Product search filter ─────────────────────────────── */
    function filterProducts(searchValue) {
        const query = String(searchValue || '').trim().toLowerCase();
        let visible = 0;
        $('.product-card').each(function () {
            const name    = String($(this).data('product-name') || '');
            const isMatch = !query || name.includes(query);
            $(this).toggleClass('is-hidden', !isMatch);
            if (isMatch) visible++;
        });
        $('#visibleProductCount').text(visible);
    }

    /* ─── Add to cart ───────────────────────────────────────── */
    function addToCart(productId, productName, price, priceKhr, allowCustomPrice, imageUrl) {
        if (allowCustomPrice) {
            openCustomPriceModal(productId, productName, price, priceKhr, imageUrl);
            return;
        }
        if (cart[productId]) {
            cart[productId].qty++;
        } else {
            cart[productId] = {
                name: productName,
                price: parseFloat(price),
                price_khr: parseFloat(priceKhr),
                qty: 1,
                discount: 0,
                image: imageUrl,
                is_custom_price: false
            };
        }
        renderInvoice();
    }

    function removeFromCart(productId) {
        delete cart[productId];
        renderInvoice();
    }

    function updateQuantity(productId, newQty) {
        newQty = parseInt(newQty) || 1;
        if (newQty <= 0) removeFromCart(productId);
        else { cart[productId].qty = newQty; renderInvoice(); }
    }

    function updateItemDiscount(productId, discountPercent) {
        const pct = parseFloat(discountPercent) || 0;
        if (cart[productId]) {
            cart[productId].discount = Math.min(Math.max(pct, 0), 100);
            renderInvoice();
        }
    }

    /* ─── Custom Price Modal ────────────────────────────────── */
    function openCustomPriceModal(id, name, usd, khr, image) {
        customProduct = { id, name, usd: parseFloat(usd), khr: parseFloat(khr), image };

        document.getElementById('cpm_name').innerText     = name;
        document.getElementById('cpm_orig_usd').innerText = '$' + parseFloat(usd).toFixed(2);
        document.getElementById('cpm_orig_khr').innerText = '៛' + Math.round(khr).toLocaleString();
        document.getElementById('cpm_usd').value          = parseFloat(usd).toFixed(2);
        document.getElementById('cpm_khr').value          = Math.round(khr);
        document.getElementById('cpm_qty').value          = 1;

        const thumb = document.getElementById('cpm_thumb');
        thumb.innerHTML = image
            ? `<img src="${escapeHtml(image)}" alt="${escapeHtml(name)}">`
            : '<i class="fas fa-tag" style="color:var(--accent); font-size:18px;"></i>';

        cpmUpdateTotal();
        document.getElementById('cpmOverlay').classList.add('show');
    }

    function cpmClose() {
        document.getElementById('cpmOverlay').classList.remove('show');
        customProduct = null;
    }

    function cpmOverlayClick(e) {
        if (e.target === document.getElementById('cpmOverlay')) cpmClose();
    }

    function cpmSyncKhr(usdVal) {
        document.getElementById('cpm_khr').value = Math.round(parseFloat(usdVal || 0) * exchangeRate);
    }

    function cpmSyncUsd(khrVal) {
        document.getElementById('cpm_usd').value = (parseFloat(khrVal || 0) / exchangeRate).toFixed(2);
    }

    function cpmStepQty(delta) {
        const input = document.getElementById('cpm_qty');
        input.value = Math.max(1, (parseInt(input.value) || 1) + delta);
        cpmUpdateTotal();
    }

    function cpmUpdateTotal() {
        const usd = parseFloat(document.getElementById('cpm_usd').value) || 0;
        const khr = parseFloat(document.getElementById('cpm_khr').value) || 0;
        const qty = parseInt(document.getElementById('cpm_qty').value)   || 1;
        document.getElementById('cpm_total_usd').innerText = '$' + (usd * qty).toFixed(2);
        document.getElementById('cpm_total_khr').innerText = '៛' + Math.round(khr * qty).toLocaleString();
    }

    function cpmConfirm() {
        if (!customProduct) return;
        const usd = parseFloat(document.getElementById('cpm_usd').value) || 0;
        const khr = parseFloat(document.getElementById('cpm_khr').value) || 0;
        const qty = Math.max(1, parseInt(document.getElementById('cpm_qty').value) || 1);
        const id  = customProduct.id;

        if (cart[id]) {
            cart[id].qty         += qty;
            cart[id].price        = usd;
            cart[id].price_khr    = khr;
            cart[id].is_custom_price = true;
        } else {
            cart[id] = {
                name: customProduct.name,
                price: usd,
                price_khr: khr,
                qty: qty,
                discount: 0,
                image: customProduct.image,
                is_custom_price: true
            };
        }
        cpmClose();
        renderInvoice();
    }

    /* ─── Render invoice ────────────────────────────────────── */
    function renderInvoice() {
        const invoiceItems = $('#invoiceItems');
        const cartCount    = Object.values(cart).reduce((t, i) => t + (parseInt(i.qty) || 0), 0);
        $('#cartCount').text(cartCount);

        if (Object.keys(cart).length === 0) {
            invoiceItems.html(`
                <div class="empty-state">
                    <div class="empty-state-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="empty-state-text">សូមជ្រើសរើសមុខទំនិញ</div>
                </div>`);
        } else {
            let html = '';
            Object.entries(cart).forEach(([productId, item]) => {
                const disc            = parseFloat(item.discount || 0);
                const discPrice       = item.price     * (1 - disc / 100);
                const discPriceKhr    = item.price_khr * (1 - disc / 100);
                const itemTotal       = discPrice    * item.qty;
                const itemTotalKhr    = discPriceKhr * item.qty;
                const customBadge     = item.is_custom_price
                    ? '<span class="custom-price-badge">-</span>' : '';

                html += `
                <div class="invoice-item">
                    <div class="invoice-item-thumb">
                        ${item.image ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.name)}">` : '<i class="fas fa-image"></i>'}
                    </div>
                    <div class="invoice-item-content">
                        <div class="invoice-item-header">
                            <div>
                                <div class="invoice-item-name">${escapeHtml(item.name)}${customBadge}</div>
                                <div class="invoice-item-qty">
                                    $${discPrice.toFixed(2)} / ៛${Math.round(discPriceKhr).toLocaleString()} × ${item.qty}
                                    ${disc > 0 ? `<span style="color:var(--danger);margin-left:8px;">-${disc}%</span>` : ''}
                                </div>
                            </div>
                            <div class="invoice-item-total">
                                $${itemTotal.toFixed(2)}
                                <span>៛${Math.round(itemTotalKhr).toLocaleString()}</span>
                            </div>
                        </div>
                        <div class="invoice-item-actions">
                            <div class="qty-stepper">
                                <button type="button" onclick="updateQuantity(${productId}, ${item.qty - 1})">−</button>
                                <input type="number" class="qty-input" value="${item.qty}"
                                    onchange="updateQuantity(${productId}, this.value)" min="1">
                                <button type="button" onclick="updateQuantity(${productId}, ${item.qty + 1})">+</button>
                            </div>
                            <div class="discount-inline">
                                <input type="number" class="discount-input" value="${disc}"
                                    onchange="updateItemDiscount(${productId}, this.value)" min="0" max="100" step="0.1">
                                <span>%</span>
                            </div>
                            <button type="button" class="btn-remove" onclick="removeFromCart(${productId})" title="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            });
            invoiceItems.html(html);
        }

        calculateTotal();
        updateCartData();
    }

    /* ─── Helpers ───────────────────────────────────────────── */
    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function getSmallPackQty() {
        return Math.max(parseInt($('#small_pack_qty').val() || 0, 10) || 0, 0);
    }

    function getBigPackQty() {
        return Math.max(parseInt($('#big_pack_qty').val() || 0, 10) || 0, 0);
    }

    function getSelectedDeliveryFeeKhr() {
        const selected = $('#delivery_select option:selected');
        const smallPrice = parseFloat(selected.data('price') || 0) || 0;
        const bigPrice = parseFloat(selected.data('price-big') || 0) || 0;
        return (smallPrice * getSmallPackQty()) + (bigPrice * getBigPackQty());
    }

    function updateTaxiPhoneVisibility() {
        const name = ($('#delivery_select option:selected').data('name') || '').toString().toLowerCase();
        if (name.includes('តាក់សុី')) {
            $('#taxi_phone_field').slideDown(180);
        } else {
            $('#taxi_phone_field').slideUp(180);
            $('#taxi_phone').val('');
        }
    }

    /* ─── Calculate totals ──────────────────────────────────── */
    function calculateTotal() {
        let subtotal = 0, subtotalKhr = 0, totalDiscount = 0, totalDiscountKhr = 0;
        let grossSubtotal = 0, grossSubtotalKhr = 0;

        Object.values(cart).forEach(item => {
            const disc          = parseFloat(item.discount || 0);
            const discPrice     = item.price     * (1 - disc / 100);
            const discPriceKhr  = item.price_khr * (1 - disc / 100);
            subtotal         += discPrice    * item.qty;
            subtotalKhr      += discPriceKhr * item.qty;
            grossSubtotal    += item.price     * item.qty;
            grossSubtotalKhr += item.price_khr * item.qty;
            totalDiscount    += (item.price     * item.qty) - (discPrice    * item.qty);
            totalDiscountKhr += (item.price_khr * item.qty) - (discPriceKhr * item.qty);
        });

        const deliveryFeeKhr = getSelectedDeliveryFeeKhr();
        const deliveryFeeUsd = deliveryFeeKhr / exchangeRate;
        const total          = subtotal    + deliveryFeeUsd;
        const totalKhr       = subtotalKhr + deliveryFeeKhr;

        // Subtotal is shown before discount, so Subtotal - Discount + Delivery = Total reads correctly.
        $('#subtotal').text(grossSubtotal.toFixed(2));
        $('#subtotal_khr').text(Math.round(grossSubtotalKhr).toLocaleString());
        $('#discountAmount').text(totalDiscount.toFixed(2));
        $('#discountAmount_khr').text(Math.round(totalDiscountKhr).toLocaleString());
        $('#deliveryFeeUsd').text(deliveryFeeUsd.toFixed(2));
        $('#deliveryFeeKhr').text(Math.round(deliveryFeeKhr).toLocaleString());
        $('#totalAmount').text(total.toFixed(2));
        $('#totalAmount_khr').text(Math.round(totalKhr).toLocaleString());

        $('#subtotal_amount').val(subtotal.toFixed(2));
        $('#discount_amount').val(totalDiscount.toFixed(2));
        $('#delivery_fee_khr').val(deliveryFeeKhr.toFixed(2));
        $('#total_amount_input').val(total.toFixed(2));
    }

    /* ─── Sync cart JSON to hidden input ────────────────────── */
    function updateCartData() {
        const deliveryId = $('#delivery_select').val() || null;
        const orderItems = [];
        Object.entries(cart).forEach(([productId, item]) => {
            const disc         = parseFloat(item.discount || 0);
            const discPrice    = item.price * (1 - disc / 100);
            orderItems.push({
                product_id:       parseInt(productId),
                quantity:         item.qty,
                unit_price:       item.price,
                unit_price_khr:   item.price_khr,
                discount_percent: disc,
                total_price:      discPrice * item.qty,
                is_custom_price:  item.is_custom_price || false,
                delivery_id:      deliveryId ? parseInt(deliveryId) : null
            });
        });
        $('#order_items').val(JSON.stringify(orderItems));
    }

    /* ─── Form validation ───────────────────────────────────── */
    document.getElementById('orderForm').addEventListener('submit', function (e) {
        if (!document.getElementById('hidden_customer_id').value) {
            e.preventDefault();
            showToast('warning', '⚠️', 'សូមជ្រើសរើសអតិថិជន', 'សូមជ្រើសរើសអតិថិជនមុននឹងបញ្ជាទិញ');
            return false;
        }
        if (Object.keys(cart).length === 0) {
            e.preventDefault();
            showToast('warning', '🛒', 'សូមបន្ថែមទំនិញ', 'សូមជ្រើសរើសទំនិញ');
            return false;
        }
    });

    /* ─── Toast ─────────────────────────────────────────────── */
    function showToast(type, icon, title, message) {
        $('.toast-overlay, .toast-box').remove();
        const overlay = $('<div class="toast-overlay"></div>');
        const box = $(`
            <div class="toast-box">
                <div class="toast-icon ${type}">${icon}</div>
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
                <button class="toast-btn" onclick="closeToast()">យល់ព្រម</button>
            </div>`);
        $('body').append(overlay).append(box);
        requestAnimationFrame(() => { overlay.addClass('show'); box.addClass('show'); });
        overlay.on('click', closeToast);
    }

    function closeToast() {
        $('.toast-overlay').removeClass('show');
        $('.toast-box').removeClass('show');
        setTimeout(() => { $('.toast-overlay, .toast-box').remove(); }, 300);
    }

    /* ─── Free product rows ─────────────────────────────────── */
    function addFreeProductRow() {
        const rowId = freeProductCount++;
        const products = @json($products->where('allow_free', true)->values());
        let opts = '<option value="">សូមជ្រើសរើស</option>';
        products.forEach(p => { opts += `<option value="${p.id}">${p.name}</option>`; });

        $('#freeProductsContainer').append(`
            <div class="free-product-row" id="freeRow${rowId}">
                <select name="free_products[${rowId}][product_id]" class="form-control od-select">${opts}</select>
                <input type="number" name="free_products[${rowId}][qty]" class="form-control"
                    min="0" placeholder="ចំនួន" value="1">
                <button type="button" class="btn-remove-free" onclick="removeFreeProductRow(${rowId})">
                    <i class="fas fa-trash"></i> លុប
                </button>
            </div>`);
    }

    function removeFreeProductRow(rowId) {
        $(`#freeRow${rowId}`).slideUp(300, function () {
            $(this).remove();
            if ($('.free-product-row').length === 0) addFreeProductRow();
        });
    }

    // Payment method visibility and multi-select
    document.addEventListener('DOMContentLoaded', function() {
        const paymentStatusSelect = document.getElementById('payment_status');
        const paymentMethodField = document.getElementById('payment_method_field');
        const paymentMethodInput = document.getElementById('payment_method');
        const paymentMethodsArray = document.getElementById('payment_methods_array');
        const selectedMethodsDisplay = document.getElementById('selected_methods_tags');
        const paymentMethodBtns = document.querySelectorAll('.payment-method-btn');

        const methodIcons = {
            'Cash': 'fa-money-bill-wave',
            'ABA': 'fa-university',
            'ACLEDA': 'fa-university',
            'Wing': 'fa-mobile-alt'
        };

        const methodLabels = {
            'Cash': 'ប្រាក់សម្រាប់',
            'ABA': 'ABA',
            'ACLEDA': 'ACLEDA',
            'Wing': 'Wing'
        };

        let selectedMethods = [];

        function updatePaymentMethodsDisplay() {
            selectedMethodsDisplay.innerHTML = '';

            if (selectedMethods.length === 0) {
                selectedMethodsDisplay.innerHTML = '<div style="color: #9ca3af; font-size: 13px;">វិធីបង់ប្រាក់មិនបានជ្រើសរើស</div>';
                return;
            }

            selectedMethods.forEach((method, index) => {
                const tag = document.createElement('div');
                tag.className = 'method-tag';
                tag.innerHTML = `
                    <i class="fas ${methodIcons[method]}"></i>
                    <span>${methodLabels[method]}</span>
                    <span class="method-tag-close" onclick="removePaymentMethod('${method}')" title="ដក">×</span>
                `;
                selectedMethodsDisplay.appendChild(tag);
            });
        }

        window.removePaymentMethod = function(method) {
            selectedMethods = selectedMethods.filter(m => m !== method);
            updatePaymentSelection();
            updatePaymentMethodsDisplay();
        };

        function updatePaymentSelection() {
            const methodString = selectedMethods.join(' + ');
            paymentMethodInput.value = methodString;
            paymentMethodsArray.value = JSON.stringify(selectedMethods);

            paymentMethodBtns.forEach(btn => {
                const method = btn.getAttribute('data-method');
                const check = btn.querySelector('.method-check');
                if (selectedMethods.includes(method)) {
                    btn.classList.add('active');
                    check.style.display = 'flex';
                } else {
                    btn.classList.remove('active');
                    check.style.display = 'none';
                }
            });
        }

        function togglePaymentMethod() {
            if (paymentStatusSelect.value === 'paid') {
                paymentMethodField.style.display = 'block';
                paymentMethodInput.setAttribute('required', 'required');
            } else {
                paymentMethodField.style.display = 'none';
                paymentMethodInput.removeAttribute('required');
                selectedMethods = [];
                updatePaymentSelection();
                updatePaymentMethodsDisplay();
            }
        }

        // Button selection handlers - Toggle on click
        paymentMethodBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const method = this.getAttribute('data-method');

                if (selectedMethods.includes(method)) {
                    selectedMethods = selectedMethods.filter(m => m !== method);
                } else {
                    selectedMethods.push(method);
                }

                updatePaymentSelection();
                updatePaymentMethodsDisplay();
            });
        });

        if (paymentStatusSelect) {
            paymentStatusSelect.addEventListener('change', togglePaymentMethod);
            togglePaymentMethod();
        }

        updatePaymentMethodsDisplay();
    });
</script>
@endpush

@endsection