<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Categorie;
use App\Http\Requests\CampagneRequest;
use Illuminate\Http\Request;
use App\Notifications\NouvelleCampagneSoumise;
use App\Notifications\CampagneObjectifAtteint;
use App\Models\User;

class CampagneController extends Controller
{
    // Afficher toutes les campagnes actives (visiteurs + donateurs)
    public function index()
    {
        $campagnes = Campagne::where('statut', 'active')
            ->with(['categorie', 'beneficiaire'])
            ->latest()
            ->paginate(10);

        return view('campagnes.index', compact('campagnes'));
    }

    // Afficher les détails d'une campagne
    public function show(Campagne $campagne)
    {
        return view('campagnes.show', compact('campagne'));
    }

    // Formulaire de création (bénéficiaire seulement)
    public function create()
    {
        $categories = Categorie::all();
        return view('campagnes.create', compact('categories'));
    }

    // Enregistrer une nouvelle campagne
    public function store(CampagneRequest $request)
    {
        Campagne::create([
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
    $admin->notify(new NouvelleCampagneSoumise($campagne));

    
        return redirect()->route('beneficiaire.dashboard')
            ->with('success', 'Campagne soumise avec succès, en attente de validation');
    }

    // Formulaire de modification (bénéficiaire seulement)
    public function edit(Campagne $campagne)
    {
        // Vérifier que c'est bien sa campagne
        if ($campagne->beneficiaire_id !== auth()->id()) {
            abort(403);
        }

        // On ne peut modifier que les campagnes en attente
        if ($campagne->statut !== 'en_attente') {
            return redirect()->route('beneficiaire.dashboard')
                ->withErrors(['error' => 'Vous ne pouvez pas modifier une campagne déjà traitée']);
        }

        $categories = Categorie::all();
        return view('campagnes.edit', compact('campagne', 'categories'));
    }

    // Mettre à jour une campagne
    public function update(CampagneRequest $request, Campagne $campagne)
    {
        // Vérifier que c'est bien sa campagne
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

    // Supprimer une campagne
    public function destroy(Campagne $campagne)
    {
        // Vérifier que c'est bien sa campagne
        if ($campagne->beneficiaire_id !== auth()->id()) {
            abort(403);
        }

        $campagne->delete();

        return redirect()->route('beneficiaire.dashboard')
            ->with('success', 'Campagne supprimée avec succès');
    }
}