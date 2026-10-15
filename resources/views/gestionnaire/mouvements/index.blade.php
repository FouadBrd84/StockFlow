<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Mouvements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">📋 Historique des Mouvements</h1>
                <p class="page-subtitle">{{ $entrepot->nom }} — {{ ucfirst($entrepot->type) }}</p>
            </div>
        </div>

        <div class="card-custom">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Produit</th>
                        <th>Type</th>
                        <th>Quantité</th>
                        <th>Par</th>
                        <th>Note</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mouvements as $m)
                        <tr>
                            <td class="ps-4" style="color:#aaa;">{{ $m->id }}</td>
                            <td class="fw-bold">{{ $m->stock->produit->nom }}</td>
                            <td>
                                @if(in_array($m->type, ['entree', 'transfert']))
                                    <span class="badge-entree">⬆ Entrée</span>
                                @else
                                    <span class="badge-sortie">⬇ Sortie</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $m->quantite }}</td>
                            <td>{{ $m->user->name }}</td>
                            <td style="color:#888;">{{ $m->note ?? '—' }}</td>
                            <td style="color:#aaa;">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4" style="color:#aaa;">Aucun mouvement</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">{{ $mouvements->links() }}</div>
        </div>
    </div>
</body>
</html>