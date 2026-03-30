<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Categorie;
use App\Models\User;
use App\Http\Requests\CampagneRequest;
use App\Notifications\NouvelleCampagneSoumiseNotification;

class CampagneController extends Controller
{
    public function index()
    {
        $campagnes = Campagne::where('statut', 'approuvee')
            ->with(['categorie', 'beneficiaire'])
            ->latest()
            ->paginate(10);

        return view('campagnes.index', compact('campagnes'));
    }

    public function show(Campagne $campagne)
    {
        return view('campagnes.show', compact('campagne'));
    }

    public function create()
    {
        $categories = Categorie::all();
        return view('campagnes.create', compact('categories'));
    }

    public function store(CampagneRequest $request)
    {
        $campagne = Campagne::create([
            'titre'              => $request->titre,
            'description'        => $request->description,
            'objectif_financier' => $request->objectif_financier,
            'categorie_id'       => $request->categorie_id,
            'beneficiaire_id'    => auth()->id(),
            'montant_collecte'   => 0,
            'statut'             => 'en_attente',
        ]);

        // Notifier l'admin
        $admin = User::where('role', 'admin')->first();
        $admin->notify(new NouvelleCampagneSoumiseNotification($campagne));

        return redirect()->route('beneficiaire.dashboard')
            ->with('success', 'Campagne soumise avec succès, en attente de validation');
    }

    public function edit(Campagne $campagne)
    {
        if ($campagne->beneficiaire_id !== auth()->id()) {
            abort(403);
        }

        if ($campagne->statut !== 'en_attente') {
            return redirect()->route('beneficiaire.dashboard')
                ->withErrors(['error' => 'Vous ne pouvez pas modifier une campagne déjà traitée']);
        }

        $categories = Categorie::all();
        return view('campagnes.edit', compact('campagne', 'categories'));
    }

    public function update(CampagneRequest $request, Campagne $campagne)
    {
        if ($campagne->beneficiaire_id !== auth()->id()) {
            abort(403);
        }

        $campagne->update([
            'titre'              => $request->titre,
            'description'        => $request->description,
            'objectif_financier' => $request->objectif_financier,
            'categorie_id'       => $request->categorie_id,
        ]);

        return redirect()->route('beneficiaire.dashboard')
            ->with('success', 'Campagne mise à jour avec succès');
    }

    public function destroy(Campagne $campagne)
    {
        if ($campagne->beneficiaire_id !== auth()->id()) {
            abort(403);
        }

        $campagne->delete();

        return redirect()->route('beneficiaire.dashboard')
            ->with('success', 'Campagne supprimée avec succès');
    }
}