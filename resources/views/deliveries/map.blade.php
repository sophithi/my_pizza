@extends('layouts.app')

@section('title', 'ផែនទីដឹកជញ្ជូនបន្តផ្ទាល់ (Live Delivery Tracking Map)')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
<style>
:root {
    --map-bg: #0f172a;
    --map-panel: #1e293b;
    --map-panel-alt: #334155;
    --map-border: #475569;
    --map-text: #f8fafc;
    --map-text-dim: #94a3b8;
    --map-accent: #f97316;
    --map-accent-2: #38bdf8;
    --map-good: #22c55e;
    --map-danger: #ef4444;
    --map-radius: 12px;
}

.live-map-page {
    padding: 6px 12px;
}

.live-map-container {
    height: calc(100vh - 120px);
    min-height: 600px;
    display: flex;
    flex-direction: column;
    background: var(--map-panel);
    color: var(--map-text);
    border-radius: var(--map-radius);
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    border: 1px solid var(--map-border);
}

/* Map Header */
.map-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 14px;
    background: var(--map-panel);
    border-bottom: 1px solid var(--map-border);
    flex-shrink: 0;
    gap: 8px;
    flex-wrap: wrap;
    z-index: 10;
}

.map-header-title {
    display: flex;
    align-items: center;
    gap: 8px;
}

.live-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
    color: var(--map-good);
    background: rgba(34, 197, 94, 0.15);
    padding: 2px 8px;
    border-radius: 999px;
    border: 1px solid rgba(34, 197, 94, 0.35);
}

.live-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--map-good);
    animation: pulseGlow 1.6s infinite;
}

@keyframes pulseGlow {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.3; transform: scale(0.85); }
    100% { opacity: 1; transform: scale(1); }
}

/* Layout */
.map-body {
    flex: 1;
    display: flex;
    overflow: hidden;
    position: relative;
    height: calc(100% - 50px);
}

#map-view {
    flex: 1;
    height: 100%;
    width: 100%;
    min-height: 540px;
    background: #1e293b;
    z-index: 1;
}

/* Sidebar */
.map-sidebar {
    width: 380px;
    flex-shrink: 0;
    background: var(--map-panel);
    border-left: 1px solid var(--map-border);
    overflow-y: auto;
    padding: 10px;
    z-index: 2;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.map-sidebar::-webkit-scrollbar {
    width: 5px;
}
.map-sidebar::-webkit-scrollbar-thumb {
    background: var(--map-border);
    border-radius: 4px;
}

/* Stats */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
}

.stat-box {
    background: var(--map-panel-alt);
    border: 1px solid var(--map-border);
    border-radius: 8px;
    padding: 6px 4px;
    text-align: center;
}
.stat-box .num {
    font-size: 16px;
    font-weight: 800;
    line-height: 1.2;
}
.stat-box .lbl {
    font-size: 9.5px;
    color: var(--map-text-dim);
    margin-top: 2px;
    font-weight: 600;
}

/* Scope Filter Pills */
.scope-pills {
    display: flex;
    gap: 4px;
    background: rgba(15, 23, 42, 0.6);
    padding: 3px;
    border-radius: 8px;
    border: 1px solid var(--map-border);
}

.scope-btn {
    font-size: 10.5px;
    padding: 3px 8px;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: var(--map-text-dim);
    cursor: pointer;
    font-weight: 600;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.scope-btn:hover {
    color: #fff;
}
.scope-btn.active {
    background: var(--map-accent);
    color: #fff;
    font-weight: bold;
}

/* Filter buttons */
.map-btn-group {
    display: flex;
    gap: 4px;
}
.map-btn {
    font-size: 10.5px;
    padding: 4px 8px;
    border-radius: 6px;
    border: 1px solid var(--map-border);
    background: var(--map-panel-alt);
    color: var(--map-text);
    cursor: pointer;
    font-weight: 600;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.map-btn:hover, .map-btn.active {
    border-color: var(--map-accent);
    color: #fff;
    background: rgba(249, 115, 22, 0.25);
}

/* Section Title */
.sec-title {
    font-size: 11px;
    font-weight: 700;
    color: var(--map-text-dim);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 5px;
    margin: 2px 0 0;
}

/* Cards */
.deliv-card {
    background: var(--map-panel-alt);
    border: 1px solid var(--map-border);
    border-radius: 8px;
    padding: 8px 10px;
    cursor: pointer;
    transition: all 0.15s ease;
    position: relative;
    margin-bottom: 6px;
}
.deliv-card:hover {
    border-color: var(--map-accent);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(0,0,0,0.35);
}
.deliv-card.active {
    border-color: var(--map-accent-2);
    box-shadow: 0 0 0 1.5px rgba(56, 189, 248, 0.5);
    background: rgba(30, 41, 59, 0.95);
}
.deliv-card .r1 {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.deliv-card .cust-name {
    font-size: 12px;
    font-weight: 700;
    color: #fff;
}
.deliv-card .order-badge {
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 4px;
    background: rgba(56, 189, 248, 0.15);
    color: var(--map-accent-2);
    border: 1px solid rgba(56, 189, 248, 0.3);
}
.deliv-card .area-text {
    font-size: 10.5px;
    color: var(--map-text-dim);
    margin-top: 2px;
}

/* Custom Marker Pins */
.marker-pin {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50% 50% 50% 0;
    transform: rotate(-45deg);
    box-shadow: 0 3px 10px rgba(0,0,0,0.6);
    border: 2px solid #ffffff;
}
.marker-pin .inner {
    transform: rotate(45deg);
    font-size: 12px;
    color: #fff;
    font-weight: bold;
}

.marker-vehicle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.45), 0 4px 12px rgba(0,0,0,0.5);
    border: 2px solid #ffffff;
    font-size: 18px;
    cursor: pointer;
}

.marker-driver-real {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.4), 0 4px 12px rgba(0,0,0,0.6);
    border: 2px solid #ffffff;
    font-size: 20px;
    animation: livePulse 2s infinite;
}

@keyframes livePulse {
    0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
    100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

/* Custom Popup Style */
.leaflet-popup-content-wrapper {
    background: var(--map-panel) !important;
    color: var(--map-text) !important;
    border-radius: 10px !important;
    border: 1px solid var(--map-border) !important;
    box-shadow: 0 8px 24px rgba(0,0,0,0.5) !important;
    padding: 2px !important;
}
.leaflet-popup-tip {
    background: var(--map-panel) !important;
    border: 1px solid var(--map-border) !important;
}
.popup-head {
    font-size: 12.5px;
    font-weight: 700;
    color: #fff;
    border-bottom: 1px solid var(--map-border);
    padding-bottom: 4px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}
.popup-body-row {
    font-size: 11px;
    color: var(--map-text-dim);
    margin-bottom: 3px;
    display: flex;
    justify-content: space-between;
    gap: 8px;
}
.popup-body-row strong {
    color: #f1f5f9;
}

@media (max-width: 992px) {
    .map-body {
        flex-direction: column;
        height: auto;
    }
    #map-view {
        height: 400px;
        min-height: 400px;
    }
    .map-sidebar {
        width: 100%;
        height: 380px;
        border-left: none;
        border-top: 1px solid var(--map-border);
    }
}
</style>
@endpush

@section('content')
<div class="live-map-page">

    <div class="live-map-container">
        
        {{-- Top Bar with Workflow Controls --}}
        <div class="map-header">
            <div class="map-header-title">
                <span class="fs-5">🛵</span>
                <div>
                    <span class="fw-bold fs-6">ផែនទីដឹកជញ្ជូន (Live Map)</span>
                </div>
                <div class="live-pill">
                    <span class="live-dot"></span>
                    <span id="liveStatusText">LIVE</span>
                </div>
            </div>

            {{-- Date & Workflow Scope Pills --}}
            <div class="scope-pills">
                <button type="button" class="scope-btn {{ ($dateFilter ?? 'today') === 'today' ? 'active' : '' }}" onclick="switchScope('today')">
                    📅 ថ្ងៃនេះ
                </button>
                <button type="button" class="scope-btn {{ ($dateFilter ?? '') === 'yesterday' ? 'active' : '' }}" onclick="switchScope('yesterday')">
                    📅 ម្សិលមិញ
                </button>
                <button type="button" class="scope-btn {{ ($dateFilter ?? '') === 'this_week' ? 'active' : '' }}" onclick="switchScope('this_week')">
                    📅 ៧ថ្ងៃ
                </button>
                <button type="button" class="scope-btn {{ ($dateFilter ?? '') === 'active' ? 'active' : '' }}" onclick="switchScope('active')">
                    ⚡ មិនទាន់ដឹកចប់
                </button>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Date Selector --}}
                <input type="date" id="customDateInput" class="form-control form-control-sm bg-dark text-light border-secondary" 
                       value="{{ $date }}" onchange="onCustomDateSelect(this.value)" style="width: 125px; font-size: 11px;" title="ជ្រើសរើសថ្ងៃជាក់លាក់">

                {{-- Delivery Agency Filter --}}
                <select id="selectDeliveryFilter" class="form-select form-select-sm bg-dark text-light border-secondary" style="width: 140px; font-size: 11px;" onchange="onDeliveryFilterChange(this.value)">
                    <option value="">អ្នកដឹកទាំងអស់</option>
                    @foreach($deliveries as $del)
                        <option value="{{ $del->id }}" {{ ($deliveryId ?? '') == $del->id ? 'selected' : '' }}>{{ $del->delivery_name }}</option>
                    @endforeach
                </select>

                {{-- Mobile App QR / Link Button --}}
                <a href="{{ route('deliveries.driver-portal') }}" target="_blank" class="btn btn-xs btn-outline-info py-1 px-2 fw-bold" style="font-size: 11px;" title="បើកផ្ទាំង Mobile របស់អ្នកដឹក">
                    📱 ផ្ទាំង Mobile អ្នកដឹក
                </a>
                <button type="button" class="btn btn-xs btn-light border py-1 px-2 fw-bold" style="font-size: 11px;" onclick="openQrModal()" title="ស្កេន QR Code លើទូរស័ព្ទ">
                    <i class="fas fa-qrcode text-dark"></i>
                </button>

                {{-- Status Filter Buttons --}}
                <div class="map-btn-group">
                    <button type="button" class="map-btn active" id="btn-filter-all" onclick="setFilterStatus('all')">ទាំងអស់</button>
                    <button type="button" class="map-btn" id="btn-filter-transit" onclick="setFilterStatus('transit')">🛵 កំពុងដឹក</button>
                    <button type="button" class="map-btn" id="btn-filter-delivered" onclick="setFilterStatus('delivered')">✓ រួចរាល់</button>
                </div>
            </div>
        </div>

        {{-- Map Body --}}
        <div class="map-body">
            
            {{-- Map View Container --}}
            <div id="map-view"></div>

            {{-- Sidebar --}}
            <div class="map-sidebar">
                
                {{-- Live Quick Stats --}}
                <div class="stat-grid">
                    <div class="stat-box">
                        <div class="num text-info" id="stat-total-orders">{{ count($activeDeliveries) }}</div>
                        <div class="lbl">កុម្ម៉ង់សរុប</div>
                    </div>
                    <div class="stat-box">
                        <div class="num text-warning" id="stat-in-transit">0</div>
                        <div class="lbl">កំពុងចេញដឹក</div>
                    </div>
                    <div class="stat-box">
                        <div class="num text-success" id="stat-delivered">0</div>
                        <div class="lbl">បានប្រគល់</div>
                    </div>
                </div>

                {{-- Section Title --}}
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="sec-title"><i class="fas fa-list-ul me-1"></i> បញ្ជីកុម្ម៉ង់ (<span id="active-list-count">{{ count($activeDeliveries) }}</span>)</span>
                    <small class="text-muted" style="font-size: 9.5px;" id="lastSyncTimeText">Sync ថ្មីៗ</small>
                </div>

                {{-- Cards Container --}}
                <div id="delivery-cards-container" class="flex-1 overflow-y-auto"></div>

            </div>

        </div>

    </div>

    @php
        $baseDriverUrl = request()->root() . '/driver';
    @endphp

    <!-- Ultra-Clean Compact Responsive QR Code Popup -->
    <div id="driverQrPopup" class="d-none" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.82); backdrop-filter: blur(5px); z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 12px;" onclick="if(event.target === this) closeQrModal();">
        <div style="background: #1e293b; color: #f8fafc; border: 1px solid #334155; border-radius: 16px; width: 100%; max-width: 330px; padding: 18px; box-shadow: 0 20px 45px rgba(0,0,0,0.65); position: relative;" onclick="event.stopPropagation();">
            
            <!-- Modal Header -->
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-50">
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-6">📱</span>
                    <span class="fw-bold" style="font-size: 14px; color: #fff;">QR Code តាមអ្នកដឹក</span>
                </div>
                <button type="button" class="btn btn-sm text-secondary p-0 border-0" onclick="closeQrModal()" style="font-size: 18px; line-height: 1; cursor: pointer;">
                    <i class="fas fa-times text-light"></i>
                </button>
            </div>

            <!-- Driver Selector -->
            <div class="mb-2">
                <select id="qrModalDriverSelect" class="form-select form-select-sm bg-dark text-light border-secondary fw-bold" style="font-size: 12.5px; border-radius: 8px;" onchange="onQrModalDriverChange(this.value)">
                    <option value="">អ្នកដឹកទាំងអស់ (All Drivers)</option>
                    @foreach($deliveries as $del)
                        <option value="{{ $del->id }}" {{ ($deliveryId ?? '') == $del->id ? 'selected' : '' }} data-name="{{ $del->delivery_name }}">
                            {{ $del->delivery_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Perfectly Centered QR Area -->
            <div class="d-flex flex-column align-items-center justify-content-center my-2">
                <div class="bg-white p-2 rounded-3 shadow-sm" style="display: inline-flex; align-items: center; justify-content: center;">
                    <img id="qrModalImage" src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($baseDriverUrl) }}" 
                         alt="Driver Mobile QR" style="width: 145px; height: 145px; display: block;">
                </div>
                
                <div class="mt-2 text-center">
                    <span id="qrDriverBadge" class="badge bg-primary bg-opacity-25 text-info border border-info border-opacity-50 px-2 py-1 fw-bold" style="font-size: 11.5px;">
                        🛵 អ្នកដឹក៖ ទាំងអស់
                    </span>
                </div>
            </div>

            <!-- URL Input & Copy Group -->
            <div class="input-group input-group-sm mb-2" style="border-radius: 8px;">
                <input type="text" id="qrModalUrlInput" class="form-control bg-dark text-info border-secondary fw-bold" style="font-size: 11px; font-family: monospace;" readonly value="{{ $baseDriverUrl }}">
                <button type="button" class="btn btn-outline-info fw-bold" onclick="copyCurrentQrUrl()" title="ចម្លង Link">
                    <i class="fas fa-copy"></i>
                </button>
            </div>

            <!-- Action Buttons Grid -->
            <div class="d-grid gap-1.5 mt-2">
                <button type="button" class="btn btn-sm btn-info text-dark fw-bold py-1.5 mb-1" style="font-size: 12px; border-radius: 8px;" onclick="copyCurrentQrUrl()">
                    <i class="fas fa-paper-plane me-1"></i> ចម្លង Link ផ្ញើ Telegram
                </button>
                <div class="d-flex gap-2">
                    <a id="qrModalOpenBtn" href="{{ $baseDriverUrl }}" target="_blank" class="btn btn-sm btn-primary flex-fill fw-bold py-1.5" style="font-size: 12px; border-radius: 8px;">
                        <i class="fas fa-external-link-alt me-1"></i> បើកមើល
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary flex-fill fw-bold py-1.5" style="font-size: 12px; border-radius: 8px;" onclick="closeQrModal()">
                        បិទ (Close)
                    </button>
                </div>
            </div>

            <div class="text-center mt-2" style="font-size: 10px;">
                <i class="fas fa-wifi text-warning me-1"></i> <span class="text-light text-opacity-75">ទូរស័ព្ទ និង PC ត្រូវភ្ជាប់ Wi-Fi ជាមួយគ្នា</span>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    let DEPOT = @json($depot);
    let ALL_ORDERS = @json($activeDeliveries);
    let currentScope = '{{ $dateFilter ?? 'today' }}';
    let currentDeliveryId = '{{ $deliveryId ?? '' }}';
    let currentFilterStatus = 'all';

    const CSRF_TOKEN = '{{ csrf_token() }}';
    const ROAD_ROUTE_URL = `{{ route('customer-locations.road-route') }}`;
    const UPDATE_STATUS_URL = `{{ route('deliveries.update-order-status') }}`;
    const LIVE_DATA_URL = `{{ route('deliveries.live-data') }}`;

    const COLORS = {
        depot: "#e85d24",
        customer: "#0284c7",
        delivering: "#f97316",
        completed: "#16a34a",
        processing: "#eab308",
        pending: "#64748b",
        driverReal: "#22c55e",
        roadLine: "#f97316",
        roadHalo: "#ffffff"
    };

    // 1. Initialize Leaflet Map
    const depotCoord = [parseFloat(DEPOT.lat), parseFloat(DEPOT.lng)];
    const map = L.map("map-view", { zoomControl: true }).setView(depotCoord, 13);

    const osmRoadmap = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);

    const cartoDark = L.tileLayer("https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png", {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 19,
    });

    const esriSatellite = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", {
        attribution: '&copy; Esri',
        maxZoom: 18,
    });

    L.control.layers({
        "🗺️ ផ្លូវថ្នល់": osmRoadmap,
        "🌙 ងងឹត": cartoDark,
        "🛰️ ផ្កាយរណប": esriSatellite
    }, null, { position: 'topright' }).addTo(map);

    setTimeout(() => map.invalidateSize(), 150);
    setTimeout(() => map.invalidateSize(), 500);
    window.addEventListener('resize', () => map.invalidateSize());

    function pinIcon(color, glyph) {
        return L.divIcon({
            className: "",
            html: `<div class="marker-pin" style="background:${color}"><div class="inner">${glyph}</div></div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -28],
        });
    }

    function vehicleIcon(color, glyph = "🛵", isRealGps = false) {
        const cls = isRealGps ? "marker-driver-real" : "marker-vehicle";
        return L.divIcon({
            className: "",
            html: `<div class="${cls}" style="background:${color}">${glyph}</div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 18],
            popupAnchor: [0, -18],
        });
    }

    // Draw Central Depot Marker (ហាងធំ)
    const depotMarker = L.marker(depotCoord, { icon: pinIcon(COLORS.depot, "🏬"), zIndexOffset: 2000 })
        .addTo(map)
        .bindPopup(`
            <div class="popup-head">
                <span>🏬 ${DEPOT.name}</span>
            </div>
            <div class="popup-body-row">
                <span>ទីតាំង៖</span>
                <strong>${DEPOT.address || 'Central Kitchen'}</strong>
            </div>
            <div class="popup-body-row">
                <span>ទូរស័ព្ទ៖</span>
                <strong>${DEPOT.phone || '012 345 678'}</strong>
            </div>
        `);

    // -------------------------------------------------------------
    // Order Rendering & Focused Route Management
    // -------------------------------------------------------------
    let customerMarkers = {};
    let deliveryVehicles = [];
    let focusedRouteLines = [];

    function clearFocusedRoute() {
        focusedRouteLines.forEach(l => map.removeLayer(l));
        focusedRouteLines = [];
    }

    function rebuildMapAndList(shouldFitBounds = false) {
        // Filter orders
        const filteredOrders = ALL_ORDERS.filter(o => {
            if (currentDeliveryId && String(o.delivery_id) !== String(currentDeliveryId)) {
                return false;
            }
            if (currentFilterStatus === 'transit' && o.status !== 'delivering') {
                return false;
            }
            if (currentFilterStatus === 'delivered' && o.status !== 'completed') {
                return false;
            }
            return true;
        });

        // Update stats
        document.getElementById('stat-total-orders').textContent = ALL_ORDERS.length;
        const inTransitCount = ALL_ORDERS.filter(o => o.status === 'delivering').length;
        const deliveredCount = ALL_ORDERS.filter(o => o.status === 'completed').length;
        document.getElementById('stat-in-transit').textContent = inTransitCount;
        document.getElementById('stat-delivered').textContent = deliveredCount;
        document.getElementById('active-list-count').textContent = filteredOrders.length;

        const bounds = L.latLngBounds([depotCoord]);
        const currentOrderIds = new Set(filteredOrders.map(o => o.id));

        // 1. Remove markers of orders no longer in filter
        Object.keys(customerMarkers).forEach(id => {
            if (!currentOrderIds.has(Number(id))) {
                map.removeLayer(customerMarkers[id]);
                delete customerMarkers[id];
            }
        });

        // 2. Remove inactive vehicle markers
        const activeDeliveringOrderIds = new Set(filteredOrders.filter(o => o.status === 'delivering' && o.driver_live_pos).map(o => o.id));
        deliveryVehicles = deliveryVehicles.filter(v => {
            if (!activeDeliveringOrderIds.has(v.id)) {
                map.removeLayer(v.marker);
                return false;
            }
            return true;
        });

        // 3. Render or Update customer pins & vehicles
        filteredOrders.forEach((order, idx) => {
            const isCompleted = order.status === 'completed';
            const isDelivering = order.status === 'delivering';
            const isProcessing = order.status === 'processing';

            let color = COLORS.pending;
            let glyph = "🏠";

            if (isCompleted) {
                color = COLORS.completed;
                glyph = "✓";
            } else if (isDelivering) {
                color = COLORS.delivering;
                glyph = "🛵";
            } else if (isProcessing) {
                color = COLORS.processing;
                glyph = "🍳";
            }

            const custLat = parseFloat(order.lat);
            const custLng = parseFloat(order.lng);

            bounds.extend([custLat, custLng]);
            const popupHtml = buildPopupHtml(order);

            if (customerMarkers[order.id]) {
                const existingMarker = customerMarkers[order.id];
                existingMarker.setIcon(pinIcon(color, glyph));
                existingMarker.setPopupContent(popupHtml);
                existingMarker.setLatLng([custLat, custLng]);
            } else {
                const marker = L.marker([custLat, custLng], { icon: pinIcon(color, glyph) })
                    .addTo(map)
                    .bindPopup(popupHtml);

                marker.on('click', () => {
                    focusOrderOnMap(order.id);
                });

                customerMarkers[order.id] = marker;
            }

            // Real Live GPS vehicle marker
            if (isDelivering && order.driver_live_pos) {
                let existingVehicle = deliveryVehicles.find(v => v.id === order.id);
                if (existingVehicle) {
                    existingVehicle.order = order;
                    existingVehicle.marker.setPopupContent(popupHtml);
                } else {
                    const realPos = [parseFloat(order.driver_live_pos.lat), parseFloat(order.driver_live_pos.lng)];
                    const vMarker = L.marker(realPos, { 
                        icon: vehicleIcon(COLORS.driverReal, "🛵", true),
                        zIndexOffset: 1500
                    }).addTo(map).bindPopup(popupHtml);

                    deliveryVehicles.push({
                        id: order.id,
                        order: order,
                        marker: vMarker,
                        isRealGps: true
                    });
                }
            }
        });

        // ONLY change map zoom/bounds when explicitly requested (e.g. initial load or scope click)
        if (shouldFitBounds && filteredOrders.length > 0) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
        }

        renderCards(filteredOrders);
    }

    function buildPopupHtml(order) {
        const isCompleted = order.status === 'completed';
        const isDelivering = order.status === 'delivering';
        const isProcessing = order.status === 'processing';
        const hasLiveDriver = !!order.driver_live_pos;

        return `
            <div style="min-width: 240px; padding: 2px;">
                <div class="popup-head">
                    <span>${order.order_code} - ${order.customer_name}</span>
                    <span class="badge ${isCompleted ? 'bg-success' : (isDelivering ? 'bg-warning text-dark' : 'bg-secondary')}">${order.status_kh}</span>
                </div>
                <div class="popup-body-row">
                    <span>ទូរស័ព្ទ៖</span>
                    <strong><a href="tel:${order.customer_phone}" class="text-info text-decoration-none">${order.customer_phone}</a></strong>
                </div>
                <div class="popup-body-row">
                    <span>ទីតាំង៖</span>
                    <strong>${order.address} ${order.landmark ? '(' + order.landmark + ')' : ''}</strong>
                </div>
                <div class="popup-body-row">
                    <span>អ្នកដឹក៖</span>
                    <strong>${order.delivery_name}</strong>
                </div>
                <div class="popup-body-row">
                    <span>ទំនិញ៖</span>
                    <strong style="max-width:140px; text-align:right;">${order.items_summary || (order.box_qty + ' ប្រអប់')}</strong>
                </div>
                <div class="popup-body-row border-top border-secondary pt-1 mt-1">
                    <span>សរុប៖</span>
                    <strong class="text-warning">$${parseFloat(order.total_amount).toFixed(2)} / ៛${Math.round(order.total_amount_khr).toLocaleString()}</strong>
                </div>

                ${hasLiveDriver ? `
                    <div class="p-1 mt-1 mb-2 rounded bg-success bg-opacity-25 border border-success border-opacity-50 small text-center text-success fw-bold" style="font-size: 10.5px;">
                        🟢 ទីតាំងពិតពី Real GPS អ្នកដឹក (ល្បឿន ${order.driver_live_pos.speed || 0} km/h)
                    </div>
                ` : (isDelivering ? `
                    <div class="p-1 mt-1 mb-2 rounded bg-warning bg-opacity-25 border border-warning border-opacity-50 small text-center text-warning fw-bold" style="font-size: 10.5px;">
                        📱 រង់ចាំអ្នកដឹកបើកមើលតាមទូរស័ព្ទ (Waiting for Driver Mobile GPS)
                    </div>
                ` : '')}

                <!-- Quick Action Buttons -->
                <div class="d-flex gap-1 mt-2">
                    <a href="https://www.google.com/maps/dir/?api=1&origin=${DEPOT.lat},${DEPOT.lng}&destination=${order.lat},${order.lng}&travelmode=driving" 
                       target="_blank" class="btn btn-xs btn-outline-danger w-50 py-1" style="font-size: 10px;">
                        🧭 Maps
                    </a>
                    <button type="button" class="btn btn-xs btn-outline-info w-50 py-1" style="font-size: 10px;" onclick="copyDriverLink('${order.driver_track_url}')">
                        📋 Link អ្នកដឹក
                    </button>
                </div>

                <!-- Production Workflow Transitions -->
                <div class="mt-2 pt-1 border-top border-secondary d-flex gap-1">
                    ${order.status === 'pending' ? `
                        <button type="button" class="btn btn-xs btn-warning text-dark w-50 py-1 fw-bold" style="font-size: 10px;" onclick="quickUpdateStatus(${order.id}, 'processing')">
                            🍳 រៀបចំ/ដុត
                        </button>
                    ` : ''}
                    ${order.status !== 'delivering' && !isCompleted ? `
                        <button type="button" class="btn btn-xs btn-primary w-50 py-1 fw-bold" style="font-size: 10px;" onclick="quickUpdateStatus(${order.id}, 'delivering')">
                            🛵 ចេញដឹក
                        </button>
                    ` : ''}
                    ${!isCompleted ? `
                        <button type="button" class="btn btn-xs btn-success ${order.status === 'delivering' ? 'w-100' : 'w-50'} py-1 fw-bold" style="font-size: 10px;" onclick="quickUpdateStatus(${order.id}, 'completed')">
                            ✓ ដឹកដល់
                        </button>
                    ` : `
                        <button type="button" class="btn btn-xs btn-outline-secondary w-100 py-1" style="font-size: 10px;" onclick="quickUpdateStatus(${order.id}, 'delivering')">
                            ↩️ ប្តូរទៅកំពុងដឹកវិញ
                        </button>
                    `}
                </div>
            </div>
        `;
    }

    function setupActiveDeliveringVehicle(order, custLat, custLng, idx) {
        const isRealGps = !!order.driver_live_pos;
        
        // ONLY place live moving vehicle if REAL GPS signal is coming from driver's mobile device!
        if (isRealGps) {
            const realPos = [parseFloat(order.driver_live_pos.lat), parseFloat(order.driver_live_pos.lng)];
            const vMarker = L.marker(realPos, { 
                icon: vehicleIcon(COLORS.driverReal, "🛵", true),
                zIndexOffset: 1500
            }).addTo(map).bindPopup(buildPopupHtml(order));

            deliveryVehicles.push({
                id: order.id,
                order: order,
                marker: vMarker,
                isRealGps: true
            });
        }
    }

    function drawSingleFocusedRoute(custLat, custLng) {
        clearFocusedRoute();

        // 1. Initial line
        const halo = L.polyline([[DEPOT.lat, DEPOT.lng], [custLat, custLng]], { color: '#ffffff', weight: 8, opacity: 0.9, lineCap: 'round' }).addTo(map);
        const polyline = L.polyline([[DEPOT.lat, DEPOT.lng], [custLat, custLng]], { color: COLORS.roadLine, weight: 5, dashArray: '6, 6', opacity: 0.95 }).addTo(map);
        focusedRouteLines.push(halo, polyline);

        // 2. Fetch road route from backend proxy
        const url = `${ROAD_ROUTE_URL}?from_lat=${DEPOT.lat}&from_lng=${DEPOT.lng}&to_lat=${custLat}&to_lng=${custLng}`;
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok' && data.coordinates && data.coordinates.length > 0) {
                    halo.setLatLngs(data.coordinates);
                    polyline.setLatLngs(data.coordinates);
                }
            })
            .catch(err => console.log('Route fetch error:', err));
    }

    function renderCards(orders) {
        const container = document.getElementById('delivery-cards-container');
        if (orders.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4 text-muted small">
                    <i class="fas fa-inbox fs-3 mb-2 d-block opacity-50"></i>
                    មិនមានការបញ្ជាទិញត្រូវនឹងលក្ខខណ្ឌនេះទេ
                </div>
            `;
            return;
        }

        container.innerHTML = orders.map(order => {
            const isCompleted = order.status === 'completed';
            const isDelivering = order.status === 'delivering';
            const isProcessing = order.status === 'processing';
            const hasRealGps = !!order.driver_live_pos;

            let badgeClass = 'bg-secondary';
            if (isCompleted) badgeClass = 'bg-success';
            else if (isDelivering) badgeClass = 'bg-warning text-dark fw-bold';
            else if (isProcessing) badgeClass = 'bg-info text-dark fw-bold';

            return `
                <div class="deliv-card" id="deliv-card-${order.id}" onclick="focusOrderOnMap(${order.id})">
                    <div class="r1">
                        <span class="cust-name text-truncate" style="max-width: 190px;">${order.customer_name}</span>
                        <span class="order-badge">${order.order_code}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="area-text text-truncate" style="max-width: 220px;">
                            <i class="fas fa-map-pin text-warning me-1"></i> ${order.address} ${order.landmark ? '(' + order.landmark + ')' : ''}
                        </span>
                        <span class="badge ${badgeClass}" style="font-size: 9.5px;">
                            ${order.status_kh}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1 small" style="font-size: 10.5px; color: var(--map-text-dim);">
                        <span>
                            ${order.delivery_name}
                            ${hasRealGps ? '<span class="badge bg-success ms-1" style="font-size: 8.5px;">🟢 GPS Live</span>' : (isDelivering ? '<span class="badge bg-warning text-dark ms-1" style="font-size: 8.5px;">📱 រង់ចាំ GPS</span>' : '')}
                        </span>
                        <span class="fw-bold text-white">$${parseFloat(order.total_amount).toFixed(2)}</span>
                    </div>

                    <!-- Workflow Action Buttons Directly on Card -->
                    <div class="mt-2 pt-1 border-top border-secondary d-flex justify-content-between align-items-center" onclick="event.stopPropagation()">
                        <div class="d-flex gap-1">
                            <a href="https://www.google.com/maps/dir/?api=1&origin=${DEPOT.lat},${DEPOT.lng}&destination=${order.lat},${order.lng}&travelmode=driving" 
                               target="_blank" class="btn btn-xs btn-outline-danger py-0 px-2 fw-bold" style="font-size: 9.5px;" title="បើក Google Maps">
                                🧭 Maps
                            </a>
                            <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 fw-bold" style="font-size: 9.5px;" onclick="copyDriverLink('${order.driver_track_url}')" title="Copy Link អ្នកដឹក">
                                📋 Link
                            </button>
                        </div>
                        <div>
                            ${order.status === 'pending' || order.status === 'processing' ? `
                                <button type="button" class="btn btn-xs btn-primary py-0 px-2 fw-bold" style="font-size: 9.5px;" onclick="quickUpdateStatus(${order.id}, 'delivering')">
                                    🛵 ចេញដឹក
                                </button>
                            ` : ''}
                            ${order.status === 'delivering' ? `
                                <button type="button" class="btn btn-xs btn-success py-0 px-2 fw-bold" style="font-size: 9.5px;" onclick="quickUpdateStatus(${order.id}, 'completed')">
                                    ✓ ដឹកដល់
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // -------------------------------------------------------------
    // Real Driver GPS Movement Animation (Smooth Real GPS tracking)
    // -------------------------------------------------------------
    function animateVehicles() {
        deliveryVehicles.forEach(v => {
            if (v.isRealGps && v.order.driver_live_pos) {
                const targetLat = parseFloat(v.order.driver_live_pos.lat);
                const targetLng = parseFloat(v.order.driver_live_pos.lng);
                const current = v.marker.getLatLng();
                const dLat = targetLat - current.lat;
                const dLng = targetLng - current.lng;

                if (Math.abs(dLat) > 0.000005 || Math.abs(dLng) > 0.000005) {
                    v.marker.setLatLng([
                        current.lat + dLat * 0.15,
                        current.lng + dLng * 0.15
                    ]);
                }
            }
        });

        requestAnimationFrame(animateVehicles);
    }
    requestAnimationFrame(animateVehicles);

    // -------------------------------------------------------------
    // Live Polling (Sync every 5 seconds - NO zoom reset!)
    // -------------------------------------------------------------
    function pollLiveData(shouldFitBounds = false) {
        const customDate = document.getElementById('customDateInput').value;
        const dateParam = (currentScope === 'custom' || currentScope === '') && customDate ? `&date=${encodeURIComponent(customDate)}` : '';
        const url = `${LIVE_DATA_URL}?date_filter=${currentScope}${dateParam}&delivery_id=${currentDeliveryId}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok' && data.deliveries) {
                    ALL_ORDERS = data.deliveries;
                    document.getElementById('lastSyncTimeText').innerText = `Sync: ${data.formatted_time || 'just now'}`;
                    rebuildMapAndList(shouldFitBounds);
                }
            })
            .catch(err => console.log('Polling sync error:', err));
    }
    setInterval(() => pollLiveData(false), 5000);

    // -------------------------------------------------------------
    // Global Workflow Scope & Filter Actions
    // -------------------------------------------------------------
    window.switchScope = function(scope) {
        currentScope = scope;
        document.getElementById('customDateInput').value = '';
        document.querySelectorAll('.scope-btn').forEach(b => b.classList.remove('active'));
        if (event && event.target) {
            const btn = event.target.closest('.scope-btn');
            if (btn) btn.classList.add('active');
        }
        pollLiveData(true);
    };

    window.onCustomDateSelect = function(val) {
        if (!val) {
            switchScope('today');
            return;
        }
        currentScope = 'custom';
        document.querySelectorAll('.scope-btn').forEach(b => b.classList.remove('active'));
        pollLiveData(true);
    };

    window.focusOrderOnMap = function(orderId) {
        document.querySelectorAll('.deliv-card').forEach(el => el.classList.remove('active'));
        const card = document.getElementById(`deliv-card-${orderId}`);
        if (card) card.classList.add('active');

        const order = ALL_ORDERS.find(o => o.id === orderId);
        const marker = customerMarkers[orderId];
        if (marker && order) {
            map.flyTo(marker.getLatLng(), 15, { duration: 1 });
            marker.openPopup();
            // Draw clean single road route to this customer!
            drawSingleFocusedRoute(parseFloat(order.lat), parseFloat(order.lng));
        }
    };

    window.setFilterStatus = function(status) {
        currentFilterStatus = status;
        document.querySelectorAll('.map-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(`btn-filter-${status === 'all' ? 'all' : (status === 'transit' ? 'transit' : 'delivered')}`).classList.add('active');
        rebuildMapAndList();
    };

    window.onDeliveryFilterChange = function(val) {
        currentDeliveryId = val;
        pollLiveData(true);
    };

    window.quickUpdateStatus = function(orderId, status) {
        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('order_id', orderId);
        formData.append('status', status);

        fetch(UPDATE_STATUS_URL, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'ok') {
                const order = ALL_ORDERS.find(o => o.id === orderId);
                if (order) {
                    order.status = data.new_status;
                    order.status_kh = data.status_kh;
                }
                rebuildMapAndList();
            }
        })
        .catch(err => alert("មានបញ្ហាក្នុងការ Update Status!"));
    };

    window.copyDriverLink = function(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert("📋 បានចម្លង Link អ្នកដឹកជញ្ជូនរួចរាល់!\nអ្នកអាចផ្ញើ Link នេះទៅកាន់ Telegram អ្នកដឹកជញ្ជូនដើម្បីបើក GPS តាមដានបន្តផ្ទាល់។");
        }).catch(() => {
            prompt("សូមចម្លង Link នេះផ្ញើទៅអ្នកដឹកជញ្ជូន៖", url);
        });
    };

    let currentQrUrl = '{{ request()->root() }}/driver';

    window.onQrModalDriverChange = function(driverId) {
        const baseRoot = '{{ request()->root() }}/driver';
        const select = document.getElementById('qrModalDriverSelect');
        const selectedOption = select ? select.options[select.selectedIndex] : null;
        const driverName = selectedOption ? (selectedOption.getAttribute('data-name') || 'ទាំងអស់') : 'ទាំងអស់';

        currentQrUrl = driverId ? `${baseRoot}/${driverId}` : baseRoot;

        // Update image
        const img = document.getElementById('qrModalImage');
        if (img) {
            img.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(currentQrUrl)}`;
        }

        // Update URL Text / Input
        const urlInput = document.getElementById('qrModalUrlInput');
        if (urlInput) urlInput.value = currentQrUrl;
        const urlText = document.getElementById('qrModalUrlText');
        if (urlText) urlText.innerText = currentQrUrl;

        // Update Button
        const openBtn = document.getElementById('qrModalOpenBtn');
        if (openBtn) openBtn.href = currentQrUrl;

        // Update Badge
        const badge = document.getElementById('qrDriverBadge');
        if (badge) {
            badge.innerText = `🛵 អ្នកដឹក៖ ${driverName}`;
        }
    };

    window.copyCurrentQrUrl = function() {
        navigator.clipboard.writeText(currentQrUrl).then(() => {
            alert("📋 បានចម្លង Link ផ្ទាំងអ្នកដឹកជញ្ជូនរួចរាល់!\n" + currentQrUrl + "\nអ្នកអាចផ្ញើ Link នេះទៅកាន់ Telegram អ្នកដឹកជញ្ជូនដើម្បីបើកលើទូរស័ព្ទ។");
        }).catch(() => {
            prompt("សូមចម្លង Link នេះ៖", currentQrUrl);
        });
    };

    window.openQrModal = function(specificDriverId) {
        const select = document.getElementById('qrModalDriverSelect');
        const targetId = specificDriverId !== undefined ? specificDriverId : currentDeliveryId;
        if (select) {
            select.value = targetId || '';
            onQrModalDriverChange(select.value);
        }

        const popup = document.getElementById('driverQrPopup');
        if (popup) {
            popup.classList.remove('d-none');
            popup.style.display = 'flex';
        }
    };

    window.closeQrModal = function() {
        const popup = document.getElementById('driverQrPopup');
        if (popup) {
            popup.classList.add('d-none');
            popup.style.display = 'none';
        }
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeQrModal();
        }
    });

    // Initial build
    rebuildMapAndList();
});
</script>
@endpush
