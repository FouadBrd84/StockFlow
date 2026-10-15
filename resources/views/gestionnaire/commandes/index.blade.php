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
                <h1 class="page-title">🛒 Mes Commandes</h1>
                <p class="page-subtitle">{{ $commandes->count() }} commande(s)</p>
            </div>
            <a href="{{ route('gestionnaire.commandes.create') }}" class="btn btn-primary-custom">
                <i class="bi bi-plus me-1"></i> Nouvelle commande
            </a>
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
                                    <span class="badge-info">En livraison 🚚</span>
                                @elseif($commande->statut === 'recue')
                                    <span class="badge-ok">Reçue ✅</span>
                                @else
                                    <span class="badge-danger">Annulée</span>
                                @endif
                            </td>
                            <td style="color:#aaa;">{{ $commande->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($commande->statut === 'confirmee')
                                    <form method="POST"
                                          action="{{ route('gestionnaire.commandes.recevoir', $commande) }}"
                                          style="display:inline">
                                        @csrf
                                        <button class="btn btn-sm fw-bold me-1"
                                                style="background:#2e7d32; color:#fff; border-radius:8px; padding:4px 12px; font-size:0.8rem;"
                                                onclick="return confirm('Confirmer la réception ?')">
                                            <i class="bi bi-check-circle"></i> Réceptionner
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('gestionnaire.commandes.show', $commande) }}"
                                   class="btn btn-sm"
                                   style="background:#f0f4ff; color:#003087; border-radius:8px; padding:4px 12px; font-size:0.8rem;">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color:#aaa;">Aucune commande</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>