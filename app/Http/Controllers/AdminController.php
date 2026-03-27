<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Campagne;
use App\Models\Don;
use App\Notifications\CampagneApprouvee;
use App\Notifications\CampagneRejetee;

class AdminController extends Controller
{
    // Tableau de bord
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

    // Approuver une campagne
    public function approuver(Campagne $campagne)
    {
        $campagne->update(['statut' => 'active']);

        $campagne->beneficiaire->notify(new CampagneApprouvee($campagne));

        return redirect()->route('admin.dashboard')
            ->with('success', 'Campagne approuvée avec succès');
    }

    // Rejeter une campagne
    public function rejeter(Campagne $campagne)
    {
        $campagne->update(['statut' => 'rejetee']);

        $campagne->beneficiaire->notify(new CampagneRejetee($campagne));
        
        return redirect()->route('admin.dashboard')
            ->with('success', 'Campagne rejetée');
    }

    // Lister tous les utilisateurs
    public function users()
    {
        $users = User::where('role', '!=', 'admin')->latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    // Suspendre un compte
    public function suspendre(User $user)
    {
        $user->update(['est_suspendu' => true]);

        return redirect()->route('admin.users')
            ->with('success', 'Compte suspendu avec succès');
    }

    // Réactiver un compte
    public function reactiver(User $user)
    {
        $user->update(['est_suspendu' => false]);

        return redirect()->route('admin.users')
            ->with('success', 'Compte réactivé avec succès');
    }

    // Supprimer une campagne
    public function supprimerCampagne(Campagne $campagne)
    {
        $campagne->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Campagne supprimée avec succès');
    }

    // Lister toutes les transactions
    public function transactions()
    {
        $dons = Don::with(['donateur', 'campagne'])
            ->latest()
            ->paginate(10);

        return view('admin.transactions', compact('dons'));
    }
}