<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Lister toutes les notifications de l'utilisateur connecté
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(10);

        return view('notifications.index', compact('notifications'));
    }

    // Marquer une notification comme lue
    public function markAsRead(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notification marquée comme lue');
    }

    // Marquer toutes les notifications comme lues
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    }

    // Supprimer une notification
    public function destroy(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification supprimée');
    }
}