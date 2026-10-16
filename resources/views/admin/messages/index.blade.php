<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')

    <div class="container-fluid px-4 py-4" style="max-width:1000px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">📬 Messages</h1>
                <p class="page-subtitle">
                    @if($nonLus > 0)
                        {{ $nonLus }} message(s) non lu(s)
                    @else
                        Tout est lu 👌
                    @endif
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @forelse($messages as $message)
            <div class="card-custom sf-message mb-3 {{ $message->estLu() ? '' : 'sf-message-nouveau' }}">
                <div class="d-flex gap-3 align-items-start">
                    @if($message->expediteur->photo)
                        <img class="sf-avatar sf-avatar-photo sf-avatar-lg" src="{{ $message->expediteur->urlPhoto() }}" alt="Photo de {{ $message->expediteur->name }}">
                    @else
                        <span class="sf-avatar sf-avatar-lg">{{ $message->expediteur->initiale() }}</span>
                    @endif
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <div>
                                <b class="sf-message-nom">{{ $message->expediteur->name }}</b>
                                <span class="{{ $message->expediteur->role === 'fournisseur' ? 'badge-info' : 'badge-ok' }} ms-1">{{ ucfirst($message->expediteur->role) }}</span>
                                @if($message->expediteur->entrepot)
                                    <small class="text-muted ms-1"><i class="bi bi-shop"></i> {{ $message->expediteur->entrepot->nom }}</small>
                                @endif
                            </div>
                            <small class="text-muted">
                                @unless($message->estLu())
                                    <span class="badge-attente me-1">Nouveau</span>
                                @endunless
                                {{ $message->created_at->format('d/m/Y à H:i') }}
                            </small>
                        </div>
                        <h3 class="sf-message-sujet">{{ $message->sujet }}</h3>
                        <p class="sf-message-contenu">{!! nl2br(e($message->contenu)) !!}</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-primary-custom"
                               href="mailto:{{ $message->expediteur->email }}?subject={{ rawurlencode('Re : '.$message->sujet) }}">
                                <i class="bi bi-reply me-1"></i>Répondre par email
                            </a>
                            @unless($message->estLu())
                                <form method="POST" action="{{ route('admin.messages.lu', $message) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm sf-btn-ghost"><i class="bi bi-check2 me-1"></i>Marquer comme lu</button>
                                </form>
                            @endunless
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card-custom">
                <div class="sf-previsions-vide">
                    <div class="sf-previsions-emoji">📭</div>
                    <b>Aucun message pour l'instant</b>
                    <p>Les messages des gestionnaires et des fournisseurs arriveront ici.</p>
                </div>
            </div>
        @endforelse

        <div class="mt-4">{{ $messages->links('pagination::bootstrap-5') }}</div>
    </div>
</body>
</html>
