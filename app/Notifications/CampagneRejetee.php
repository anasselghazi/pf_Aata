<?php

namespace App\Notifications;

use App\Models\Campagne;
use Illuminate\Notifications\Notification;

class CampagneRejetee extends Notification
{
    public function __construct(protected Campagne $campagne) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message'     => 'Votre campagne "' . $this->campagne->titre . '" a été rejetée',
            'campagne_id' => $this->campagne->id,
            'type'        => 'campagne_rejetee',
        ];
    }
}