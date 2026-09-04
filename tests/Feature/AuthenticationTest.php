<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_seeded_admin_can_login(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'admin@turingdesenvolvimento.com',
            'password' => 'admin@123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_guest_cannot_access_user_creation(): void
    {
        $this->get('/usuarios/novo')->assertRedirect('/');
        $this->post('/usuarios', [])->assertRedirect('/');
    }

    public function test_authenticated_user_can_create_user(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post('/usuarios', [
            'name' => 'Novo Usuario',
            'email' => 'novo@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/usuarios/novo');
        $this->assertDatabaseHas('users', [
            'email' => 'novo@example.com',
            'active' => true,
        ]);
    }

    public function test_authenticated_user_can_update_user(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create([
            'name' => 'Nome Antigo',
            'email' => 'antigo@example.com',
        ]);

        $response = $this->actingAs($admin)->put("/usuarios/{$user->id}", [
            'name' => 'Nome Novo',
            'email' => 'novo-email@example.com',
            'active' => true,
        ]);

        $response->assertRedirect('/usuarios');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Novo',
            'email' => 'novo-email@example.com',
            'active' => true,
        ]);
    }

    public function test_authenticated_user_can_deactivate_another_user(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create(['active' => true]);

        $response = $this->actingAs($admin)->delete("/usuarios/{$user->id}");

        $response->assertRedirect('/usuarios');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'active' => false,
        ]);
    }

    public function test_authenticated_user_cannot_deactivate_itself(): void
    {
        $user = User::factory()->create(['active' => true]);

        $response = $this->actingAs($user)->delete("/usuarios/{$user->id}");

        $response->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'active' => true,
        ]);
    }

    public function test_authenticated_user_can_change_own_password(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
        ]);

        $response = $this->actingAs($user)->put('/minha-senha', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect('/minha-senha');
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'inativo@example.com',
            'password' => 'password123',
            'active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inativo@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }
}
