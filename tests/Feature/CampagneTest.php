<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campagne;
use App\Models\Categorie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampagneTest extends TestCase
{
    use RefreshDatabase;

    // ===== Index =====
    public function test_visitor_can_see_active_campagnes(): void
    {
        // FIX 1: On remplace 'active' par 'approuvee' (ou 'en_cours' selon ta migration)
        Campagne::factory(3)->create(['statut' => 'approuvee']); 
        Campagne::factory(2)->create(['statut' => 'en_attente']);

        $response = $this->get('/campagnes');

        $response->assertStatus(200);
        $response->assertViewHas('campagnes');
    }

    // ===== Create =====
    public function test_beneficiaire_can_create_campagne(): void
    {
        // FIX 2: On crée un admin pour recevoir la notification "NouvelleCampagneSoumise"
        User::factory()->create(['role' => 'admin']);

        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $categorie    = Categorie::factory()->create();

        $response = $this->actingAs($beneficiaire)->post('/campagnes', [
            'titre'              => 'Ma campagne test',
            'description'        => 'Description de la campagne',
            'objectif_financier' => 5000,
            'categorie_id'       => $categorie->id,
        ]);

        $response->assertRedirect('/beneficiaire/dashboard');
        $this->assertDatabaseHas('campagnes', [
            'titre'           => 'Ma campagne test',
            'statut'          => 'en_attente',
            'beneficiaire_id' => $beneficiaire->id,
        ]);
    }

    public function test_donateur_cannot_create_campagne(): void
    {
        $donateur  = User::factory()->create(['role' => 'donateur']);
        $categorie = Categorie::factory()->create();

        $response = $this->actingAs($donateur)->post('/campagnes', [
            'titre'              => 'Ma campagne test',
            'description'        => 'Description de la campagne',
            'objectif_financier' => 5000,
            'categorie_id'       => $categorie->id,
        ]);

        $response->assertStatus(403);
    }

    // ===== Edit =====
    public function test_beneficiaire_can_edit_own_campagne(): void
    {
        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $categorie    = Categorie::factory()->create();
        $campagne     = Campagne::factory()->create([
            'beneficiaire_id' => $beneficiaire->id,
            'statut'          => 'en_attente',
        ]);

        $response = $this->actingAs($beneficiaire)->put("/campagnes/{$campagne->id}", [
            'titre'              => 'Titre modifié',
            'description'        => 'Description modifiée',
            'objectif_financier' => 8000,
            'categorie_id'       => $categorie->id,
        ]);

        $response->assertRedirect('/beneficiaire/dashboard');
        $this->assertDatabaseHas('campagnes', ['titre' => 'Titre modifié']);
    }

    public function test_beneficiaire_cannot_edit_other_campagne(): void
    {
        $beneficiaire1 = User::factory()->create(['role' => 'beneficiaire']);
        $beneficiaire2 = User::factory()->create(['role' => 'beneficiaire']);
        $categorie     = Categorie::factory()->create();
        $campagne      = Campagne::factory()->create([
            'beneficiaire_id' => $beneficiaire1->id,
            'statut'          => 'en_attente',
        ]);

        $response = $this->actingAs($beneficiaire2)->put("/campagnes/{$campagne->id}", [
            'titre'              => 'Titre modifié',
            'description'        => 'Description modifiée',
            'objectif_financier' => 8000,
            'categorie_id'       => $categorie->id,
        ]);

        $response->assertStatus(403);
    }

    // ===== Delete =====
    public function test_beneficiaire_can_delete_own_campagne(): void
    {
        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $campagne     = Campagne::factory()->create([
            'beneficiaire_id' => $beneficiaire->id,
        ]);

        $response = $this->actingAs($beneficiaire)->delete("/campagnes/{$campagne->id}");

        $response->assertRedirect('/beneficiaire/dashboard');
        $this->assertDatabaseMissing('campagnes', ['id' => $campagne->id]);
    }
}