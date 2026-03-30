<?php

use Illuminate\Support\Facades\Broadcast;

// Canal privé pour chaque utilisateur
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Canal privé pour l'admin
Broadcast::channel('admin', function ($user) {
    return $user->role === 'admin';
});