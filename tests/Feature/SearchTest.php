<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campagne;
use App\Models\Categorie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    // ===== Recherche par mot-clé =====
    public function test_can_search_by_titre(): void
    {
        Campagne::factory()->create(['titre' => 'Aide pour éducation', 'statut' => 'approuvee']);
        Campagne::factory()->create(['titre' => 'Soutien médical', 'statut' => 'approuvee']);

        $response = $this->get('/search?q=éducation');

        $response->assertStatus(200);
        $response->assertViewHas('campagnes', function ($campagnes) {
            return $campagnes->count() === 1;
        });
    }

    public function test_can_search_by_description(): void
    {
        Campagne::factory()->create([
            'description' => 'Besoin urgent pour opération chirurgicale',
            'statut'      => 'approuvee',
        ]);
        Campagne::factory()->create([
            'description' => 'Aide pour les fournitures scolaires',
            'statut'      => 'approuvee',
        ]);

        $response = $this->get('/search?q=chirurgicale');

        $response->assertStatus(200);
        $response->assertViewHas('campagnes', function ($campagnes) {
            return $campagnes->count() === 1;
        });
    }

    public function test_can_search_by_beneficiaire_name(): void
    {
        $beneficiaire = User::factory()->create([
            'role' => 'beneficiaire',
            'name' => 'Mohammed Alaoui',
        ]);

        Campagne::factory()->create([
            'beneficiaire_id' => $beneficiaire->id,
            'statut'          => 'approuvee',
        ]);

        $response = $this->get('/search?q=Mohammed');

        $response->assertStatus(200);
        $response->assertViewHas('campagnes', function ($campagnes) {
            return $campagnes->count() === 1;
        });
    }

    // ===== Filtre par catégorie =====
    public function test_can_filter_by_categorie(): void
    {
        $categorie1 = Categorie::factory()->create(['libelle' => 'Santé']);
        $categorie2 = Categorie::factory()->create(['libelle' => 'Éducation']);

        Campagne::factory()->create(['categorie_id' => $categorie1->id, 'statut' => 'approuvee']);
        Campagne::factory()->create(['categorie_id' => $categorie1->id, 'statut' => 'approuvee']);
        Campagne::factory()->create(['categorie_id' => $categorie2->id, 'statut' => 'approuvee']);

        $response = $this->get("/search?categorie_id={$categorie1->id}");

        $response->assertStatus(200);
        $response->assertViewHas('campagnes', function ($campagnes) {
            return $campagnes->count() === 2;
        });
    }

    // ===== Filtre par statut =====
    public function test_can_filter_by_statut(): void
    {
        Campagne::factory()->create(['statut' => 'approuvee']);
        Campagne::factory()->create(['statut' => 'approuvee']);
        Campagne::factory()->create(['statut' => 'terminee']);

        $response = $this->get('/search?statut=terminee');

        $response->assertStatus(200);
        $response->assertViewHas('campagnes', function ($campagnes) {
            return $campagnes->count() === 1;
        });
    }

    // ===== Filtre objectif atteint =====
    public function test_can_filter_by_objectif_atteint(): void
    {
        Campagne::factory()->create([
            'statut'             => 'approuvee',
            'objectif_financier' => 1000,
            'montant_collecte'   => 1000,
        ]);
        Campagne::factory()->create([
            'statut'             => 'approuvee',
            'objectif_financier' => 1000,
            'montant_collecte'   => 500,
        ]);

        $response = $this->get('/search?objectif_atteint=1');

        $response->assertStatus(200);
        $response->assertViewHas('campagnes', function ($campagnes) {
            return $campagnes->count() === 1;
        });
    }

    // ===== Sans filtre =====
    public function test_returns_all_approved_campagnes_without_filters(): void
    {
        Campagne::factory(3)->create(['statut' => 'approuvee']);
        Campagne::factory(2)->create(['statut' => 'en_attente']);

        $response = $this->get('/search');

        $response->assertStatus(200);
        $response->assertViewHas('campagnes', function ($campagnes) {
            return $campagnes->count() === 3;
        });
    }
}