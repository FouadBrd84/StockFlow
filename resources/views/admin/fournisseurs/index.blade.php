<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Fournisseurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">🚛 Gestion des Fournisseurs</h1>
                <p class="page-subtitle">{{ $fournisseurs->count() }} fournisseur(s) enregistré(s)</p>
            </div>
            <a href="{{ route('admin.fournisseurs.create') }}" class="btn btn-primary-custom">
                <i class="bi bi-plus me-1"></i> Ajouter
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
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fournisseurs as $fournisseur)
                        <tr>
                            <td class="ps-4" style="color:#aaa;">{{ $fournisseur->id }}</td>
                            <td class="fw-bold">{{ $fournisseur->name }}</td>
                            <td style="color:#555;">{{ $fournisseur->email }}</td>
                            <td style="color:#555;">{{ $fournisseur->telephone ?? '—' }}</td>
                            <td style="color:#555;">{{ $fournisseur->adresse ?? '—' }}</td>
                            <td>
                                <a href="{{ route('admin.fournisseurs.edit', $fournisseur) }}"
                                   class="btn btn-sm me-1"
                                   style="background:#fff8e1; color:#f57f17; border:1px solid #f57f17; border-radius:8px; padding:4px 12px; font-size:0.8rem;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.fournisseurs.destroy', $fournisseur) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm"
                                            style="background:#fce8e8; color:#c8102e; border:1px solid #c8102e; border-radius:8px; padding:4px 12px; font-size:0.8rem;"
                                            onclick="return confirm('Supprimer ce fournisseur ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4" style="color:#aaa;">Aucun fournisseur</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>