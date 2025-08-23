<header class="bg-avss78-primary brand-header text-white shadow">
    <div class="container mx-auto px-4 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-md bg-avss78-accent flex items-center justify-center text-avss78-dark font-bold">AV</div>
            <div>
                <h1 class="text-lg font-semibold">AVSS78 logistique</h1>
                <p class="text-sm opacity-80">Gestion des stocks — interface de démonstration</p>
            </div>
        </div>

        <nav class="space-x-4">
            <a href="/" class="text-white/90 hover:text-white">Accueil</a>
            <a href="#" class="text-white/90 hover:text-white">Produits</a>
            <a href="#" class="text-white/90 hover:text-white">Batches</a>
            @auth
                @if(auth()->user()->role === 'dev')
                    <a href="{{ route('users.index') }}" class="text-white/90 hover:text-white">Utilisateurs</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="text-white/90 hover:text-white">Se déconnecter</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-white/90 hover:text-white">Se connecter</a>
            @endauth
        </nav>
    </div>
</header>
