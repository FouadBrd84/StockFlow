<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #003087 0%, #0055b3 50%, #c8102e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }
        .register-wrapper {
            display: flex;
            width: 100%;
            max-width: 1000px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .register-left {
            background: #003087;
            width: 280px;
            min-width: 280px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 30px;
            color: #fff;
        }
        .register-right {
            background: #fff;
            flex: 1;
            padding: 40px;
            overflow-y: auto;
        }
        .form-control, .form-select {
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.88rem;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #003087;
            box-shadow: 0 0 0 3px rgba(0,48,135,0.1);
        }
        .form-label { font-weight: 600; color: #555; font-size: 0.85rem; margin-bottom: 5px; }
        .btn-register {
            background: #003087;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 0.95rem;
            font-weight: 700;
            width: 100%;
            transition: all 0.2s;
        }
        .btn-register:hover { background: #002060; }
        .role-btn {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            color: #555;
            background: #fff;
        }
        .role-btn:hover { border-color: #003087; background: #f0f4ff; }
        .role-btn.active { border-color: #003087; background: #f0f4ff; color: #003087; }
        .role-btn i { font-size: 1.8rem; display: block; margin-bottom: 6px; }
        .divider { height: 1px; background: #f0f0f0; margin: 20px 0; }
        .alert-danger { background: #fce8e8; border: none; color: #c8102e; border-radius: 10px; border-left: 4px solid #c8102e; padding: 12px 16px; }
        .fields-hidden { display: none; }
    </style>
    @include('layouts.neo-assets')
</head>
<body>
    <div class="register-wrapper">

        {{-- Côté gauche --}}
        <div class="register-left">
            <img src="{{ asset('images/stockflow-logo.svg') }}" alt="Logo StockFlow" class="sf-auth-logo"
                 style="height:55px; margin-bottom:20px;">
            <h2 style="font-weight:800; font-size:1.5rem; margin-bottom:8px;">StockFlow</h2>
            <p style="opacity:0.8; text-align:center; font-size:0.85rem; line-height:1.5;">
                La logistique multi-entrepôts, en mode fluide ⚡
            </p>
            <div style="margin-top:30px; text-align:center;">
                <img src="{{ asset('images/adg.webp') }}" alt="ADG Education"
                     style="height:45px; border-radius:8px; background:#fff; padding:4px;">
                <p style="opacity:0.6; font-size:0.75rem; margin-top:8px;">Projet DWWM 2026</p>
            </div>
        </div>

        {{-- Côté droit --}}
        <div class="register-right">
            <h3 style="color:#003087; font-weight:800; margin-bottom:4px;">Créer un compte</h3>
            <p style="color:#888; font-size:0.85rem; margin-bottom:20px;">
                Rejoignez la plateforme StockFlow
            </p>

            @if($errors->any())
                <div class="alert-danger mb-4">
                    @foreach($errors->all() as $error)
                        <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Choix du rôle --}}
                <div class="mb-4">
                    <label class="form-label">Je suis</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="role-btn {{ old('role') == 'gestionnaire' ? 'active' : '' }}"
                                 onclick="selectRole('gestionnaire', this)">
                                <i class="bi bi-shop"></i>
                                Magasin Carrefour
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="role-btn {{ old('role') == 'fournisseur' ? 'active' : '' }}"
                                 onclick="selectRole('fournisseur', this)">
                                <i class="bi bi-truck"></i>
                                Fournisseur
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="role" id="roleInput" value="{{ old('role') }}">
                </div>

                {{-- Champs Gestionnaire --}}
                <div id="gestionnaire-fields" class="{{ old('role') == 'gestionnaire' ? '' : 'fields-hidden' }}">
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Nom du magasin</label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Ex: Carrefour City Lyon"
                                   value="{{ old('name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type de magasin</label>
                            <select name="type_entrepot" class="form-select">
                                <option value="">-- Sélectionner --</option>
                                <option value="city" {{ old('type_entrepot') == 'city' ? 'selected' : '' }}>Carrefour City</option>
                                <option value="express" {{ old('type_entrepot') == 'express' ? 'selected' : '' }}>Carrefour Express</option>
                                <option value="market" {{ old('type_entrepot') == 'market' ? 'selected' : '' }}>Carrefour Market</option>
                                <option value="hypermarket" {{ old('type_entrepot') == 'hypermarket' ? 'selected' : '' }}>Carrefour Hypermarché</option>
                                <option value="bio" {{ old('type_entrepot') == 'bio' ? 'selected' : '' }}>Carrefour Bio</option>
                                <option value="drive" {{ old('type_entrepot') == 'drive' ? 'selected' : '' }}>Carrefour Drive</option>
                                <option value="proximite" {{ old('type_entrepot') == 'proximite' ? 'selected' : '' }}>Carrefour Proximité</option>
                                <option value="flash" {{ old('type_entrepot') == 'flash' ? 'selected' : '' }}>Carrefour Flash</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Champs Fournisseur --}}
                <div id="fournisseur-fields" class="{{ old('role') == 'fournisseur' ? '' : 'fields-hidden' }}">
                    <div class="row g-3 mb-2">
                        <div class="col-md-12">
                            <label class="form-label">Nom de la société</label>
                            <input type="text" name="nom" class="form-control"
                                   placeholder="Ex: Danone"
                                   value="{{ old('nom') }}">
                        </div>
                    </div>
                </div>

                {{-- Champs communs --}}
                <div id="common-fields" class="{{ old('role') ? '' : 'fields-hidden' }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" name="telephone" class="form-control"
                                   placeholder="Ex: 0123456789"
                                   value="{{ old('telephone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-control"
                                   placeholder="Ex: 1 Rue de Paris"
                                   value="{{ old('adresse') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Email professionnel</label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="Ex: contact@societe.com"
                                   value="{{ old('email') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Min. 8 caractères">
                                   <div style="background:#f0f4ff; border-radius:10px; padding:12px; font-size:0.8rem; margin-top:8px;">
    <p style="color:#003087; font-weight:700; margin-bottom:6px;">Le mot de passe doit contenir :</p>
    <div id="reg-length" style="color:#aaa;">❌ Au moins 8 caractères</div>
    <div id="reg-upper" style="color:#aaa;">❌ Au moins une majuscule</div>
    <div id="reg-lower" style="color:#aaa;">❌ Au moins une minuscule</div>
    <div id="reg-number" style="color:#aaa;">❌ Au moins un chiffre</div>
    <div id="reg-special" style="color:#aaa;">❌ Au moins un caractère spécial (@$!%*#?&)</div>
</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirmer</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Répéter">
                        </div>
                    </div>

                    <button type="submit" class="btn-register mt-4">
                        <i class="bi bi-person-plus me-2"></i>Créer mon compte
                    </button>
                </div>

            </form>

            <div class="divider"></div>
            <p class="text-center mb-0" style="color:#888; font-size:0.85rem;">
                Déjà un compte ?
                <a href="{{ route('login') }}" style="color:#003087; font-weight:600; text-decoration:none;">
                    Se connecter
                </a>
            </p>
        </div>
    </div>

    <script>
        function selectRole(role, el) {
            document.getElementById('roleInput').value = role;
            document.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('active'));
            el.classList.add('active');
            document.getElementById('gestionnaire-fields').className = role === 'gestionnaire' ? '' : 'fields-hidden';
            document.getElementById('fournisseur-fields').className = role === 'fournisseur' ? '' : 'fields-hidden';
            document.getElementById('common-fields').className = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const oldRole = '{{ old('role') }}';
            if (oldRole) {
                document.getElementById('gestionnaire-fields').className = oldRole === 'gestionnaire' ? '' : 'fields-hidden';
                document.getElementById('fournisseur-fields').className = oldRole === 'fournisseur' ? '' : 'fields-hidden';
                document.getElementById('common-fields').className = '';
            }
        });
        const regInput = document.querySelector('input[name="password"]');
const regRequirements = {
    'reg-length':  { test: v => v.length >= 8,        label: 'Au moins 8 caractères' },
    'reg-upper':   { test: v => /[A-Z]/.test(v),      label: 'Au moins une majuscule' },
    'reg-lower':   { test: v => /[a-z]/.test(v),      label: 'Au moins une minuscule' },
    'reg-number':  { test: v => /[0-9]/.test(v),      label: 'Au moins un chiffre' },
    'reg-special': { test: v => /[@$!%*#?&]/.test(v), label: 'Au moins un caractère spécial (@$!%*#?&)' },
};

if (regInput) {
    regInput.addEventListener('input', function() {
        const val = this.value;
        Object.entries(regRequirements).forEach(([id, req]) => {
            const el = document.getElementById(id);
            if (el) {
                const valid = req.test(val);
                el.style.color = valid ? '#2e7d32' : '#aaa';
                el.innerHTML = (valid ? '✅' : '❌') + ' ' + req.label;
            }
        });
    });
}
    </script>
</body>
</html>