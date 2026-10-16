<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Barlow:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/stockflow.css') }}?v={{ filemtime(public_path('css/stockflow.css')) }}">
<link rel="stylesheet" href="{{ asset('css/stockflow-themes.css') }}?v={{ filemtime(public_path('css/stockflow-themes.css')) }}">
@include('layouts.theme-init')

<nav class="navbar navbar-expand-lg sf-nav">
    <div class="container-fluid px-4">

        {{-- Logos + Titre --}}
        <a class="navbar-brand d-flex align-items-center gap-2 sf-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/stockflow-logo.svg') }}" alt="" height="38" class="sf-logo">
            <span class="sf-brand-name">StockFlow</span>
        </a>

        {{-- Menu central --}}
        <div class="d-flex align-items-center gap-1 mx-auto flex-wrap">
            @auth

                {{-- ADMIN --}}
                @if(Auth::user()->role === 'admin')
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.stocks*') ? 'active' : '' }}"
                       href="{{ route('admin.stocks.index') }}">
                        <i class="bi bi-boxes me-1"></i>Stock
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.commandes*') ? 'active' : '' }}"
                       href="{{ route('admin.commandes.index') }}">
                        <i class="bi bi-bag-check me-1"></i>Commandes
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.magasins*') ? 'active' : '' }}"
                       href="{{ route('admin.magasins.index') }}">
                        <i class="bi bi-shop me-1"></i>Magasins
                    </a>
                    <a class="nav-link sf-nav-alert {{ request()->routeIs('admin.alertes*') ? 'active' : '' }} {{ $alertesNonLues > 0 ? 'sf-has-alerts' : '' }}"
                       href="{{ route('admin.alertes.index') }}">
                        <i class="bi bi-bell-fill me-1"></i>Alertes
                        @if($alertesNonLues > 0)
                            <span class="sf-count" title="Alertes non lues">{{ $alertesNonLues > 99 ? '99+' : $alertesNonLues }}</span>
                        @endif
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}"
                       href="{{ route('admin.messages.index') }}">
                        <i class="bi bi-envelope-fill me-1"></i>Messages
                        @if($messagesNonLus > 0)
                            <span class="sf-count" title="Messages non lus">{{ $messagesNonLus > 99 ? '99+' : $messagesNonLus }}</span>
                        @endif
                    </a>

                    {{-- Dropdown Gestion --}}
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-1"></i>Gestion
                        </a>
                        <ul class="dropdown-menu" style="min-width:200px;">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.fournisseurs.index') }}">
                                    <i class="bi bi-truck me-2"></i>Fournisseurs
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.produits.index') }}">
                                    <i class="bi bi-box-seam me-2"></i>Produits
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.mouvements.index') }}">
                                    <i class="bi bi-clock-history me-2"></i>Mouvements
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                    <i class="bi bi-people me-2"></i>Utilisateurs
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif

                {{-- GESTIONNAIRE --}}
                @if(Auth::user()->role === 'gestionnaire')
                    <a class="nav-link {{ request()->routeIs('gestionnaire.dashboard') ? 'active' : '' }}"
                       href="{{ route('gestionnaire.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('gestionnaire.stocks*') ? 'active' : '' }}"
                       href="{{ route('gestionnaire.stocks.index') }}">
                        <i class="bi bi-boxes me-1"></i>Mon Stock
                    </a>
                    <a class="nav-link {{ request()->routeIs('gestionnaire.commandes*') ? 'active' : '' }}"
                       href="{{ route('gestionnaire.commandes.index') }}">
                        <i class="bi bi-cart me-1"></i>Commandes
                    </a>
                    <a class="nav-link {{ request()->routeIs('gestionnaire.sorties*') ? 'active' : '' }}"
                       href="{{ route('gestionnaire.sorties.index') }}">
                        <i class="bi bi-box-arrow-up me-1"></i>Sorties
                    </a>
                    <a class="nav-link {{ request()->routeIs('gestionnaire.mouvements*') ? 'active' : '' }}"
                       href="{{ route('gestionnaire.mouvements.index') }}">
                        <i class="bi bi-clock-history me-1"></i>Mouvements
                    </a>
                    <a class="nav-link sf-nav-alert {{ request()->routeIs('gestionnaire.alertes*') ? 'active' : '' }} {{ $alertesNonLues > 0 ? 'sf-has-alerts' : '' }}"
                       href="{{ route('gestionnaire.alertes.index') }}">
                        <i class="bi bi-bell-fill me-1"></i>Alertes
                        @if($alertesNonLues > 0)
                            <span class="sf-count" title="Alertes non lues">{{ $alertesNonLues > 99 ? '99+' : $alertesNonLues }}</span>
                        @endif
                    </a>
                @endif

                {{-- FOURNISSEUR --}}
                @if(Auth::user()->role === 'fournisseur')
                    <a class="nav-link {{ request()->routeIs('fournisseur.dashboard') ? 'active' : '' }}"
                       href="{{ route('fournisseur.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                    <a class="nav-link {{ request()->routeIs('fournisseur.produits*') ? 'active' : '' }}"
                       href="{{ route('fournisseur.produits.index') }}">
                        <i class="bi bi-box-seam me-1"></i>Mes Produits
                    </a>
                    <a class="nav-link {{ request()->routeIs('fournisseur.commandes*') ? 'active' : '' }}"
                       href="{{ route('fournisseur.commandes.index') }}">
                        <i class="bi bi-bag-check me-1"></i>Mes Commandes
                    </a>
                @endif

                {{-- Contacter l'admin (gestionnaire et fournisseur) --}}
                @if(in_array(Auth::user()->role, ['gestionnaire', 'fournisseur'], true))
                    <a class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}"
                       href="{{ route('contact.create') }}">
                        <i class="bi bi-envelope-paper me-1"></i>Contacter l'admin
                    </a>
                @endif

            @endauth
        </div>

        {{-- Accessibilité --}}
        <div class="d-flex align-items-center gap-1 me-2">
            @auth
                <button type="button" class="btn btn-sm sf-chip sf-cmdk-btn" data-sf-cmdk title="Rechercher une page (Ctrl + K)">
                    <i class="bi bi-search"></i><kbd>Ctrl K</kbd>
                </button>
                <button type="button" class="btn btn-sm sf-chip sf-install" data-sf-install title="Installer StockFlow sur cet appareil">
                    <i class="bi bi-phone"></i> Installer
                </button>
            @endauth
            <div class="dropdown">
                <button type="button" class="btn btn-sm sf-chip" data-bs-toggle="dropdown" aria-expanded="false" title="Changer de thème" aria-label="Changer de thème">
                    <i class="bi bi-palette2"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end sf-themes">
                    <li>
                        <button type="button" class="dropdown-item" data-sf-theme="neo">
                            <span class="sf-swatch sf-swatch-neo"></span>
                            <span><b>Neo</b><small>Coloré et joueur</small></span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-sf-theme="prestige">
                            <span class="sf-swatch sf-swatch-prestige"></span>
                            <span><b>Prestige</b><small>Sombre, épuré, haut de gamme</small></span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item" data-sf-theme="champions">
                            <span class="sf-swatch sf-swatch-champions"></span>
                            <span><b>Champions</b><small>La grande soirée européenne</small></span>
                        </button>
                    </li>
                </ul>
            </div>
            <button onclick="changerTaille(1)" title="Agrandir le texte" class="btn btn-sm sf-chip">A+</button>
            <button onclick="changerTaille(-1)" title="Réduire le texte" class="btn btn-sm sf-chip">A-</button>
            <button onclick="toggleContraste()" title="Contraste élevé" id="btnContraste" class="btn btn-sm sf-chip">🌙</button>
        </div>

        {{-- Droite : User + Déconnexion --}}
        <div class="d-flex align-items-center gap-2">
            @auth
                <div class="sf-user">
                    @if(Auth::user()->photo)
                        <img class="sf-avatar sf-avatar-photo" src="{{ Auth::user()->urlPhoto() }}" alt="Photo de profil de {{ Auth::user()->name }}">
                    @else
                        <span class="sf-avatar">{{ Auth::user()->initiale() }}</span>
                    @endif
                    <a href="{{ route('profil.edit') }}">{{ Auth::user()->name }}</a>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm sf-btn-logout" title="Se déconnecter" aria-label="Se déconnecter">
                        <i class="bi bi-box-arrow-right"></i> <span class="sf-libelle">Déconnexion</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm sf-btn-ghost">Connexion</a>
                <a href="{{ route('register') }}" class="btn btn-sm sf-btn-logout">Inscription</a>
            @endauth
        </div>

    </div>
</nav>

<script>
    // Agrandissement du texte
    let taille = parseFloat(localStorage.getItem('taille') || 1);
    document.documentElement.style.fontSize = (taille * 16) + 'px';

    function changerTaille(direction) {
        taille = Math.min(Math.max(taille + (direction * 0.1), 0.8), 1.5);
        document.documentElement.style.fontSize = (taille * 16) + 'px';
        localStorage.setItem('taille', taille);
    }

    // Contraste élevé
    if (localStorage.getItem('contraste') === '1') {
        document.body.classList.add('contraste-eleve');
        document.getElementById('btnContraste').innerHTML = '☀️';
    }

    function toggleContraste() {
        document.body.classList.toggle('contraste-eleve');
        const actif = document.body.classList.contains('contraste-eleve');
        localStorage.setItem('contraste', actif ? '1' : '0');
        document.getElementById('btnContraste').innerHTML = actif ? '☀️' : '🌙';
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/stockflow.js') }}?v={{ filemtime(public_path('js/stockflow.js')) }}"></script>
