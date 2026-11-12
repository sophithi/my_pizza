@push('styles')
<style>
    .product-editor { max-width:1350px; margin:0 auto; padding:5px 0 24px; color:#0f172a; }
    .pe-heading { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; gap:15px; }
    .pe-heading h2 { font-size:25px; font-weight:800; margin:12px 0 0; }
    .pe-back { font-size:12px; color:#64748b; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
    .pe-sku { background:#fff2e9; color:#c2410c; border:1px solid #ffdfc8; border-radius:8px; padding:8px 12px; font-size:12px; }
    .product-editor .card { background:transparent; box-shadow:none!important; }
    .product-editor .card-body { padding:0!important; }
    .pe-grid { display:grid; grid-template-columns:minmax(0,1.7fr) minmax(0,1fr); gap:22px; align-items:start; }
    .pe-section { padding:26px; background:#fff; border:1px solid #e8edf3; border-radius:15px; box-shadow:0 4px 18px #0f172a04; }
    .pe-section h3 { font-size:15px; font-weight:800; margin:0 0 23px; display:flex; align-items:center; gap:10px; }
    .pe-section h3 i { color:#e85d24; }
    .pe-section .pe-divider { padding-top:24px; margin-top:24px; border-top:1px solid #edf1f5; }
    .product-editor .form-label { color:#475569; font-size:12px; margin-bottom:9px; }
    .product-editor .form-control,.product-editor .form-select { border:1px solid #e2e8f0; border-radius:8px; min-height:44px; font-size:13px; padding:10px 12px; }
    .product-editor .form-control:focus,.product-editor .form-select:focus { border-color:#e85d24; box-shadow:0 0 0 3px #fff1e6; }
    .product-editor .input-group-text { background:#f8fafc; color:#64748b; border-color:#e2e8f0; min-width:40px; justify-content:center; }
    .product-editor .input-group .form-control { border-radius:0 8px 8px 0; }
    .product-editor .input-group-text { border-radius:8px 0 0 8px; }
    .product-editor small { font-size:11px; line-height:1.8; }
    .pe-toggle { padding:14px; margin-top:10px; border:1px solid #edf1f5; border-radius:9px; background:#fafbfd; }
    .pe-toggle label { display:flex; align-items:center; gap:10px; cursor:pointer; font-size:13px; }
    .pe-toggle input[type=checkbox] { accent-color:#e85d24; width:17px; height:17px; }
    .pe-image-preview { display:grid; place-items:center; height:240px; border:1px dashed #e2e8f0; border-radius:12px; background:#f8fafc; margin-bottom:20px; overflow:hidden; }
    .pe-image-preview img { width:100%; height:100%; object-fit:contain; padding:12px; }
    .pe-image-preview span { font-size:40px; color:#cbd5e1; text-align:center; }
    .pe-image-preview span small { color:#94a3b8; font-size:12px; }
    .pe-media textarea { min-height:145px!important; resize:vertical; }
    .pe-footer { margin-top:24px; padding:18px 0 0; border-top:1px solid #e2e8f0; justify-content:flex-end; align-items:center; gap:12px!important; }
    .pe-footer .btn { display:inline-flex; align-items:center; justify-content:center; min-height:46px; min-width:130px; padding:11px 22px!important; font-size:13px; border-radius:10px!important; transition:background .15s,box-shadow .15s; }
    .pe-footer button[type=submit] { order:2; border:1px solid #e85d24; box-shadow:0 4px 12px #e85d2426; }
    .pe-footer button[type=submit]:hover { background:#d94a10!important; box-shadow:0 6px 16px #e85d2433; }
    .pe-footer .btn-outline-secondary { order:1; background:#fff; color:#64748b; border-color:#dce3ec; }
    .pe-footer .btn-outline-secondary:hover { background:#f1f5f9; color:#334155; border-color:#cbd5e1; }
    .pe-footer .btn:focus-visible { outline:3px solid #fdba74; outline-offset:3px; }
    @media(max-width:900px) { .pe-grid { grid-template-columns:1fr; } }
    @media(max-width:600px) { .pe-section { padding:18px; } .pe-heading h2 { font-size:21px; } .pe-footer { padding:16px 0 0; } .pe-footer .btn { flex:1; min-width:0; } }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('productImageInput');
    const image = document.getElementById('productImagePreview');
    const placeholder = document.getElementById('productImagePlaceholder');
    const original = image.getAttribute('src');
    let previewUrl;
    input.addEventListener('change', () => {
        if (previewUrl) URL.revokeObjectURL(previewUrl);
        const file = input.files[0];
        if (!file) { image.src = original; image.hidden = !original; placeholder.hidden = !!original; return; }
        if (!file.type.startsWith('image/')) return;
        previewUrl = URL.createObjectURL(file);
        image.src = previewUrl; image.hidden = false; placeholder.hidden = true;
    });
    const showImageError = () => {
        image.hidden = true; placeholder.hidden = false;
        document.getElementById('productImageMessage').textContent = original ? 'រកមិនឃើញឯកសាររូបភាព' : 'មិនទាន់មានរូបភាព';
    };
    image.addEventListener('error', showImageError);
    if (original && image.complete && image.naturalWidth === 0) showImageError();
});
</script>
@endpush
