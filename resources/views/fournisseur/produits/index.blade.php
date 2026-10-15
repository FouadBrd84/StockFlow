<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Mes Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">📦 Mes Produits</h1>
                <p class="page-subtitle">{{ $produits->count() }} produit(s) dans votre catalogue</p>
            </div>
            <a href="{{ route('fournisseur.produits.create') }}" class="btn btn-primary-custom">
                <i class="bi bi-plus me-1"></i> Ajouter un produit
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="card-custom">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Unité</th>
                        <th>Seuil alerte</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produits as $produit)
                        <tr>
                            <td class="ps-4" style="color:#aaa;">{{ $produit->id }}</td>
                            <td class="fw-bold">{{ $produit->nom }}</td>
                            <td style="color:#003087; font-weight:600;">{{ number_format($produit->prix, 2) }} €</td>
                            <td>{{ $produit->unite }}</td>
                            <td>{{ $produit->seuil_alerte }}</td>
                            <td>
                                <a href="{{ route('fournisseur.produits.edit', $produit) }}"
                                   class="btn btn-sm me-1"
                                   style="background:#fff8e1; color:#f57f17; border:1px solid #f57f17; border-radius:8px; padding:4px 12px; font-size:0.8rem;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('fournisseur.produits.destroy', $produit) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm"
                                            style="background:#fce8e8; color:#c8102e; border:1px solid #c8102e; border-radius:8px; padding:4px 12px; font-size:0.8rem;"
                                            onclick="return confirm('Supprimer ce produit ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color:#aaa;">
                                Aucun produit — ajoutez votre premier produit
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>