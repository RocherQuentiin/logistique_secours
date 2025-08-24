@extends('layouts.app')

@section('title', 'Accueil — AVSS78 logistique')

@section('content')
<div class="space-y-6">
            <div class="form-toolbar">
                <h2 class="text-2xl font-semibold">Tableau de bord</h2>
                <div class="flex items-center gap-2">
                            <form method="GET" action="/" class="flex items-center gap-2">
                        <select name="location_id" class="select">
                            <option value="">Tous les lieux</option>
                            @if(!empty($locations))
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" @selected(($filters['location_id'] ?? null) == $loc->id)>{{ $loc->name }}</option>
                                @endforeach
                            @endif
                        </select>
                                <select name="product_id" class="select">
                                    <option value="">Tous les produits</option>
                                    @if(!empty($products))
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" @selected(($filters['product_id'] ?? null) == $p->id)>{{ $p->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                        <select name="period" class="select">
                            @php($choices=[30=>'30j',60=>'60j',90=>'90j',180=>'180j'])
                            @foreach($choices as $d=>$lbl)
                                <option value="{{ $d }}" @selected(($filters['period'] ?? 60)==$d)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @if(request('mock'))
                            <input type="hidden" name="mock" value="1" />
                        @endif
                        <button class="btn btn-primary">Appliquer</button>
                                <a href="{{ request('mock') ? url('/?mock=1') : url('/') }}" class="btn btn-ghost" title="Réinitialiser">Réinitialiser</a>
                    </form>
                    <a href="{{ request('mock') ? url('/?mock=0') : url('/?mock=1') }}" class="btn btn-ghost" title="Basculer mock">
                        {{ request('mock') ? 'Données réelles' : 'Demo (mock)' }}
                    </a>
                </div>
            </div>

    <!-- Alertes -->
    @if(!empty($alerts))
    <div class="grid md:grid-cols-3 gap-4">
        <button type="button" class="card p-4 border border-red-200 bg-red-50 dark:bg-red-950/30 text-left hover:shadow" data-modal-target="#modal-expired">
            <div class="text-sm text-red-700 dark:text-red-300">Lots périmés</div>
            <div class="text-2xl font-bold mt-1">{{ number_format($alerts['expired'] ?? 0, 0, ',', ' ') }}</div>
            <div class="text-xs text-red-700/70 dark:text-red-300/70 mt-1">Cliquer pour voir le détail</div>
        </button>
        <button type="button" class="card p-4 border border-amber-200 bg-amber-50 dark:bg-amber-950/30 text-left hover:shadow" data-modal-target="#modal-expiring">
            <div class="text-sm text-amber-800 dark:text-amber-300">Péremptions ≤ {{ $filters['period'] ?? 60 }}j</div>
            <div class="text-2xl font-bold mt-1">{{ number_format($alerts['expiring'] ?? 0, 0, ',', ' ') }}</div>
            <div class="text-xs text-amber-800/70 dark:text-amber-300/70 mt-1">Cliquer pour voir le détail</div>
        </button>
        <button type="button" class="card p-4 border border-orange-200 bg-orange-50 dark:bg-orange-950/30 text-left hover:shadow" data-modal-target="#modal-lowstock">
            <div class="text-sm text-orange-800 dark:text-orange-300">Stocks bas (≤ {{ $lowThreshold ?? 10 }})</div>
            <div class="text-2xl font-bold mt-1">{{ number_format($alerts['lowStock'] ?? 0, 0, ',', ' ') }}</div>
            <div class="text-xs text-orange-800/70 dark:text-orange-300/70 mt-1">Cliquer pour voir le détail</div>
        </button>
    </div>
    @endif

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
        // Simple modal logic (no framework)
        function qs(s,root){return (root||document).querySelector(s)}
        function qsa(s,root){return Array.from((root||document).querySelectorAll(s))}
        qsa('[data-modal-target]').forEach(btn=>{
            btn.addEventListener('click',()=>{
                const sel = btn.getAttribute('data-modal-target');
                const el = qs(sel);
                if (!el) return;
                el.classList.remove('hidden');
            });
        });
        qsa('.modal [data-close], .modal [data-modal-close]').forEach(btn=>{
            btn.addEventListener('click',()=> btn.closest('.modal')?.classList.add('hidden'));
        });
        qsa('.modal').forEach(modal=>{
            modal.addEventListener('click',(e)=>{
                if (e.target === modal) modal.classList.add('hidden');
            });
        });

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
@php($lists = $lists ?? ['expired'=>[], 'expiring'=>[], 'lowStock'=>[]])
<!-- Modals: expired -->
<div id="modal-expired" class="modal fixed inset-0 bg-black/40 backdrop-blur-sm hidden p-4 z-50">
    <div class="mx-auto max-w-3xl w-full mt-10 card bg-white dark:bg-neutral-900 p-0 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-white/10">
            <h4 class="font-semibold">Lots périmés</h4>
            <button class="btn btn-ghost" data-close>Fermer</button>
        </div>
        <div class="p-4 overflow-auto max-h-[70vh]">
            @if(($lists['expired'] ?? collect())->isEmpty())
                <div class="text-sm text-gray-500">Aucun lot périmé selon les filtres.</div>
            @else
            <div class="data-table overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th class="text-left p-2">Produit</th>
                            <th class="text-left p-2">Lot</th>
                            <th class="text-left p-2">Lieu</th>
                            <th class="text-right p-2">Qté</th>
                            <th class="text-left p-2">Date péremption</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lists['expired'] as $b)
                        <tr>
                            <td class="p-2">{{ is_array($b)?($b['product'] ?? $b['product_name'] ?? '—') : ($b->product->name ?? '—') }}</td>
                            <td class="p-2">{{ is_array($b)?($b['batch'] ?? '—') : ($b->name ?? '—') }}</td>
                            <td class="p-2">{{ is_array($b)?($b['location'] ?? '—') : ($b->location->name ?? '—') }}</td>
                            <td class="p-2 text-right">{{ number_format(is_array($b)?($b['qty'] ?? 0):($b->quantity ?? 0),0,',',' ') }}</td>
                            <td class="p-2">{{ is_array($b)?($b['expiry_date'] ?? '—') : optional($b->expiry_date)->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    <div class="absolute inset-0" data-modal-close></div>
    <style>
        .modal .card{ box-shadow: 0 10px 30px rgba(0,0,0,.2) }
    </style>
</div>

<!-- Modals: expiring -->
<div id="modal-expiring" class="modal fixed inset-0 bg-black/40 backdrop-blur-sm hidden p-4 z-50">
    <div class="mx-auto max-w-3xl w-full mt-10 card bg-white dark:bg-neutral-900 p-0 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-white/10">
            <h4 class="font-semibold">Péremptions ≤ {{ $filters['period'] ?? 60 }}j</h4>
            <button class="btn btn-ghost" data-close>Fermer</button>
        </div>
        <div class="p-4 overflow-auto max-h-[70vh]">
            @if(($lists['expiring'] ?? collect())->isEmpty())
                <div class="text-sm text-gray-500">Aucun lot concerné selon les filtres.</div>
            @else
            <div class="data-table overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th class="text-left p-2">Produit</th>
                            <th class="text-left p-2">Lot</th>
                            <th class="text-left p-2">Lieu</th>
                            <th class="text-right p-2">Qté</th>
                            <th class="text-left p-2">Date péremption</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lists['expiring'] as $b)
                        <tr>
                            <td class="p-2">{{ is_array($b)?($b['product'] ?? $b['product_name'] ?? '—') : ($b->product->name ?? '—') }}</td>
                            <td class="p-2">{{ is_array($b)?($b['batch'] ?? '—') : ($b->name ?? '—') }}</td>
                            <td class="p-2">{{ is_array($b)?($b['location'] ?? '—') : ($b->location->name ?? '—') }}</td>
                            <td class="p-2 text-right">{{ number_format(is_array($b)?($b['qty'] ?? 0):($b->quantity ?? 0),0,',',' ') }}</td>
                            <td class="p-2">{{ is_array($b)?($b['expiry_date'] ?? '—') : optional($b->expiry_date)->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    <div class="absolute inset-0" data-modal-close></div>
</div>

<!-- Modals: low stock -->
<div id="modal-lowstock" class="modal fixed inset-0 bg-black/40 backdrop-blur-sm hidden p-4 z-50">
    <div class="mx-auto max-w-2xl w-full mt-10 card bg-white dark:bg-neutral-900 p-0 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-white/10">
            <h4 class="font-semibold">Stocks bas (≤ {{ $lowThreshold ?? 10 }})</h4>
            <button class="btn btn-ghost" data-close>Fermer</button>
        </div>
        <div class="p-4 overflow-auto max-h-[70vh]">
            @if(($lists['lowStock'] ?? collect())->isEmpty())
                <div class="text-sm text-gray-500">Aucun produit sous le seuil selon les filtres.</div>
            @else
            <div class="data-table overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th class="text-left p-2">Produit</th>
                            <th class="text-right p-2">Qté totale</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lists['lowStock'] as $r)
                        <tr>
                            <td class="p-2">{{ is_array($r)?($r['product_name'] ?? '—') : ($r['product_name'] ?? '—') }}</td>
                            <td class="p-2 text-right">{{ number_format(is_array($r)?($r['qty'] ?? 0):($r['qty'] ?? 0),0,',',' ') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    <div class="absolute inset-0" data-modal-close></div>
</div>
@endsection
