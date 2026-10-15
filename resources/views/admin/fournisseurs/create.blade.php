<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Nouveau Fournisseur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:700px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">➕ Ajouter un fournisseur</h1>
            </div>
            <a href="{{ route('admin.fournisseurs.index') }}" class="btn btn-sm"
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
            <form method="POST" action="{{ route('admin.fournisseurs.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nom de la société</label>
                        <input type="text" name="nom" class="form-control"
                               placeholder="Ex: Danone" value="{{ old('nom') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="Ex: contact@danone.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control"
                               placeholder="Ex: 0123456789" value="{{ old('telephone') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="adresse" class="form-control"
                               placeholder="Ex: 1 Rue de Paris" value="{{ old('adresse') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mot de passe temporaire</label>
                        <input type="text" name="password" class="form-control"
                               placeholder="Ex: Fournisseur2024!" value="{{ old('password') }}" required>
                        <small style="color:#999; font-size:0.78rem;">
                            Le fournisseur devra le changer à la première connexion
                        </small>
                    </div>
                </div>
                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-check-circle me-2"></i>Enregistrer
                    </button>
                    <a href="{{ route('admin.fournisseurs.index') }}"
                       class="btn" style="background:#f5f5f5; color:#555; border-radius:10px; padding:10px 20px; font-weight:600;">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>