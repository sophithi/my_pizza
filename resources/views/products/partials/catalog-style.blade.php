@push('styles')
<style>
    .product-catalog { color:#0f172a; padding:8px 0 20px; }
    .product-catalog > .mb-4 { margin-bottom:24px!important; }
    .product-catalog h2 { font-size:25px!important; font-weight:800!important; }
    .product-catalog > .mb-4 .btn { display:inline-flex; align-items:center; gap:7px; border-radius:8px!important; padding:10px 14px!important; font-size:12px; }
    .product-catalog .btn-outline-success,.product-catalog .btn-outline-danger { background:#fff; border-color:#e2e8f0; color:#475569; }
    .catalog-metrics { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; margin-bottom:22px; }
    .catalog-metric { background:#fff; border:1px solid #e9edf3; border-radius:14px; padding:20px; display:flex; align-items:center; gap:14px; box-shadow:0 3px 14px #0f172a03; }
    .catalog-metric i { display:grid; place-items:center; width:44px; height:44px; border-radius:12px; background:#fff2e9; color:#e85d24; }
    .catalog-metric:nth-child(2) i { background:#eff6ff; color:#3b82f6; }
    .catalog-metric:nth-child(3) i { background:#fffbeb; color:#d97706; }
    .catalog-metric:nth-child(4) i { background:#ecfdf5; color:#059669; }
    .catalog-metric span { display:block; font-size:12px; color:#64748b; }
    .catalog-metric strong { display:block; font-size:27px; margin-top:6px; font-variant-numeric:tabular-nums; }
    .catalog-filters { display:flex; flex-wrap:wrap; align-items:center; gap:12px; padding:16px; margin-bottom:16px; background:#fff; border:1px solid #e9edf3; border-radius:12px; }
    .catalog-search { display:flex; align-items:center; gap:10px; flex:1 1 240px; padding:0 12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; color:#94a3b8; }
    .catalog-search input { width:100%; padding:11px 0; border:0; background:transparent; outline:0; font-size:13px; }
    .catalog-search:focus-within { border-color:#e85d24; box-shadow:0 0 0 3px #fff3eb; }
    .catalog-filters select { padding:11px; border:1px solid #e2e8f0; border-radius:8px; background:white; color:#475569; font-size:12px; min-width:170px; }
    .catalog-search-button { padding:11px 20px; border:0; border-radius:8px; background:#e85d24; color:white; font-size:12px; font-weight:700; }
    .product-catalog .card { border:1px solid #e9edf3!important; border-radius:14px!important; overflow:hidden; box-shadow:0 3px 14px #0f172a04!important; }
    .product-catalog .card-body { padding:0!important; }
    .product-catalog thead { border:0!important; }
    .product-catalog th { background:#f8fafc; color:#64748b!important; padding:16px!important; font-size:11px!important; white-space:nowrap; border-bottom:1px solid #edf1f5!important; }
    .product-catalog td { padding:18px 16px!important; border-color:#edf1f5; font-size:13px; vertical-align:middle; }
    .product-catalog td:nth-child(2) { font-weight:700; min-width:190px; }
    .product-catalog td:first-child { font-size:12px; }
    .product-catalog td:nth-child(5) { white-space:nowrap; font-variant-numeric:tabular-nums; }
    .catalog-usd { display:block; color:#94a3b8; margin-top:4px; font-weight:400; font-size:11px; }
    .product-catalog td:last-child { white-space:nowrap; }
    .product-catalog .btn-action { display:inline-flex; align-items:center; gap:5px; padding:7px 9px; border-radius:7px; font-size:11px; border:1px solid #e2e8f0!important; background:#fff; color:#64748b; }
    .product-catalog .btn-action:hover { background:#fff7ed; color:#e85d24; border-color:#fed7aa!important; }
    .product-catalog .btn-delete { color:#dc2626; border-color:#fee2e2!important; background:#fff7f7; }
    .product-catalog .pager-wrap { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px; margin-top:20px; }
    .product-catalog .pagination { margin-bottom:0; }
    @media(max-width:1000px) { .catalog-metrics { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:600px) { .catalog-metrics { gap:10px; } .catalog-metric { padding:14px; gap:8px; } .catalog-metric i { width:32px; height:32px; } .catalog-metric strong { font-size:23px; } .catalog-filters select { flex:1; min-width:120px; } .product-catalog table { min-width:1050px; } }
</style>
@endpush
