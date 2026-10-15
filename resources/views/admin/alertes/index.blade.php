<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>StockFlow - Alertes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')
    <div class="container-fluid px-4 py-4" style="max-width:1400px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">🔔 Alertes de stock</h1>
                <p class="page-subtitle">{{ $alertes->count() }} alerte(s) enregistrée(s)</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @if($alertes->isEmpty())
            <div class="card-custom p-5 text-center">
                <i class="bi bi-check-circle mb-3" style="font-size:3rem; color:#2e7d32;"></i>
                <p style="color:#aaa;">Aucune alerte — tout va bien ✅</p>
            </div>
        @else
            <div class="card-custom">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Type</th>
                            <th>Message</th>
                            <th>Entrepôt</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alertes as $alerte)
                            <tr style="{{ $alerte->type === 'rupture' ? 'border-left:3px solid #c8102e;' : 'border-left:3px solid #f57f17;' }}">
                                <td class="ps-4">
                                    @if($alerte->type === 'rupture')
                                        <span class="badge-danger">🚨 Rupture</span>
                                    @else
                                        <span class="badge-attente">⚠️ Stock bas</span>
                                    @endif
                                </td>
                                <td>{{ $alerte->message }}</td>
                                <td>{{ $alerte->stock->entrepot->nom }}</td>
                                <td>
                                    @if($alerte->statut === 'non_lue')
                                        <span class="badge-danger">Non lue</span>
                                    @elseif($alerte->statut === 'lue')
                                        <span class="badge-attente">Lue</span>
                                    @else
                                        <span class="badge-ok">Traitée ✅</span>
                                    @endif
                                </td>
                                <td style="color:#aaa;">{{ $alerte->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($alerte->statut === 'non_lue')
                                        <form method="POST" action="{{ route('admin.alertes.lue', $alerte) }}" style="display:inline">
                                            @csrf
                                            <button class="btn btn-sm me-1"
                                                    style="background:#fff8e1; color:#f57f17; border:1px solid #f57f17; border-radius:8px; padding:4px 12px; font-size:0.8rem;">
                                                <i class="bi bi-eye"></i> Lu
                                            </button>
                                        </form>
                                    @endif
                                    @if($alerte->statut !== 'traitee')
                                        <form method="POST" action="{{ route('admin.alertes.traitee', $alerte) }}" style="display:inline">
                                            @csrf
                                            <button class="btn btn-sm"
                                                    style="background:#e8f5e9; color:#2e7d32; border:1px solid #2e7d32; border-radius:8px; padding:4px 12px; font-size:0.8rem;">
                                                <i class="bi bi-check"></i> Traité
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>