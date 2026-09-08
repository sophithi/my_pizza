<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🛵 {{ $currentDelivery ? $currentDelivery->delivery_name : 'ផ្ទាំងអ្នកដឹកជញ្ជូន' }} - Pizza Happy Family</title>
    
    <!-- Google Font: Inter / Kantumruy Pro -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-orange: #e85d24;
            --brand-dark: #c2410c;
            --app-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --success-green: #16a34a;
            --success-light: #f0fdf4;
            --radius-md: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 6px 20px rgba(15, 23, 42, 0.07);
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

        /* Top App Bar */
        .driver-top-bar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-light);
            padding: 12px 16px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }

        /* App Wrapper */
        .app-wrapper {
            max-width: 520px;
            margin: 0 auto;
            padding: 14px 14px 40px;
        }

        /* Driver Selection Card */
        .driver-select-card {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 16px 18px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: var(--text-heading);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }
        .driver-select-card:hover {
            border-color: var(--brand-orange);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            color: var(--brand-orange);
        }

        /* Stats Row */
        .stats-banner {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: 10px 8px;
            text-align: center;
            box-shadow: var(--shadow-sm);
        }
        .stat-card .val {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-heading);
            line-height: 1.2;
        }
        .stat-card .lbl {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* GPS Status Banner */
        .gps-status-card {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 12px 14px;
            margin-bottom: 14px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .btn-gps-toggle {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 10px;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-gps-toggle:hover {
            transform: translateY(-1px);
        }
        .btn-gps-toggle.active {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        }

        .live-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            animation: dotGlow 1.5s infinite;
        }
        @keyframes dotGlow {
            0% { transform: scale(0.9); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.4; }
            100% { transform: scale(0.9); opacity: 1; }
        }

        /* Tab Filter Pills */
        .filter-pills {
            display: flex;
            gap: 6px;
            background: #e2e8f0;
            padding: 4px;
            border-radius: 12px;
            margin-bottom: 16px;
        }
        .filter-pill-btn {
            flex: 1;
            padding: 8px 6px;
            font-size: 12.5px;
            font-weight: 700;
            border: none;
            background: transparent;
            color: var(--text-muted);
            border-radius: 8px;
            transition: all 0.15s ease;
            text-align: center;
        }
        .filter-pill-btn.active {
            background: #ffffff;
            color: var(--text-heading);
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        /* Order Delivery Card */
        .order-card {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
            position: relative;
        }
        .order-card:hover {
            border-color: #cbd5e1;
            box-shadow: var(--shadow-md);
        }
        .order-card.delivering {
            border-left: 4px solid var(--brand-orange);
            background: #fffdfb;
        }
        .order-card.completed {
            border-left: 4px solid var(--success-green);
            opacity: 0.85;
            background: #fdfdfd;
        }

        /* Status Badge */
        .status-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .status-badge.pending {
            background: #f1f5f9;
            color: #475569;
        }
        .status-badge.delivering {
            background: #fff7ed;
            color: var(--brand-orange);
            border: 1px solid #fed7aa;
        }
        .status-badge.completed {
            background: var(--success-light);
            color: var(--success-green);
            border: 1px solid #bbf7d0;
        }

        /* Money & Items Summary Box */
        .order-summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 12px;
            margin: 12px 0;
        }

        .cash-tag {
            font-size: 17px;
            font-weight: 800;
            color: #15803d;
        }

        /* Action Buttons */
        .btn-action-call {
            background: #f0f9ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            font-weight: 700;
            font-size: 13px;
            border-radius: 10px;
            padding: 9px 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex: 1;
            transition: all 0.15s ease;
        }
        .btn-action-call:hover {
            background: #e0f2fe;
            color: #0369a1;
        }

        .btn-action-nav {
            background: #ffffff;
            color: #dc2626;
            border: 1px solid #fca5a5;
            font-weight: 700;
            font-size: 13px;
            border-radius: 10px;
            padding: 9px 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex: 1;
            transition: all 0.15s ease;
        }
        .btn-action-nav:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        .btn-main-workflow {
            width: 100%;
            padding: 12px;
            font-size: 14.5px;
            font-weight: 800;
            border-radius: 12px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .btn-main-workflow.start {
            background: linear-gradient(135deg, var(--brand-orange), var(--brand-dark));
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(232, 93, 36, 0.3);
        }
        .btn-main-workflow.start:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(232, 93, 36, 0.4);
        }
        .btn-main-workflow.complete {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.3);
        }
        .btn-main-workflow.complete:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(22, 163, 74, 0.4);
        }
    </style>
</head>
<body>

    <!-- Sticky Top Navigation Bar -->
    <div class="driver-top-bar">
        <div class="d-flex justify-content-between align-items-center" style="max-width: 520px; margin: 0 auto;">
            <div class="d-flex align-items-center gap-2">
                <span class="fs-4">🍕</span>
                <div>
                    <div class="fw-bold" style="font-size: 15px; color: var(--text-heading); line-height: 1.1;">Pizza Happy Family</div>
                    <div class="text-muted" style="font-size: 11px; font-weight: 600;">
                        {{ $currentDelivery ? $currentDelivery->delivery_name : 'ផ្ទាំងអ្នកដឹកជញ្ជូន' }}
                    </div>
                </div>
            </div>

            @if($currentDelivery)
                <a href="{{ route('deliveries.driver-portal') }}" class="btn btn-sm btn-outline-secondary py-1 px-2 fw-bold" style="font-size: 11px;">
                    <i class="fas fa-sync-alt me-1"></i> ប្តូរអ្នកដឹក
                </a>
            @endif
        </div>
    </div>

    <!-- Main Content App Wrapper -->
    <div class="app-wrapper">

        @if(!$selectedDeliveryId)
            <!-- SCREEN 1: Driver Picker Screen when opening /driver directly -->
            <div class="text-center py-3 mb-2">
                <h5 class="fw-bold text-dark">🛵 សូមជ្រើសរើសអ្នកដឹកជញ្ជូន</h5>
                <p class="text-muted small">ចុចលើឈ្មោះរបស់អ្នកដើម្បីបើកមើលបញ្ជីកុម្ម៉ង់ផ្ទាល់ខ្លួន៖</p>
            </div>

            <div class="d-flex flex-column gap-2">
                @foreach($deliveries as $del)
                    <a href="{{ route('deliveries.driver-portal-delivery', $del->id) }}" class="driver-select-card">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-warning bg-opacity-25 p-2 text-warning fs-5">
                                🛵
                            </div>
                            <div>
                                <div class="fw-bold fs-6">{{ $del->delivery_name }}</div>
                                <div class="text-muted small" style="font-size: 11px;">
                                    កេសតូច ៛{{ number_format($del->delivery_price_khr) }} | កេសធំ ៛{{ number_format($del->delivery_price_khr_big) }}
                                </div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                @endforeach
            </div>

        @else
            <!-- SCREEN 2: Dedicated Order Dispatch Screen for $currentDelivery -->
            
            <!-- Dedicated Driver Banner -->
            <div class="p-3 mb-3 bg-white rounded-3 border d-flex justify-content-between align-items-center shadow-sm">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-warning bg-opacity-25 p-2 text-warning fs-5">
                        🛵
                    </div>
                    <div>
                        <div class="text-muted small" style="font-size: 11px;">ផ្ទាំងកុម្ម៉ង់ផ្ទាល់ខ្លួនសម្រាប់អ្នកដឹក៖</div>
                        <div class="fw-bold fs-6 text-dark">{{ $currentDelivery->delivery_name }}</div>
                    </div>
                </div>
                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-2 py-1" style="font-size: 11px;">
                    <i class="fas fa-check-circle me-1"></i> ផ្ទាល់ខ្លួន
                </span>
            </div>

            <!-- Quick Summary Stats -->
            @php
                $totalOrdersCount = count($formattedOrders);
                $inTransitCount = collect($formattedOrders)->filter(fn($o) => $o['status'] === 'delivering')->count();
                $totalCashKhr = collect($formattedOrders)->sum('total_amount_khr');
                $totalCashUsd = collect($formattedOrders)->sum('total_amount');
            @endphp
            <div class="stats-banner">
                <div class="stat-card">
                    <div class="val text-primary">{{ $totalOrdersCount }}</div>
                    <div class="lbl">កុម្ម៉ង់សរុប</div>
                </div>
                <div class="stat-card">
                    <div class="val text-warning">{{ $inTransitCount }}</div>
                    <div class="lbl">កំពុងចេញដឹក</div>
                </div>
                <div class="stat-card">
                    <div class="val text-success">${{ number_format($totalCashUsd, 2) }}</div>
                    <div class="lbl">លុយសរុប (Cash)</div>
                </div>
            </div>

            <!-- Master GPS Live Sharing Card -->
            <div class="gps-status-card">
                <div class="d-flex align-items-center gap-2">
                    <div class="live-pulse-dot" id="gpsPulseDot" style="display: none;"></div>
                    <div>
                        <div class="fw-bold" style="font-size: 13px; color: var(--text-heading);" id="gpsTitleText">
                            📍 ទីតាំង GPS បន្តផ្ទាល់
                        </div>
                        <div class="text-muted" style="font-size: 11px;" id="gpsSubtitleText">
                            ចែករំលែកទីតាំងជាមួយ Admin Map
                        </div>
                    </div>
                </div>

                <button type="button" id="btnToggleGps" class="btn-gps-toggle" onclick="toggleGpsSharing()">
                    <i class="fas fa-satellite-dish"></i>
                    <span id="btnGpsLabel">បើក GPS</span>
                </button>
            </div>

            <!-- Filter Tab Pills -->
            <div class="filter-pills">
                <button type="button" class="filter-pill-btn active" id="tab-all" onclick="setFilterTab('all')">
                    ទាំងអស់ ({{ $totalOrdersCount }})
                </button>
                <button type="button" class="filter-pill-btn" id="tab-delivering" onclick="setFilterTab('delivering')">
                    🛵 ត្រូវដឹក ({{ $totalOrdersCount - collect($formattedOrders)->filter(fn($o) => $o['status'] === 'completed')->count() }})
                </button>
                <button type="button" class="filter-pill-btn" id="tab-completed" onclick="setFilterTab('completed')">
                    ✓ ដឹកដល់ ({{ collect($formattedOrders)->filter(fn($o) => $o['status'] === 'completed')->count() }})
                </button>
            </div>

            <!-- Orders List Queue -->
            <div id="ordersListContainer">
                @forelse($formattedOrders as $order)
                    @php
                        $isDelivering = $order['status'] === 'delivering';
                        $isCompleted = $order['status'] === 'completed';
                        $destLat = $order['lat'] ?: 11.5564;
                        $destLng = $order['lng'] ?: 104.9282;
                        $gmapsUrl = "https://www.google.com/maps/dir/?api=1&origin={$depot['lat']},{$depot['lng']}&destination={$destLat},{$destLng}&travelmode=driving";
                    @endphp
                    <div class="order-card {{ $isDelivering ? 'delivering' : ($isCompleted ? 'completed' : '') }}" 
                         id="order-card-{{ $order['id'] }}" data-status="{{ $order['status'] }}">
                        
                        <!-- Header Row -->
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold" style="font-size: 13.5px; color: var(--brand-orange);">{{ $order['order_code'] }}</span>
                                <span class="text-muted small" style="font-size: 11px;">• {{ $order['time'] }}</span>
                            </div>
                            <span class="status-badge {{ $isCompleted ? 'completed' : ($isDelivering ? 'delivering' : 'pending') }}" id="badge-{{ $order['id'] }}">
                                @if($isCompleted) <i class="fas fa-check-circle"></i> @elseif($isDelivering) <i class="fas fa-motorcycle"></i> @endif
                                {{ $order['status_kh'] }}
                            </span>
                        </div>

                        <!-- Customer Info -->
                        <div class="mb-2">
                            <div class="fw-bold" style="font-size: 15px; color: var(--text-heading);">
                                {{ $order['customer_name'] }}
                            </div>
                            <div class="text-muted mt-1" style="font-size: 12.5px;">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $order['address'] }}
                                @if($order['landmark'])
                                    <span class="badge bg-light text-dark border ms-1 fw-bold" style="font-size: 10.5px;">📌 {{ $order['landmark'] }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Money & Items Summary -->
                        <div class="order-summary-box">
                            <div class="d-flex justify-content-between align-items-center text-muted small" style="font-size: 11.5px;">
                                <span>ទំនិញ៖ <strong class="text-dark">{{ $order['items_summary'] ?: ($order['box_qty'] . ' ប្រអប់') }}</strong></span>
                                <span>ចំនួន៖ <strong class="text-dark">{{ $order['box_qty'] }} កេស/ប្រអប់</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                <span class="fw-bold" style="font-size: 12.5px; color: var(--text-heading);">💵 ប្រាក់ត្រូវប្រមូល (Cash):</span>
                                <span class="cash-tag">
                                    ${{ number_format($order['total_amount'], 2) }} 
                                    <small class="text-muted fw-normal" style="font-size: 12px;">(៛{{ number_format($order['total_amount_khr']) }})</small>
                                </span>
                            </div>
                        </div>

                        <!-- Direct Contact & Map Navigation Buttons -->
                        <div class="d-flex gap-2 mb-3">
                            @if($order['customer_phone'] && $order['customer_phone'] !== '—')
                                <a href="tel:{{ $order['customer_phone'] }}" class="btn-action-call">
                                    <i class="fas fa-phone-alt"></i> ខល {{ $order['customer_phone'] }}
                                </a>
                            @endif
                            <a href="{{ $gmapsUrl }}" target="_blank" class="btn-action-nav">
                                <i class="fab fa-google"></i> Google Maps នាំផ្លូវ
                            </a>
                        </div>

                        <!-- Workflow Stage Action Button -->
                        <div id="workflow-action-{{ $order['id'] }}">
                            @if(!$isDelivering && !$isCompleted)
                                <button type="button" class="btn-main-workflow start" onclick="updateOrderStatus({{ $order['id'] }}, 'delivering')">
                                    <i class="fas fa-motorcycle fs-5"></i>
                                    <span>🛵 ចាប់ផ្តើមចេញដឹក (Start Delivery)</span>
                                </button>
                            @elseif($isDelivering)
                                <button type="button" class="btn-main-workflow complete" onclick="updateOrderStatus({{ $order['id'] }}, 'completed')">
                                    <i class="fas fa-check-double fs-5"></i>
                                    <span>✅ បានដឹកដល់ & ប្រមូលប្រាក់ (Delivered & Paid)</span>
                                </button>
                            @else
                                <div class="text-center py-2 bg-light rounded text-success fw-bold small" style="font-size: 12.5px;">
                                    <i class="fas fa-check-circle me-1"></i> បានប្រគល់ភីហ្សា និងទទួលប្រាក់រួចរាល់
                                </div>
                            @endif
                        </div>

                    </div>
                @empty
                    <div class="text-center py-5 bg-white rounded-3 border p-4">
                        <i class="fas fa-box-open fs-1 text-muted opacity-25 mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark">មិនមានការបញ្ជាទិញសម្រាប់ {{ $currentDelivery->delivery_name }} ទេ</h6>
                        <p class="text-muted small mb-0">នៅពេលមានការកុម្ម៉ង់ចាត់ចែងឱ្យអ្នកដឹកនេះ វានឹងបង្ហាញនៅទីនេះដោយស្វ័យប្រវត្តិ។</p>
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
        const HAS_ACTIVE_DELIVERY = {{ collect($formattedOrders)->where('status', 'delivering')->count() > 0 ? 'true' : 'false' }};

        let isGpsActive = false;
        let watchId = null;
        let sendTimer = null;
        let currentPos = { lat: STORE_LAT, lng: STORE_LNG, speed: 0 };

        function toggleGpsSharing() {
            if (isGpsActive) {
                stopGps();
            } else {
                startGps();
            }
        }

        function startGps() {
            isGpsActive = true;
            try { localStorage.setItem('driver_gps_active', '1'); } catch(e){}

            const btn = document.getElementById('btnToggleGps');
            if (btn) btn.classList.add('active');
            const lbl = document.getElementById('btnGpsLabel');
            if (lbl) lbl.innerText = "បិទ GPS";
            const title = document.getElementById('gpsTitleText');
            if (title) title.innerText = "🟢 កំពុងចែករំលែក Live";
            const dot = document.getElementById('gpsPulseDot');
            if (dot) dot.style.display = 'block';

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
                        const sub = document.getElementById('gpsSubtitleText');
                        if (sub) sub.innerText = `ល្បឿន៖ ${currentPos.speed} km/h (GPS Live)`;
                        sendLocationToServer();
                    },
                    err => {
                        console.log("GPS single pos notice:", err.message);
                        fallbackLocationBroadcast();
                    },
                    { enableHighAccuracy: true, timeout: 6000 }
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
                        const sub = document.getElementById('gpsSubtitleText');
                        if (sub) sub.innerText = `ល្បឿន៖ ${currentPos.speed} km/h (GPS Live)`;
                    },
                    err => {
                        console.log("GPS watch warning:", err.message);
                        fallbackLocationBroadcast();
                    },
                    { enableHighAccuracy: true, maximumAge: 0, timeout: 8000 }
                );
            } else {
                fallbackLocationBroadcast();
            }

            if (sendTimer) clearInterval(sendTimer);
            sendTimer = setInterval(sendLocationToServer, 4000);
            sendLocationToServer();
        }

        function fallbackLocationBroadcast() {
            const sub = document.getElementById('gpsSubtitleText');
            if (sub) sub.innerText = `Live Connected (Server Sync)`;
        }

        function stopGps() {
            isGpsActive = false;
            try { localStorage.removeItem('driver_gps_active'); } catch(e){}

            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            if (sendTimer) {
                clearInterval(sendTimer);
                sendTimer = null;
            }
            const btn = document.getElementById('btnToggleGps');
            if (btn) btn.classList.remove('active');
            const lbl = document.getElementById('btnGpsLabel');
            if (lbl) lbl.innerText = "បើក GPS";
            const title = document.getElementById('gpsTitleText');
            if (title) title.innerText = "📍 ទីតាំង GPS បន្តផ្ទាល់";
            const sub = document.getElementById('gpsSubtitleText');
            if (sub) sub.innerText = "ចែករំលែកទីតាំងជាមួយ Admin Map";
            const dot = document.getElementById('gpsPulseDot');
            if (dot) dot.style.display = 'none';
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
            .then(data => console.log("Location sent successfully:", data))
            .catch(err => console.log("Sync notice:", err));
        }

        function updateOrderStatus(orderId, status) {
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
                    if (status === 'delivering') {
                        try { localStorage.setItem('driver_gps_active', '1'); } catch(e){}
                    }
                    window.location.reload();
                }
            })
            .catch(err => alert("មានបញ្ហាក្នុងការកែប្រែស្ថានភាព!"));
        }

        function setFilterTab(tab) {
            document.querySelectorAll('.filter-pill-btn').forEach(btn => btn.classList.remove('active'));
            const targetBtn = document.getElementById(`tab-${tab}`);
            if (targetBtn) targetBtn.classList.add('active');

            document.querySelectorAll('.order-card').forEach(card => {
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

        // Auto-start GPS if previously active or if this driver currently has delivering orders
        document.addEventListener('DOMContentLoaded', function() {
            if (SELECTED_DELIVERY_ID) {
                const wasGpsActive = localStorage.getItem('driver_gps_active') === '1';
                if (wasGpsActive || HAS_ACTIVE_DELIVERY) {
                    startGps();
                }
            }
        });
    </script>
</body>
</html>
