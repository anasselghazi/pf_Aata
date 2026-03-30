<?php

namespace App\Notifications;

use App\Models\Campagne;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class CampagneRejeteeNotification extends Notification implements ShouldBroadcast
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
            'titre'       => 'Campagne rejetée',
            'message'     => 'Votre campagne "' . $this->campagne->titre . '" a été rejetée',
            'campagne_id' => $this->campagne->id,
            'type'        => 'campagne_rejetee',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'titre'       => 'Campagne rejetée',
            'message'     => 'Votre campagne "' . $this->campagne->titre . '" a été rejetée',
            'campagne_id' => $this->campagne->id,
            'type'        => 'campagne_rejetee',
        ]);
    }

    public function broadcastOn(): array
    {
        return ['private-user.' . $this->campagne->beneficiaire_id];
    }
}