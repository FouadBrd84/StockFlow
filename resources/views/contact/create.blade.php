<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StockFlow - Contacter l'admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/stockflow.css') }}">
</head>
<body>
    @include('layouts.navigation')

    <div class="container-fluid px-4 py-4" style="max-width:1100px; margin:0 auto;">

        <div class="section-header">
            <div>
                <h1 class="page-title">✉️ Contacter l'admin</h1>
                <p class="page-subtitle">Une question, un problème de livraison, une demande ? Écris directement à l'administrateur.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card-custom p-4">
                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="sujet">Sujet</label>
                            <input type="text" name="sujet" id="sujet" class="form-control" maxlength="150"
                                   value="{{ old('sujet') }}" placeholder="Ex : Retard de livraison sur la commande #24" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" for="contenu">Message</label>
                            <textarea name="contenu" id="contenu" class="form-control" rows="8" maxlength="3000"
                                      placeholder="Explique ta demande en quelques lignes…" required>{{ old('contenu') }}</textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <small class="text-muted"><i class="bi bi-reply me-1"></i>L'admin te répondra à <b>{{ Auth::user()->email }}</b></small>
                            <small class="sf-compteur" id="compteur">0 / 3000</small>
                        </div>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-send me-2"></i>Envoyer le message
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card-custom h-100">
                    <div class="card-header-custom"><i class="bi bi-clock-history"></i> Mes derniers messages</div>
                    @forelse($messagesEnvoyes as $message)
                        <div class="sf-message-mini">
                            <div class="d-flex justify-content-between gap-2">
                                <b>{{ $message->sujet }}</b>
                                @if($message->estLu())
                                    <span class="badge-ok">Lu</span>
                                @else
                                    <span class="badge-attente">Envoyé</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $message->created_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    @empty
                        <div class="sf-previsions-vide">
                            <div class="sf-previsions-emoji">📭</div>
                            <b>Aucun message envoyé</b>
                            <p>Tes messages apparaîtront ici, avec leur statut.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        const contenu = document.getElementById('contenu');
        const compteur = document.getElementById('compteur');
        const majCompteur = () => { compteur.textContent = contenu.value.length + ' / 3000'; };
        contenu.addEventListener('input', majCompteur);
        majCompteur();
    </script>
</body>
</html>
