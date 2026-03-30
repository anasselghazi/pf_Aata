<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Campagne;
use App\Models\Don;
use App\Notifications\CampagneApprouveeNotification;
use App\Notifications\CampagneRejeteeNotification;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_campagnes'   => Campagne::count(),
            'campagnes_attente' => Campagne::where('statut', 'en_attente')->count(),
            'total_dons'        => Don::sum('montant'),
            'total_users'       => User::where('role', '!=', 'admin')->count(),
        ];

        $campagnes_attente = Campagne::where('statut', 'en_attente')
            ->with(['beneficiaire', 'categorie'])
            ->latest()
            ->get();

        return view('admin.dashboard', compact('stats', 'campagnes_attente'));
    }

    public function approuver(Campagne $campagne)
    {
        $campagne->update(['statut' => 'approuvee']);

        // Notifier le bénéficiaire
        $campagne->beneficiaire->notify(new CampagneApprouveeNotification($campagne));

        return redirect()->route('admin.dashboard')
            ->with('success', 'Campagne approuvée avec succès');
    }

    public function rejeter(Campagne $campagne)
    {
        $campagne->update(['statut' => 'rejetee']);

        // Notifier le bénéficiaire
        $campagne->beneficiaire->notify(new CampagneRejeteeNotification($campagne));

        return redirect()->route('admin.dashboard')
            ->with('success', 'Campagne rejetée');
    }

    public function users()
    {
        $users = User::where('role', '!=', 'admin')->latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function suspendre(User $user)
    {
        $user->update(['est_suspendu' => true]);

        return redirect()->route('admin.users')
            ->with('success', 'Compte suspendu avec succès');
    }

    public function reactiver(User $user)
    {
        $user->update(['est_suspendu' => false]);

        return redirect()->route('admin.users')
            ->with('success', 'Compte réactivé avec succès');
    }

    public function supprimerCampagne(Campagne $campagne)
    {
        $campagne->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Campagne supprimée avec succès');
    }

    public function transactions()
    {
        $dons = Don::with(['donateur', 'campagne'])
            ->latest()
            ->paginate(10);

        return view('admin.transactions', compact('dons'));
    }
}