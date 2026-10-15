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
        .alert-info-custom { background: #e3f2fd; border-left: 4px solid #1565c0; border-radius: 10px; color: #1565c0; padding: 10px 16px; font-size: 0.88rem; }
    </style>
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:900px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">➕ Nouvelle commande fournisseur</h1>
                <p class="page-subtitle">Entrepôt central : {{ $entrepot->nom ?? 'Non défini' }}</p>
            </div>
            <a href="{{ route('admin.commandes.index') }}" class="btn btn-sm"
               style="background:#f0f4ff; color:#003087; border-radius:8px; padding:8px 16px; font-weight:600;">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger mb-4">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="card-custom p-4">
            <form method="POST" action="{{ route('admin.commandes.store') }}">
                @csrf

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Fournisseur</label>
                        <select name="fournisseur_id" class="form-select" required
                                id="fournisseurSelect" onchange="chargerProduits(this.value)">
                            <option value="">-- Sélectionner --</option>
                            @foreach($fournisseurs as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Note (optionnelle)</label>
                        <input type="text" name="note" class="form-control"
                               placeholder="Ex: Livraison urgente" value="{{ old('note') }}">
                    </div>
                </div>

                <div id="no-produits" class="alert-info-custom mb-4">
                    <i class="bi bi-info-circle me-2"></i>Sélectionnez d'abord un fournisseur pour voir ses produits.
                </div>

                <div id="produits-section" style="display:none;">
                    <h6 class="fw-bold mb-3" style="color:#003087;">
                        <i class="bi bi-box-seam me-2"></i>Produits à commander
                    </h6>

                    <div id="produits-container">
                        <div class="produit-row row g-2">
                            <div class="col-md-8">
                                <select name="produits[0][id]" class="form-select produit-select" required>
                                    <option value="">-- Sélectionner un produit --</option>
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
                            <i class="bi bi-check-circle me-2"></i>Créer la commande
                        </button>
                        <a href="{{ route('admin.commandes.index') }}"
                           class="btn" style="background:#f5f5f5; color:#555; border-radius:10px; padding:10px 20px; font-weight:600;">
                            Annuler
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let index = 1;
        let produitsList = [];

        function chargerProduits(fournisseurId) {
            if (!fournisseurId) {
                document.getElementById('produits-section').style.display = 'none';
                document.getElementById('no-produits').style.display = 'block';
                return;
            }

            fetch(`/admin/produits-fournisseur/${fournisseurId}`)
                .then(res => res.json())
                .then(produits => {
                    produitsList = produits;

                    if (produits.length === 0) {
                        document.getElementById('produits-section').style.display = 'none';
                        document.getElementById('no-produits').style.display = 'block';
                        document.getElementById('no-produits').innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Ce fournisseur n\'a aucun produit.';
                        return;
                    }

                    document.getElementById('produits-section').style.display = 'block';
                    document.getElementById('no-produits').style.display = 'none';

                    document.querySelectorAll('.produit-select').forEach(select => {
                        select.innerHTML = '<option value="">-- Sélectionner un produit --</option>';
                        produits.forEach(p => {
                            select.innerHTML += `<option value="${p.id}">${p.nom} (${p.prix} €/${p.unite})</option>`;
                        });
                    });
                });
        }

        function ajouterProduit() {
            const container = document.getElementById('produits-container');
            let options = '<option value="">-- Sélectionner un produit --</option>';
            produitsList.forEach(p => {
                options += `<option value="${p.id}">${p.nom} (${p.prix} €/${p.unite})</option>`;
            });

            const div = document.createElement('div');
            div.className = 'produit-row row g-2';
            div.innerHTML = `
                <div class="col-md-8">
                    <select name="produits[${index}][id]" class="form-select produit-select" required>
                        ${options}
                    </select>
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
            if (rows.length > 1) {
                btn.closest('.produit-row').remove();
            }
        }
    </script>
</body>
</html>