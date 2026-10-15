<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Mes Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">📋 Mes Commandes</h1>
                <p class="page-subtitle">{{ $commandes->count() }} commande(s) reçue(s)</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        <div class="card-custom">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Entrepôt</th>
                        <th>Produits</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commandes as $commande)
                        <tr>
                            <td class="ps-4">#{{ $commande->id }}</td>
                            <td class="fw-bold">{{ $commande->entrepot->nom }}</td>
                            <td>
                                @foreach($commande->produits as $p)
                                    <span class="badge bg-light text-dark border me-1" style="font-size:0.78rem;">
                                        {{ $p->nom }} x{{ $p->pivot->quantite }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                @if($commande->statut === 'en_attente')
                                    <span class="badge-attente">En attente</span>
                                @elseif($commande->statut === 'confirmee')
                                    <span class="badge-info">Confirmée</span>
                                @elseif($commande->statut === 'expediee')
                                    <span class="badge-ok">Expédiée 🚚</span>
                                @elseif($commande->statut === 'recue')
                                    <span class="badge-ok">Reçue ✅</span>
                                @endif
                            </td>
                            <td style="color:#aaa;">{{ $commande->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($commande->statut === 'en_attente')
                                    <form method="POST"
                                          action="{{ route('fournisseur.commandes.confirmer', $commande) }}"
                                          style="display:inline">
                                        @csrf
                                        <button class="btn btn-sm me-1"
                                                style="background:#e8f5e9; color:#2e7d32; border:1px solid #2e7d32; border-radius:8px; padding:4px 12px; font-size:0.8rem;">
                                            <i class="bi bi-check"></i> Confirmer
                                        </button>
                                    </form>
                                @elseif($commande->statut === 'confirmee')
                                    <form method="POST"
                                          action="{{ route('fournisseur.commandes.expedier', $commande) }}"
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
                            <td colspan="6" class="text-center py-4" style="color:#aaa;">Aucune commande reçue</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>