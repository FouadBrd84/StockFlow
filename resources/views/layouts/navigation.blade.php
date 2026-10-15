<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">

<nav class="navbar navbar-expand-lg" style="background:#fff; box-shadow:0 2px 12px rgba(0,48,135,0.1); border-bottom:3px solid #c8102e;">
    <div class="container-fluid px-4">

        {{-- Logos + Titre --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="{{ asset('images/carrefour.png') }}" alt="Carrefour" height="36">
            <div style="width:1px; height:28px; background:#e0e0e0;"></div>
            <img src="{{ asset('images/adg.webp') }}" alt="ADG" height="34" style="border-radius:4px;">
            <div style="width:1px; height:28px; background:#e0e0e0;"></div>
            <span style="color:#003087; font-weight:800; font-size:1.1rem; letter-spacing:1px;">StockFlow</span>
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
                    <a class="nav-link {{ request()->routeIs('admin.alertes*') ? 'active' : '' }}"
                       href="{{ route('admin.alertes.index') }}"
                       style="color:#c8102e !important;">
                        <i class="bi bi-bell-fill me-1"></i>Alertes
                    </a>

                    {{-- Dropdown Gestion --}}
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-1"></i>Gestion
                        </a>
                        <ul class="dropdown-menu shadow"
                            style="border:none; border-radius:12px; border-top:3px solid #003087; min-width:190px; box-shadow:0 8px 24px rgba(0,48,135,0.12) !important;">
                            
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('admin.fournisseurs.index') }}"
                                   style="font-size:0.88rem; color:#333;">
                                    <i class="bi bi-truck me-2" style="color:#003087;"></i>Fournisseurs
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('admin.produits.index') }}"
                                   style="font-size:0.88rem; color:#333;">
                                    <i class="bi bi-box-seam me-2" style="color:#003087;"></i>Produits
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('admin.mouvements.index') }}"
                                   style="font-size:0.88rem; color:#333;">
                                    <i class="bi bi-clock-history me-2" style="color:#003087;"></i>Mouvements
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('admin.users.index') }}"
                                   style="font-size:0.88rem; color:#333;">
                                    <i class="bi bi-people me-2" style="color:#003087;"></i>Utilisateurs
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
                    <a class="nav-link {{ request()->routeIs('gestionnaire.alertes*') ? 'active' : '' }}"
                       href="{{ route('gestionnaire.alertes.index') }}"
                       style="color:#c8102e !important;">
                        <i class="bi bi-bell-fill me-1"></i>Alertes
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

            @endauth
        </div>
{{-- Accessibilité --}}
<div class="d-flex align-items-center gap-1 me-2">
    <button onclick="changerTaille(1)" title="Agrandir le texte"
            class="btn btn-sm"
            style="background:#f0f4ff; color:#003087; border-radius:8px; padding:4px 10px; font-weight:800; font-size:0.85rem; border:1px solid #e0e0e0;">
        A+
    </button>
    <button onclick="changerTaille(-1)" title="Réduire le texte"
            class="btn btn-sm"
            style="background:#f0f4ff; color:#003087; border-radius:8px; padding:4px 10px; font-weight:800; font-size:0.85rem; border:1px solid #e0e0e0;">
        A-
    </button>
    <button onclick="toggleContraste()" title="Contraste élevé" id="btnContraste"
            class="btn btn-sm"
            style="background:#f0f4ff; color:#003087; border-radius:8px; padding:4px 10px; font-size:0.85rem; border:1px solid #e0e0e0;">
        🌙
    </button>
</div>
        {{-- Droite : User + Déconnexion --}}
<div class="d-flex align-items-center gap-2">
    @auth
       <div class="d-flex align-items-center gap-2 px-3 py-1"
     style="background:#f0f4ff; border-radius:20px;">
    <i class="bi bi-person-circle" style="color:#003087; font-size:1rem;"></i>
    <a href="{{ route('profil.edit') }}"
       style="color:#003087; font-size:0.85rem; font-weight:600; text-decoration:none; max-width:140px;
              overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
        {{ Auth::user()->name }}
    </a>
</div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm fw-bold"
                    style="background:#c8102e; color:#fff; border-radius:20px;
                           padding:7px 16px; font-size:0.83rem; border:none;">
                <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
            </button>
        </form>
    @else
        <a href="{{ route('login') }}" class="btn btn-sm"
           style="color:#003087; border:1.5px solid #003087; border-radius:20px;
                  padding:6px 16px; font-size:0.85rem; font-weight:600;">
            Connexion
        </a>
        <a href="{{ route('register') }}" class="btn btn-sm fw-bold"
           style="background:#c8102e; color:#fff; border-radius:20px;
                  padding:6px 16px; font-size:0.85rem; border:none;">
            Inscription
        </a>
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