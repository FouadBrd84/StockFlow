<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Historique Mouvements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
    <style>
        .filter-section { display: none; }
        .filter-section.active { display: block; }
        .checkbox-box { background: #f8faff; border-radius: 10px; padding: 10px; max-height: 150px; overflow-y: auto; border: 1px solid #e0e0e0; }
        .form-check-label { font-size: 0.88rem; color: #333; }
        .select-all-btn { font-size: 0.75rem; color: #003087; cursor: pointer; text-decoration: underline; background: none; border: none; padding: 0; margin-bottom: 6px; font-weight: 600; }
        .empty-produits { color: #aaa; font-size: 0.85rem; font-style: italic; padding: 5px; margin: 0; }
        .direction-badge { font-size: 0.78rem; padding: 3px 10px; border-radius: 20px; display: inline-block; font-weight: 600; }
    </style>
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">📋 Historique des Mouvements</h1>
                <p class="page-subtitle">{{ $mouvements->total() }} mouvement(s) enregistré(s)</p>
            </div>
            <a href="{{ route('admin.mouvements.export-pdf') }}?{{ http_build_query(request()->all()) }}"
               class="btn btn-danger-custom">
                <i class="bi bi-file-pdf me-2"></i>Exporter PDF
            </a>
        </div>

        {{-- Filtres --}}
        <div class="card-custom p-4 mb-4">
            <form method="GET" action="{{ route('admin.mouvements.index') }}">
                <div class="row g-3">

                    <div class="col-md-2">
                        <label class="form-label">Type de mouvement</label>
                        <select name="type" class="form-select" id="typeSelect" onchange="toggleFilters()">
                            <option value="">-- Tous --</option>
                            <option value="entree" {{ request('type') == 'entree' ? 'selected' : '' }}>⬆️ Réception</option>
                            <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>⬇️ Envoi magasin</option>
                        </select>
                    </div>

                    {{-- Fournisseurs --}}
                    <div class="col-md-3 filter-section {{ request('type') == 'entree' ? 'active' : '' }}" id="filterEntree">
                        <label class="form-label">Depuis quel(s) fournisseur(s)</label>
                        <button type="button" class="select-all-btn d-block"
                                onclick="toggleAll('fournisseurs[]', 'fournisseursBox', this)">
                            Tout sélectionner
                        </button>
                        <div class="checkbox-box" id="fournisseursBox">
                            @foreach($fournisseurs as $f)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="fournisseurs[]" value="{{ $f->id }}" id="f{{ $f->id }}"
                                           onchange="chargerProduitsFournisseurs()"
                                           {{ in_array($f->id, request('fournisseurs', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="f{{ $f->id }}">{{ $f->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Produits entrée --}}
                    <div class="col-md-3 filter-section {{ request('type') == 'entree' ? 'active' : '' }}" id="filterProduitEntree">
                        <label class="form-label">Quel(s) produit(s)</label>
                        <button type="button" class="select-all-btn d-block" id="selectAllProduits"
                                onclick="toggleAll('produits[]', 'produitsBox', this)" style="display:none !important;">
                            Tout sélectionner
                        </button>
                        <div class="checkbox-box" id="produitsBox">
                            @if(request('fournisseurs') && count(request('fournisseurs')) > 0 && $produitsFiltres->count() > 0)
                                @foreach($produitsFiltres as $p)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="produits[]" value="{{ $p->id }}" id="p{{ $p->id }}"
                                               {{ in_array($p->id, request('produits', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="p{{ $p->id }}">{{ $p->nom }}</label>
                                    </div>
                                @endforeach
                            @else
                                <p class="empty-produits">Sélectionnez d'abord un fournisseur</p>
                            @endif
                        </div>
                    </div>

                    {{-- Sortie : entrepôt --}}
                    <div class="col-md-3 filter-section {{ request('type') == 'sortie' ? 'active' : '' }}" id="filterSortie">
                        <label class="form-label">Vers quel magasin</label>
                        <select name="entrepot_dest_id" class="form-select">
                            <option value="">-- Tous les magasins --</option>
                            @foreach($entrepots as $e)
                                <option value="{{ $e->id }}" {{ request('entrepot_dest_id') == $e->id ? 'selected' : '' }}>
                                    {{ $e->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Produits sortie --}}
                    <div class="col-md-3 filter-section {{ request('type') == 'sortie' ? 'active' : '' }}" id="filterProduitSortie">
                        <label class="form-label">Quel(s) produit(s)</label>
                        <button type="button" class="select-all-btn d-block"
                                onclick="toggleAll('produits[]', 'produitsBoxSortie', this)">
                            Tout sélectionner
                        </button>
                        <div class="checkbox-box" id="produitsBoxSortie">
                            @foreach($produits as $p)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="produits[]" value="{{ $p->id }}" id="ps{{ $p->id }}"
                                           {{ in_array($p->id, request('produits', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ps{{ $p->id }}">{{ $p->nom }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-search me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.mouvements.index') }}"
                       class="btn" style="background:#f5f5f5; color:#555; border-radius:10px; padding:10px 20px; font-weight:600;">
                        <i class="bi bi-x me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Tableau --}}
        <div class="card-custom">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
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
                            <td class="ps-4" style="color:#aaa;">{{ $m->id }}</td>
                            <td class="fw-bold">{{ $m->stock->produit->nom }}</td>
                            <td>
                                @if($m->type === 'entree')
                                    <span class="badge-entree">⬆ Réception</span>
                                @else
                                    <span class="badge-sortie">⬇ Envoi</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $m->quantite }}</td>
                            <td>{{ $m->stock->entrepot->nom }}</td>
                            <td>
                                @if($m->type === 'entree' && $m->entree->commande && $m->entree->commande->fournisseur)
                                    <span class="direction-badge" style="background:#e3f2fd; color:#1565c0;">
                                        📦 Depuis : {{ $m->entree->commande->fournisseur->name }}
                                    </span>
                                @elseif($m->type === 'transfert')
                                    <span class="direction-badge" style="background:#fce8e8; color:#c8102e;">
                                        🚚 Vers : {{ $m->transfert->stockDestination->entrepot->nom }}
                                    </span>
                                @elseif($m->type === 'entree')
                                    <span class="direction-badge" style="background:#e8f5e9; color:#2e7d32;">
                                        ✅ Réception magasin
                                    </span>
                                @else
                                    <span style="color:#aaa; font-size:0.85rem;">{{ $m->note ?? '—' }}</span>
                                @endif
                            </td>
                            <td>{{ $m->user->name }}</td>
                            <td style="color:#aaa;">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4" style="color:#aaa;">Aucun mouvement</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">{{ $mouvements->links() }}</div>
        </div>
    </div>

    <script>
        function toggleFilters() {
            const type = document.getElementById('typeSelect').value;
            document.getElementById('filterEntree').classList.toggle('active', type === 'entree');
            document.getElementById('filterProduitEntree').classList.toggle('active', type === 'entree');
            document.getElementById('filterSortie').classList.toggle('active', type === 'sortie');
            document.getElementById('filterProduitSortie').classList.toggle('active', type === 'sortie');

            if (type !== 'entree') {
                document.querySelectorAll('input[name="fournisseurs[]"]').forEach(cb => cb.checked = false);
                document.getElementById('produitsBox').innerHTML = '<p class="empty-produits">Sélectionnez d\'abord un fournisseur</p>';
                document.getElementById('selectAllProduits').style.display = 'none';
            }
        }

        function toggleAll(name, boxId, btn) {
            const checkboxes = document.querySelectorAll(`#${boxId} input[name="${name}"]`);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            btn.textContent = allChecked ? 'Tout sélectionner' : 'Tout désélectionner';
            if (name === 'fournisseurs[]') chargerProduitsFournisseurs();
        }

        function chargerProduitsFournisseurs() {
            const checkboxes = document.querySelectorAll('input[name="fournisseurs[]"]:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);
            const produitsBox = document.getElementById('produitsBox');
            const selectAllBtn = document.getElementById('selectAllProduits');
            const selectedProduits = {{ json_encode(request('produits', [])) }};

            if (ids.length === 0) {
                produitsBox.innerHTML = '<p class="empty-produits">Sélectionnez d\'abord un fournisseur</p>';
                selectAllBtn.style.display = 'none';
                return;
            }

            produitsBox.innerHTML = '<p class="empty-produits">Chargement...</p>';

            Promise.all(ids.map(id =>
                fetch(`/admin/produits-par-fournisseur/${id}`).then(r => r.json())
            )).then(results => {
                const tousLesProduits = results.flat();
                const uniques = tousLesProduits.filter((p, i, arr) =>
                    arr.findIndex(x => x.id === p.id) === i
                );

                if (uniques.length === 0) {
                    produitsBox.innerHTML = '<p class="empty-produits">Aucun produit trouvé</p>';
                    selectAllBtn.style.display = 'none';
                    return;
                }

                selectAllBtn.style.display = 'inline';
                produitsBox.innerHTML = '';
                uniques.forEach(p => {
                    const checked = selectedProduits.includes(String(p.id)) ? 'checked' : '';
                    produitsBox.innerHTML += `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="produits[]" value="${p.id}" id="p${p.id}" ${checked}>
                            <label class="form-check-label" for="p${p.id}">${p.nom}</label>
                        </div>
                    `;
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleFilters();
            const checked = document.querySelectorAll('input[name="fournisseurs[]"]:checked');
            if (checked.length > 0) chargerProduitsFournisseurs();
        });
    </script>
</body>
</html>