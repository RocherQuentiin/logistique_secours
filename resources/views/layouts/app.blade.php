<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'AVSS78 logistique')</title>
    <!-- Compiled assets: prefer public/build when present, otherwise use Vite dev inclusion -->
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
        @yield('content')
    </main>
</body>
</html>
