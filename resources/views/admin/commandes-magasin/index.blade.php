<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Commandes Magasins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0f0c29, #302b63, #24243e); min-height: 100vh; color: #fff; }
        .card { border: none; border-radius: 16px; background: rgba(255,255,255,0.08); backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.3); color: #fff; }
        .table { color: #fff; }
        .table thead { background: rgba(0,0,0,0.3); }
        .table tbody tr:hover { background: rgba(255,255,255,0.05); }
        .text-muted { color: #cce0ff !important; }
    </style>
</head>
<body>
    @include('layouts.navigation')
    <div class="container mt-5">
        <div class="mb-4">
            <h2 class="fw-bold">🏪 Commandes des Magasins</h2>
            <p class="text-muted">{{ $commandes->count() }} commande(s) en attente de traitement</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0" style="border-radius:12px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0" style="border-radius:12px;">{{ session('error') }}</div>
        @endif

        <div class="card p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Magasin</th>
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
                                    <span class="badge bg-secondary me-1">
                                        {{ $p->nom }} x{{ $p->pivot->quantite }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                @if($commande->statut === 'en_attente')
                                    <span class="badge bg-warning text-dark">En attente</span>
                                @elseif($commande->statut === 'confirmee')
                                    <span class="badge bg-info">En cours</span>
                                @elseif($commande->statut === 'recue')
                                    <span class="badge bg-success">Livrée ✅</span>
                                @endif
                            </td>
                            <td>{{ $commande->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($commande->statut === 'en_attente')
                                    <form method="POST"
                                          action="{{ route('admin.commandes-magasin.valider', $commande) }}"
                                          style="display:inline">
                                        @csrf
                                        <button class="btn btn-success btn-sm"
                                                onclick="return confirm('Valider et créer le transfert ?')">
                                            <i class="bi bi-check-circle"></i> Valider
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Aucune commande magasin</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>