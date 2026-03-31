<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\Categorie;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Campagne::query()->with(['categorie', 'beneficiaire']);

        if (!$request->filled('statut')) {
        $query->where('statut', 'approuvee');
    }    
        // Recherche par mot-clé
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('titre', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%')
                  ->orWhereHas('beneficiaire', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->q . '%');
                  })
                  ->orWhereHas('categorie', function ($q) use ($request) {
                      $q->where('libelle', 'like', '%' . $request->q . '%');
                  });
            });
        }

        // Filtre par catégorie
        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre objectif atteint
        if ($request->boolean('objectif_atteint')) {
            $query->whereColumn('montant_collecte', '>=', 'objectif_financier');
        }

        $campagnes  = $query->latest()->paginate(10)->withQueryString();
        $categories = Categorie::all();

        return view('campagnes.index', compact('campagnes', 'categories'));
    }
}