<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Campagne;
use App\Notifications\CampagneApprouveeNotification;
use App\Notifications\CampagneRejeteeNotification;
use App\Notifications\NouvelleCampagneSoumiseNotification;
use App\Notifications\CampagneObjectifAtteintNotification;
use App\Notifications\NouveauDonNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Models\Categorie;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    // ===== Soumission campagne =====
    public function test_admin_receives_notification_when_campagne_submitted(): void
    {
        Notification::fake();

        $admin        = User::factory()->create(['role' => 'admin']);
        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $categorie    = Categorie::factory()->create();

        $this->actingAs($beneficiaire)->post('/campagnes', [
            'titre'              => 'Campagne test',
            'description'        => 'Description test',
            'objectif_financier' => 5000,
            'categorie_id'       => $categorie->id,
        ]);

        Notification::assertSentTo($admin, NouvelleCampagneSoumiseNotification::class);    }

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

        $this->actingAs($admin)->post("/admin/campagnes/{$campagne->slug}/approuver");

        Notification::assertSentTo($beneficiaire, CampagneApprouveeNotification::class);

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

        $this->actingAs($admin)->post("/admin/campagnes/{$campagne->slug}/rejeter");

        Notification::assertSentTo($beneficiaire, CampagneRejeteeNotification::class);

    }

    // ===== Don effectué =====
    public function test_donateur_receives_notification_after_don(): void
    {
        Notification::fake();

        $donateur = User::factory()->create(['role' => 'donateur']);
        $campagne = Campagne::factory()->create([
            'statut'             => 'approuvee',
            'objectif_financier' => 5000,
        ]);

        $this->actingAs($donateur)->post('/dons', [
            'montant'     => 100,
            'campagne_id' => $campagne->id,
        ]);

        Notification::assertSentTo($donateur, NouveauDonNotification::class);
    }

    // ===== Objectif atteint =====
    public function test_beneficiaire_receives_notification_when_objectif_atteint(): void
    {
        Notification::fake();

        $donateur     = User::factory()->create(['role' => 'donateur']);
        $beneficiaire = User::factory()->create(['role' => 'beneficiaire']);
        $campagne     = Campagne::factory()->create([
            'beneficiaire_id'    => $beneficiaire->id,
            'statut'             => 'approuvee',
            'objectif_financier' => 100,
            'montant_collecte'   => 0,
        ]);

        $this->actingAs($donateur)->post('/dons', [
            'montant'     => 100,
            'campagne_id' => $campagne->id,
        ]);

        Notification::assertSentTo($beneficiaire, CampagneObjectifAtteintNotification::class);
    }
}