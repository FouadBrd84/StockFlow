<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Dashboard Fournisseur</title>
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
                <h1 class="page-title">🚛 Dashboard — {{ Auth::user()->name }}</h1>
                <p class="page-subtitle">Espace fournisseur StockFlow</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card" style="border-color:#b8972e;">
                    <i class="bi bi-hourglass stat-icon" style="color:#b8972e;"></i>
                    <div class="stat-label">En attente</div>
                    <div class="stat-value" style="color:#b8972e;">{{ $commandesEnAttente }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-color:#1565c0;">
                    <i class="bi bi-check-circle stat-icon" style="color:#1565c0;"></i>
                    <div class="stat-label">Confirmées</div>
                    <div class="stat-value" style="color:#1565c0;">{{ $commandesConfirmees }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-color:#2e7d32;">
                    <i class="bi bi-truck stat-icon" style="color:#2e7d32;"></i>
                    <div class="stat-label">Expédiées</div>
                    <div class="stat-value" style="color:#2e7d32;">{{ $commandesExpediees }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-color:#003087;">
                    <i class="bi bi-bag-check stat-icon" style="color:#003087;"></i>
                    <div class="stat-label">Reçues</div>
                    <div class="stat-value" style="color:#003087;">{{ $commandesRecues }}</div>
                </div>
            </div>
        </div>

        {{-- Graphique --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card-custom p-4">
                    <div class="card-header-custom mb-3">
                        <i class="bi bi-bar-chart"></i> Statut de mes commandes
                    </div>
                    <canvas id="chartCommandes" height="250"></canvas>
                </div>
            </div>

            {{-- Dernières commandes --}}
            <div class="col-md-6">
                <div class="card-custom">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bag me-2"></i>Dernières commandes</span>
                        <a href="{{ route('fournisseur.commandes.index') }}"
                           style="font-size:0.8rem; color:#003087; text-decoration:none; font-weight:600;">
                            Voir toutes →
                        </a>
                    </div>
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Entrepôt</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dernieresCommandes as $c)
                                <tr>
                                    <td class="ps-4">#{{ $c->id }}</td>
                                    <td class="fw-bold">{{ $c->entrepot->nom }}</td>
                                    <td>
                                        @if($c->statut === 'en_attente')
                                            <span class="badge-attente">En attente</span>
                                        @elseif($c->statut === 'confirmee')
                                            <span class="badge-info">Confirmée</span>
                                        @elseif($c->statut === 'expediee')
                                            <span class="badge-ok">Expédiée 🚚</span>
                                        @else
                                            <span class="badge-ok">Reçue ✅</span>
                                        @endif
                                    </td>
                                    <td style="color:#aaa;">{{ $c->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if($c->statut === 'en_attente')
                                            <form method="POST"
                                                  action="{{ route('fournisseur.commandes.confirmer', $c) }}"
                                                  style="display:inline">
                                                @csrf
                                                <button class="btn btn-sm"
                                                        style="background:#e8f5e9; color:#2e7d32; border:1px solid #2e7d32; border-radius:8px; padding:4px 12px; font-size:0.8rem;">
                                                    <i class="bi bi-check"></i> Confirmer
                                                </button>
                                            </form>
                                        @elseif($c->statut === 'confirmee')
                                            <form method="POST"
                                                  action="{{ route('fournisseur.commandes.expedier', $c) }}"
                                                  style="display:inline">
                                                @csrf
                                                <button class="btn btn-sm"
                                                        style="background:#f0f4ff; color:#003087; border:1px solid #003087; border-radius:8px; padding:4px 12px; font-size:0.8rem;">
                                                    <i class="bi bi-truck"></i> Expédier
                                                </button>
                                            </form>
                                        @else
                                            <span style="color:#aaa;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4" style="color:#aaa;">
                                        Aucune commande reçue
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
        new Chart(document.getElementById('chartCommandes'), {
            type: 'bar',
            data: {
                labels: ['En attente', 'Confirmées', 'Expédiées', 'Reçues'],
                datasets: [{
                    label: 'Commandes',
                    data: [
                        {{ $commandesEnAttente }},
                        {{ $commandesConfirmees }},
                        {{ $commandesExpediees }},
                        {{ $commandesRecues }}
                    ],
                    backgroundColor: ['#b8972e', '#1565c0', '#2e7d32', '#003087'],
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>