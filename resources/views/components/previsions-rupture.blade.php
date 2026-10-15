@props(['previsions', 'avecEntrepot' => false])

<div class="card-custom h-100 sf-previsions">
    <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap">
        <span><i class="bi bi-stars"></i> Prévisions de rupture</span>
        <span class="sf-ia-chip">✨ rythme des 30 derniers jours</span>
    </div>

    @if($previsions->isEmpty())
        <div class="sf-previsions-vide">
            <div class="sf-previsions-emoji">😎</div>
            <b>Rien à signaler</b>
            <p>Pas assez de sorties récentes pour prévoir une rupture.</p>
        </div>
    @else
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Produit</th>
                    @if($avecEntrepot)
                        <th>Entrepôt</th>
                    @endif
                    <th>Stock</th>
                    <th>Conso / jour</th>
                    <th>Rupture prévue</th>
                    <th>À commander</th>
                </tr>
            </thead>
            <tbody>
                @foreach($previsions as $prevision)
                    @php($couverture = $prevision['jours_restants'] === null ? 100 : min(100, (int) round($prevision['jours_restants'] / 30 * 100)))
                    <tr>
                        <td class="ps-4">
                            <b>{{ $prevision['produit'] }}</b>
                            <div class="sf-jauge sf-jauge-{{ $prevision['niveau'] }}"><span style="width: {{ $couverture }}%"></span></div>
                        </td>
                        @if($avecEntrepot)
                            <td>{{ $prevision['entrepot'] }}</td>
                        @endif
                        <td>{{ $prevision['quantite'] }}</td>
                        <td>{{ number_format($prevision['consommation_jour'], 1, ',', ' ') }}</td>
                        <td>
                            @php($badge = ['critique' => 'badge-danger', 'attention' => 'badge-attente', 'ok' => 'badge-ok'][$prevision['niveau']])
                            <span class="{{ $badge }}">
                                @if($prevision['jours_restants'] === 0)
                                    aujourd'hui
                                @else
                                    dans {{ $prevision['jours_restants'] }} j
                                @endif
                            </span>
                            <div class="sf-date-rupture">vers le {{ $prevision['date_rupture']->format('d/m') }}</div>
                        </td>
                        <td>
                            @if($prevision['a_commander'] > 0)
                                <span class="sf-a-commander">+{{ $prevision['a_commander'] }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
