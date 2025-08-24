<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'AVSS78 logistique')</title>
    <!-- Compiled assets: prefer public/build when present, otherwise use Vite dev inclusion -->
    <link rel="stylesheet" href="{{ asset('fallback.css') }}">
    @if (file_exists(public_path('build/manifest.json')))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $css = $manifest['resources/css/app.css']['file'] ?? null;
            $js = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if ($css)
            <link rel="stylesheet" href="{{ asset('build/' . $css) }}">
        @endif
        @if ($js)
            <script type="module" src="{{ asset('build/' . $js) }}"></script>
        @endif
    @else
        @vite('resources/js/app.js')
    @endif
</head>
<body class="min-h-screen">
    @include('partials.header')

    <main class="container mx-auto px-4 py-8">
        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3">
                <div class="font-semibold mb-1">Des erreurs ont été détectées :</div>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="mb-4 rounded border border-green-200 bg-green-50 text-green-800 px-4 py-3">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>
    <script>
        // Fallback theme toggle (works even if Vite/module JS isn't loaded)
        (function(){
            if (window.__avssThemeHook) return; window.__avssThemeHook = true;
            var root = document.documentElement; var key='theme';
            function apply(t){ if(t==='dark'){ root.setAttribute('data-theme','dark'); } else { root.removeAttribute('data-theme'); } }
            try { var saved = localStorage.getItem(key); if(saved){ apply(saved); } } catch(e){}
            function wire(){ var btn = document.getElementById('themeToggle'); if(!btn) return; function setIcon(){ var isDark = root.getAttribute('data-theme')==='dark'; btn.textContent = isDark ? '🌙' : '☀️'; btn.setAttribute('aria-label', isDark ? 'Passer en thème clair' : 'Passer en thème sombre'); btn.title = isDark ? 'Thème sombre' : 'Thème clair'; } setIcon(); btn.addEventListener('click', function(){ var dark=root.getAttribute('data-theme')==='dark'; var next=dark?'light':'dark'; apply(next); try{localStorage.setItem(key,next)}catch(e){} setIcon(); }); }
            if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', wire); } else { wire(); }
        })();
    </script>
    <script>
        // Fallback table search (works even if Vite/module JS isn't loaded)
        (function(){
            if (window.__avssTableSearchHook) return; window.__avssTableSearchHook = true;
            function filterTable(el, q){
                var table = el && el.tagName === 'TABLE' ? el : el && el.closest && el.closest('table');
                var term = (q||'').toLowerCase();
                var rows = table ? table.querySelectorAll('tbody tr') : null;
                if(!rows) return;
                rows.forEach(function(tr){
                    var text = tr.textContent.toLowerCase();
                    tr.style.display = (!term || text.indexOf(term) !== -1) ? '' : 'none';
                });
            }
            function wire(){
                document.querySelectorAll('input[data-table-search]').forEach(function(inp){
                    var sel = inp.getAttribute('data-target');
                    var target = sel ? document.querySelector(sel) : (inp.closest('.card') && inp.closest('.card').querySelector('table'));
                    if(!target) return;
                    var handler = function(){ filterTable(target, inp.value); };
                    inp.addEventListener('input', handler);
                    inp.addEventListener('keyup', handler);
                    inp.addEventListener('search', handler);
                });
            }
            if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', wire); } else { wire(); }
        })();
    </script>
</body>
</html>
