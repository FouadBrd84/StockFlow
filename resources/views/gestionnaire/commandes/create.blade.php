<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Nouvelle Commande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
    <style>
        .produit-row { background: #f8faff; border-radius: 10px; padding: 12px; margin-bottom: 8px; border: 1px solid #e8edf5; }
    </style>
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:900px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">➕ Nouvelle commande au dépôt</h1>
                <p class="page-subtitle">Commander des produits depuis le dépôt central</p>
            </div>
            <a href="{{ route('gestionnaire.commandes.index') }}" class="btn btn-sm"
               style="background:#f0f4ff; color:#003087; border-radius:8px; padding:8px 16px; font-weight:600;">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger mb-4">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if($produits->isEmpty())
            <div class="card-custom p-5 text-center">
                <i class="bi bi-inbox mb-3" style="font-size:3rem; color:#ccc;"></i>
                <p style="color:#aaa;">Aucun produit disponible au dépôt central pour le moment.</p>
            </div>
        @else
            <div class="card-custom p-4">
                <form method="POST" action="{{ route('gestionnaire.commandes.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label">Note (optionnelle)</label>
                        <input type="text" name="note" class="form-control"
                               placeholder="Ex: Commande urgente" value="{{ old('note') }}">
                    </div>

                    <h6 class="fw-bold mb-3" style="color:#003087;">
                        <i class="bi bi-box-seam me-2"></i>Produits disponibles au dépôt
                    </h6>

                    <div id="produits-container">
                        <div class="produit-row row g-2">
                            <div class="col-md-8">
                                <select name="produits[0][id]" class="form-select" required>
                                    <option value="">-- Sélectionner un produit --</option>
                                    @foreach($produits as $stock)
                                        <option value="{{ $stock->produit->id }}">
                                            {{ $stock->produit->nom }} — Stock : {{ $stock->quantite }} {{ $stock->produit->unite }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="produits[0][quantite]" class="form-control"
                                       placeholder="Quantité" min="1" required>
                            </div>
                            <div class="col-md-1 d-flex align-items-center">
                                <button type="button" class="btn btn-sm w-100"
                                        style="background:#fce8e8; color:#c8102e; border-radius:8px;"
                                        onclick="supprimerLigne(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm mt-3"
                            style="background:#f0f4ff; color:#003087; border-radius:8px; padding:8px 16px; font-weight:600;"
                            onclick="ajouterProduit()">
                        <i class="bi bi-plus me-1"></i> Ajouter un produit
                    </button>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-check-circle me-2"></i>Passer la commande
                        </button>
                        <a href="{{ route('gestionnaire.commandes.index') }}"
                           class="btn" style="background:#f5f5f5; color:#555; border-radius:10px; padding:10px 20px; font-weight:600;">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <script>
        let index = 1;
        const produits = @json($produits);

        function ajouterProduit() {
            const container = document.getElementById('produits-container');
            let options = '<option value="">-- Sélectionner un produit --</option>';
            produits.forEach(s => {
                options += `<option value="${s.produit.id}">${s.produit.nom} — Stock : ${s.quantite} ${s.produit.unite}</option>`;
            });

            const div = document.createElement('div');
            div.className = 'produit-row row g-2';
            div.innerHTML = `
                <div class="col-md-8">
                    <select name="produits[${index}][id]" class="form-select" required>${options}</select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="produits[${index}][quantite]" class="form-control"
                           placeholder="Quantité" min="1" required>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-sm w-100"
                            style="background:#fce8e8; color:#c8102e; border-radius:8px;"
                            onclick="supprimerLigne(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
            index++;
        }

        function supprimerLigne(btn) {
            const rows = document.querySelectorAll('.produit-row');
            if (rows.length > 1) btn.closest('.produit-row').remove();
        }
    </script>
</body>
</html>