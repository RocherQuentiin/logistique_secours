<header class="sticky-header glassbar text-white">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between gap-4">
        <a href="/" class="brand">
            <div class="brand-mark">AV</div>
            <div>
                <div class="brand-title">AVSS78 logistique</div>
                <div class="brand-sub">Gestion des stocks</div>
            </div>
        </a>

        <nav class="desktop-only">
            <div class="nav-pills">
                <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Accueil</a>
                @auth
                    <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">Produits</a>
                    <a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">Lieux</a>
                    <a href="{{ route('batches.index') }}" class="nav-link {{ request()->routeIs('batches.*') ? 'active' : '' }}">Lots</a>
                    @if(in_array(auth()->user()->role, ['admin','dev']))
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">Utilisateurs</a>
                    @endif
                @endauth
            </div>
        </nav>

        <div class="header-actions">
            <button type="button" id="themeToggle" title="Thème" class="btn btn-ghost">🌓</button>
            @auth
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="btn btn-ghost">Déconnexion</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost">Connexion</a>
            @endauth
            <div class="mobile-menu">
                <details>
                    <summary class="btn btn-ghost">Menu</summary>
                    <div class="card p-2 mt-2 min-w-48">
                        <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Accueil</a>
                        @auth
                            <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">Produits</a>
                            <a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">Lieux</a>
                            <a href="{{ route('batches.index') }}" class="nav-link {{ request()->routeIs('batches.*') ? 'active' : '' }}">Lots</a>
                            @if(in_array(auth()->user()->role, ['admin','dev']))
                                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">Utilisateurs</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="inline mt-2">
                                @csrf
                                <button class="btn btn-ghost w-full">Déconnexion</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-ghost w-full mt-2">Connexion</a>
                        @endauth
                    </div>
                </details>
            </div>
        </div>
    </div>
</header>
