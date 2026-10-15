<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Dashboard Gestionnaire</title>
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
                <h1 class="page-title">🏪 Dashboard — {{ Auth::user()->name }}</h1>
                <p class="page-subtitle">
                    {{ Auth::user()->entrepot->nom ?? '' }}
                    — Type : {{ ucfirst(Auth::user()->entrepot->type ?? '') }}
                </p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card" style="border-color:#003087;">
                    <i class="bi bi-boxes stat-icon" style="color:#003087;"></i>
                    <div class="stat-label">Stock total</div>
                    <div class="stat-value" style="color:#003087;">{{ $stockTotal }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-color:#2e7d32;">
                    <i class="bi bi-box-seam stat-icon" style="color:#2e7d32;"></i>
                    <div class="stat-label">Produits</div>
                    <div class="stat-value" style="color:#2e7d32;">{{ $totalProduits }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-color:#c8102e;">
                    <i class="bi bi-bell stat-icon" style="color:#c8102e;"></i>
                    <div class="stat-label">Alertes</div>
                    <div class="stat-value" style="color:#c8102e;">{{ $totalAlertes }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-color:#b8972e;">
                    <i class="bi bi-truck stat-icon" style="color:#b8972e;"></i>
                    <div class="stat-label">Transferts en cours</div>
                    <div class="stat-value" style="color:#b8972e;">{{ $transfertsEnCours }}</div>
                </div>
            </div>
        </div>

        {{-- Pilotage : valeur du stock + prévisions de rupture --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-3">
                <x-valeur-stock :valeur="$valeurStock" libelle="Valeur de mon stock" />
            </div>
            <div class="col-lg-9">
                <x-previsions-rupture :previsions="$previsions" />
            </div>
        </div>

        {{-- Graphiques --}}
        <div class="row g-4 mb-4">
            {{-- Graphique donut stock par produit --}}
            <div class="col-md-5">
                <div class="card-custom p-4">
                    <div class="card-header-custom mb-3">
                        <i class="bi bi-pie-chart"></i> Répartition du stock
                    </div>
                    @if($stockParProduit->isEmpty())
                        <div class="text-center py-4" style="color:#aaa;">Aucun stock</div>
                    @else
                        <canvas id="chartDonut" height="280"></canvas>
                    @endif
                </div>
            </div>

            {{-- Graphique mouvements 7 jours --}}
            <div class="col-md-7">
                <div class="card-custom p-4">
                    <div class="card-header-custom mb-3">
                        <i class="bi bi-graph-up"></i> Mouvements des 7 derniers jours
                    </div>
                    <canvas id="chartMouvements" height="250"></canvas>
                </div>
            </div>
        </div>

        {{-- Derniers mouvements --}}
        <div class="card-custom">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history"></i> Derniers mouvements</span>
                <a href="{{ route('gestionnaire.mouvements.index') }}"
                   style="font-size:0.8rem; color:#003087; text-decoration:none; font-weight:600;">
                    Voir tout →
                </a>
            </div>
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Produit</th>
                        <th>Type</th>
                        <th>Quantité</th>
                        <th>Par</th>
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
                            <td>{{ $m->user->name }}</td>
                            <td style="color:#aaa;">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color:#aaa;">
                                Aucun mouvement enregistré
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        @if($stockParProduit->isNotEmpty())
        // ══ Graphique Donut ══
        new Chart(document.getElementById('chartDonut'), {
            type: 'doughnut',
            data: {
                labels: @json($stockParProduit->pluck('nom')),
                datasets: [{
                    data: @json($stockParProduit->pluck('quantite')),
                    backgroundColor: [
                        '#003087', '#c8102e', '#b8972e', '#2e7d32',
                        '#1565c0', '#6a1b9a', '#e65100', '#00838f'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'right' }
                },
                cutout: '65%'
            }
        });
        @endif

        // ══ Graphique Mouvements 7 jours ══
        new Chart(document.getElementById('chartMouvements'), {
            type: 'line',
            data: {
                labels: @json($mouvements7Jours->pluck('jour')),
                datasets: [
                    {
                        label: 'Entrées',
                        data: @json($mouvements7Jours->pluck('entrees')),
                        borderColor: '#2e7d32',
                        backgroundColor: 'rgba(46,125,50,0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#2e7d32',
                    },
                    {
                        label: 'Sorties',
                        data: @json($mouvements7Jours->pluck('sorties')),
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
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>