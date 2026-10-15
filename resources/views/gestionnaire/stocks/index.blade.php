<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Mon Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">📦 Mon Stock</h1>
                <p class="page-subtitle">{{ $entrepot->nom }} — {{ ucfirst($entrepot->type) }}</p>
            </div>
            <a href="{{ route('gestionnaire.commandes.create') }}" class="btn btn-primary-custom">
                <i class="bi bi-plus me-1"></i> Commander au dépôt
            </a>
        </div>

        @if($stocks->isEmpty())
            <div class="card-custom p-5 text-center">
                <i class="bi bi-inbox mb-3" style="font-size:3rem; color:#ccc;"></i>
                <p style="color:#aaa;">Aucun stock — passez une commande au dépôt</p>
            </div>
        @else
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card" style="border-color:#003087;">
                        <div class="stat-label">Total produits</div>
                        <div class="stat-value" style="color:#003087;">{{ $stocks->count() }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" style="border-color:#2e7d32;">
                        <div class="stat-label">Total unités</div>
                        <div class="stat-value" style="color:#2e7d32;">{{ $stocks->sum('quantite') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card" style="border-color:#c8102e;">
                        <div class="stat-label">Produits en alerte</div>
                        <div class="stat-value" style="color:#c8102e;">
                            {{ $stocks->filter(fn($s) => $s->quantite <= $s->produit->seuil_alerte)->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="bi bi-boxes"></i> Détail du stock
                </div>
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Produit</th>
                            <th>Quantité</th>
                            <th>Unité</th>
                            <th>Seuil alerte</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $stock)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $stock->produit->nom }}</td>
                                <td class="fw-bold">{{ $stock->quantite }}</td>
                                <td>{{ $stock->produit->unite }}</td>
                                <td>{{ $stock->produit->seuil_alerte }}</td>
                                <td>
                                    @if($stock->quantite == 0)
                                        <span class="badge-danger">🚨 Rupture</span>
                                    @elseif($stock->quantite <= $stock->produit->seuil_alerte)
                                        <span class="badge-attente">⚠️ Stock bas</span>
                                    @else
                                        <span class="badge-ok">✅ OK</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>