<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockBasNotification extends Notification
{
    public function __construct(
        public string $produitNom,
        public int $quantite,
        public int $seuil,
        public string $entrepotNom
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Stock bas — '.$this->produitNom)
            ->greeting('Bonjour,')
            ->line('Un stock bas a été détecté sur votre entrepôt.')
            ->line('**Produit :** '.$this->produitNom)
            ->line('**Entrepôt :** '.$this->entrepotNom)
            ->line('**Stock actuel :** '.$this->quantite)
            ->line('**Seuil d\'alerte :** '.$this->seuil)
            ->action('Voir le stock', url('/gestionnaire/stocks'))
            ->line('Pensez à passer une commande au dépôt central.')
            ->salutation('L\'équipe StockFlow');
    }
}
