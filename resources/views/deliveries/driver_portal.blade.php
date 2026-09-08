<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $currentDelivery ? $currentDelivery->delivery_name : 'ផ្ទាំងអ្នកដឹក' }} - Pizza Happy Family</title>
    
    <!-- Google Font: Kantumruy Pro & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-orange: #e85d24;
            --brand-dark: #c2410c;
            --app-bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-heading: #0f172a;
            --text-body: #1e293b;
            --text-muted: #64748b;
            --border-color: #cbd5e1;
            --success-green: #15803d;
            --success-bg: #dcfce7;
            --call-blue: #0284c7;
            --nav-red: #dc2626;
            --radius-md: 14px;
            --radius-lg: 18px;
            --shadow-card: 0 4px 16px rgba(15, 23, 42, 0.08);
        }

        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: var(--app-bg);
            color: var(--text-body);
            font-family: 'Kantumruy Pro', 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Navigation Header */
        .driver-top-bar {
            background: #ffffff;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .app-wrapper {
            max-width: 500px;
            margin: 0 auto;
            padding: 14px 14px 60px;
        }

        /* Driver Selection Card */
        .driver-select-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: var(--text-heading);
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }
        .driver-select-card:active {
            background: #fff7ed;
            border-color: var(--brand-orange);
            transform: scale(0.98);
        }
        .driver-avatar-circle {
            width: 52px;
            height: 52px;
            background: #ffedd5;
            color: var(--brand-orange);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        /* Top Driver Profile Strip */
        .driver-profile-pill {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 12px 14px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }

        /* Quick Stats 2 Columns */
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 14px;
        }
        .stat-box {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: var(--radius-md);
            padding: 12px 8px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        .stat-box .stat-val {
            font-size: 26px;
            font-weight: 800;
            line-height: 1;
            font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif;
        }
        .stat-box .stat-lbl {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Auto GPS Live Status */
        .gps-banner {
            background: #f0fdf4;
            border: 2px solid #bbf7d0;
            border-radius: var(--radius-md);
            padding: 10px 14px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .live-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #16a34a;
            display: inline-block;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.2);
            animation: pulseGlow 1.8s infinite;
        }
        @keyframes pulseGlow {
            0% { transform: scale(0.9); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.5; }
            100% { transform: scale(0.9); opacity: 1; }
        }

        /* Filter Tab Switcher */
        .filter-tabs {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr;
            gap: 6px;
            background: #e2e8f0;
            padding: 5px;
            border-radius: 14px;
            margin-bottom: 16px;
        }
        .tab-btn {
            border: none;
            background: transparent;
            padding: 10px 6px;
            font-size: 13.5px;
            font-weight: 800;
            color: var(--text-muted);
            border-radius: 10px;
            transition: all 0.15s ease;
            text-align: center;
            cursor: pointer;
        }
        .tab-btn.active {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }

        /* Big Order Card */
        .delivery-card {
            background: #ffffff;
            border: 2px solid #cbd5e1;
            border-radius: var(--radius-lg);
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-card);
            position: relative;
        }
        .delivery-card.delivering {
            border: 2.5px solid var(--brand-orange);
            background: #ffffff;
        }
        .delivery-card.completed {
            border: 2px solid #86efac;
            background: #fafdfa;
            opacity: 0.9;
        }

        /* Card Header */
        .card-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            margin-bottom: 12px;
            border-bottom: 1.5px solid #f1f5f9;
        }
        .order-code-badge {
            background: #fff7ed;
            color: var(--brand-dark);
            border: 1.5px solid #fed7aa;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 15px;
            padding: 3px 10px;
            border-radius: 8px;
        }
        .order-status-pill {
            font-size: 12px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .order-status-pill.pending {
            background: #f1f5f9;
            color: #475569;
        }
        .order-status-pill.delivering {
            background: #ffedd5;
            color: #c2410c;
            border: 1px solid #fdba74;
        }
        .order-status-pill.completed {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }

        /* Customer & Address Details */
        .cust-name {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.3;
        }
        .cust-address-box {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            margin: 10px 0;
        }
        .cust-address-text {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            line-height: 1.4;
        }
        .landmark-badge {
            display: inline-block;
            background: #fef08a;
            color: #854d0e;
            font-weight: 700;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 6px;
            margin-top: 4px;
        }

        /* Items Details */
        .items-info-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 12px;
            padding: 0 4px;
        }

        /* Payment Banner - Ultra Clear for Low Knowledge */
        .payment-box {
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 14px;
        }
        /* When Unpaid / Need Cash */
        .payment-box.need-cash {
            background: #ecfdf5;
            border: 2px solid #34d399;
            color: #065f46;
        }
        .payment-box.need-cash .pay-label {
            font-size: 13px;
            font-weight: 800;
            color: #047857;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
        }
        .payment-box.need-cash .pay-amount {
            font-size: 22px;
            font-weight: 800;
            color: #065f46;
            font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif;
            line-height: 1.2;
        }
        .payment-box.need-cash .pay-khr {
            font-size: 15px;
            font-weight: 700;
            color: #047857;
        }

        /* When Already Paid Online */
        .payment-box.paid-online {
            background: #eff6ff;
            border: 2px solid #93c5fd;
            color: #1e40af;
        }
        .payment-box.paid-online .pay-label {
            font-size: 14px;
            font-weight: 800;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .payment-box.paid-online .pay-desc {
            font-size: 12px;
            font-weight: 600;
            color: #3b82f6;
            margin-top: 2px;
        }

        /* Big Action Buttons (Phone & Map) */
        .action-btn-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 12px;
        }
        .btn-call {
            background: #f0f9ff;
            border: 2px solid #bae6fd;
            color: var(--call-blue);
            font-size: 14.5px;
            font-weight: 800;
            padding: 12px 10px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            min-height: 50px;
            transition: all 0.15s ease;
        }
        .btn-call:active {
            background: #e0f2fe;
            border-color: #7dd3fc;
            transform: scale(0.98);
            color: #0369a1;
        }
        .btn-map {
            background: #fff1f2;
            border: 2px solid #fecdd3;
            color: var(--nav-red);
            font-size: 14.5px;
            font-weight: 800;
            padding: 12px 10px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            min-height: 50px;
            transition: all 0.15s ease;
        }
        .btn-map:active {
            background: #ffe4e6;
            border-color: #fda4af;
            transform: scale(0.98);
            color: #9f1239;
        }

        /* Workflow Main Big Action Button */
        .btn-workflow {
            width: 100%;
            min-height: 54px;
            font-size: 16px;
            font-weight: 800;
            border-radius: 14px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .btn-workflow:active {
            transform: scale(0.98);
        }

        /* 1. Start Delivery (Orange) */
        .btn-workflow.start {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(234, 88, 12, 0.35);
        }

        /* 2. Mark Delivered & Cash Collected (Green) */
        .btn-workflow.complete {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.35);
        }

        /* Completed Banner */
        .completed-notice {
            background: #f0fdf4;
            border: 1.5px solid #86efac;
            color: #166534;
            text-align: center;
            padding: 10px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
    </style>
</head>
<body>

    <!-- Sticky Top Header -->
    <div class="driver-top-bar">
        <div class="d-flex justify-content-between align-items-center" style="max-width: 500px; margin: 0 auto;">
            <div class="d-flex align-items-center gap-2">
                <span style="font-size: 22px;">🍕</span>
                <div>
                    <div class="fw-bold" style="font-size: 15px; color: var(--text-heading); line-height: 1.1;">Pizza Happy Family</div>
                    <div style="font-size: 11.5px; font-weight: 700; color: var(--text-muted);">
                        {{ $currentDelivery ? $currentDelivery->delivery_name : 'ផ្ទាំងអ្នកដឹក' }}
                    </div>
                </div>
            </div>

            @if($currentDelivery)
                <button type="button" class="btn btn-sm btn-light border fw-bold text-dark px-2 py-1" onclick="window.location.reload();" style="font-size: 12px; border-radius: 8px;">
                    <i class="fas fa-redo-alt text-primary me-1"></i> ទាញទិន្នន័យថ្មី
                </button>
            @endif
        </div>
    </div>

    <div class="app-wrapper">

        @if(!$selectedDeliveryId)
            <!-- SCREEN 1: Choose Driver (Clear & Large) -->
            <div class="text-center py-3 mb-2">
                <div style="font-size: 38px; margin-bottom: 6px;">🛵</div>
                <h4 class="fw-bold text-dark mb-1">ជ្រើសរើសឈ្មោះរបស់អ្នក</h4>
                <p class="text-muted" style="font-size: 13.5px; font-weight: 600;">ចុចលើឈ្មោះរបស់អ្នកដើម្បីមើលបញ្ជីនំត្រូវដឹក៖</p>
            </div>

            <div class="d-flex flex-column">
                @foreach($deliveries as $del)
                    <a href="{{ route('deliveries.driver-portal-delivery', $del->id) }}" class="driver-select-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="driver-avatar-circle">
                                🛵
                            </div>
                            <div>
                                <div class="fw-bold" style="font-size: 17px; color: #0f172a;">{{ $del->delivery_name }}</div>
                                <div class="text-muted" style="font-size: 12px; font-weight: 600;">
                                    កេសតូច ៛{{ number_format($del->delivery_price_khr) }} | កេសធំ ៛{{ number_format($del->delivery_price_khr_big) }}
                                </div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted fs-5"></i>
                    </a>
                @endforeach
            </div>

        @else
            <!-- SCREEN 2: Driver Orders View (Mobile Optimized) -->

            <!-- Driver Identity Pill -->
            <div class="driver-profile-pill">
                <div class="d-flex align-items-center gap-2">
                    <span style="font-size: 20px;">🛵</span>
                    <div>
                        <div class="text-muted" style="font-size: 11px; font-weight: 700;">អ្នកដឹកជញ្ជូន៖</div>
                        <div class="fw-bold text-dark" style="font-size: 16px;">{{ $currentDelivery->delivery_name }}</div>
                    </div>
                </div>
                <a href="{{ route('deliveries.driver-portal') }}" class="btn btn-sm btn-outline-secondary fw-bold px-2 py-1" style="font-size: 11px; border-radius: 8px;">
                    <i class="fas fa-user-friends me-1"></i> ប្តូរឈ្មោះ
                </a>
            </div>

            @php
                $totalOrdersCount = count($formattedOrders);
                $deliveredOrders = collect($formattedOrders)->filter(fn($o) => $o['status'] === 'completed');
                $deliveredCount = $deliveredOrders->count();
                $toDeliverOrders = collect($formattedOrders)->filter(fn($o) => $o['status'] !== 'completed');
                $toDeliverCount = $toDeliverOrders->count();
                $inTransitCount = collect($formattedOrders)->filter(fn($o) => $o['status'] === 'delivering')->count();
            @endphp

            <!-- Quick Stats Summary -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-val text-warning">{{ $toDeliverCount }}</div>
                    <div class="stat-lbl">🛵 ត្រូវដឹក (សល់)</div>
                </div>
                <div class="stat-box">
                    <div class="stat-val text-success">{{ $deliveredCount }}</div>
                    <div class="stat-lbl">✅ ដឹកដល់រួច</div>
                </div>
            </div>

            <!-- Auto GPS Status Indicator -->
            <div class="gps-banner">
                <div class="d-flex align-items-center gap-2">
                    <span class="live-dot"></span>
                    <div>
                        <div class="fw-bold" style="font-size: 13px; color: #166534;" id="gpsStatusText">
                            🟢 GPS បើកស្វ័យប្រវត្តិ (ចែករំលែកទីតាំង)
                        </div>
                        <div class="text-muted" style="font-size: 11px; font-weight: 600;" id="gpsDetailText">
                            ហាងកំពុងតាមដានទីតាំងអ្នកផ្ទាល់
                        </div>
                    </div>
                </div>
                <i class="fas fa-satellite-dish text-success fs-5 opacity-75"></i>
            </div>

            <!-- Filter Tabs: Default to "To Deliver" for zero clutter -->
            <div class="filter-tabs">
                <button type="button" class="tab-btn active" id="tab-delivering" onclick="setFilterTab('delivering')">
                    🛵 ត្រូវដឹក ({{ $toDeliverCount }})
                </button>
                <button type="button" class="tab-btn" id="tab-completed" onclick="setFilterTab('completed')">
                    ✅ ដឹកដល់ ({{ $deliveredCount }})
                </button>
                <button type="button" class="tab-btn" id="tab-all" onclick="setFilterTab('all')">
                    ទាំងអស់ ({{ $totalOrdersCount }})
                </button>
            </div>

            <!-- Orders Container -->
            <div id="ordersListContainer">
                @forelse($formattedOrders as $order)
                    @php
                        $isDelivering = $order['status'] === 'delivering';
                        $isCompleted = $order['status'] === 'completed';
                        $isPaid = ($order['payment_status'] ?? '') === 'paid';
                        
                        $destLat = $order['lat'] ?: 11.5564;
                        $destLng = $order['lng'] ?: 104.9282;
                        $gmapsUrl = "https://www.google.com/maps/dir/?api=1&origin={$depot['lat']},{$depot['lng']}&destination={$destLat},{$destLng}&travelmode=driving";
                    @endphp

                    <div class="delivery-card {{ $isDelivering ? 'delivering' : ($isCompleted ? 'completed' : '') }}" 
                         id="order-card-{{ $order['id'] }}" 
                         data-status="{{ $order['status'] }}">
                        
                        <!-- Top Bar: Code & Status -->
                        <div class="card-top-row">
                            <div class="d-flex align-items-center gap-2">
                                <span class="order-code-badge">{{ $order['order_code'] }}</span>
                                <span class="text-muted" style="font-size: 12px; font-weight: 700;">{{ $order['time'] }}</span>
                            </div>
                            <span class="order-status-pill {{ $isCompleted ? 'completed' : ($isDelivering ? 'delivering' : 'pending') }}" id="badge-{{ $order['id'] }}">
                                @if($isCompleted)
                                    <i class="fas fa-check-circle"></i> ដឹកដល់ហើយ
                                @elseif($isDelivering)
                                    <i class="fas fa-motorcycle"></i> កំពុងចេញដឹក
                                @else
                                    <i class="fas fa-clock"></i> កំពុងរង់ចាំ
                                @endif
                            </span>
                        </div>

                        <!-- Customer Info -->
                        <div class="mb-2">
                            <div class="cust-name">
                                👤 {{ $order['customer_name'] }}
                            </div>
                            
                            <div class="cust-address-box">
                                <div class="cust-address-text">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                    {{ $order['address'] }}
                                </div>
                                @if($order['landmark'])
                                    <div class="landmark-badge">
                                        📌 ចំណុចសម្គាល់៖ {{ $order['landmark'] }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Item Summary -->
                        <div class="items-info-row">
                            <span>🍕 <strong>{{ $order['box_qty'] }} កេស/ប្រអប់</strong></span>
                            @if($order['items_summary'])
                                <span class="text-muted">• {{ $order['items_summary'] }}</span>
                            @endif
                        </div>

                        <!-- Payment Collection Banner (Foolproof for Driver) -->
                        @if($isPaid)
                            <!-- Already Paid Online -->
                            <div class="payment-box paid-online">
                                <div class="pay-label">
                                    <i class="fas fa-check-circle fs-5"></i> បានបង់ប្រាក់រួច (មិនបាច់យកលុយទេ)
                                </div>
                                <div class="pay-desc">
                                    អតិថិជនបានបង់ប្រាក់រួចរាល់តាម Online / KHQR (${{ number_format($order['total_amount'], 2) }})
                                </div>
                            </div>
                        @else
                            <!-- Cash on Delivery -->
                            <div class="payment-box need-cash">
                                <div class="pay-label">
                                    <i class="fas fa-hand-holding-usd fs-5"></i> ត្រូវប្រមូលលុយពីភ្ញៀវ (Cash):
                                </div>
                                <div class="d-flex justify-content-between align-items-baseline">
                                    <div class="pay-amount">
                                        ${{ number_format($order['total_amount'], 2) }}
                                    </div>
                                    <div class="pay-khr">
                                        (៛{{ number_format($order['total_amount_khr']) }})
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons: One-Tap Call & Navigation -->
                        <div class="action-btn-row">
                            @if($order['customer_phone'] && $order['customer_phone'] !== '—')
                                <a href="tel:{{ $order['customer_phone'] }}" class="btn-call">
                                    <i class="fas fa-phone-alt fs-5"></i>
                                    <span>ខលទៅភ្ញៀវ</span>
                                </a>
                            @else
                                <div class="btn-call text-muted" style="opacity: 0.6; pointer-events: none;">
                                    <i class="fas fa-phone-slash fs-5"></i>
                                    <span>គ្មានលេខទូរស័ព្ទ</span>
                                </div>
                            @endif

                            <a href="{{ $gmapsUrl }}" target="_blank" class="btn-map">
                                <i class="fab fa-google fs-5"></i>
                                <span>នាំផ្លូវ Maps</span>
                            </a>
                        </div>

                        <!-- 1-Touch Stage Action Button -->
                        <div id="workflow-action-{{ $order['id'] }}">
                            @if(!$isDelivering && !$isCompleted)
                                <button type="button" class="btn-workflow start" onclick="updateOrderStatus({{ $order['id'] }}, 'delivering')">
                                    <i class="fas fa-motorcycle fs-4"></i>
                                    <span>🛵 ចាប់ផ្តើមចេញដឹក</span>
                                </button>
                            @elseif($isDelivering)
                                <button type="button" class="btn-workflow complete" onclick="updateOrderStatus({{ $order['id'] }}, 'completed')">
                                    <i class="fas fa-check-circle fs-4"></i>
                                    <span>✅ ដឹកដល់ហើយ (រួចរាល់)</span>
                                </button>
                            @else
                                <div class="completed-notice">
                                    <i class="fas fa-check-double fs-5"></i>
                                    <span>✓ បានដឹកដល់ដៃអតិថិជនរួចរាល់</span>
                                </div>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded-4 border p-4 shadow-sm">
                        <div style="font-size: 48px; margin-bottom: 10px;">🛵</div>
                        <h5 class="fw-bold text-dark">មិនទាន់មានកុម្ម៉ង់ត្រូវដឹកទេ</h5>
                        <p class="text-muted small mb-0">នៅពេលហាងចាត់ចែងការកុម្ម៉ង់ឱ្យអ្នក វានឹងលោតបង្ហាញនៅទីនេះ។</p>
                    </div>
                @endforelse
            </div>

        @endif

    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const SELECTED_DELIVERY_ID = {{ $selectedDeliveryId ?: 'null' }};
        const STORE_LAT = {{ $depot['lat'] ?: 11.5564 }};
        const STORE_LNG = {{ $depot['lng'] ?: 104.9282 }};

        let watchId = null;
        let sendTimer = null;
        let currentPos = { lat: STORE_LAT, lng: STORE_LNG, speed: 0 };

        // Auto Start Background GPS without driver needing to do anything
        function initAutoGps() {
            if (!SELECTED_DELIVERY_ID) return;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        currentPos = {
                            lat: pos.coords.latitude,
                            lng: pos.coords.longitude,
                            speed: pos.coords.speed ? (pos.coords.speed * 3.6).toFixed(1) : 0,
                            heading: pos.coords.heading || 0,
                            accuracy: pos.coords.accuracy || 10
                        };
                        updateGpsUi(currentPos.speed);
                        sendLocationToServer();
                    },
                    err => {
                        console.log("GPS Notice:", err.message);
                    },
                    { enableHighAccuracy: true, timeout: 8000 }
                );

                watchId = navigator.geolocation.watchPosition(
                    pos => {
                        currentPos = {
                            lat: pos.coords.latitude,
                            lng: pos.coords.longitude,
                            speed: pos.coords.speed ? (pos.coords.speed * 3.6).toFixed(1) : 0,
                            heading: pos.coords.heading || 0,
                            accuracy: pos.coords.accuracy || 10
                        };
                        updateGpsUi(currentPos.speed);
                    },
                    err => {
                        console.log("GPS Watch Notice:", err.message);
                    },
                    { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 }
                );
            }

            if (sendTimer) clearInterval(sendTimer);
            sendTimer = setInterval(sendLocationToServer, 5000);
            sendLocationToServer();
        }

        function updateGpsUi(speed) {
            const detail = document.getElementById('gpsDetailText');
            if (detail) {
                detail.innerText = speed > 0 ? `ល្បឿន៖ ${speed} km/h (កំពុងដំណើរការ)` : `GPS ភ្ជាប់រួចរាល់ (កំពុងរង់ចាំ)`;
            }
        }

        function sendLocationToServer() {
            if (!SELECTED_DELIVERY_ID) return;

            const url = `{{ route('deliveries.update-driver-location') }}`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    delivery_id: SELECTED_DELIVERY_ID,
                    lat: currentPos.lat,
                    lng: currentPos.lng,
                    speed: currentPos.speed,
                    heading: currentPos.heading || 0,
                    accuracy: currentPos.accuracy || 0
                })
            })
            .then(res => res.json())
            .then(data => console.log("GPS synced:", data.status))
            .catch(err => console.log("GPS Sync Notice:", err));
        }

        // Update Order Status (One-Tap Action)
        function updateOrderStatus(orderId, status) {
            const btn = event?.currentTarget;
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<i class="fas fa-spinner fa-spin fs-4"></i> <span>កំពុងរក្សាទុក...</span>`;
            }

            const url = `{{ url('/driver/orders') }}/${orderId}/status`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok') {
                    // Quick vibrate if on mobile device
                    if (navigator.vibrate) {
                        navigator.vibrate(100);
                    }
                    window.location.reload();
                } else {
                    alert("មានបញ្ហាក្នុងការរក្សាទុក!");
                    if (btn) btn.disabled = false;
                }
            })
            .catch(err => {
                alert("មានបញ្ហាក្នុងការភ្ជាប់អ៊ីនធឺណិត!");
                if (btn) btn.disabled = false;
            });
        }

        // Tab Filter
        function setFilterTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            const targetBtn = document.getElementById(`tab-${tab}`);
            if (targetBtn) targetBtn.classList.add('active');

            document.querySelectorAll('.delivery-card').forEach(card => {
                const status = card.dataset.status;
                if (tab === 'all') {
                    card.style.display = 'block';
                } else if (tab === 'delivering') {
                    card.style.display = (status !== 'completed') ? 'block' : 'none';
                } else if (tab === 'completed') {
                    card.style.display = (status === 'completed') ? 'block' : 'none';
                }
            });
        }

        // Auto initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            initAutoGps();
            // Default filter to "delivering" (pending & delivering) to reduce cognitive load
            setFilterTab('delivering');
        });
    </script>
</body>
</html>
