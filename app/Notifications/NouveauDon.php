<?php

namespace App\Notifications;

use App\Models\Don;
use Illuminate\Notifications\Notification;

class NouveauDon extends Notification
{
    public function __construct(protected Don $don) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message'     => 'Votre don de ' . $this->don->montant . ' DH a été effectué avec succès',
            'campagne_id' => $this->don->campagne_id,
            'type'        => 'nouveau_don',
        ];
    }
}