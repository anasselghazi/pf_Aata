<?php

namespace App\Notifications;

use App\Models\Don;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NouveauDonNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(public Don $don) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titre'       => 'Don effectué',
            'message'     => 'Votre don de ' . $this->don->montant . ' DH a été effectué avec succès',
            'campagne_id' => $this->don->campagne_id,
            'type'        => 'nouveau_don',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'titre'       => 'Don effectué',
            'message'     => 'Votre don de ' . $this->don->montant . ' DH a été effectué avec succès',
            'campagne_id' => $this->don->campagne_id,
            'type'        => 'nouveau_don',
        ]);
    }

    public function broadcastOn(): array
    {
        return ['private-user.' . $this->don->donateur_id];
    }
}