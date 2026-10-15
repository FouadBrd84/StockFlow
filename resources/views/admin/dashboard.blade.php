<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    @include('layouts.navigation')

    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">🏭 Dashboard Administrateur</h1>
                <p class="page-subtitle">Vue globale de la plateforme StockFlow</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <div class="stat-card" style="border-color:#003087;">
                    <i class="bi bi-box-seam stat-icon" style="color:#003087;"></i>
                    <div class="stat-label">Produits</div>
                    <div class="stat-value" style="color:#003087;">{{ $totalProduits }}</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card" style="border-color:#2e7d32;">
                    <i class="bi bi-building stat-icon" style="color:#2e7d32;"></i>
                    <div class="stat-label">Magasins</div>
                    <div class="stat-value" style="color:#2e7d32;">{{ $totalEntrepots }}</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card" style="border-color:#b8972e;">
                    <i class="bi bi-truck stat-icon" style="color:#b8972e;"></i>
                    <div class="stat-label">Fournisseurs</div>
                    <div class="stat-value" style="color:#b8972e;">{{ $totalFournisseurs }}</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card" style="border-color:#1565c0;">
                    <i class="bi bi-people stat-icon" style="color:#1565c0;"></i>
                    <div class="stat-label">Utilisateurs</div>
                    <div class="stat-value" style="color:#1565c0;">{{ $totalUsers }}</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card" style="border-color:#6a1b9a;">
                    <i class="bi bi-bag-check stat-icon" style="color:#6a1b9a;"></i>
                    <div class="stat-label">Commandes</div>
                    <div class="stat-value" style="color:#6a1b9a;">{{ $totalCommandes }}</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card" style="border-color:#c8102e;">
                    <i class="bi bi-bell-fill stat-icon" style="color:#c8102e;"></i>
                    <div class="stat-label">Alertes</div>
                    <div class="stat-value" style="color:#c8102e;">{{ $totalAlertes }}</div>
                </div>
            </div>
        </div>

        {{-- Pilotage : valeur du stock + prévisions de rupture --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-3">
                <x-valeur-stock :valeur="$valeurStock" libelle="Valeur du stock (réseau)" />
            </div>
            <div class="col-lg-9">
                <x-previsions-rupture :previsions="$previsions" :avec-entrepot="true" />
            </div>
        </div>

        {{-- Graphiques --}}
        <div class="row g-4 mb-4">
            {{-- Graphique stock par entrepôt --}}
            <div class="col-md-6">
                <div class="card-custom p-4">
                    <div class="card-header-custom mb-3">
                        <i class="bi bi-bar-chart"></i> Stock par magasin
                    </div>
                    <canvas id="chartStock" height="250"></canvas>
                </div>
            </div>

            {{-- Graphique mouvements 7 jours --}}
            <div class="col-md-6">
                <div class="card-custom p-4">
                    <div class="card-header-custom mb-3">
                        <i class="bi bi-graph-up"></i> Mouvements des 7 derniers jours
                    </div>
                    <canvas id="chartMouvements" height="250"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Commandes en attente --}}
            <div class="col-md-6">
                <div class="card-custom h-100">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-hourglass me-2"></i>Commandes fournisseurs en attente</span>
                        <a href="{{ route('admin.commandes.index') }}"
                           style="font-size:0.8rem; color:#003087; text-decoration:none; font-weight:600;">
                            Voir tout →
                        </a>
                    </div>
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Fournisseur</th>
                                <th>Statut</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commandesEnAttente as $c)
                                <tr>
                                    <td class="ps-4">#{{ $c->id }}</td>
                                    <td class="fw-bold">{{ $c->fournisseur->name ?? '—' }}</td>
                                    <td><span class="badge-attente">En attente</span></td>
                                    <td style="color:#aaa;">{{ $c->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3" style="color:#aaa;">
                                        Aucune commande en attente ✅
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Derniers mouvements --}}
            <div class="col-md-6">
                <div class="card-custom h-100">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history me-2"></i>Derniers mouvements</span>
                        <a href="{{ route('admin.mouvements.index') }}"
                           style="font-size:0.8rem; color:#003087; text-decoration:none; font-weight:600;">
                            Voir tout →
                        </a>
                    </div>
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Produit</th>
                                <th>Type</th>
                                <th>Qté</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($derniersMouvements as $m)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $m->stock->produit->nom }}</td>
                                    <td>
                                        @if($m->type === 'entree')
                                            <span class="badge-entree">⬆ Entrée</span>
                                        @else
                                            <span class="badge-sortie">⬇ Sortie</span>
                                        @endif
                                    </td>
                                    <td>{{ $m->quantite }}</td>
                                    <td style="color:#aaa;">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3" style="color:#aaa;">
                                        Aucun mouvement
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ══ Graphique Stock par entrepôt ══
        const stockLabels = @json($stockParEntrepot->pluck('nom'));
        const stockData   = @json($stockParEntrepot->pluck('total'));

        new Chart(document.getElementById('chartStock'), {
            type: 'bar',
            data: {
                labels: stockLabels,
                datasets: [{
                    label: 'Stock total',
                    data: stockData,
                    backgroundColor: [
                        '#003087', '#c8102e', '#b8972e', '#2e7d32', '#1565c0', '#6a1b9a'
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // ══ Graphique Mouvements 7 jours ══
        const joursLabels = @json($mouvements7Jours->pluck('jour'));
        const entreesData = @json($mouvements7Jours->pluck('entrees'));
        const sortiesData = @json($mouvements7Jours->pluck('sorties'));

        new Chart(document.getElementById('chartMouvements'), {
            type: 'line',
            data: {
                labels: joursLabels,
                datasets: [
                    {
                        label: 'Entrées',
                        data: entreesData,
                        borderColor: '#2e7d32',
                        backgroundColor: 'rgba(46,125,50,0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#2e7d32',
                    },
                    {
                        label: 'Sorties',
                        data: sortiesData,
                        borderColor: '#c8102e',
                        backgroundColor: 'rgba(200,16,46,0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#c8102e',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
</body>
</html>