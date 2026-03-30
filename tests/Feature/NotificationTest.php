<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campagne;
use App\Notifications\CampagneApprouvee;
use App\Notifications\CampagneRejetee;
use App\Notifications\CampagneObjectifAtteint;
use App\Notifications\NouveauDon;
use App\Notifications\NouvelleCampagneSoumise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    // ===== Soumission campagne =====
    public function test_admin_receives_notification_when_campagne_submitted(): void
    {
        Notification::fake();

        $admin        = User::factory()->create(['role' => 'admin']);
        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $categorie    = \App\Models\Categorie::factory()->create();

        $this->actingAs($beneficiaire)->post('/campagnes', [
            'titre'              => 'Campagne test',
            'description'        => 'Description test',
            'objectif_financier' => 5000,
            'categorie_id'       => $categorie->id,
        ]);

        Notification::assertSentTo($admin, NouvelleCampagneSoumise::class);
    }

    // ===== Approbation campagne =====
    public function test_beneficiaire_receives_notification_when_campagne_approved(): void
    {
        Notification::fake();

        $admin        = User::factory()->create(['role' => 'admin']);
        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $campagne     = Campagne::factory()->create([
            'beneficiaire_id' => $beneficiaire->id,
            'statut'          => 'en_attente',
        ]);

        $this->actingAs($admin)->post("/admin/campagnes/{$campagne->id}/approuver");

        Notification::assertSentTo($beneficiaire, CampagneApprouvee::class);
    }

    // ===== Rejet campagne =====
    public function test_beneficiaire_receives_notification_when_campagne_rejected(): void
    {
        Notification::fake();

        $admin        = User::factory()->create(['role' => 'admin']);
        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $campagne     = Campagne::factory()->create([
            'beneficiaire_id' => $beneficiaire->id,
            'statut'          => 'en_attente',
        ]);

        $this->actingAs($admin)->post("/admin/campagnes/{$campagne->id}/rejeter");

        Notification::assertSentTo($beneficiaire, CampagneRejetee::class);
    }

    // ===== Don effectué =====
    public function test_donateur_receives_notification_after_don(): void
    {
        Notification::fake();

        $donateur = User::factory()->create(['role' => 'donateur']);
        $campagne = Campagne::factory()->create([
            'statut'             => 'active',
            'objectif_financier' => 5000,
        ]);

        $this->actingAs($donateur)->post('/dons', [
            'montant'     => 100,
            'campagne_id' => $campagne->id,
        ]);

        Notification::assertSentTo($donateur, NouveauDon::class);
    }

    // ===== Objectif atteint =====
    public function test_beneficiaire_receives_notification_when_objectif_atteint(): void
    {
        Notification::fake();

        $donateur     = User::factory()->create(['role' => 'donateur']);
        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $campagne     = Campagne::factory()->create([
            'beneficiaire_id'    => $beneficiaire->id,
            'statut'             => 'active',
            'objectif_financier' => 100,
            'montant_collecte'   => 0,
        ]);

        $this->actingAs($donateur)->post('/dons', [
            'montant'     => 100,
            'campagne_id' => $campagne->id,
        ]);

        Notification::assertSentTo($beneficiaire, CampagneObjectifAtteint::class);
    }
}