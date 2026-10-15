<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommandeExpedieeNotification extends Notification
{
    public function __construct(public Commande $commande) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚚 Commande #'.$this->commande->id.' expédiée')
            ->greeting('Bonjour,')
            ->line('Une commande a été expédiée et est en route vers votre entrepôt.')
            ->line('**Commande #'.$this->commande->id.'**')
            ->line('**Fournisseur :** '.($this->commande->fournisseur->nom ?? '—'))
            ->line('**Entrepôt destination :** '.$this->commande->entrepot->nom)
            ->line('**Date d\'expédition :** '.$this->commande->updated_at->format('d/m/Y à H:i'))
            ->action('Voir la commande', url('/admin/commandes/'.$this->commande->id))
            ->salutation('L\'équipe StockFlow');
    }
}
