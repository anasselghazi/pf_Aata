<?php

namespace App\Notifications;

use App\Models\Campagne;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NouvelleCampagneSoumiseNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(public Campagne $campagne) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titre'       => 'Nouvelle campagne soumise',
            'message'     => 'La campagne "' . $this->campagne->titre . '" est en attente de validation',
            'campagne_id' => $this->campagne->id,
            'type'        => 'nouvelle_campagne_soumise',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'titre'       => 'Nouvelle campagne soumise',
            'message'     => 'La campagne "' . $this->campagne->titre . '" est en attente de validation',
            'campagne_id' => $this->campagne->id,
            'type'        => 'nouvelle_campagne_soumise',
        ]);
    }

    public function broadcastOn(): array
    {
        return ['private-admin'];
    }
}