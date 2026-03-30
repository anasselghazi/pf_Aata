<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ===== Register =====
    public function test_user_can_register_as_donateur(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'donateur',
        ]);

        $response->assertRedirect('/donateur/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'john@example.com', 'role' => 'donateur']);
    }

    public function test_user_can_register_as_beneficiaire(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'beneficiaire',
        ]);

        $response->assertRedirect('/beneficiaire/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com', 'role' => 'beneficiaire']);
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->post('/register', [
            'name'                  => 'John Doe',
            'email'                 => 'john@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'donateur',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ===== Login =====
    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email'    => 'john@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'donateur',
        ]);

        $response = $this->post('/login', [
            'email'    => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/donateur/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email'    => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_suspended_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role'          => 'donateur',
            'est_suspendu'  => true,
        ]);

        $response = $this->actingAs($user)->get('/donateur/dashboard');

        $response->assertRedirect('/login');
    }

    // ===== Logout =====
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}