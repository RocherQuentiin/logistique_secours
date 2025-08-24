@extends('layouts.app')

@section('title', 'Accueil — AVSS78 logistique')

@section('content')
<div class="space-y-6">
        <div class="form-toolbar">
            <h2 class="text-2xl font-semibold">Tableau de bord</h2>
               <a href="{{ request('mock') ? url('/?mock=0') : url('/?mock=1') }}" class="btn btn-ghost" title="Basculer mock">
                   {{ request('mock') ? 'Données réelles' : 'Demo (mock)' }}
               </a>
        </div>

    <!-- KPIs -->
    <div class="grid md:grid-cols-4 gap-4">
        @foreach($kpis as $label => $val)
            <div class="card p-4">
                <div class="text-sm text-gray-500">{{ $label }}</div>
                <div class="text-2xl font-bold mt-1">{{ number_format($val, 0, ',', ' ') }}</div>
            </div>
        @endforeach
    </div>

    <!-- Charts -->
    <div class="grid lg:grid-cols-2 gap-4">
        <div class="card p-4">
            <h3 class="font-semibold mb-2">Quantités par lieu</h3>
            <canvas id="byLocationChart" height="140"></canvas>
        </div>
        <div class="card p-4">
            <h3 class="font-semibold mb-2">Top produits par quantité</h3>
            <canvas id="topProductsChart" height="140"></canvas>
        </div>
        <div class="card p-4 lg:col-span-2">
            <h3 class="font-semibold mb-2">Péremptions sur 6 mois</h3>
            <canvas id="expirationsChart" height="120"></canvas>
        </div>
    </div>
</div>

<!-- Charts JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // Fallback if CDN blocked: try another provider
    (function(){
        function inject(src){ var s=document.createElement('script'); s.src=src; document.head.appendChild(s); }
        if (!window.Chart) { setTimeout(function(){ if (!window.Chart) inject('https://unpkg.com/chart.js@4.4.3/dist/chart.umd.min.js'); }, 300); }
    })();
</script>
<script>
    (function(){
        const cssVar = (v)=> getComputedStyle(document.documentElement).getPropertyValue(v).trim() || undefined;
        const accent = cssVar('--avss78-accent') || '#eee234';
        const primary = cssVar('--avss78-primary') || '#2e3d84';
        const gridColor = document.documentElement.getAttribute('data-theme')==='dark' ? 'rgba(255,255,255,.12)' : 'rgba(0,0,0,.08)';
        const textColor = document.documentElement.getAttribute('data-theme')==='dark' ? '#e7e9ee' : '#111';

        const byLocation = @json($byLocation);
        const topProducts = @json($topProducts);
        const expirations = @json($expirations);

        const makeCfg = (type, labels, datasets)=>({
            type,
            data: { labels, datasets },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: textColor } } },
                scales: {
                    x: { grid: { color: gridColor }, ticks: { color: textColor } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true }
                }
            }
        });

        // By location (bar)
        if (document.getElementById('byLocationChart')) {
            const labels = byLocation.map(i=>i.label);
            const data = byLocation.map(i=>i.qty);
            new Chart(document.getElementById('byLocationChart'), makeCfg('bar', labels, [{
                label: 'Quantité', data, backgroundColor: accent, borderColor: primary
            }]));
        }

        // Top products (horizontal bar)
        if (document.getElementById('topProductsChart')) {
            const labels = topProducts.map(i=>i.label);
            const data = topProducts.map(i=>i.qty);
            new Chart(document.getElementById('topProductsChart'), {
                ...makeCfg('bar', labels, [{ label: 'Quantité', data, backgroundColor: 'rgba(46,61,132,.15)', borderColor: primary }]),
                options: { ...makeCfg('bar', [], []).options, indexAxis: 'y' }
            });
        }

        // Expirations next 6 months (line)
        if (document.getElementById('expirationsChart')) {
            const labels = expirations.map(i=>i.label);
            const data = expirations.map(i=>i.count);
            new Chart(document.getElementById('expirationsChart'), makeCfg('line', labels, [{
                label: 'Lots expirant', data, borderColor: primary, backgroundColor: 'rgba(46,61,132,.2)', tension: .3, fill: true, pointRadius: 3
            }]));
        }

        // Optional: re-render on theme toggle
        const btn = document.getElementById('themeToggle');
        if (btn) btn.addEventListener('click', ()=> setTimeout(()=>location.reload(), 50));
    })();
</script>
@endsection
