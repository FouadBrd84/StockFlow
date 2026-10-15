<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Mouvements</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        h1 { text-align: center; color: #1e3a5f; font-size: 18px; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #888; font-size: 11px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead { background-color: #1e3a5f; color: #fff; }
        th { padding: 8px 10px; text-align: left; font-size: 11px; }
        td { padding: 7px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
        tr:nth-child(even) { background-color: #f5f5f5; }
        .badge-entree { background: #16a34a; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 10px; }
        .badge-sortie { background: #dc2626; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 10px; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; color: #aaa; }
        .depuis { color: #0369a1; }
        .vers { color: #dc2626; }
    </style>
</head>
<body>

    <h1>📋 Historique des Mouvements — StockFlow</h1>
    <p class="subtitle">Généré le {{ now()->format('d/m/Y à H:i') }} — {{ $mouvements->count() }} mouvement(s)</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Produit</th>
                <th>Type</th>
                <th>Quantité</th>
                <th>Entrepôt</th>
                <th>Depuis / Vers</th>
                <th>Par</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mouvements as $m)
                <tr>
                    <td>{{ $m->id }}</td>
                    <td><strong>{{ $m->stock->produit->nom }}</strong></td>
                    <td>
                        @if($m->type === 'entree')
                            <span class="badge-entree">Réception</span>
                        @else
                            <span class="badge-sortie">Envoi</span>
                        @endif
                    </td>
                    <td>{{ $m->quantite }}</td>
                    <td>{{ $m->stock->entrepot->nom }}</td>
                    <td>
                        @if($m->type === 'entree' && $m->entree->commande && $m->entree->commande->fournisseur)
                            <span class="depuis">📦 Depuis : {{ $m->entree->commande->fournisseur->name }}</span>
                        @elseif($m->type === 'transfert')
                            <span class="vers">🚚 Vers : {{ $m->transfert->stockDestination->entrepot->nom }}</span>
                        @else
                            {{ $m->note ?? '—' }}
                        @endif
                    </td>
                    <td>{{ $m->user->name }}</td>
                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:#aaa;">Aucun mouvement</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        StockFlow — Rapport généré automatiquement
    </div>

</body>
</html>