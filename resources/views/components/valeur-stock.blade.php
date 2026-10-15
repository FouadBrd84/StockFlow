@props(['valeur', 'libelle' => 'Valeur du stock'])

<div class="stat-card sf-valeur h-100">
    <i class="bi bi-currency-euro stat-icon"></i>
    <div class="stat-label">{{ $libelle }}</div>
    <div class="stat-value">{{ number_format($valeur, 0, ',', ' ') }} €</div>
    <div class="sf-valeur-note">quantité × prix de chaque produit</div>
</div>
