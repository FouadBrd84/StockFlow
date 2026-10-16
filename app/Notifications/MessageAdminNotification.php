<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageAdminNotification extends Notification
{
    public function __construct(public Message $message) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $expediteur = $this->message->expediteur;

        return (new MailMessage)
            ->subject('✉️ '.$this->message->sujet.' — de '.$expediteur->name)
            ->replyTo($expediteur->email, $expediteur->name)
            ->greeting('Bonjour,')
            ->line('Vous avez reçu un nouveau message sur StockFlow.')
            ->line('**De :** '.$expediteur->name.' ('.ucfirst($expediteur->role).')')
            ->line('**Sujet :** '.$this->message->sujet)
            ->line('**Message :**')
            ->line($this->message->contenu)
            ->action('Voir mes messages', route('admin.messages.index'))
            ->line('Répondez simplement à cet email pour écrire directement à l\'expéditeur.')
            ->salutation('L\'équipe StockFlow');
    }
}
