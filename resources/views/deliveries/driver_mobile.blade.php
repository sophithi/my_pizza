<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🛵 ដឹកជញ្ជូន #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} - Pizza Happy Family</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #e85d24;
            --primary-dark: #c2410c;
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --border-c: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #22c55e;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-bottom: 30px;
        }

        .header-bar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-c);
            padding: 14px 18px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .info-card {
            background: var(--card-bg);
            border: 1px solid var(--border-c);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .status-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-track {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            padding: 14px;
            border-radius: 12px;
            border: none;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 20px rgba(34, 197, 94, 0.4);
            transition: all 0.2s ease;
        }
        .btn-track.active {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.4);
        }

        .pulse-circle {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ffffff;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.4; }
            100% { transform: scale(0.9); opacity: 1; }
        }

        .btn-action {
            padding: 12px;
            font-weight: bold;
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .gps-status-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px dashed var(--border-c);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header-bar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="fs-4">🍕</span>
            <div>
                <div class="fw-bold fs-6">Pizza Happy Family</div>
                <div class="text-muted small" style="font-size: 11px;">ផ្ទាំងអ្នកដឹកជញ្ជូន (Driver Dispatch)</div>
            </div>
        </div>
        <div class="status-pill bg-primary bg-opacity-25 text-info border border-info border-opacity-25">
            #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <div class="container py-3" style="max-width: 500px;">

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if($order->status === 'completed')
            <div class="info-card text-center py-4">
                <div class="display-4 text-success mb-2"><i class="fas fa-check-circle"></i></div>
                <h5 class="fw-bold text-success">ការដឹកជញ្ជូនបានបញ្ចប់ជោគជ័យ!</h5>
                <p class="text-muted small mb-0">ការបញ្ជាទិញនេះត្រូវបានប្រគល់ជូនអតិថិជនរួចរាល់។</p>
            </div>
        @else
            <!-- Real-time GPS Tracker Button -->
            <div class="info-card">
                <button type="button" id="btnToggleGps" class="btn-track" onclick="toggleGpsTracking()">
                    <i class="fas fa-satellite-dish fs-5"></i>
                    <span id="btnTrackText">🟢 ចាប់ផ្តើមចែករំលែក GPS</span>
                </button>

                <div class="gps-status-box mt-3" id="gpsStatusBox">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>ស្ថានភាព GPS៖ <strong id="gpsStateText" class="text-warning">មិនទាន់បើក</strong></span>
                        <span id="gpsPulseDot" class="d-none"><span class="pulse-circle bg-success"></span></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1" style="font-size: 11px;">
                        <span>កូអរដោនេ៖ <span id="gpsCoordsText" class="text-light">—</span></span>
                        <span>ល្បឿន៖ <span id="gpsSpeedText" class="text-info">0 km/h</span></span>
                    </div>
                </div>
            </div>

            <!-- Customer & Delivery Info Card -->
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom border-secondary">
                    <div>
                        <div class="text-muted small" style="font-size: 11px;">អតិថិជន (Customer)</div>
                        <h5 class="fw-bold mb-0 text-white">{{ $customer?->name ?? 'Walk-in Customer' }}</h5>
                    </div>
                    @if($customer?->phone || $order->taxi_phone)
                        @php $phone = $customer?->phone ?? $order->taxi_phone; @endphp
                        <a href="tel:{{ $phone }}" class="btn btn-sm btn-outline-info px-3 fw-bold">
                            <i class="fas fa-phone-alt me-1"></i> ខល
                        </a>
                    @endif
                </div>

                <div class="mb-2">
                    <div class="text-muted small" style="font-size: 11px;">អាសយដ្ឋានដឹកជញ្ជូន៖</div>
                    <div class="fw-semibold text-light">{{ $customer?->address ?: 'Phnom Penh' }}</div>
                    @if($customer?->landmark)
                        <div class="badge bg-secondary text-light mt-1">📌 ចំណាំ៖ {{ $customer->landmark }}</div>
                    @endif
                </div>

                <!-- Navigation & Map Action -->
                @php 
                    $destLat = $custLat ?: 11.5564;
                    $destLng = $custLng ?: 104.9282;
                    $gmapsUrl = "https://www.google.com/maps/dir/?api=1&origin={$depot['lat']},{$depot['lng']}&destination={$destLat},{$destLng}&travelmode=driving";
                @endphp
                <div class="mt-3">
                    <a href="{{ $gmapsUrl }}" target="_blank" class="btn btn-danger btn-action w-100 shadow-sm">
                        <i class="fab fa-google"></i> បើក Google Maps នាំផ្លូវ (Navigate)
                    </a>
                </div>
            </div>

            <!-- Order Items & Price Summary -->
            <div class="info-card">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary">
                    <span class="text-muted small" style="font-size: 11px;">ទំនិញក្នុងកញ្ចប់ (Items)</span>
                    <span class="badge bg-info text-dark fw-bold">{{ $order->box_qty ?: 1 }} ប្រអប់</span>
                </div>

                <div class="small text-light mb-3">
                    @foreach($order->items as $item)
                        <div class="d-flex justify-content-between py-1 border-bottom border-dark">
                            <span>{{ $item->product?->name ?? 'Pizza Item' }} <small class="text-muted">x{{ $item->quantity }}</small></span>
                            <span class="fw-semibold">${{ number_format($item->total_price, 2) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between align-items-center pt-1">
                    <span class="fw-bold">ទឹកប្រាក់សរុប (Total)៖</span>
                    <div class="text-end">
                        <div class="fs-5 fw-bold text-warning">${{ number_format($order->total_amount, 2) }}</div>
                        <div class="text-muted small" style="font-size: 11px;">៛{{ number_format($order->totalKhr()) }}</div>
                    </div>
                </div>
            </div>

            <!-- Complete Delivery Button -->
            <div class="mt-4">
                <button type="button" class="btn btn-success btn-action w-100 py-3 shadow" onclick="confirmCompleteOrder()">
                    <i class="fas fa-check-double fs-5"></i>
                    <span>✅ បានដឹកជញ្ជូនដល់ដៃអតិថិជនរួចរាល់ (Delivered)</span>
                </button>
            </div>
        @endif

    </div>

    <!-- Complete Order Modal -->
    <div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light border-secondary">
                <div class="modal-header border-secondary">
                    <h6 class="modal-title fw-bold">✅ បញ្ជាក់ការដឹកជញ្ជូន</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="fs-1 text-success mb-2"><i class="fas fa-box-open"></i></div>
                    <p class="mb-1 fw-bold">តើអ្នកពិតជាបានប្រគល់ទំនិញ និងទូទាត់ប្រាក់រួចរាល់មែនទេ?</p>
                    <small class="text-muted">លេខកូដ៖ #ORD-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</small>
                </div>
                <div class="modal-footer border-secondary d-flex gap-2">
                    <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">មិនទាន់</button>
                    <button type="button" id="btnConfirmComplete" class="btn btn-success w-50 fw-bold" onclick="submitCompleteOrder()">
                        យល់ព្រម
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const ORDER_ID = {{ $order->id }};
        const DELIVERY_ID = {{ $order->delivery_id ?: 'null' }};
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        let watchId = null;
        let isTracking = false;
        let sendInterval = null;
        let lastCoords = null;

        function toggleGpsTracking() {
            if (isTracking) {
                stopGpsTracking();
            } else {
                startGpsTracking();
            }
        }

        function startGpsTracking() {
            if (!navigator.geolocation) {
                alert("ទូរស័ព្ទរបស់អ្នកមិនគាំទ្រ GPS ទេ!");
                return;
            }

            document.getElementById('gpsStateText').innerText = "កំពុងស្វែងរក GPS...";
            document.getElementById('gpsStateText').className = "text-info";

            watchId = navigator.geolocation.watchPosition(
                function(position) {
                    lastCoords = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        speed: position.coords.speed ? (position.coords.speed * 3.6).toFixed(1) : 0,
                        heading: position.coords.heading || 0,
                        accuracy: position.coords.accuracy || 0
                    };

                    document.getElementById('gpsCoordsText').innerText = `${lastCoords.lat.toFixed(5)}, ${lastCoords.lng.toFixed(5)}`;
                    document.getElementById('gpsSpeedText').innerText = `${lastCoords.speed} km/h`;
                    document.getElementById('gpsStateText').innerText = "🟢 កំពុងចែករំលែកបន្តផ្ទាល់";
                    document.getElementById('gpsStateText').className = "text-success fw-bold";
                    document.getElementById('gpsPulseDot').classList.remove('d-none');
                },
                function(error) {
                    document.getElementById('gpsStateText').innerText = "បរាជ័យ៖ " + error.message;
                    document.getElementById('gpsStateText').className = "text-danger";
                },
                { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 }
            );

            // Send live coordinates to server every 5 seconds
            sendInterval = setInterval(sendLocationToServer, 5000);

            isTracking = true;
            document.getElementById('btnToggleGps').classList.add('active');
            document.getElementById('btnTrackText').innerText = "🔴 បញ្ឈប់ការចែករំលែក GPS";
        }

        function stopGpsTracking() {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            if (sendInterval) {
                clearInterval(sendInterval);
                sendInterval = null;
            }

            isTracking = false;
            document.getElementById('btnToggleGps').classList.remove('active');
            document.getElementById('btnTrackText').innerText = "🟢 ចាប់ផ្តើមចែករំលែក GPS";
            document.getElementById('gpsStateText').innerText = "បានផ្អាក";
            document.getElementById('gpsStateText').className = "text-warning";
            document.getElementById('gpsPulseDot').classList.add('d-none');
        }

        function sendLocationToServer() {
            if (!lastCoords) return;

            const url = `{{ route('deliveries.driver-location', $order->id) }}`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    order_id: ORDER_ID,
                    delivery_id: DELIVERY_ID,
                    lat: lastCoords.lat,
                    lng: lastCoords.lng,
                    speed: lastCoords.speed,
                    heading: lastCoords.heading,
                    accuracy: lastCoords.accuracy
                })
            })
            .then(res => res.json())
            .then(data => {
                console.log("GPS Location Pushed:", data);
            })
            .catch(err => console.error("Error sending GPS:", err));
        }

        function confirmCompleteOrder() {
            const modal = new bootstrap.Modal(document.getElementById('completeModal'));
            modal.show();
        }

        function submitCompleteOrder() {
            const btn = document.getElementById('btnConfirmComplete');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>...';

            stopGpsTracking();

            const url = `{{ route('deliveries.driver-complete', $order->id) }}`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || "✅ បានដឹកជញ្ជូនដល់ដៃអតិថិជនរួចរាល់!");
                window.location.reload();
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'យល់ព្រម';
                alert("មានបញ្ហាក្នុងការរក្សាទុក!");
            });
        }
    </script>
</body>
</html>
