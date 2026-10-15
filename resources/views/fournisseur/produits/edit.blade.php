<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Modifier Produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:700px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">✏️ Modifier le produit</h1>
            </div>
            <a href="{{ route('fournisseur.produits.index') }}" class="btn btn-sm"
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
            <form method="POST" action="{{ route('fournisseur.produits.update', $produit) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nom du produit</label>
                        <input type="text" name="nom" class="form-control"
                               value="{{ old('nom', $produit->nom) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Unité</label>
                        <select name="unite" class="form-select" required>
                            <option value="unité" {{ $produit->unite == 'unité' ? 'selected' : '' }}>Unité</option>
                            <option value="kg" {{ $produit->unite == 'kg' ? 'selected' : '' }}>Kg</option>
                            <option value="litre" {{ $produit->unite == 'litre' ? 'selected' : '' }}>Litre</option>
                            <option value="carton" {{ $produit->unite == 'carton' ? 'selected' : '' }}>Carton</option>
                            <option value="palette" {{ $produit->unite == 'palette' ? 'selected' : '' }}>Palette</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"
                                  rows="2">{{ old('description', $produit->description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Prix unitaire (€)</label>
                        <input type="number" step="0.01" name="prix" class="form-control"
                               value="{{ old('prix', $produit->prix) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Seuil d'alerte</label>
                        <input type="number" name="seuil_alerte" class="form-control"
                               value="{{ old('seuil_alerte', $produit->seuil_alerte) }}" min="0" required>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-check-circle me-2"></i>Modifier
                    </button>
                    <a href="{{ route('fournisseur.produits.index') }}"
                       class="btn" style="background:#f5f5f5; color:#555; border-radius:10px; padding:10px 20px; font-weight:600;">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>