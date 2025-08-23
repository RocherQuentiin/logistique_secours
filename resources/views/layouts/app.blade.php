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
<body class="bg-avss78-50 text-gray-900 min-h-screen">
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
</body>
</html>
