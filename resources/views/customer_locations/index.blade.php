@extends('layouts.app')

@section('title', 'ផែនទីដឹកជញ្ជូន')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
<style>
:root {
    --primary-c: #e85d24;
    --primary-hover: #d04b16;
    --border-c: #e2e8f0;
    --border-subtle: #f1f5f9;
    --text-dark: #0f172a;
    --text-secondary: #475569;
    --text-muted: #64748b;
    --bg-card: #ffffff;
    --bg-soft: #f8fafc;
}

.loc-page {
    padding: 8px 14px;
}

/* ===== TOP BAR ===== */
.loc-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.btn-top-back {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #ffffff;
    border: 1px solid var(--border-c);
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.15s ease;
    font-size: 12px;
}

.btn-top-back:hover {
    background: #f1f5f9;
    color: var(--text-dark);
    border-color: #cbd5e1;
}

.store-badge-btn {
    background: #fff7ed;
    border: 1px solid #ffedd5;
    color: #c2410c;
    font-weight: 700;
    font-size: 12px;
    padding: 4px 12px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.store-badge-btn:hover {
    background: #ffedd5;
    border-color: #fdba74;
    color: #9a3412;
}

/* ===== MAIN CONTAINER ===== */
.loc-main-container {
    display: flex;
    gap: 10px;
    height: calc(100vh - 125px);
    min-height: 580px;
    position: relative;
}

/* ===== MAP PANE ===== */
.loc-map-pane {
    flex: 1;
    height: 100%;
    min-height: 580px;
    background: #ffffff;
    border: 1px solid var(--border-c);
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

#customer-map {
    width: 100%;
    height: 100%;
    min-height: 580px;
    background: #f1f5f9;
}

/* Floating District Filter Bar */
.map-floating-districts {
    position: absolute;
    top: 10px;
    left: 55px;
    z-index: 1000;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(226, 232, 240, 0.85);
    border-radius: 30px;
    padding: 3px 6px;
    display: flex;
    gap: 3px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    overflow-x: auto;
    max-width: calc(100% - 130px);
}

.map-floating-districts::-webkit-scrollbar {
    display: none;
}

.district-pill {
    font-size: 11px;
    padding: 3px 9px;
    border-radius: 20px;
    border: none;
    background: transparent;
    color: #475569;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.district-pill:hover {
    background: #f1f5f9;
    color: var(--primary-c);
}
.district-pill.active {
    background: var(--primary-c);
    color: #ffffff;
}

/* Floating Store Relocate Bar */
.store-relocate-alert {
    position: absolute;
    top: 50px;
    left: 50%;
    transform: translateX(-50%);
    background: #ffffff;
    border: 2px solid var(--primary-c);
    border-radius: 30px;
    padding: 5px 14px;
    box-shadow: 0 8px 25px rgba(232, 93, 36, 0.25);
    z-index: 1000;
    display: none;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    font-weight: bold;
}

/* Floating Route Info Badge */
.map-route-badge {
    position: absolute;
    bottom: 12px;
    left: 12px;
    background: #ffffff;
    border: 1px solid var(--border-c);
    border-radius: 10px;
    padding: 6px 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    z-index: 1000;
    display: none;
    font-size: 12px;
}

/* ===== SIDEBAR PANE ===== */
.loc-sidebar-pane {
    width: 360px;
    flex: 0 0 360px;
    height: 100%;
    min-height: 580px;
    background: #ffffff;
    border: 1px solid var(--border-c);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

.loc-sidebar-header {
    padding: 8px 10px;
    background: #fafbfc;
    border-bottom: 1px solid var(--border-c);
}

.loc-sidebar-body {
    flex: 1;
    overflow-y: auto;
    padding: 8px 10px;
}

/* Compact Customer Card */
.loc-cust-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 10px;
    margin-bottom: 6px;
    transition: all 0.12s ease;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.loc-cust-card:hover {
    border-color: #cbd5e1;
    background: #fafbfc;
    transform: translateY(-1px);
}
.loc-cust-card.active {
    border-color: var(--primary-c);
    background: #fff8f5;
    box-shadow: 0 0 0 1.5px rgba(232, 93, 36, 0.3);
}

/* Custom Marker Pins */
.store-pin-marker {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e85d24, #c2410c);
    color: #fff;
    font-size: 22px;
    box-shadow: 0 0 0 4px rgba(232, 93, 36, 0.35), 0 4px 12px rgba(0,0,0,0.35);
    border: 2px solid #ffffff;
    cursor: grab;
}
.store-pin-marker:active {
    cursor: grabbing;
}

.cust-pin-marker {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50% 50% 50% 0;
    transform: rotate(-45deg);
    box-shadow: 0 2px 8px rgba(0,0,0,0.28);
    border: 2px solid #ffffff;
    background: #0284c7;
    cursor: pointer;
}
.cust-pin-marker .inner {
    transform: rotate(45deg);
    font-size: 13px;
    color: #fff;
}

.temp-pin-marker {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50% 50% 50% 0;
    transform: rotate(-45deg);
    box-shadow: 0 0 0 4px rgba(232, 93, 36, 0.4), 0 4px 12px rgba(0,0,0,0.4);
    border: 2px solid #ffffff;
    background: #e85d24;
    cursor: grab;
}
.temp-pin-marker .inner {
    transform: rotate(45deg);
    font-size: 15px;
}

/* Slide-Over Drawer over Sidebar */
.quick-edit-drawer {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 360px;
    background: #ffffff;
    border-left: 2px solid var(--primary-c);
    border-radius: 0 12px 12px 0;
    padding: 14px;
    box-shadow: -4px 0 25px rgba(0,0,0,0.15);
    z-index: 1050;
    display: none;
    overflow-y: auto;
}

/* ===== PAGER-WRAPPER ===== */
.loc-sidebar-body .pager-wrap {
    display: flex;
    justify-content: center;
    margin-top: 10px;
    margin-bottom: 6px;
}
.loc-sidebar-body .pager-wrap .pagination {
    display: flex;
    gap: 3px;
    margin-bottom: 0;
    flex-wrap: wrap;
    align-items: center;
    list-style: none;
    padding: 0;
}
.loc-sidebar-body .pager-wrap .page-item .page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 28px;
    height: 28px;
    padding: 0 6px;
    border-radius: 6px;
    border: 1px solid var(--border-c);
    background: #ffffff;
    color: #475569;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease;
}
.loc-sidebar-body .pager-wrap .page-item .page-link:hover,
.loc-sidebar-body .pager-wrap .page-item .page-link:focus {
    background: #fff7ed;
    border-color: #fdba74;
    color: var(--primary-c);
    box-shadow: none;
}
.loc-sidebar-body .pager-wrap .page-item.active .page-link {
    background: var(--primary-c);
    border-color: var(--primary-c);
    color: #ffffff;
    font-weight: 700;
}
.loc-sidebar-body .pager-wrap .page-item.disabled .page-link {
    background: #f8fafc;
    border-color: #edf2f7;
    color: #94a3b8;
    pointer-events: none;
}

@media (max-width: 992px) {
    .loc-main-container {
        flex-direction: column;
        height: auto;
    }
    .loc-map-pane {
        height: 480px;
        min-height: 480px;
    }
    .loc-sidebar-pane {
        width: 100%;
        height: 350px;
        min-height: 350px;
    }
    .quick-edit-drawer {
        width: 100%;
        border-left: none;
        border-top: 2px solid var(--primary-c);
    }
}
</style>
@endpush

@section('content')
<div class="loc-page">

    {{-- Top Clean Compact Bar --}}
    <div class="loc-topbar">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('customers.index') }}" class="btn-top-back" title="ត្រឡប់ទៅបញ្ជីអតិថិជន">
                <i class="fas fa-arrow-left"></i>
            </a>
            <span class="fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                <i class="fas fa-map-marked-alt text-primary"></i> ផែនទីទីតាំងអតិថិជន
            </span>
            <button type="button" class="store-badge-btn" onclick="openStoreModal()" title="ចុចដើម្បីកំណត់ទីតាំងហាងធំ">
                 <span id="headerStoreName">{{ $storeLocation['name'] }}</span> <i class="fas fa-cog text-muted ms-1" style="font-size: 10px;"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-primary py-1 px-3 fw-bold shadow-sm" onclick="openQuickAddMode()">
                <i class="fas fa-plus me-1"></i> បន្ថែមទីតាំង
            </button>
        </div>
    </div>

    {{-- Main Container (Huge Map + Compact Sidebar) --}}
    <div class="loc-main-container">
        
        {{-- Map Pane (75% screen width) --}}
        <div class="loc-map-pane">
            
            {{-- Floating District Filter Bar --}}
            <div class="map-floating-districts">
                <button type="button" class="district-pill active" onclick="filterDistrict('all')">ទាំងអស់</button>
                <button type="button" class="district-pill" onclick="filterDistrict('bkk', 11.5480, 104.9284)">BKK</button>
                <button type="button" class="district-pill" onclick="filterDistrict('toul_kork', 11.5764, 104.8925)">Toul Kork</button>
                <button type="button" class="district-pill" onclick="filterDistrict('chamkarmon', 11.5449, 104.9160)">Chamkarmon</button>
                <button type="button" class="district-pill" onclick="filterDistrict('sen_sok', 11.5989, 104.8797)">Sen Sok</button>
                <button type="button" class="district-pill" onclick="filterDistrict('meanchey', 11.5250, 104.9130)">Meanchey</button>
                <button type="button" class="district-pill" onclick="filterDistrict('daun_penh', 11.5690, 104.9250)">Daun Penh</button>
                <button type="button" class="district-pill" onclick="filterDistrict('7_makara', 11.5610, 104.9120)">7 Makara</button>
                <button type="button" class="district-pill" onclick="filterDistrict('chroy_changvar', 11.5830, 104.9310)">Chroy Changvar</button>
                <button type="button" class="district-pill" onclick="filterDistrict('toul_tompoung', 11.5360, 104.9140)">Toul Tompoung</button>
                <button type="button" class="district-pill" onclick="filterDistrict('takhmao', 11.4780, 104.9510)">Ta Khmau</button>
            </div>

            <div id="customer-map"></div>

            {{-- Floating Relocation Alert --}}
            <div id="storeRelocateAlert" class="store-relocate-alert">
                <span>🍕 ទីតាំងហាងថ្មី៖ <span id="tempStoreCoordText" class="text-primary">11.5564, 104.9282</span></span>
                <button type="button" class="btn btn-xs btn-primary py-1 px-2 fw-bold" onclick="saveStoreCoordinatesDirectly()">
                    <i class="fas fa-save me-1"></i> រក្សាទុក
                </button>
                <button type="button" class="btn btn-xs btn-light border py-1 px-2" onclick="resetStoreMarker()">
                    <i class="fas fa-undo"></i>
                </button>
            </div>

            {{-- Floating Route Info Badge --}}
            <div id="routeInfoBadge" class="map-route-badge">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-primary fw-bold"><i class="fas fa-route me-1"></i><span id="routeDistanceText">0.0 km</span></span>
                    <span class="text-muted small">~<strong id="routeEstTime">0</strong> នាទី</span>
                    <a id="btnGmapsNavDirect" href="#" target="_blank" class="btn btn-xs btn-outline-danger py-0 px-2 fw-bold" style="font-size: 10.5px;">
                        <i class="fab fa-google me-1"></i> Maps
                    </a>
                </div>
            </div>
        </div>

        {{-- Sidebar Directory Pane (Compact 360px) --}}
        <div class="loc-sidebar-pane">
            
            <div class="loc-sidebar-header">
                {{-- Quick Link Paste / Search --}}
                <div class="input-group input-group-sm mb-2">
                    <span class="input-group-text bg-white border-end-0 text-danger"><i class="fab fa-google"></i></span>
                    <input type="text" id="sidebar_gmaps_paste" class="form-control border-start-0" 
                           placeholder="Paste Google Maps Link / GPS..." onkeydown="if(event.key==='Enter') parseSidebarGmaps()">
                    <button class="btn btn-outline-secondary" type="button" onclick="parseSidebarGmaps()" title="Parse">
                        <i class="fas fa-magic"></i>
                    </button>
                </div>

                {{-- Search & Filter --}}
                <form method="GET" action="{{ route('customer-locations.index') }}" class="d-flex gap-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="ស្វែងរកអតិថិជន..." 
                           class="form-control form-control-sm">
                    <select name="location_status" class="form-select form-select-sm" style="width: 105px;" onchange="this.form.submit()">
                        <option value="all" {{ request('location_status') == 'all' ? 'selected' : '' }}>ទាំងអស់</option>
                        <option value="pinned" {{ request('location_status') == 'pinned' ? 'selected' : '' }}>មាន GPS</option>
                        <option value="unpinned" {{ request('location_status') == 'unpinned' ? 'selected' : '' }}>គ្មាន GPS</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-secondary px-2" title="ស្វែងរក">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            {{-- Customer Cards List --}}
            <div class="loc-sidebar-body">
                @forelse($customers as $c)
                    @php 
                        $hasGps = !empty($c->latitude) && !empty($c->longitude);
                    @endphp
                    <div class="loc-cust-card" id="card-cust-{{ $c->id }}" onclick="focusCustomerOnMap({{ $c->id }})">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 200px;">
                                <span id="cust-card-name-{{ $c->id }}">{{ $c->name }}</span>
                                @if($c->phone)
                                    <small class="text-muted ms-1 font-monospace" id="cust-card-phone-{{ $c->id }}">{{ $c->phone }}</small>
                                @endif
                            </div>
                            <span id="cust-card-badge-{{ $c->id }}" class="badge {{ $hasGps ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}" style="font-size: 10px;">
                                {{ $hasGps ? '📍 GPS' : '⚠️ No GPS' }}
                            </span>
                        </div>

                        <div class="text-secondary text-truncate small mt-1" style="font-size: 11px;">
                            <span id="cust-card-addr-{{ $c->id }}">{{ $c->address ?: '—' }}</span>
                            <span id="cust-card-landmark-wrap-{{ $c->id }}">
                                @if($c->landmark)
                                    <span class="badge bg-light text-dark border ms-1">📌 {{ $c->landmark }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="mt-2 pt-1 border-top d-flex justify-content-between align-items-center">
                            <div class="small fw-semibold text-primary" style="font-size: 11px;" id="dist-text-{{ $c->id }}">
                                @if($hasGps)
                                    <span class="cust-distance-val" data-lat="{{ $c->latitude }}" data-lng="{{ $c->longitude }}">— km</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>

                            <div class="d-flex gap-1" id="cust-card-actions-{{ $c->id }}">
                                @if($hasGps)
                                    <a id="cust-card-gmaps-{{ $c->id }}" href="https://www.google.com/maps/dir/?api=1&origin={{ $storeLocation['lat'] }},{{ $storeLocation['lng'] }}&destination={{ $c->latitude }},{{ $c->longitude }}&travelmode=driving" 
                                       target="_blank" class="btn btn-xs btn-light border py-0 px-2 text-danger fw-bold" style="font-size: 10px;" onclick="event.stopPropagation()">
                                        <i class="fab fa-google"></i>
                                    </a>
                                @endif
                                <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold" style="font-size: 10.5px;" onclick="event.stopPropagation(); editCustomerDrawer(@js($c))">
                                    <i class="fas fa-map-marker-alt"></i> កែទីតាំង
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted small">
                        <i class="fas fa-map-marker-alt fs-3 mb-2 d-block opacity-25"></i>
                        គ្មានអតិថិជន
                    </div>
                @endforelse

                <div class="pager-wrap">
                    {{ $customers->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>

        {{-- Slide-Over Drawer Editor (Over Sidebar) --}}
        <div id="quickEditDrawer" class="quick-edit-drawer">
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <h6 class="mb-0 fw-bold text-dark" id="drawerTitle">
                    <i class="fas fa-map-pin text-primary me-1"></i> កំណត់ទីតាំងអតិថិជន
                </h6>
                <button type="button" class="btn-close btn-sm" onclick="closeQuickDrawer()"></button>
            </div>

            <div id="drawerAlertBox" class="alert alert-danger py-1 px-2 small mb-2 d-none" style="font-size: 11px;"></div>

            <form id="drawerLocationForm" onsubmit="saveDrawerLocation(event)">
                @csrf
                <input type="hidden" id="drawer_customer_id" value="">
                
                {{-- Quick Customer Auto-Suggest --}}
                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1" style="font-size: 11px;">ស្វែងរកអតិថិជន:</label>
                    <input class="form-control form-control-sm" list="drawerCustList" id="drawer_cust_search" 
                           placeholder="វាយឈ្មោះ ឬលេខទូរស័ព្ទ..." oninput="onDrawerCustSelect(this.value)">
                    <datalist id="drawerCustList">
                        @foreach($allCustomers as $cust)
                            <option value="{{ $cust->name }} | {{ $cust->phone ?: 'No Phone' }} [#{{ $cust->id }}]"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1" style="font-size: 11px;">ឈ្មោះ <span class="text-danger">*</span></label>
                    <input type="text" id="drawer_name" class="form-control form-control-sm" required placeholder="ឈ្មោះអតិថិជន">
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold mb-1" style="font-size: 11px;">លេខទូរស័ព្ទ</label>
                        <input type="text" id="drawer_phone" class="form-control form-control-sm" placeholder="លេខទូរស័ព្ទ">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold mb-1" style="font-size: 11px;">ខេត្ត/ក្រុង</label>
                        <input type="text" id="drawer_city" class="form-control form-control-sm" value="ភ្នំពេញ">
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1" style="font-size: 11px;">ចំណាំសម្គាល់</label>
                    <input type="text" id="drawer_landmark" class="form-control form-control-sm" placeholder="ទល់មុខសាលា, ជិតផ្សារ...">
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1" style="font-size: 11px;">អាសយដ្ឋាន</label>
                    <textarea id="drawer_address" class="form-control form-control-sm" rows="2" placeholder="ផ្ទះលេខ, ផ្លូវ..."></textarea>
                </div>

                <div class="mb-3 p-2 bg-light rounded border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark" style="font-size: 11px;">📍 កូអរដោនេ (ចុចលើផែនទី):</span>
                        <span id="drawer_dist_preview" class="text-primary fw-bold" style="font-size: 11px;"></span>
                    </div>
                    <div class="row g-1">
                        <div class="col-6">
                            <input type="text" id="drawer_lat" class="form-control form-control-sm bg-white" readonly required placeholder="Lat">
                        </div>
                        <div class="col-6">
                            <input type="text" id="drawer_lng" class="form-control form-control-sm bg-white" readonly required placeholder="Lng">
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light border w-50" onclick="closeQuickDrawer()">បោះបង់</button>
                    <button type="submit" id="btnDrawerSave" class="btn btn-sm btn-primary w-50 fw-bold shadow-sm">
                        <i class="fas fa-save me-1"></i> រក្សាទុក
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Change Main Office Store Location (ប្តូរទីតាំងហាងធំ) --}}
<div class="modal fade" id="storeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-3 px-4 border-bottom">
                <h6 class="modal-title fw-bold text-dark mb-0">
                    កំណត់ទីតាំងហាងធំ (Main Office Location)
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="storeSettingsForm" onsubmit="saveStoreSettings(event)">
                @csrf
                <div class="modal-body p-3">
                    {{-- Google Maps URL Paste Helper --}}
                    <div class="p-2 mb-3 rounded bg-light border">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="fab fa-google text-danger me-1"></i> Paste Google Maps Link ឬកូអរដោនេ៖
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="store_gmaps_input" class="form-control" placeholder="https://maps.app.goo.gl/... ឬ 11.5564, 104.9282">
                            <button type="button" class="btn btn-outline-primary" onclick="parseStoreGmaps()">
                                <i class="fas fa-magic me-1"></i> Parse
                            </button>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">ឈ្មោះហាង / សាខា <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="store_name_input" class="form-control form-control-sm" required value="{{ $storeLocation['name'] }}">
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold">លេខទូរស័ព្ទ</label>
                            <input type="text" name="phone" id="store_phone_input" class="form-control form-control-sm" value="{{ $storeLocation['phone'] ?? '012 345 678' }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">អាសយដ្ឋាន</label>
                            <input type="text" name="address" id="store_address_input" class="form-control form-control-sm" value="{{ $storeLocation['address'] ?? 'Phnom Penh' }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label small fw-bold text-dark mb-0">កូអរដោនេ GPS ហាង៖</label>
                        <button type="button" class="btn btn-xs btn-outline-success py-0 px-2" style="font-size: 11px;" onclick="useMyGpsForStore()">
                            <i class="fas fa-crosshairs me-1"></i> យកទីតាំងខ្ញុំ
                        </button>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted mb-0" style="font-size: 10px;">Lat</label>
                            <input type="number" step="any" name="lat" id="store_lat_input" class="form-control form-control-sm" required value="{{ $storeLocation['lat'] }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted mb-0" style="font-size: 10px;">Lng</label>
                            <input type="number" step="any" name="lng" id="store_lng_input" class="form-control form-control-sm" required value="{{ $storeLocation['lng'] }}">
                        </div>
                    </div>

                    <div class="border-top pt-2">
                        <label class="form-label small fw-bold text-muted mb-1">រង្វង់តំបន់ដឹកជញ្ជូន (Delivery Radius):</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="text-muted" style="font-size: 10px;">Zone 1 (បៃតង)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" name="radius_1" id="store_r1" class="form-control" value="{{ $storeLocation['radius_1'] ?? 3 }}">
                                    <span class="input-group-text">km</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <label class="text-muted" style="font-size: 10px;">Zone 2 (ទឹកក្រូច)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" name="radius_2" id="store_r2" class="form-control" value="{{ $storeLocation['radius_2'] ?? 5 }}">
                                    <span class="input-group-text">km</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <label class="text-muted" style="font-size: 10px;">Zone 3 (ខៀវ)</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.5" name="radius_3" id="store_r3" class="form-control" value="{{ $storeLocation['radius_3'] ?? 8 }}">
                                    <span class="input-group-text">km</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer py-2 px-3 border-top">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" id="btnSaveStore" class="btn btn-sm btn-danger px-3 fw-bold shadow-sm">
                        <i class="fas fa-save me-1"></i> រក្សាទុក
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const storeModalEl = document.getElementById('storeModal');
    if (storeModalEl && storeModalEl.parentNode !== document.body) {
        document.body.appendChild(storeModalEl);
    }

    let STORE = @json($storeLocation);
    const MAP_CUSTOMERS = @json($mapCustomers);
    const ALL_CUSTOMERS = @json($allCustomers);

    let storeCoord = [parseFloat(STORE.lat), parseFloat(STORE.lng)];

    // Map centered on Store with robust initialization
    const mapElement = document.getElementById('customer-map');
    const mainMap = L.map(mapElement, { zoomControl: true }).setView(storeCoord, 13);

    const osmRoadmap = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
    }).addTo(mainMap);

    const esriSatellite = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", {
        attribution: '&copy; Esri',
        maxZoom: 18,
    });

    L.control.layers({
        "🗺️ ផ្លូវថ្នល់": osmRoadmap,
        "🛰️ ផ្កាយរណប": esriSatellite
    }, null, { position: 'topright' }).addTo(mainMap);

    // Multiple trigger invalidateSize to ensure tiles render immediately
    setTimeout(() => mainMap.invalidateSize(), 100);
    setTimeout(() => mainMap.invalidateSize(), 300);
    setTimeout(() => mainMap.invalidateSize(), 800);
    window.addEventListener('resize', () => mainMap.invalidateSize());

    function getDistanceFromStore(lat, lng) {
        const R = 6371;
        const dLat = (lat - STORE.lat) * Math.PI / 180;
        const dLng = (lng - STORE.lng) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(STORE.lat * Math.PI / 180) * Math.cos(lat * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return (R * c).toFixed(1);
    }

    function refreshAllDistances() {
        document.querySelectorAll('.cust-distance-val').forEach(el => {
            const lat = parseFloat(el.dataset.lat);
            const lng = parseFloat(el.dataset.lng);
            if (lat && lng) {
                const dist = getDistanceFromStore(lat, lng);
                el.innerHTML = `<strong>${dist} km</strong>`;
            }
        });
    }
    refreshAllDistances();

    function createStoreIcon() {
        return L.divIcon({
            className: "",
            html: `<div class="store-pin-marker" title="អូសដើម្បីប្តូរទីតាំងហាង">🍕</div>`,
            iconSize: [42, 42],
            iconAnchor: [21, 21],
            popupAnchor: [0, -22],
        });
    }

    function createCustIcon() {
        return L.divIcon({
            className: "",
            html: `<div class="cust-pin-marker"><div class="inner">👤</div></div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -28],
        });
    }

    function createTempIcon() {
        return L.divIcon({
            className: "",
            html: `<div class="temp-pin-marker"><div class="inner">📍</div></div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            popupAnchor: [0, -34],
        });
    }

    let storeCircles = [];
    function drawStoreRadiusCircles(lat, lng) {
        storeCircles.forEach(c => mainMap.removeLayer(c));
        storeCircles = [];

        const r1 = (parseFloat(STORE.radius_1) || 3) * 1000;
        const r2 = (parseFloat(STORE.radius_2) || 5) * 1000;
        const r3 = (parseFloat(STORE.radius_3) || 8) * 1000;

        storeCircles.push(L.circle([lat, lng], { radius: r1, color: '#16a34a', weight: 1.5, fillOpacity: 0.04, dashArray: '4,4' }).addTo(mainMap));
        storeCircles.push(L.circle([lat, lng], { radius: r2, color: '#e85d24', weight: 1.5, fillOpacity: 0.03, dashArray: '5,5' }).addTo(mainMap));
        storeCircles.push(L.circle([lat, lng], { radius: r3, color: '#0284c7', weight: 1, fillOpacity: 0.02, dashArray: '6,6' }).addTo(mainMap));
    }
    drawStoreRadiusCircles(STORE.lat, STORE.lng);

    const storeMarker = L.marker(storeCoord, { 
        icon: createStoreIcon(), 
        draggable: true,
        zIndexOffset: 2000 
    }).addTo(mainMap);

    function updateStorePopupContent() {
        storeMarker.bindPopup(`
            <div style="min-width: 200px; padding: 2px;">
                <div style="font-size: 13.5px; font-weight: bold; color: #e85d24; margin-bottom: 2px;">
                    🍕 ${STORE.name}
                </div>
                <div style="font-size: 11px; color: #475569; margin-bottom: 4px;">
                    📍 ${STORE.address || 'Central Kitchen'} | 📞 ${STORE.phone || '012 345 678'}
                </div>
                <button type="button" class="btn btn-xs btn-outline-danger w-100 py-1 fw-bold" style="font-size: 10.5px;" onclick="openStoreModal()">
                    ⚙️ កំណត់ព័ត៌មានហាង
                </button>
            </div>
        `);
    }
    updateStorePopupContent();

    let tempDraggedStorePos = null;

    storeMarker.on('drag', function(e) {
        const pos = e.target.getLatLng();
        drawStoreRadiusCircles(pos.lat, pos.lng);
        document.getElementById('tempStoreCoordText').innerText = `${pos.lat.toFixed(4)}, ${pos.lng.toFixed(4)}`;
        document.getElementById('storeRelocateAlert').style.display = 'flex';
        tempDraggedStorePos = pos;
    });

    storeMarker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        tempDraggedStorePos = pos;
        drawStoreRadiusCircles(pos.lat, pos.lng);
        document.getElementById('tempStoreCoordText').innerText = `${pos.lat.toFixed(4)}, ${pos.lng.toFixed(4)}`;
        document.getElementById('storeRelocateAlert').style.display = 'flex';
    });

    window.resetStoreMarker = function() {
        storeMarker.setLatLng(storeCoord);
        drawStoreRadiusCircles(STORE.lat, STORE.lng);
        document.getElementById('storeRelocateAlert').style.display = 'none';
        tempDraggedStorePos = null;
    };

    window.saveStoreCoordinatesDirectly = function() {
        if (!tempDraggedStorePos) return;

        const url = `{{ route('customer-locations.update-store') }}`;
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('name', STORE.name);
        formData.append('address', STORE.address || '');
        formData.append('phone', STORE.phone || '');
        formData.append('lat', tempDraggedStorePos.lat);
        formData.append('lng', tempDraggedStorePos.lng);
        formData.append('radius_1', STORE.radius_1 || 3);
        formData.append('radius_2', STORE.radius_2 || 5);
        formData.append('radius_3', STORE.radius_3 || 8);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            STORE.lat = tempDraggedStorePos.lat;
            STORE.lng = tempDraggedStorePos.lng;
            storeCoord = [tempDraggedStorePos.lat, tempDraggedStorePos.lng];
            document.getElementById('storeRelocateAlert').style.display = 'none';
            refreshAllDistances();
            alert("✅ បានផ្លាស់ប្តូរទីតាំងហាងធំជោគជ័យ!");
        })
        .catch(err => {
            alert("មានបញ្ហាក្នុងការរក្សាទុកទីតាំងហាង!");
            console.error(err);
        });
    };

    const mapMarkers = {};
    let activeRouteLine = null;
    let activeRouteHalo = null;

    function renderSingleCustomerMarker(c) {
        if (!c.latitude || !c.longitude) return null;

        const lat = parseFloat(c.latitude);
        const lng = parseFloat(c.longitude);
        const dist = getDistanceFromStore(lat, lng);
        const estMins = Math.round(dist * 3.5 + 5);

        if (mapMarkers[c.id]) {
            mainMap.removeLayer(mapMarkers[c.id]);
        }

        const marker = L.marker([lat, lng], { icon: createCustIcon() })
            .addTo(mainMap)
            .bindPopup(`
                <div style="min-width: 200px; padding: 2px;">
                    <div style="font-size: 13px; font-weight: bold; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px; margin-bottom: 4px;">
                        👤 ${c.name}
                    </div>
                    <div style="font-size: 11px; color: #64748b; margin-bottom: 2px;">
                        📞 ${c.phone || '—'}
                    </div>
                    <div style="font-size: 11px; color: #64748b; margin-bottom: 2px;">
                        📍 ${c.address || ''} ${c.landmark ? '(' + c.landmark + ')' : ''}
                    </div>
                    <div style="font-size: 11px; color: #e85d24; font-weight: bold; margin-bottom: 6px;">
                        📏 ${dist} km (~${estMins} នាទី)
                    </div>
                    <div style="display: flex; gap: 4px;">
                        <a href="https://www.google.com/maps/dir/?api=1&origin=${STORE.lat},${STORE.lng}&destination=${lat},${lng}&travelmode=driving" 
                           target="_blank" class="btn btn-xs btn-outline-danger w-50 py-0" style="font-size: 10px;">
                            🧭 Maps
                        </a>
                        <button type="button" class="btn btn-xs btn-primary w-50 py-0 fw-bold" style="font-size: 10px;" onclick="editCustomerDrawer(${JSON.stringify(c).replace(/"/g, '&quot;')})">
                            ✏️ កែ
                        </button>
                    </div>
                </div>
            `);

        marker.on('click', () => {
            drawRouteToStore(lat, lng, c.name, dist, estMins);
        });

        mapMarkers[c.id] = marker;
        return marker;
    }

    MAP_CUSTOMERS.forEach(c => renderSingleCustomerMarker(c));

    function drawRouteToStore(custLat, custLng, custName, dist, estMins) {
        if (activeRouteLine) {
            mainMap.removeLayer(activeRouteLine);
            activeRouteLine = null;
        }
        if (activeRouteHalo) {
            mainMap.removeLayer(activeRouteHalo);
            activeRouteHalo = null;
        }

        // 1. Direct dashed line while loading road route
        activeRouteLine = L.polyline([[STORE.lat, STORE.lng], [custLat, custLng]], {
            color: '#e85d24',
            weight: 3.5,
            opacity: 0.7,
            dashArray: '5, 5',
            lineCap: 'round'
        }).addTo(mainMap);

        const badge = document.getElementById('routeInfoBadge');
        document.getElementById('routeDistanceText').innerText = `${dist} km`;
        document.getElementById('routeEstTime').innerText = estMins;
        document.getElementById('btnGmapsNavDirect').href = `https://www.google.com/maps/dir/?api=1&origin=${STORE.lat},${STORE.lng}&destination=${custLat},${custLng}&travelmode=driving`;
        badge.style.display = 'block';

        // 2. Fetch actual road driving route from Backend Road Proxy
        const routeUrl = `{{ route('customer-locations.road-route') }}?from_lat=${STORE.lat}&from_lng=${STORE.lng}&to_lat=${custLat}&to_lng=${custLng}`;

        fetch(routeUrl)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok' && data.coordinates && data.coordinates.length > 0) {
                    const roadCoords = data.coordinates;
                    const actualKm = data.distance_km;
                    const actualMins = data.duration_mins;

                    if (activeRouteLine) {
                        mainMap.removeLayer(activeRouteLine);
                    }
                    if (activeRouteHalo) {
                        mainMap.removeLayer(activeRouteHalo);
                    }

                    activeRouteHalo = L.polyline(roadCoords, {
                        color: '#ffffff',
                        weight: 8,
                        opacity: 0.9,
                        lineJoin: 'round',
                        lineCap: 'round'
                    }).addTo(mainMap);

                    activeRouteLine = L.polyline(roadCoords, {
                        color: '#e85d24',
                        weight: 5,
                        opacity: 0.95,
                        lineJoin: 'round',
                        lineCap: 'round'
                    }).addTo(mainMap);

                    document.getElementById('routeDistanceText').innerText = `${actualKm} km (តាមផ្លូវ)`;
                    document.getElementById('routeEstTime').innerText = actualMins;
                }
            })
            .catch(err => {
                console.log('Road routing fallback to direct line:', err);
            });
    }

    let tempMarker = null;
    let activeEditingCustomerId = null;

    function setDraggablePin(lat, lng) {
        if (tempMarker) {
            mainMap.removeLayer(tempMarker);
        }

        tempMarker = L.marker([lat, lng], { 
            draggable: true, 
            icon: createTempIcon(),
            zIndexOffset: 3000
        }).addTo(mainMap);

        updateDrawerCoordinates(lat, lng);

        tempMarker.on('drag', function(ev) {
            const pos = ev.target.getLatLng();
            updateDrawerCoordinates(pos.lat, pos.lng);
        });

        tempMarker.on('dragend', function(ev) {
            const pos = ev.target.getLatLng();
            updateDrawerCoordinates(pos.lat, pos.lng);
        });
    }

    function updateDrawerCoordinates(lat, lng) {
        document.getElementById('drawer_lat').value = Number(lat).toFixed(7);
        document.getElementById('drawer_lng').value = Number(lng).toFixed(7);
        const dist = getDistanceFromStore(lat, lng);
        document.getElementById('drawer_dist_preview').innerText = `📏 ${dist} km ពីហាង`;
    }

    mainMap.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        const drawer = document.getElementById('quickEditDrawer');
        const isDrawerOpen = (drawer.style.display === 'block');

        setDraggablePin(lat, lng);

        if (!isDrawerOpen) {
            openDrawerWithCoords(lat, lng);
        }
    });

    window.focusCustomerOnMap = function(id) {
        document.querySelectorAll('.loc-cust-card').forEach(el => el.classList.remove('active'));
        const card = document.getElementById(`card-cust-${id}`);
        if (card) card.classList.add('active');

        const marker = mapMarkers[id];
        if (marker) {
            const latlng = marker.getLatLng();
            mainMap.flyTo(latlng, 15, { duration: 1 });
            marker.openPopup();
            const dist = getDistanceFromStore(latlng.lat, latlng.lng);
            const estMins = Math.round(dist * 3.5 + 5);
            drawRouteToStore(latlng.lat, latlng.lng, '', dist, estMins);
        }
    };

    window.filterDistrict = function(key, lat, lng) {
        document.querySelectorAll('.district-pill').forEach(el => el.classList.remove('active'));
        if (event && event.target) event.target.classList.add('active');

        if (key === 'all') {
            mainMap.flyTo([STORE.lat, STORE.lng], 13, { duration: 1 });
        } else if (lat && lng) {
            mainMap.flyTo([lat, lng], 15, { duration: 1 });
        }
    };

    window.openDrawerWithCoords = function(lat, lng) {
        activeEditingCustomerId = null;
        document.getElementById('drawerAlertBox').classList.add('d-none');
        document.getElementById('drawerTitle').innerHTML = `<i class="fas fa-plus-circle text-primary me-1"></i> បន្ថែមទីតាំងថ្មី`;
        document.getElementById('drawer_customer_id').value = '';
        document.getElementById('drawer_cust_search').value = '';
        document.getElementById('drawer_name').value = '';
        document.getElementById('drawer_phone').value = '';
        document.getElementById('drawer_landmark').value = '';
        document.getElementById('drawer_city').value = 'ភ្នំពេញ';
        document.getElementById('drawer_address').value = '';
        setDraggablePin(lat, lng);
        document.getElementById('quickEditDrawer').style.display = 'block';
    };

    window.openQuickAddMode = function() {
        openDrawerWithCoords(STORE.lat, STORE.lng);
    };

    window.editCustomerDrawer = function(cust) {
        activeEditingCustomerId = cust.id;
        document.getElementById('drawerAlertBox').classList.add('d-none');
        document.getElementById('drawerTitle').innerHTML = `<i class="fas fa-edit text-primary me-1"></i> កែទីតាំង - ${cust.name}`;
        document.getElementById('drawer_customer_id').value = cust.id;
        document.getElementById('drawer_cust_search').value = `${cust.name} [#${cust.id}]`;
        document.getElementById('drawer_name').value = cust.name || '';
        document.getElementById('drawer_phone').value = cust.phone || '';
        document.getElementById('drawer_landmark').value = cust.landmark || '';
        document.getElementById('drawer_city').value = cust.city || 'ភ្នំពេញ';
        document.getElementById('drawer_address').value = cust.address || '';

        const lat = cust.latitude ? parseFloat(cust.latitude) : STORE.lat;
        const lng = cust.longitude ? parseFloat(cust.longitude) : STORE.lng;

        setDraggablePin(lat, lng);
        document.getElementById('quickEditDrawer').style.display = 'block';

        mainMap.flyTo([lat, lng], 16, { duration: 1 });
    };

    window.closeQuickDrawer = function() {
        document.getElementById('quickEditDrawer').style.display = 'none';
        activeEditingCustomerId = null;
        if (tempMarker) {
            mainMap.removeLayer(tempMarker);
            tempMarker = null;
        }
    };

    window.onDrawerCustSelect = function(val) {
        const match = val.match(/\[#(\d+)\]/);
        if (match) {
            const customerId = parseInt(match[1]);
            const customer = ALL_CUSTOMERS.find(c => c.id === customerId);
            if (customer) {
                activeEditingCustomerId = customer.id;
                document.getElementById('drawer_customer_id').value = customer.id;
                document.getElementById('drawer_name').value = customer.name || '';
                document.getElementById('drawer_phone').value = customer.phone || '';
                document.getElementById('drawer_landmark').value = customer.landmark || '';
                document.getElementById('drawer_city').value = customer.city || 'ភ្នំពេញ';
                document.getElementById('drawer_address').value = customer.address || '';
                
                const lat = customer.latitude ? parseFloat(customer.latitude) : STORE.lat;
                const lng = customer.longitude ? parseFloat(customer.longitude) : STORE.lng;
                setDraggablePin(lat, lng);
                mainMap.flyTo([lat, lng], 16, { duration: 1 });
            }
        }
    };

    window.parseSidebarGmaps = function() {
        const input = document.getElementById('sidebar_gmaps_paste').value.trim();
        if (!input) return;

        const coordMatch = input.match(/(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)/);
        if (coordMatch) {
            const lat = parseFloat(coordMatch[1]);
            const lng = parseFloat(coordMatch[2]);
            setDraggablePin(lat, lng);
            if (document.getElementById('quickEditDrawer').style.display !== 'block') {
                openDrawerWithCoords(lat, lng);
            }
            mainMap.flyTo([lat, lng], 16, { duration: 1 });
            return;
        }

        const urlMatch = input.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/) || input.match(/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/);
        if (urlMatch) {
            const lat = parseFloat(urlMatch[1]);
            const lng = parseFloat(urlMatch[2]);
            setDraggablePin(lat, lng);
            if (document.getElementById('quickEditDrawer').style.display !== 'block') {
                openDrawerWithCoords(lat, lng);
            }
            mainMap.flyTo([lat, lng], 16, { duration: 1 });
            return;
        }

        alert("មិនអាចទាញយកកូអរដោនេបានទេ។ សូមចុចលើផែនទីដោយផ្ទាល់!");
    };

    window.saveDrawerLocation = function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnDrawerSave');
        const alertBox = document.getElementById('drawerAlertBox');
        alertBox.classList.add('d-none');

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> កំពុងរក្សាទុក...';

        const custId = activeEditingCustomerId || document.getElementById('drawer_customer_id').value;
        const url = `{{ route('customer-locations.store') }}`;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        if (custId) {
            formData.append('customer_id', custId);
        }
        formData.append('name', document.getElementById('drawer_name').value);
        formData.append('phone', document.getElementById('drawer_phone').value);
        formData.append('landmark', document.getElementById('drawer_landmark').value);
        formData.append('city', document.getElementById('drawer_city').value);
        formData.append('address', document.getElementById('drawer_address').value);
        formData.append('latitude', document.getElementById('drawer_lat').value);
        formData.append('longitude', document.getElementById('drawer_lng').value);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ status, body }) => {
            if (status >= 200 && status < 300 && body.status === 'ok') {
                alert(body.message || "✅ បានរក្សាទុកទីតាំងអតិថិជនជោគជ័យ!");
                window.location.reload();
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> រក្សាទុក';
                let errorMsg = body.message || 'មានបញ្ហាក្នុងការរក្សាទុក!';
                if (body.errors) {
                    errorMsg = Object.values(body.errors).flat().join('<br>');
                }
                alertBox.innerHTML = errorMsg;
                alertBox.classList.remove('d-none');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> រក្សាទុក';
            alertBox.innerHTML = 'មានបញ្ហាក្នុងការភ្ជាប់ Server!';
            alertBox.classList.remove('d-none');
            console.error(err);
        });
    };

    window.openStoreModal = function() {
        document.getElementById('store_name_input').value = STORE.name;
        document.getElementById('store_phone_input').value = STORE.phone || '';
        document.getElementById('store_address_input').value = STORE.address || '';
        document.getElementById('store_lat_input').value = STORE.lat;
        document.getElementById('store_lng_input').value = STORE.lng;
        document.getElementById('store_r1').value = STORE.radius_1 || 3;
        document.getElementById('store_r2').value = STORE.radius_2 || 5;
        document.getElementById('store_r3').value = STORE.radius_3 || 8;

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('storeModal'));
        modal.show();
    };

    window.parseStoreGmaps = function() {
        const input = document.getElementById('store_gmaps_input').value.trim();
        if (!input) return;

        const coordMatch = input.match(/(-?\d+\.\d+)[,\s]+(-?\d+\.\d+)/);
        if (coordMatch) {
            document.getElementById('store_lat_input').value = parseFloat(coordMatch[1]);
            document.getElementById('store_lng_input').value = parseFloat(coordMatch[2]);
            return;
        }

        const urlMatch = input.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/) || input.match(/[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/);
        if (urlMatch) {
            document.getElementById('store_lat_input').value = parseFloat(urlMatch[1]);
            document.getElementById('store_lng_input').value = parseFloat(urlMatch[2]);
            return;
        }

        alert("មិនអាចទាញយកកូអរដោនេពី Link បានទេ។");
    };

    window.useMyGpsForStore = function() {
        if (!navigator.geolocation) {
            alert("Geolocation មិនដំណើរការ!");
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('store_lat_input').value = position.coords.latitude.toFixed(7);
            document.getElementById('store_lng_input').value = position.coords.longitude.toFixed(7);
        }, function(error) {
            alert("មិនអាចទាញយក GPS បានទេ៖ " + error.message);
        }, { enableHighAccuracy: true });
    };

    window.saveStoreSettings = function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveStore');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>...';

        const url = `{{ route('customer-locations.update-store') }}`;
        const formData = new FormData(document.getElementById('storeSettingsForm'));

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            alert("✅ បានផ្លាស់ប្តូរទីតាំងហាងធំជោគជ័យ!");
            window.location.reload();
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> រក្សាទុក';
            alert("មានបញ្ហាក្នុងការរក្សាទុក!");
            console.error(err);
        });
    };
});
</script>
@endpush
