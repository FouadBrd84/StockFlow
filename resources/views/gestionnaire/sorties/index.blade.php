<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Enregistrer une sortie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
    <style>
        .motif-btn { border: 2px solid #e0e0e0; border-radius: 12px; padding: 14px; text-align: center; cursor: pointer; transition: all 0.2s; color: #555; background: #fff; }
        .motif-btn:hover { border-color: #003087; }
        .motif-btn.active { border-color: #003087; background: #f0f4ff; color: #003087; }
        .motif-btn.casse.active { border-color: #c8102e; background: #fce8e8; color: #c8102e; }
        .motif-btn.perte.active { border-color: #f57f17; background: #fff8e1; color: #f57f17; }
        .motif-btn.autre.active { border-color: #6a1b9a; background: #f3e5f5; color: #6a1b9a; }
    </style>
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">📤 Enregistrer une sortie</h1>
                <p class="page-subtitle">{{ $entrepot->nom }} — Vente, casse ou perte de stock</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif

        @if($stocks->isEmpty())
            <div class="card-custom p-5 text-center">
                <i class="bi bi-inbox mb-3" style="font-size:3rem; color:#ccc;"></i>
                <p style="color:#aaa;">Aucun produit en stock</p>
                <a href="{{ route('gestionnaire.commandes.create') }}" class="btn btn-primary-custom mt-2">
                    <i class="bi bi-plus me-1"></i> Commander au dépôt
                </a>
            </div>
        @else
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card-custom p-4">
                        <div class="card-header-custom mb-4" style="padding:0 0 16px; margin-bottom:20px;">
                            <i class="bi bi-pencil-square"></i> Nouvelle sortie
                        </div>
                        <form method="POST" action="{{ route('gestionnaire.sorties.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Produit</label>
                                <select name="produit_id" class="form-select" id="produitSelect"
                                        onchange="updateStock(this)" required>
                                    <option value="">-- Sélectionner un produit --</option>
                                    @foreach($stocks as $stock)
                                        <option value="{{ $stock->produit->id }}"
                                                data-stock="{{ $stock->quantite }}"
                                                data-unite="{{ $stock->produit->unite }}"
                                                {{ old('produit_id') == $stock->produit->id ? 'selected' : '' }}>
                                            {{ $stock->produit->nom }} — {{ $stock->quantite }} {{ $stock->produit->unite }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="stockInfo" class="mt-2" style="display:none;">
                                    <span class="badge-info">
                                        Stock actuel : <span id="stockQty">0</span> <span id="stockUnite"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Quantité</label>
                                <input type="number" name="quantite" class="form-control"
                                       placeholder="Ex: 5" min="1"
                                       value="{{ old('quantite') }}" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Motif</label>
                                <input type="hidden" name="motif" id="motifInput" value="{{ old('motif') }}">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="motif-btn vente {{ old('motif') == 'vente' ? 'active' : '' }}"
                                             onclick="selectMotif('vente', this)">
                                            <i class="bi bi-cart-check" style="font-size:1.4rem; display:block; margin-bottom:4px;"></i>
                                            Vente
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="motif-btn casse {{ old('motif') == 'casse' ? 'active' : '' }}"
                                             onclick="selectMotif('casse', this)">
                                            <i class="bi bi-exclamation-triangle" style="font-size:1.4rem; display:block; margin-bottom:4px;"></i>
                                            Casse
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="motif-btn perte {{ old('motif') == 'perte' ? 'active' : '' }}"
                                             onclick="selectMotif('perte', this)">
                                            <i class="bi bi-question-circle" style="font-size:1.4rem; display:block; margin-bottom:4px;"></i>
                                            Perte
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="motif-btn autre {{ old('motif') == 'autre' ? 'active' : '' }}"
                                             onclick="selectMotif('autre', this)">
                                            <i class="bi bi-three-dots" style="font-size:1.4rem; display:block; margin-bottom:4px;"></i>
                                            Autre
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Note (optionnelle)</label>
                                <input type="text" name="note" class="form-control"
                                       placeholder="Ex: carton tombé, date expirée..."
                                       value="{{ old('note') }}">
                            </div>

                            <button type="submit" class="btn btn-primary-custom w-100">
                                <i class="bi bi-check-circle me-2"></i>Enregistrer la sortie
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card-custom">
                        <div class="card-header-custom">
                            <i class="bi bi-boxes"></i> Stock actuel
                        </div>
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Produit</th>
                                    <th>Quantité</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stocks as $stock)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $stock->produit->nom }}</td>
                                        <td>{{ $stock->quantite }} {{ $stock->produit->unite }}</td>
                                        <td>
                                            @if($stock->quantite == 0)
                                                <span class="badge-danger">Rupture</span>
                                            @elseif($stock->quantite <= $stock->produit->seuil_alerte)
                                                <span class="badge-attente">Stock bas ⚠️</span>
                                            @else
                                                <span class="badge-ok">OK ✅</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function selectMotif(motif, el) {
            document.getElementById('motifInput').value = motif;
            document.querySelectorAll('.motif-btn').forEach(btn => btn.classList.remove('active'));
            el.classList.add('active');
        }

        function updateStock(select) {
            const option = select.options[select.selectedIndex];
            const stock  = option.dataset.stock;
            const unite  = option.dataset.unite;
            const info   = document.getElementById('stockInfo');
            if (stock) {
                document.getElementById('stockQty').textContent   = stock;
                document.getElementById('stockUnite').textContent = unite;
                info.style.display = 'block';
            } else {
                info.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('produitSelect');
            if (select.value) updateStock(select);
        });
    </script>
</body>
</html>