<?php

namespace App\Notifications;

use App\Models\Campagne;
use Illuminate\Notifications\Notification;

class CampagneApprouvee extends Notification
{
    public function __construct(protected Campagne $campagne) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message'     => 'Votre campagne "' . $this->campagne->titre . '" a été approuvée',
            'campagne_id' => $this->campagne->id,
            'type'        => 'campagne_approuvee',
        ];
    }
}