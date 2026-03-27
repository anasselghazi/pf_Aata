<?php

namespace App\Http\Controllers;

use App\Models\Campagne;

class FavoriController extends Controller
{
    // Ajouter une campagne aux favoris
    public function store(Campagne $campagne)
    {
        $favori = auth()->user()->favoris()->where('campagne_id', $campagne->id)->first();

        // Vérifier si déjà en favori
        if ($favori) {
            return back()->withErrors(['error' => 'Cette campagne est déjà dans vos favoris']);
        }

        auth()->user()->favoris()->attach($campagne->id);

        return back()->with('success', 'Campagne ajoutée aux favoris');
    }

    // Supprimer une campagne des favoris
    public function destroy(Campagne $campagne)
    {
        auth()->user()->favoris()->detach($campagne->id);

        return back()->with('success', 'Campagne retirée des favoris');
    }

    // Lister les favoris du donateur
    public function index()
    {
        $favoris = auth()->user()->favoris()
            ->with(['categorie', 'beneficiaire'])
            ->latest()
            ->paginate(10);

        return view('donateur.favoris', compact('favoris'));
    }
}