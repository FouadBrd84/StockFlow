<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Mon Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
    <style>
        .req-box { background: #f8faff; border-radius: 10px; padding: 14px; font-size: 0.82rem; margin-top: 10px; }
        .req-item { padding: 3px 0; color: #aaa; transition: color 0.2s; }
        .req-item.valid { color: #2e7d32; }
        .avatar { width: 80px; height: 80px; background: #003087; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #fff; font-weight: 800; margin: 0 auto 16px; }
    </style>
</head>
<body>
    @include('layouts.navigation')

    <div class="container-fluid px-4 py-4" style="max-width:800px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">👤 Mon Profil</h1>
                <p class="page-subtitle">Gérez vos informations personnelles</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="row g-4">
            {{-- Infos profil --}}
            <div class="col-md-4">
                <div class="card-custom p-4 text-center">
                    <label for="photoProfil" class="sf-photo-zone" title="Changer ma photo">
                        @if($user->photo)
                            <img id="apercuPhoto" class="sf-photo-profil" src="{{ $user->urlPhoto() }}" alt="Ma photo de profil">
                        @else
                            <img id="apercuPhoto" class="sf-photo-profil d-none" alt="Aperçu de ma photo">
                            <span id="initialePhoto" class="sf-photo-profil sf-photo-initiale">{{ $user->initiale() }}</span>
                        @endif
                        <span class="sf-photo-camera"><i class="bi bi-camera-fill"></i></span>
                    </label>

                    <form method="POST" action="{{ route('profil.photo.update') }}" enctype="multipart/form-data" id="formPhoto">
                        @csrf
                        <input type="file" name="photo" id="photoProfil" class="d-none" accept="image/jpeg,image/png,image/webp">
                        <div id="actionsPhoto" class="d-none mb-2">
                            <button type="submit" class="btn btn-sm btn-primary-custom"><i class="bi bi-upload me-1"></i>Enregistrer la photo</button>
                        </div>
                    </form>
                    <small class="d-block text-muted mb-2">JPG, PNG ou WebP · 2 Mo max</small>
                    @if($user->photo)
                        <form method="POST" action="{{ route('profil.photo.destroy') }}" class="mb-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm sf-btn-ghost"><i class="bi bi-trash me-1"></i>Supprimer la photo</button>
                        </form>
                    @endif
                    <h5 class="fw-bold" style="color:#003087;">{{ $user->name }}</h5>
                    <p style="color:#888; font-size:0.85rem;">{{ $user->email }}</p>
                    <div class="mt-2">
                        @if($user->role === 'admin')
                            <span style="background:#fce8e8; color:#c8102e; padding:4px 14px; border-radius:20px; font-size:0.8rem; font-weight:700;">
                                Administrateur
                            </span>
                        @elseif($user->role === 'gestionnaire')
                            <span style="background:#e8f5e9; color:#2e7d32; padding:4px 14px; border-radius:20px; font-size:0.8rem; font-weight:700;">
                                Gestionnaire
                            </span>
                        @elseif($user->role === 'fournisseur')
                            <span style="background:#f0f4ff; color:#003087; padding:4px 14px; border-radius:20px; font-size:0.8rem; font-weight:700;">
                                Fournisseur
                            </span>
                        @endif
                    </div>
                    @if($user->entrepot)
                        <p class="mt-3" style="color:#888; font-size:0.85rem;">
                            <i class="bi bi-shop me-1"></i>{{ $user->entrepot->nom }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Formulaire --}}
            <div class="col-md-8">
                <div class="card-custom p-4">
                    <form method="POST" action="{{ route('profil.update') }}">
                        @csrf
                        @method('PUT')

                        <h6 class="fw-bold mb-3" style="color:#003087;">
                            <i class="bi bi-person me-2"></i>Informations générales
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nom</label>
                                <input type="text" name="name" class="form-control"
                                       value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>

                        <hr style="border-color:#f0f0f0;">

                        <h6 class="fw-bold mb-3 mt-3" style="color:#003087;">
                            <i class="bi bi-lock me-2"></i>Changer le mot de passe
                            <small style="color:#aaa; font-weight:400; font-size:0.78rem;">(laisser vide pour ne pas changer)</small>
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Mot de passe actuel</label>
                                <input type="password" name="password_actuel" class="form-control"
                                       placeholder="Votre mot de passe actuel">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" name="password" id="newPassword"
                                       class="form-control" placeholder="Nouveau mot de passe">
                                <div class="req-box">
                                    <p style="color:#555; font-weight:700; margin-bottom:6px; font-size:0.82rem;">Doit contenir :</p>
                                    <div class="req-item" id="req-length">❌ Au moins 8 caractères</div>
                                    <div class="req-item" id="req-upper">❌ Une majuscule</div>
                                    <div class="req-item" id="req-lower">❌ Une minuscule</div>
                                    <div class="req-item" id="req-number">❌ Un chiffre</div>
                                    <div class="req-item" id="req-special">❌ Un caractère spécial (@$!%*#?&)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation"
                                       class="form-control" placeholder="Répéter le mot de passe">
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-check-circle me-2"></i>Enregistrer
                            </button>
                            <a href="{{ route('dashboard') }}"
                               class="btn" style="background:#f5f5f5; color:#555; border-radius:10px; padding:10px 20px; font-weight:600;">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const requirements = {
            'req-length':  { test: v => v.length >= 8,        label: 'Au moins 8 caractères' },
            'req-upper':   { test: v => /[A-Z]/.test(v),      label: 'Une majuscule' },
            'req-lower':   { test: v => /[a-z]/.test(v),      label: 'Une minuscule' },
            'req-number':  { test: v => /[0-9]/.test(v),      label: 'Un chiffre' },
            'req-special': { test: v => /[@$!%*#?&]/.test(v), label: 'Un caractère spécial (@$!%*#?&)' },
        };

        // Aperçu de la photo avant l'envoi
        document.getElementById('photoProfil').addEventListener('change', function () {
            const fichier = this.files[0];
            if (!fichier) { return; }
            const apercu = document.getElementById('apercuPhoto');
            apercu.src = URL.createObjectURL(fichier);
            apercu.classList.remove('d-none');
            document.getElementById('initialePhoto')?.classList.add('d-none');
            document.getElementById('actionsPhoto').classList.remove('d-none');
        });

        document.getElementById('newPassword').addEventListener('input', function() {
            const val = this.value;
            Object.entries(requirements).forEach(([id, req]) => {
                const el = document.getElementById(id);
                const valid = req.test(val);
                el.classList.toggle('valid', valid);
                el.innerHTML = (valid ? '✅' : '❌') + ' ' + req.label;
            });
        });
    </script>
</body>
</html>