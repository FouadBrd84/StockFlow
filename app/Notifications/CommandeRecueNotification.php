<?php

namespace App\Notifications;

use App\Models\Commande;
use App\Models\Stock;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommandeRecueNotification extends Notification
{
    public function __construct(public Commande $commande) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Récupérer le stock actuel après réception
        $lignesStock = '';
        foreach ($this->commande->produits as $produit) {
            $stock = Stock::where('produit_id', $produit->id)
                ->where('entrepot_id', $this->commande->entrepot_id)
                ->first();
            $stockActuel = $stock ? $stock->quantite : 0;
            $lignesStock .= "- {$produit->nom} : reçu {$produit->pivot->quantite} — stock actuel : {$stockActuel} {$produit->unite}\n";
        }

        // Lien correct selon le rôle
        $lien = $notifiable->role === 'admin'
            ? url('/admin/commandes/'.$this->commande->id)
            : url('/gestionnaire/commandes');

        return (new MailMessage)
            ->subject('✅ Commande #'.$this->commande->id.' reçue')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Votre commande a bien été reçue et le stock a été mis à jour.')
            ->line('**Commande #'.$this->commande->id.'**')
            ->line('**Entrepôt :** '.$this->commande->entrepot->nom)
            ->line('**Date :** '.$this->commande->updated_at->format('d/m/Y à H:i'))
            ->line('**Produits reçus :**')
            ->line($lignesStock)
            ->action('Voir la commande', $lien)
            ->salutation('L\'équipe StockFlow');
    }
}
