<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Commandes Magasins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
    <style>
        .section-card-title { font-weight: 700; font-size: 1rem; padding: 16px 24px; border-bottom: 2px solid #f0f4ff; display: flex; align-items: center; gap: 8px; }
        .stock-warning { background: #fff8e1; border: 1px solid #f57f17; border-radius: 8px; padding: 5px 12px; font-size: 0.8rem; color: #f57f17; margin-top: 4px; display: inline-block; }
        .stock-danger { background: #fce8e8; border: 1px solid #c8102e; border-radius: 8px; padding: 5px 12px; font-size: 0.8rem; color: #c8102e; margin-top: 4px; display: inline-block; }
    </style>
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">🏪 Commandes des Magasins</h1>
                <p class="page-subtitle">Gérez les commandes et livraisons vers les magasins</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        {{-- EN ATTENTE --}}
        <div class="card-custom mb-4">
            <div class="section-card-title" style="color:#f57f17;">
                <i class="bi bi-hourglass"></i>
                En attente de validation
                <span style="background:#fff8e1; color:#f57f17; font-size:0.75rem; padding:2px 10px; border-radius:20px; font-weight:700;">
                    {{ $commandesEnAttente->count() }}
                </span>
            </div>
            @if($commandesEnAttente->isEmpty())
                <div class="text-center py-4" style="color:#aaa;">
                    <i class="bi bi-check-circle me-2"></i>Aucune commande en attente
                </div>
            @else
                @php
                    $stockDemande = [];
                    foreach($commandesEnAttente as $c) {
                        foreach($c->produits as $p) {
                            $stockDemande[$p->id] = ($stockDemande[$p->id] ?? 0) + $p->pivot->quantite;
                        }
                    }
                @endphp
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Magasin</th>
                            <th>Produits</th>
                            <th>Avertissement stock</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commandesEnAttente as $c)
                            <tr>
                                <td class="ps-4">#{{ $c->id }}</td>
                                <td class="fw-bold">{{ $c->entrepot->nom }}</td>
                                <td>
                                    @foreach($c->produits as $p)
                                        <span class="badge bg-light text-dark border me-1" style="font-size:0.78rem;">
                                            {{ $p->nom }} x{{ $p->pivot->quantite }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>
                                    @php $hasWarning = false; @endphp
                                    @foreach($c->produits as $p)
                                        @php
                                            $stock = \App\Models\Stock::where('produit_id', $p->id)
                                                ->whereHas('entrepot', fn($q) => $q->where('type', 'central'))
                                                ->first();
                                            $stockDispo  = $stock ? $stock->quantite : 0;
                                            $totalDemande = $stockDemande[$p->id] ?? 0;
                                        @endphp
                                        @if($stockDispo < $p->pivot->quantite)
                                            @php $hasWarning = true; @endphp
                                            <div class="stock-danger">
                                                🚨 {{ $p->nom }} — Stock : {{ $stockDispo }} / Demandé : {{ $p->pivot->quantite }}
                                                <a href="{{ route('admin.commandes.create') }}"
                                                   class="ms-2 fw-bold"
                                                   style="color:#c8102e; font-size:0.75rem;">
                                                    + Commander au fournisseur
                                                </a>
                                            </div>
                                        @elseif($totalDemande > $stockDispo)
                                            @php $hasWarning = true; @endphp
                                            <div class="stock-warning">
                                                ⚠️ {{ $p->nom }} — Conflit : {{ $totalDemande }} demandés / {{ $stockDispo }} disponibles
                                                <a href="{{ route('admin.commandes.create') }}"
                                                   class="ms-2 fw-bold"
                                                   style="color:#f57f17; font-size:0.75rem;">
                                                    + Commander au fournisseur
                                                </a>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if(!$hasWarning)
                                        <span class="badge-ok">✅ Stock OK</span>
                                    @endif
                                </td>
                                <td style="color:#aaa;">{{ $c->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $canValidate = true;
                                        foreach($c->produits as $p) {
                                            $stock = \App\Models\Stock::where('produit_id', $p->id)
                                                ->whereHas('entrepot', fn($q) => $q->where('type', 'central'))
                                                ->first();
                                            if (!$stock || $stock->quantite < $p->pivot->quantite) {
                                                $canValidate = false;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if($canValidate)
                                        <form method="POST"
                                              action="{{ route('admin.commandes-magasin.valider', $c) }}"
                                              style="display:inline">
                                            @csrf
                                            <button class="btn btn-sm fw-bold"
                                                    style="background:#e8f5e9; color:#2e7d32; border:1px solid #2e7d32; border-radius:8px; padding:4px 14px; font-size:0.8rem;"
                                                    onclick="return confirm('Valider et envoyer ?')">
                                                <i class="bi bi-check-circle"></i> Valider
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm" disabled
                                                style="background:#fce8e8; color:#c8102e; border:1px solid #c8102e; border-radius:8px; padding:4px 14px; font-size:0.8rem;">
                                            <i class="bi bi-x-circle"></i> Stock insuffisant
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- EN COURS --}}
        <div class="card-custom mb-4">
            <div class="section-card-title" style="color:#1565c0;">
                <i class="bi bi-truck"></i>
                En cours de livraison
                <span style="background:#e3f2fd; color:#1565c0; font-size:0.75rem; padding:2px 10px; border-radius:20px; font-weight:700;">
                    {{ $commandesEnCours->count() }}
                </span>
            </div>
            @if($commandesEnCours->isEmpty())
                <div class="text-center py-4" style="color:#aaa;">Aucune livraison en cours</div>
            @else
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Magasin</th>
                            <th>Produits</th>
                            <th>Date validation</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commandesEnCours as $c)
                            <tr>
                                <td class="ps-4">#{{ $c->id }}</td>
                                <td class="fw-bold">{{ $c->entrepot->nom }}</td>
                                <td>
                                    @foreach($c->produits as $p)
                                        <span class="badge bg-light text-dark border me-1" style="font-size:0.78rem;">
                                            {{ $p->nom }} x{{ $p->pivot->quantite }}
                                        </span>
                                    @endforeach
                                </td>
                                <td style="color:#aaa;">{{ $c->updated_at->format('d/m/Y') }}</td>
                                <td><span class="badge-info">🚚 En livraison</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- LIVRÉES --}}
        <div class="card-custom">
            <div class="section-card-title" style="color:#2e7d32;">
                <i class="bi bi-check-circle"></i>
                Livrées
                <span style="background:#e8f5e9; color:#2e7d32; font-size:0.75rem; padding:2px 10px; border-radius:20px; font-weight:700;">
                    {{ $commandesLivrees->count() }}
                </span>
            </div>
            @if($commandesLivrees->isEmpty())
                <div class="text-center py-4" style="color:#aaa;">Aucune livraison terminée</div>
            @else
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Magasin</th>
                            <th>Produits</th>
                            <th>Date livraison</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commandesLivrees as $c)
                            <tr>
                                <td class="ps-4">#{{ $c->id }}</td>
                                <td class="fw-bold">{{ $c->entrepot->nom }}</td>
                                <td>
                                    @foreach($c->produits as $p)
                                        <span class="badge bg-light text-dark border me-1" style="font-size:0.78rem;">
                                            {{ $p->nom }} x{{ $p->pivot->quantite }}
                                        </span>
                                    @endforeach
                                </td>
                                <td style="color:#aaa;">{{ $c->updated_at->format('d/m/Y') }}</td>
                                <td><span class="badge-ok">✅ Livrée</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>
</body>
</html>