{{-- Thème « Neo » pour les pages sans barre de navigation (connexion, inscription, mots de passe) --}}
<link rel="icon" type="image/svg+xml" href="{{ asset('images/stockflow-logo.svg') }}">
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
<meta name="theme-color" content="#8b5cf6">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Barlow:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/stockflow.css') }}?v={{ filemtime(public_path('css/stockflow.css')) }}">
<link rel="stylesheet" href="{{ asset('css/stockflow-auth.css') }}?v={{ filemtime(public_path('css/stockflow-auth.css')) }}">
<link rel="stylesheet" href="{{ asset('css/stockflow-themes.css') }}?v={{ filemtime(public_path('css/stockflow-themes.css')) }}">
@include('layouts.theme-init')
<script src="{{ asset('js/stockflow.js') }}?v={{ filemtime(public_path('js/stockflow.js')) }}" defer></script>
