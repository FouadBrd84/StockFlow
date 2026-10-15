<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Nouvel Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:700px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">➕ Ajouter un utilisateur</h1>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm"
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

        <div class="card-custom p-4">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Rôle</label>
                        <select name="role" class="form-select" required id="roleSelect"
                                onchange="toggleFields()">
                            <option value="">-- Sélectionner --</option>
                            <option value="gestionnaire" {{ old('role') == 'gestionnaire' ? 'selected' : '' }}>Gestionnaire (Magasin)</option>
                            <option value="fournisseur" {{ old('role') == 'fournisseur' ? 'selected' : '' }}>Fournisseur</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nom</label>
                        <input type="text" name="name" class="form-control"
                               placeholder="Nom du magasin ou société"
                               value="{{ old('name') }}" required>
                    </div>

                    {{-- Champs gestionnaire --}}
                    <div id="gestionnaireFields" style="display:none; width:100%;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Type de magasin</label>
                                <select name="type" class="form-select">
                                    <option value="">-- Sélectionner --</option>
                                    <option value="city" {{ old('type') == 'city' ? 'selected' : '' }}>Carrefour City</option>
                                    <option value="express" {{ old('type') == 'express' ? 'selected' : '' }}>Carrefour Express</option>
                                    <option value="market" {{ old('type') == 'market' ? 'selected' : '' }}>Carrefour Market</option>
                                    <option value="hypermarket" {{ old('type') == 'hypermarket' ? 'selected' : '' }}>Carrefour Hypermarché</option>
                                    <option value="bio" {{ old('type') == 'bio' ? 'selected' : '' }}>Carrefour Bio</option>
                                    <option value="drive" {{ old('type') == 'drive' ? 'selected' : '' }}>Carrefour Drive</option>
                                    <option value="proximite" {{ old('type') == 'proximite' ? 'selected' : '' }}>Carrefour Proximité</option>
                                    <option value="flash" {{ old('type') == 'flash' ? 'selected' : '' }}>Carrefour Flash</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone" class="form-control"
                                       placeholder="Ex: 0123456789"
                                       value="{{ old('telephone') }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Adresse</label>
                                <input type="text" name="adresse" class="form-control"
                                       placeholder="Ex: 1 Rue de Paris"
                                       value="{{ old('adresse') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               placeholder="Ex: contact@carrefour.com"
                               value="{{ old('email') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mot de passe temporaire</label>
                        <input type="text" name="password" class="form-control"
                               placeholder="Ex: Carrefour2024!"
                               value="{{ old('password') }}" required>
                        <small style="color:#999; font-size:0.78rem;">
                            L'utilisateur devra le changer à la première connexion
                        </small>
                    </div>

                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-check-circle me-2"></i>Créer l'utilisateur
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="btn" style="background:#f5f5f5; color:#555; border-radius:10px; padding:10px 20px; font-weight:600;">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleFields() {
            const role = document.getElementById('roleSelect').value;
            document.getElementById('gestionnaireFields').style.display =
                role === 'gestionnaire' ? 'block' : 'none';
        }
        document.addEventListener('DOMContentLoaded', toggleFields);
    </script>
</body>
</html>