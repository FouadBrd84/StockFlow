<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Stock Global</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">📊 Stock Global</h1>
                <p class="page-subtitle">Vue complète du stock par entrepôt</p>
            </div>
        </div>

        @foreach($entrepots as $entrepot)
            @if($entrepot->stocks->count() > 0)
                <div class="card-custom mb-4">
                    <div class="card-header-custom">
                        @if($entrepot->type === 'central') 🏭
                        @elseif($entrepot->type === 'city') 🏙️
                        @elseif($entrepot->type === 'express') ⚡
                        @elseif($entrepot->type === 'market') 🛒
                        @elseif($entrepot->type === 'hypermarket') 🏬
                        @elseif($entrepot->type === 'bio') 🌿
                        @elseif($entrepot->type === 'drive') 🚗
                        @elseif($entrepot->type === 'proximite') 📍
                        @elseif($entrepot->type === 'flash') ⚡
                        @endif
                        {{ $entrepot->nom }}
                        <span class="ms-2" style="background:#f0f4ff; color:#003087; font-size:0.75rem; padding:2px 10px; border-radius:20px; font-weight:600;">
                            {{ ucfirst($entrepot->type) }}
                        </span>
                    </div>
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Produit</th>
                                <th>Fournisseur</th>
                                <th>Quantité</th>
                                <th>Unité</th>
                                <th>Seuil alerte</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entrepot->stocks as $stock)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $stock->produit->nom }}</td>
                                    <td>{{ $stock->produit->fournisseur->name ?? '—' }}</td>
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
        @endforeach

        @if($entrepots->every(fn($e) => $e->stocks->count() === 0))
            <div class="card-custom p-5 text-center">
                <p style="color:#aaa;">Aucun stock enregistré</p>
            </div>
        @endif
    </div>
</body>
</html>