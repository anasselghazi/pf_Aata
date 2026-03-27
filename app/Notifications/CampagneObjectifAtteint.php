<?php

namespace App\Notifications;

use App\Models\Campagne;
use Illuminate\Notifications\Notification;

class CampagneObjectifAtteint extends Notification
{
    public function __construct(protected Campagne $campagne) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message'     => 'La campagne "' . $this->campagne->titre . '" a atteint son objectif',
            'campagne_id' => $this->campagne->id,
            'type'        => 'objectif_atteint',
        ];
    }
}