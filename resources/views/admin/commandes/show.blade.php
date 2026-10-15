<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Commande #{{ $commande->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
    <style>
        .info-item { padding: 12px 0; border-bottom: 1px solid #f0f4ff; }
        .info-label { font-size: 0.78rem; color: #999; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-weight: 600; color: #333; margin-top: 4px; }
    </style>
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1200px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">🛒 Commande #{{ $commande->id }}</h1>
                <p class="page-subtitle">Détail de la commande fournisseur</p>
            </div>
            <a href="{{ route('admin.commandes.index') }}" class="btn btn-sm"
               style="background:#f0f4ff; color:#003087; border-radius:8px; padding:8px 16px; font-weight:600;">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-custom p-4">
                    <h6 class="fw-bold mb-3" style="color:#003087;">Informations</h6>
                    <div class="info-item">
                        <div class="info-label">Fournisseur</div>
                        <div class="info-value">{{ $commande->fournisseur->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Entrepôt</div>
                        <div class="info-value">{{ $commande->entrepot->nom }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Statut</div>
                        <div class="info-value mt-2">
                            @if($commande->statut === 'en_attente')
                                <span class="badge-attente">En attente</span>
                            @elseif($commande->statut === 'confirmee')
                                <span class="badge-info">Confirmée</span>
                            @elseif($commande->statut === 'expediee')
                                <span class="badge-info">Expédiée 🚚</span>
                            @elseif($commande->statut === 'recue')
                                <span class="badge-ok">Reçue ✅</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date</div>
                        <div class="info-value">{{ $commande->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    @if($commande->note)
                        <div class="info-item">
                            <div class="info-label">Note</div>
                            <div class="info-value">{{ $commande->note }}</div>
                        </div>
                    @endif

                    @if($commande->statut === 'expediee')
                        <form method="POST" action="{{ route('admin.commandes.recevoir', $commande) }}" class="mt-4">
                            @csrf
                            <button class="btn btn-primary-custom w-100"
                                    onclick="return confirm('Confirmer la réception et mettre à jour le stock ?')">
                                <i class="bi bi-check-circle me-2"></i>Confirmer réception
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="col-md-8">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <i class="bi bi-box-seam"></i> Produits commandés
                    </div>
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Produit</th>
                                <th>Quantité</th>
                                <th>Prix unitaire</th>
                                <th>Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commande->produits as $produit)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $produit->nom }}</td>
                                    <td>{{ $produit->pivot->quantite }} {{ $produit->unite }}</td>
                                    <td>{{ number_format($produit->pivot->prix_unitaire, 2) }} €</td>
                                    <td style="color:#003087; font-weight:700;">
                                        {{ number_format($produit->pivot->quantite * $produit->pivot->prix_unitaire, 2) }} €
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-3" style="color:#aaa;">Aucun produit</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8faff;">
                                <td colspan="3" class="ps-4 fw-bold text-end" style="color:#555;">Total :</td>
                                <td style="color:#003087; font-weight:800; font-size:1.1rem;">
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