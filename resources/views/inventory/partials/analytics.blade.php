@push('styles')
<style>
    .inventory-page { background:#f5f7fb; }
    .inventory-page .stat-label { font-size:13px; letter-spacing:0; }
    .inventory-page .stat-card { border-radius:16px; padding:22px; }
    .inventory-page .stat-value { font-size:34px; }
    .analytics-grid { display:grid; grid-template-columns:minmax(0,1.85fr) minmax(0,1fr); gap:20px; margin-bottom:24px; }
    .analytics-card { background:#fff; border:1px solid #e9edf3; border-radius:16px; padding:22px; box-shadow:0 4px 20px #0f172a05; min-width:0; }
    .analytics-card h3 { font-size:17px; font-weight:800; margin:0 0 8px; color:#0f172a; }
    .analytics-card p,.analytics-muted { font-size:12px; color:#64748b; line-height:1.7; }
    .analytics-card-head { display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:18px; }
    .analytics-date-form { display:flex; align-items:center; flex-wrap:wrap; gap:6px; }
    .analytics-date-form input,.analytics-date-form select { border:1px solid #dbe3ed; border-radius:8px; padding:9px 10px; font-size:13px; min-width:0; background:#fff; }
    .analytics-date-form select { max-width:240px; }
    .analytics-date-form button { background:#e85d24; color:#fff; border:0; padding:10px 14px; border-radius:8px; font-size:13px; font-weight:700; }
    .analytics-date-form button:hover { background:#d94a10; }
    .analytics-canvas { position:relative; height:245px; }
    .analytics-graph-card .analytics-canvas { height:270px; }
    .analytics-interval { display:inline-flex; align-items:center; background:#fff3eb; border-radius:999px; color:#c2410c; font-size:12px; font-weight:700; padding:4px 10px; }
    .analytics-graph-title { display:flex; align-items:center; flex-wrap:wrap; gap:8px; }
    .analytics-graph-title h3 { margin:0; }
    .analytics-legend { display:flex; justify-content:space-between; gap:10px; margin-top:20px; }
    .analytics-legend div { font-size:12px; color:#64748b; }
    .analytics-legend strong { display:block; color:#0f172a; margin-top:5px; font-size:17px; }
    .analytics-dot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:5px; }
    @media(max-width:1000px) { .analytics-grid { grid-template-columns:1fr; } }
    @media(max-width:600px) { .analytics-card { padding:16px; } .analytics-date-form { width:100%; } .analytics-date-form select { max-width:100%; width:100%; } .analytics-date-form input { width:40%; } }
</style>
@endpush
<div class="analytics-grid">
    <section class="analytics-card analytics-graph-card">
        <div class="analytics-card-head">
            <div class="analytics-graph-title">
                <h3>ទំនិញចេញពីស្តុក</h3>
                <span class="analytics-interval">{{ ['day' => 'ប្រចាំថ្ងៃ', 'week' => 'ប្រចាំសប្តាហ៍', 'month' => 'ប្រចាំខែ'][$analytics['granularity']] }}</span>
            </div>
            <form class="analytics-date-form" method="GET" action="{{ route('inventory.index') }}">
                <select name="chart_product_id" aria-label="ជ្រើសផលិតផល">
                    <option value="">ផលិតផលទាំងអស់</option>
                    @foreach($analytics['products'] as $item)
                        <option value="{{ $item->product_id }}" @selected((string) request('chart_product_id') === (string) $item->product_id)>{{ $item->product->name }} ({{ $item->product->unitLabel() }})</option>
                    @endforeach
                </select>
                @foreach(request()->except(['chart_from','chart_to','chart_product_id','page']) as $key => $value)
                    @if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif
                @endforeach
                <input type="date" aria-label="ចាប់ពីថ្ងៃ" name="chart_from" value="{{ $chartFrom }}" max="{{ now()->toDateString() }}" required>
                <input type="date" aria-label="ដល់ថ្ងៃ" name="chart_to" value="{{ $chartTo }}" max="{{ now()->toDateString() }}" required>
                <button type="submit">បង្ហាញ</button>
            </form>
        </div>
        <div class="analytics-canvas"><canvas id="inventoryOutflowChart" role="img" aria-label="ចំនួនទំនិញចេញពីស្តុក បែងចែកលក់ ថែម និងផ្សេងៗ"></canvas></div>
        @unless(request()->filled('chart_product_id'))
            <p class="mt-2 mb-0">សរុបឯកតាផលិតផលចម្រុះ។ ជ្រើសផលិតផលដើម្បីមើលចំនួនតាមខ្នាតរបស់វា។</p>
        @endunless
        <p id="inventoryChartError" class="text-danger" hidden>មិនអាចផ្ទុកក្រាហ្វបានទេ។ សូមផ្ទុកទំព័រឡើងវិញ។</p>
    </section>
    <section class="analytics-card">
        <h3>ស្ថានភាពស្តុកសរុប</h3><p>ទំនិញទាំងអស់ · {{ number_format($stats['total']) }} មុខ</p>
        <div class="analytics-canvas"><canvas id="inventoryStatusChart" role="img" aria-label="សមាមាត្រស្ថានភាពស្តុក"></canvas>
        @if(!$stats['total'])<p class="text-center mt-3">មិនទាន់មានទិន្នន័យស្តុក</p>@endif</div>
        <div class="analytics-legend">
            @foreach([['in_stock','មានក្នុងស្តុក','#10b981'],['low_stock','ជិតអស់','#f59e0b'],['out_stock','អស់ស្តុក','#ef4444']] as [$key,$label,$color])
                <div><span class="analytics-dot" style="background:{{ $color }}"></span>{{ $label }}<strong>{{ $stats[$key] }} <small>({{ $stats['total'] ? number_format($stats[$key] / $stats['total'] * 100, 1) : '0' }}%)</small></strong></div>
            @endforeach
        </div>
    </section>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') { document.getElementById('inventoryChartError').hidden = false; return; }
    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    const labels = @js($analytics['labels']);
    const sold = @js($analytics['sold']);
    const free = @js($analytics['free']);
    const other = @js($analytics['other']);
    const status = @js([$stats['in_stock'], $stats['low_stock'], $stats['out_stock']]);
    const outflowTotal = sold.reduce((sum, value, index) => sum + value + free[index] + other[index], 0);
    new Chart(document.getElementById('inventoryOutflowChart'), {
        type:'bar',
        data:{labels,datasets:[
            {label:'លក់',data:sold,backgroundColor:'#e85d24',stack:'outflow',borderRadius:5,borderSkipped:false,maxBarThickness:48},
            {label:'ថែម',data:free,backgroundColor:'#3b82f6',stack:'outflow',borderRadius:5,borderSkipped:false,maxBarThickness:48},
            {label:'ផ្សេងៗ',data:other,backgroundColor:'#94a3b8',stack:'outflow',borderRadius:5,borderSkipped:false,maxBarThickness:48}
        ]},
        options:{
            responsive:true,maintainAspectRatio:false,interaction:{intersect:false,mode:'index'},
            datasets:{bar:{categoryPercentage:.7,barPercentage:.9}},
            layout:{padding:{top:8,right:8}},
            plugins:{
                legend:{position:'bottom',labels:{usePointStyle:true,pointStyle:'circle',boxWidth:9,padding:20,color:'#475569',font:{size:13,weight:'600'}}},
                tooltip:{backgroundColor:'#0f172a',padding:12,cornerRadius:10,displayColors:true,bodySpacing:6,
                    filter:item=>item.parsed.y>0,
                    callbacks:{label:ctx=>`${ctx.dataset.label}: ${Number(ctx.parsed.y).toLocaleString()}`,footer:items=>`សរុប: ${items.reduce((sum,item)=>sum+Number(item.parsed.y),0).toLocaleString()}`}
                }
            },
            scales:{
                x:{stacked:true,grid:{display:false},border:{display:false},ticks:{maxTicksLimit:9,color:'#64748b',maxRotation:0,autoSkip:true}},
                y:{stacked:true,beginAtZero:true,border:{display:false},suggestedMax:outflowTotal ? undefined : 1,ticks:{precision:0,color:'#64748b',callback:value=>Number(value).toLocaleString()},grid:{color:'#eef2f7'}}
            }
        }
    });
    new Chart(document.getElementById('inventoryStatusChart'), {
        type:'doughnut', data:{labels:['មានក្នុងស្តុក','ជិតអស់','អស់ស្តុក'],datasets:[{data:status,backgroundColor:['#10b981','#f59e0b','#ef4444'],borderWidth:4,borderColor:'#fff',hoverOffset:5}]},
        options:{responsive:true,maintainAspectRatio:false,cutout:'76%',plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>{const total=status.reduce((a,b)=>a+b,0); return `${ctx.label}: ${ctx.raw} (${total ? (ctx.raw/total*100).toFixed(1) : 0}%)`;}}}}}
    });
});
</script>
@endpush
