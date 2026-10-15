<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Commande #{{ $commande->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0a3d2e, #145a3c, #1e8449); min-height: 100vh; color: #fff; }
        .card { border: none; border-radius: 16px; background: rgba(255,255,255,0.08); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.3); color: #fff; }
        .table { color: #fff; }
        .table thead { background: rgba(0,0,0,0.3); }
        .text-muted { color: #cce0ff !important; }
        .info-item { padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body>
    @include('layouts.navigation')
    <div class="container mt-5">
        <div class="d-flex align-items-center mb-4 gap-3">
            <h2 class="fw-bold mb-0">🛒 Commande #{{ $commande->id }}</h2>
            <a href="{{ route('gestionnaire.commandes.index') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card p-4">
                    <h5 class="fw-bold mb-3">Informations</h5>
                    <div class="info-item">
                        <small class="text-muted">Statut</small>
                        <p class="mb-0">
                            @if($commande->statut === 'en_attente')
                                <span class="badge bg-warning text-dark">En attente</span>
                            @elseif($commande->statut === 'confirmee')
                                <span class="badge bg-info">En cours de livraison</span>
                            @elseif($commande->statut === 'recue')
                                <span class="badge bg-success">Reçue ✅</span>
                            @endif
                        </p>
                    </div>
                    <div class="info-item">
                        <small class="text-muted">Date</small>
                        <p class="fw-bold mb-0">{{ $commande->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    @if($commande->note)
                        <div class="info-item">
                            <small class="text-muted">Note</small>
                            <p class="mb-0">{{ $commande->note }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-8">
                <div class="card p-4">
                    <h5 class="fw-bold mb-3">Produits commandés</h5>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commande->produits as $produit)
                                <tr>
                                    <td class="fw-bold">{{ $produit->nom }}</td>
                                    <td>{{ $produit->pivot->quantite }} {{ $produit->unite }}</td>
                                    <td>{{ number_format($produit->pivot->prix_unitaire, 2) }} €</td>
                                    <td style="color:#4ade80;">
                                        {{ number_format($produit->pivot->quantite * $produit->pivot->prix_unitaire, 2) }} €
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Aucun produit</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="fw-bold text-end">Total :</td>
                                <td class="fw-bold fs-5" style="color:#4ade80;">
                                    {{ number_format($commande->produits->sum(fn($p) => $p->pivot->quantite * $p->pivot->prix_unitaire), 2) }} €
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>