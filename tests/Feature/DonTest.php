<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campagne;
use App\Models\Don;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonTest extends TestCase
{
    use RefreshDatabase;

    // ===== Store =====
    public function test_donateur_can_make_don(): void
    {
        $donateur  = User::factory()->create(['role' => 'donateur']);
        $campagne  = Campagne::factory()->create(['statut' => 'approuvee', 'objectif_financier' => 5000]);

        $response = $this->actingAs($donateur)->post('/dons', [
            'montant'     => 100,
            'campagne_id' => $campagne->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('dons', [
            'montant'     => 100,
            'campagne_id' => $campagne->id,
            'donateur_id' => $donateur->id,
        ]);
    }

    public function test_montant_collecte_is_updated_after_don(): void
    {
        $donateur = User::factory()->create(['role' => 'donateur']);
        $campagne = Campagne::factory()->create([
            'statut'           => 'approuvee',
            'objectif_financier' => 5000,
            'montant_collecte' => 0,
        ]);

        $this->actingAs($donateur)->post('/dons', [
            'montant'     => 200,
            'campagne_id' => $campagne->id,
        ]);

        $this->assertDatabaseHas('campagnes', [
            'id'               => $campagne->id,
            'montant_collecte' => 200,
        ]);
    }

    public function test_campagne_becomes_terminee_when_objectif_atteint(): void
    {
        $donateur = User::factory()->create(['role' => 'donateur']);
        $campagne = Campagne::factory()->create([
            'statut'             => 'approuvee',
            'objectif_financier' => 100,
            'montant_collecte'   => 0,
        ]);

        $this->actingAs($donateur)->post('/dons', [
            'montant'     => 100,
            'campagne_id' => $campagne->id,
        ]);

        $this->assertDatabaseHas('campagnes', [
            'id'     => $campagne->id,
            'statut' => 'terminee',
        ]);
    }

    public function test_donateur_cannot_don_to_inactive_campagne(): void
    {
        $donateur = User::factory()->create(['role' => 'donateur']);
        $campagne = Campagne::factory()->create(['statut' => 'en_attente']);

        $response = $this->actingAs($donateur)->post('/dons', [
            'montant'     => 100,
            'campagne_id' => $campagne->id,
        ]);

        $response->assertSessionHasErrors('error');
    }

    public function test_beneficiaire_cannot_make_don(): void
    {
        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $campagne     = Campagne::factory()->create(['statut' => 'approuvee']);

        $response = $this->actingAs($beneficiaire)->post('/dons', [
            'montant'     => 100,
            'campagne_id' => $campagne->id,
        ]);

        $response->assertStatus(403);
    }

    // ===== Historique =====
    public function test_donateur_can_see_historique(): void
    {
        
        $donateur = User::factory()->create(['role' => 'donateur']);
        Don::factory(3)->create(['donateur_id' => $donateur->id]);

        $response = $this->actingAs($donateur)->get('/dons/historique');

        $response->assertStatus(200);
    }
}