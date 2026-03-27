<?php

namespace App\Notifications;

use App\Models\Campagne;
use Illuminate\Notifications\Notification;

class NouvelleCampagneSoumise extends Notification
{
    public function __construct(protected Campagne $campagne) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message'     => 'Nouvelle campagne soumise : "' . $this->campagne->titre . '" en attente de validation',
            'campagne_id' => $this->campagne->id,
            'type'        => 'nouvelle_campagne_soumise',
        ];
    }
}