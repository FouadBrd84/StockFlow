<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Réinitialiser le mot de passe</title>
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
        }
        .card-custom {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .form-control {
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.9rem;
        }
        .form-control:focus {
            border-color: #003087;
            box-shadow: 0 0 0 3px rgba(0,48,135,0.1);
        }
        .form-label { font-weight: 600; color: #555; font-size: 0.88rem; }
        .btn-primary-custom {
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
        .btn-primary-custom:hover { background: #002060; }
        .alert-danger { background: #fce8e8; border: none; color: #c8102e; border-radius: 10px; border-left: 4px solid #c8102e; padding: 12px 16px; margin-bottom: 16px; }
        .req-box { background: #f8faff; border-radius: 10px; padding: 12px; font-size: 0.8rem; margin-top: 8px; }
        .req-item { padding: 3px 0; color: #aaa; transition: color 0.2s; }
        .req-item.valid { color: #2e7d32; }
    </style>
    @include('layouts.neo-assets')
</head>
<body>
    <div class="card-custom">
        <div class="text-center mb-4">
            <img src="{{ asset('images/carrefour.png') }}" alt="Carrefour" height="40" class="mb-3">
            <h4 style="color:#003087; font-weight:800;">Nouveau mot de passe</h4>
            <p style="color:#888; font-size:0.85rem; margin-top:8px;">
                Choisissez un nouveau mot de passe sécurisé.
            </p>
        </div>

        @if($errors->any())
            <div class="alert-danger">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $request->email) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" id="newPassword"
                       class="form-control" placeholder="Votre nouveau mot de passe" required>
                <div class="req-box">
                    <p style="color:#555; font-weight:700; margin-bottom:6px;">Doit contenir :</p>
                    <div class="req-item" id="req-length">❌ Au moins 8 caractères</div>
                    <div class="req-item" id="req-upper">❌ Une majuscule</div>
                    <div class="req-item" id="req-lower">❌ Une minuscule</div>
                    <div class="req-item" id="req-number">❌ Un chiffre</div>
                    <div class="req-item" id="req-special">❌ Un caractère spécial (@$!%*#?&)</div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation"
                       class="form-control" placeholder="Répéter le mot de passe" required>
            </div>

            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-check-circle me-2"></i>Réinitialiser le mot de passe
            </button>
        </form>
    </div>

    <script>
        const requirements = {
            'req-length':  { test: v => v.length >= 8,        label: 'Au moins 8 caractères' },
            'req-upper':   { test: v => /[A-Z]/.test(v),      label: 'Une majuscule' },
            'req-lower':   { test: v => /[a-z]/.test(v),      label: 'Une minuscule' },
            'req-number':  { test: v => /[0-9]/.test(v),      label: 'Un chiffre' },
            'req-special': { test: v => /[@$!%*#?&]/.test(v), label: 'Un caractère spécial (@$!%*#?&)' },
        };

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