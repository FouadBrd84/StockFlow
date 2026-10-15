<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Changer le mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
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
        .alert-warning { background: #fff8e1; border: none; color: #f57f17; border-radius: 10px; border-left: 4px solid #fbbf24; padding: 12px 16px; margin-bottom: 16px; }
        .req-box { background: #f8faff; border-radius: 10px; padding: 14px; font-size: 0.82rem; margin-top: 10px; }
        .req-item { padding: 3px 0; color: #aaa; transition: color 0.2s; }
        .req-item.valid { color: #2e7d32; }
    </style>
</head>
<body>
    <div class="card-custom">
        <div class="text-center mb-4">
            <img src="{{ asset('images/carrefour.png') }}" alt="Carrefour" height="40" class="mb-3">
            <h4 style="color:#003087; font-weight:800;">Changement de mot de passe</h4>
        </div>

        <div class="alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Pour des raisons de sécurité vous devez changer votre mot de passe avant de continuer.
        </div>

        @if($errors->any())
            <div class="alert-danger">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.change.update') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" id="passwordInput"
                       class="form-control" placeholder="Votre nouveau mot de passe" required>
                <div class="req-box">
                    <p style="color:#555; font-weight:700; margin-bottom:8px; font-size:0.83rem;">
                        Le mot de passe doit contenir :
                    </p>
                    <div class="req-item" id="req-length">❌ Au moins 8 caractères</div>
                    <div class="req-item" id="req-upper">❌ Au moins une majuscule (A-Z)</div>
                    <div class="req-item" id="req-lower">❌ Au moins une minuscule (a-z)</div>
                    <div class="req-item" id="req-number">❌ Au moins un chiffre (0-9)</div>
                    <div class="req-item" id="req-special">❌ Au moins un caractère spécial (@$!%*#?&)</div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" class="form-control"
                       placeholder="Répéter le mot de passe" required>
            </div>

            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-check-circle me-2"></i>Changer le mot de passe
            </button>
        </form>
    </div>

    <script>
    const input = document.getElementById('passwordInput');

    const requirements = {
        'req-length':  { test: v => v.length >= 8,          label: 'Au moins 8 caractères' },
        'req-upper':   { test: v => /[A-Z]/.test(v),        label: 'Au moins une majuscule (A-Z)' },
        'req-lower':   { test: v => /[a-z]/.test(v),        label: 'Au moins une minuscule (a-z)' },
        'req-number':  { test: v => /[0-9]/.test(v),        label: 'Au moins un chiffre (0-9)' },
        'req-special': { test: v => /[@$!%*#?&]/.test(v),   label: 'Au moins un caractère spécial (@$!%*#?&)' },
    };

    input.addEventListener('input', function() {
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