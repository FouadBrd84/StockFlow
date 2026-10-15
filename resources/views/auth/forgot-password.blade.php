<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Mot de passe oublié</title>
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
        .alert-success { background: #e8f5e9; border: none; color: #2e7d32; border-radius: 10px; border-left: 4px solid #2e7d32; padding: 12px 16px; margin-bottom: 16px; }
        .alert-danger { background: #fce8e8; border: none; color: #c8102e; border-radius: 10px; border-left: 4px solid #c8102e; padding: 12px 16px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="card-custom">
        <div class="text-center mb-4">
            <img src="{{ asset('images/carrefour.png') }}" alt="Carrefour" height="40" class="mb-3">
            <h4 style="color:#003087; font-weight:800;">Mot de passe oublié ?</h4>
            <p style="color:#888; font-size:0.85rem; margin-top:8px;">
                Entrez votre email et nous vous enverrons un lien de réinitialisation.
            </p>
        </div>

        @if(session('status'))
            <div class="alert-success">
                <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-danger">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label">Email professionnel</label>
                <input type="email" name="email" class="form-control"
                       placeholder="Ex: admin@stockflow.com"
                       value="{{ old('email') }}" required autofocus>
            </div>
            <button type="submit" class="btn-primary-custom">
                <i class="bi bi-envelope me-2"></i>Envoyer le lien de réinitialisation
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}"
               style="color:#003087; font-size:0.85rem; font-weight:600; text-decoration:none;">
                <i class="bi bi-arrow-left me-1"></i>Retour à la connexion
            </a>
        </div>
    </div>
</body>
</html>