<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campagne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriTest extends TestCase
{
    use RefreshDatabase;

    // ===== Store =====
    public function test_donateur_can_add_campagne_to_favoris(): void
    {
        $donateur = User::factory()->create(['role' => 'donateur']);
        $campagne = Campagne::factory()->create(['statut' => 'approuvee']);

        $response = $this->actingAs($donateur)->post("/favoris/{$campagne->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('favoris', [
            'donateur_id' => $donateur->id,
            'campagne_id' => $campagne->id,
        ]);
    }

    public function test_donateur_cannot_add_same_campagne_twice(): void
    {
        $donateur = User::factory()->create(['role' => 'donateur']);
        $campagne = Campagne::factory()->create(['statut' => 'approuvee']);

        $this->actingAs($donateur)->post("/favoris/{$campagne->id}");
        $response = $this->actingAs($donateur)->post("/favoris/{$campagne->id}");

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseCount('favoris', 1);
    }

    // ===== Destroy =====
    public function test_donateur_can_remove_campagne_from_favoris(): void
    {
        $donateur = User::factory()->create(['role' => 'donateur']);
        $campagne = Campagne::factory()->create(['statut' => 'approuvee']);

        $donateur->favoris()->attach($campagne->id);

        $response = $this->actingAs($donateur)->delete("/favoris/{$campagne->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('favoris', [
            'donateur_id' => $donateur->id,
            'campagne_id' => $campagne->id,
        ]);
    }

    // ===== Index =====
    public function test_donateur_can_see_favoris(): void
    {
        $donateur  = User::factory()->create(['role' => 'donateur']);
        $campagnes = Campagne::factory(3)->create(['statut' => 'approuvee']);

        $donateur->favoris()->attach($campagnes->pluck('id'));

        $response = $this->actingAs($donateur)->get('/favoris');

        $response->assertStatus(200);
        $response->assertViewHas('favoris');
    }

    // ===== Permissions =====
    public function test_beneficiaire_cannot_add_to_favoris(): void
    {
        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $campagne     = Campagne::factory()->create(['statut' => 'approuvee']);

        $response = $this->actingAs($beneficiaire)->post("/favoris/{$campagne->id}");

        $response->assertStatus(403);
    }
}