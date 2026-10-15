<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Connexion</title>
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
        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 500px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-left {
            background: #003087;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: #fff;
        }
        .login-right {
            background: #fff;
            flex: 1;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-control {
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #003087;
            box-shadow: 0 0 0 3px rgba(0,48,135,0.1);
        }
        .form-label { font-weight: 600; color: #555; font-size: 0.88rem; }
        .btn-login {
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
        .btn-login:hover { background: #002060; transform: translateY(-1px); }
        .divider { height: 1px; background: #f0f0f0; margin: 20px 0; }
        .alert-danger { background: #fce8e8; border: none; color: #c8102e; border-radius: 10px; border-left: 4px solid #c8102e; }
    </style>
    @include('layouts.neo-assets')
</head>
<body>
    <div class="login-wrapper">

        {{-- Côté gauche --}}
        <div class="login-left">
            <img src="{{ asset('images/carrefour.png') }}" alt="Carrefour" style="height:60px; margin-bottom:20px;">
            <h2 style="font-weight:800; font-size:1.8rem; margin-bottom:8px;">StockFlow</h2>
            <p style="opacity:0.8; text-align:center; font-size:0.9rem;">
                Plateforme de gestion logistique multi-entrepôts Carrefour
            </p>
            <div style="margin-top:40px; text-align:center;">
                <img src="{{ asset('images/adg.webp') }}" alt="ADG Education"
                     style="height:50px; border-radius:8px; background:#fff; padding:4px;">
                <p style="opacity:0.6; font-size:0.78rem; margin-top:8px;">Projet de fin d'année — DWWM</p>
            </div>
        </div>

        {{-- Côté droit --}}
        <div class="login-right">
            <div class="sf-hello">Salut <span>👋</span></div>
            <h3 style="color:#003087; font-weight:800; margin-bottom:6px;">Connexion</h3>
            <p style="color:#888; font-size:0.88rem; margin-bottom:24px;">
                Accédez à votre espace de gestion
            </p>

            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    @foreach($errors->all() as $error)
                        <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email professionnel</label>
        <input type="email" name="email" class="form-control"
               placeholder="Ex: admin@stockflow.com"
               value="{{ old('email') }}" required autofocus>
    </div>
    <div class="mb-4">
        <label class="form-label">Mot de passe</label>
        <input type="password" name="password" class="form-control"
               placeholder="Votre mot de passe" required>
    </div>
    <div class="text-center mb-3">
        <a href="{{ route('password.request') }}"
           style="color:#003087; font-size:0.85rem; font-weight:600; text-decoration:none;">
            🔑 Mot de passe oublié ?
        </a>
    </div>
    <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
    </button>
</form>

            <div class="divider"></div>

            <p class="text-center mb-0" style="color:#888; font-size:0.88rem;">
                Pas encore de compte ?
                <a href="{{ route('register') }}" style="color:#003087; font-weight:600; text-decoration:none;">
                    S'inscrire
                </a>
            </p>
        </div>
    </div>
</body>
</html>