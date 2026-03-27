<?php

namespace App\Http\Controllers;

use App\Models\Don;
use App\Models\Campagne;
use App\Http\Requests\DonRequest;

class DonController extends Controller
{
    // Effectuer un don
    public function store(DonRequest $request)
    {
        $campagne = Campagne::findOrFail($request->campagne_id);

        // Vérifier que la campagne est active
        if ($campagne->statut !== 'active') {
            return back()->withErrors(['error' => 'Cette campagne n\'est plus active']);
        }

        // Créer le don
        Don::create([
            'montant'     => $request->montant,
            'campagne_id' => $request->campagne_id,
            'donateur_id' => auth()->id(),
        ]);

        // Mettre à jour le montant collecté
        $campagne->increment('montant_collecte', $request->montant);

        // Vérifier si l'objectif est atteint
        if ($campagne->montant_collecte >= $campagne->objectif_financier) {
            $campagne->update(['statut' => 'terminee']);
        }

        return redirect()->route('campagnes.show', $campagne)
            ->with('success', 'Don effectué avec succès, merci pour votre générosité !');
    }

    // Historique des dons du donateur
    public function historique()
    {
        $dons = Don::where('donateur_id', auth()->id())
            ->with('campagne')
            ->latest()
            ->paginate(10);

        return view('donateur.historique', compact('dons'));
    }
}
