<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Gestion Logistique Carrefour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; color: #333; }

        /* NAVBAR */
        .navbar-home {
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,48,135,0.1);
            border-bottom: 3px solid #c8102e;
            padding: 12px 0;
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, #003087 0%, #0055b3 60%, #c8102e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(200,16,46,0.2);
            border-radius: 50%;
        }
        .hero-content { position: relative; z-index: 2; }
        .hero h1 { font-size: 3.5rem; font-weight: 900; color: #fff; line-height: 1.1; }
        .hero h1 span { color: #fbbf24; }
        .hero p { font-size: 1.1rem; color: rgba(255,255,255,0.85); line-height: 1.7; }
        .btn-hero-primary {
            background: #fff;
            color: #003087;
            border: none;
            border-radius: 12px;
            padding: 14px 32px;
            font-weight: 800;
            font-size: 1rem;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-hero-primary:hover { background: #f0f4ff; transform: translateY(-2px); color: #003087; }
        .btn-hero-secondary {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255,255,255,0.5);
            border-radius: 12px;
            padding: 14px 32px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-hero-secondary:hover { background: rgba(255,255,255,0.1); color: #fff; }

        /* STATS HERO */
        .stat-hero {
            background: rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .stat-hero-value { font-size: 2rem; font-weight: 900; color: #fff; }
        .stat-hero-label { font-size: 0.8rem; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 1px; }

        /* FEATURES */
        .features { background: #f4f6fb; padding: 80px 0; }
        .feature-card {
            background: #fff;
            border-radius: 16px;
            padding: 32px 24px;
            box-shadow: 0 2px 12px rgba(0,48,135,0.08);
            transition: all 0.2s;
            height: 100%;
            border-top: 4px solid;
        }
        .feature-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,48,135,0.15); }
        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        .feature-card h5 { font-weight: 800; color: #003087; margin-bottom: 8px; }
        .feature-card p { color: #666; font-size: 0.88rem; line-height: 1.6; }

        /* ROLES */
        .roles { background: #fff; padding: 80px 0; }
        .role-card {
            border-radius: 20px;
            padding: 36px 28px;
            color: #fff;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        .role-card::after {
            content: '';
            position: absolute;
            bottom: -20px;
            right: -20px;
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }
        .role-card h4 { font-weight: 800; margin-bottom: 12px; }
        .role-card ul { list-style: none; padding: 0; }
        .role-card ul li { padding: 6px 0; font-size: 0.88rem; opacity: 0.9; }
        .role-card ul li::before { content: '✓ '; font-weight: 700; }

        /* FLUX */
        .flux { background: #f4f6fb; padding: 80px 0; }
        .flux-step {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,48,135,0.06);
            text-align: center;
            position: relative;
        }
        .flux-step-number {
            width: 36px;
            height: 36px;
            background: #003087;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            margin: 0 auto 12px;
        }
        .flux-arrow { color: #003087; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; }

        /* CTA */
        .cta { background: #003087; padding: 80px 0; text-align: center; }

        /* FOOTER */
        footer { background: #1a1a2e; color: rgba(255,255,255,0.6); padding: 30px 0; text-align: center; font-size: 0.85rem; }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar-home">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/carrefour.png') }}" alt="Carrefour" height="36">
                <div style="width:1px; height:28px; background:#e0e0e0;"></div>
                <img src="{{ asset('images/adg.webp') }}" alt="ADG" height="34" style="border-radius:4px;">
                <div style="width:1px; height:28px; background:#e0e0e0;"></div>
                <span style="color:#003087; font-weight:800; font-size:1.1rem;">StockFlow</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('login') }}"
                   style="color:#003087; border:1.5px solid #003087; border-radius:20px; padding:7px 20px; font-size:0.85rem; font-weight:600; text-decoration:none;">
                    Connexion
                </a>
                <a href="{{ route('register') }}"
                   style="background:#c8102e; color:#fff; border-radius:20px; padding:7px 20px; font-size:0.85rem; font-weight:600; text-decoration:none;">
                    S'inscrire
                </a>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="container hero-content">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div style="display:inline-block; background:rgba(255,255,255,0.15); border-radius:20px; padding:6px 16px; font-size:0.8rem; color:#fff; margin-bottom:20px; font-weight:600;">
                        📦 Projet de fin d'année DWWM 2026
                    </div>
                    <h1 class="mb-4">
                        Gestion logistique
                        <span>Carrefour</span>
                        simplifiée
                    </h1>
                    <p class="mb-5">
                        StockFlow centralise la gestion des stocks entre le dépôt central et tous les magasins Carrefour. Commandes fournisseurs, transferts, alertes automatiques — tout en un seul endroit.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('login') }}" class="btn-hero-primary">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Accéder à la plateforme
                        </a>
                        <a href="{{ route('register') }}" class="btn-hero-secondary">
                            <i class="bi bi-person-plus me-2"></i>Créer un compte
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-hero">
                                <div class="stat-hero-value">3</div>
                                <div class="stat-hero-label">Rôles utilisateurs</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-hero">
                                <div class="stat-hero-value">10</div>
                                <div class="stat-hero-label">Tables en base</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-hero">
                                <div class="stat-hero-value">8</div>
                                <div class="stat-hero-label">Types de magasins</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-hero">
                                <div class="stat-hero-value">100%</div>
                                <div class="stat-hero-label">Traçabilité stock</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section class="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 style="color:#003087; font-weight:800; font-size:2rem;">Fonctionnalités principales</h2>
                <p style="color:#888; font-size:0.95rem;">Tout ce dont vous avez besoin pour gérer votre logistique</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card" style="border-color:#003087;">
                        <div class="feature-icon" style="background:#f0f4ff; color:#003087;">📦</div>
                        <h5>Gestion des stocks</h5>
                        <p>Suivez en temps réel le stock de chaque produit dans chaque entrepôt. Alertes automatiques si le stock est bas.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card" style="border-color:#c8102e;">
                        <div class="feature-icon" style="background:#fce8e8; color:#c8102e;">🚚</div>
                        <h5>Commandes & Transferts</h5>
                        <p>Passez des commandes aux fournisseurs et gérez les transferts du dépôt vers les magasins automatiquement.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card" style="border-color:#b8972e;">
                        <div class="feature-icon" style="background:#fff8e1; color:#b8972e;">📋</div>
                        <h5>Historique & Traçabilité</h5>
                        <p>Consultez l'historique complet de tous les mouvements avec filtres avancés et export PDF.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card" style="border-color:#2e7d32;">
                        <div class="feature-icon" style="background:#e8f5e9; color:#2e7d32;">🔔</div>
                        <h5>Alertes automatiques</h5>
                        <p>Recevez des alertes dès qu'un stock atteint le seuil critique. Évitez les ruptures de stock.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card" style="border-color:#1565c0;">
                        <div class="feature-icon" style="background:#e3f2fd; color:#1565c0;">👥</div>
                        <h5>Multi-utilisateurs</h5>
                        <p>3 rôles distincts : Admin, Gestionnaire de magasin et Fournisseur. Chacun a son propre espace.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card" style="border-color:#6a1b9a;">
                        <div class="feature-icon" style="background:#f3e5f5; color:#6a1b9a;">📊</div>
                        <h5>Tableau de bord</h5>
                        <p>Vue globale avec statistiques en temps réel pour l'administrateur et les gestionnaires.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ROLES -->
    <section class="roles">
        <div class="container">
            <div class="text-center mb-5">
                <h2 style="color:#003087; font-weight:800; font-size:2rem;">Les 3 acteurs de la plateforme</h2>
                <p style="color:#888;">Chaque utilisateur a son propre espace et ses propres responsabilités</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="role-card" style="background:linear-gradient(135deg, #003087, #0055b3);">
                        <div style="font-size:2.5rem; margin-bottom:16px;">🏭</div>
                        <h4>Administrateur</h4>
                        <ul>
                            <li>Gère le dépôt central</li>
                            <li>Commande aux fournisseurs</li>
                            <li>Valide les commandes magasins</li>
                            <li>Vue stock globale</li>
                            <li>Historique complet</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="role-card" style="background:linear-gradient(135deg, #c8102e, #e53935);">
                        <div style="font-size:2.5rem; margin-bottom:16px;">🚛</div>
                        <h4>Fournisseur</h4>
                        <ul>
                            <li>Gère son catalogue produits</li>
                            <li>Reçoit les commandes</li>
                            <li>Confirme les commandes</li>
                            <li>Marque les livraisons</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="role-card" style="background:linear-gradient(135deg, #2e7d32, #43a047);">
                        <div style="font-size:2.5rem; margin-bottom:16px;">🏪</div>
                        <h4>Gestionnaire</h4>
                        <ul>
                            <li>Gère son magasin</li>
                            <li>Commande au dépôt</li>
                            <li>Confirme les réceptions</li>
                            <li>Enregistre ventes/casses</li>
                            <li>Consulte ses alertes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FLUX -->
    <section class="flux">
        <div class="container">
            <div class="text-center mb-5">
                <h2 style="color:#003087; font-weight:800; font-size:2rem;">Flux logistique</h2>
                <p style="color:#888;">De la commande fournisseur jusqu'au magasin</p>
            </div>
            <div class="row align-items-center g-2">
                <div class="col">
                    <div class="flux-step">
                        <div class="flux-step-number">1</div>
                        <div style="font-size:1.5rem;">🏭</div>
                        <p style="font-size:0.78rem; color:#555; margin-top:8px; font-weight:600;">Fournisseur ajoute produits</p>
                    </div>
                </div>
                <div class="col-auto flux-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="col">
                    <div class="flux-step">
                        <div class="flux-step-number">2</div>
                        <div style="font-size:1.5rem;">📋</div>
                        <p style="font-size:0.78rem; color:#555; margin-top:8px; font-weight:600;">Admin commande</p>
                    </div>
                </div>
                <div class="col-auto flux-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="col">
                    <div class="flux-step">
                        <div class="flux-step-number">3</div>
                        <div style="font-size:1.5rem;">🚚</div>
                        <p style="font-size:0.78rem; color:#555; margin-top:8px; font-weight:600;">Fournisseur expédie</p>
                    </div>
                </div>
                <div class="col-auto flux-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="col">
                    <div class="flux-step">
                        <div class="flux-step-number">4</div>
                        <div style="font-size:1.5rem;">📦</div>
                        <p style="font-size:0.78rem; color:#555; margin-top:8px; font-weight:600;">Stock dépôt augmente</p>
                    </div>
                </div>
                <div class="col-auto flux-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="col">
                    <div class="flux-step">
                        <div class="flux-step-number">5</div>
                        <div style="font-size:1.5rem;">🏪</div>
                        <p style="font-size:0.78rem; color:#555; margin-top:8px; font-weight:600;">Magasin commande</p>
                    </div>
                </div>
                <div class="col-auto flux-arrow"><i class="bi bi-arrow-right"></i></div>
                <div class="col">
                    <div class="flux-step">
                        <div class="flux-step-number">6</div>
                        <div style="font-size:1.5rem;">✅</div>
                        <p style="font-size:0.78rem; color:#555; margin-top:8px; font-weight:600;">Stock magasin augmente</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <div class="container">
            <h2 style="color:#fff; font-weight:800; font-size:2rem; margin-bottom:12px;">
                Prêt à optimiser votre logistique ?
            </h2>
            <p style="color:rgba(255,255,255,0.8); margin-bottom:32px;">
                Rejoignez StockFlow et gérez vos stocks en toute simplicité
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('login') }}" class="btn-hero-primary">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                </a>
                <a href="{{ route('register') }}" class="btn-hero-secondary">
                    <i class="bi bi-person-plus me-2"></i>Créer un compte
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
                <img src="{{ asset('images/carrefour.png') }}" alt="Carrefour" height="28">
                <span style="color:rgba(255,255,255,0.3);">×</span>
                <img src="{{ asset('images/adg.webp') }}" alt="ADG" height="28" style="border-radius:4px; background:#fff; padding:2px;">
            </div>
            <p>StockFlow — Projet de fin d'année DWWM 2026</p>
            <p style="margin-top:4px; color:rgba(255,255,255,0.3); font-size:0.78rem;">
                Développé avec Laravel 12 · MySQL · Bootstrap 5
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>