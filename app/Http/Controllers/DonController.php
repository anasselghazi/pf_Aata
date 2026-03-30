<?php

namespace App\Http\Controllers;

use App\Models\Don;
use App\Models\Campagne;
use App\Http\Requests\DonRequest;
use App\Notifications\NouveauDonNotification;
use App\Notifications\CampagneObjectifAtteintNotification;

class DonController extends Controller
{
    public function store(DonRequest $request)
    {
        $campagne = Campagne::findOrFail($request->campagne_id);

        if ($campagne->statut !== 'approuvee') {
            return back()->withErrors(['error' => 'Cette campagne n\'est plus active']);
        }

        $don = Don::create([
            'montant'     => $request->montant,
            'campagne_id' => $request->campagne_id,
            'donateur_id' => auth()->id(),
        ]);

        $campagne->increment('montant_collecte', $request->montant);
        $campagne->refresh();

        // Notifier le donateur
        auth()->user()->notify(new NouveauDonNotification($don));

        // Vérifier si l'objectif est atteint
        if ($campagne->fresh()->montant_collecte >= $campagne->objectif_financier) {
            $campagne->update(['statut' => 'terminee']);

            // Notifier le bénéficiaire
            $campagne->beneficiaire->notify(new CampagneObjectifAtteintNotification($campagne));

            // Notifier les donateurs qui ont cette campagne en favori
            $campagne->favoris()->each(function ($donateur) use ($campagne) {
                $donateur->notify(new CampagneObjectifAtteintNotification($campagne));
            });
        }

        return redirect()->route('campagnes.show', $campagne)
            ->with('success', 'Don effectué avec succès, merci pour votre générosité !');
    }

    public function historique()
    {
        $dons = Don::where('donateur_id', auth()->id())
            ->with('campagne')
            ->latest()
            ->paginate(10);

        return view('donateur.historique', compact('dons'));
    }
}