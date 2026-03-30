<?php

namespace App\Notifications;

use App\Models\Campagne;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CampagneObjectifAtteintNotification extends Notification implements ShouldBroadcast
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
            'titre'       => 'Objectif atteint !',
            'message'     => 'La campagne "' . $this->campagne->titre . '" a atteint son objectif',
            'campagne_id' => $this->campagne->id,
            'type'        => 'objectif_atteint',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'titre'       => 'Objectif atteint !',
            'message'     => 'La campagne "' . $this->campagne->titre . '" a atteint son objectif',
            'campagne_id' => $this->campagne->id,
            'type'        => 'objectif_atteint',
        ]);
    }

    public function broadcastOn(): array
    {
        return ['private-user.' . $this->campagne->beneficiaire_id];
    }
}