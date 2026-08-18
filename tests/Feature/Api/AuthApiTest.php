<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_and_returns_token(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Novo Usuario',
            'email' => 'novo@projectlens.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'token_type',
                'user' => ['id', 'name', 'email'],
            ])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.name', 'Novo Usuario')
            ->assertJsonPath('user.email', 'novo@projectlens.com');

        $this->assertDatabaseHas('users', [
            'email' => 'novo@projectlens.com',
        ]);
    }

    public function test_register_returns_422_when_fields_missing(): void
    {
        $this->postJson('/api/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_register_returns_422_when_email_already_taken(): void
    {
        User::factory()->create([
            'email' => 'existente@projectlens.com',
        ]);

        $this->postJson('/api/register', [
            'name' => 'Novo Usuario',
            'email' => 'existente@projectlens.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_register_returns_422_for_short_password(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Novo Usuario',
            'email' => 'novo@projectlens.com',
            'password' => 'curta',
            'password_confirmation' => 'curta',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_register_returns_422_when_password_confirmation_does_not_match(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Novo Usuario',
            'email' => 'novo@projectlens.com',
            'password' => 'password123',
            'password_confirmation' => 'outra-senha',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    public function test_token_from_register_grants_access_to_protected_routes(): void
    {
        $register = $this->postJson('/api/register', [
            'name' => 'Novo Usuario',
            'email' => 'novo@projectlens.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $token = $register->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/projects')
            ->assertOk();
    }

    public function test_register_hashes_the_password(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Novo Usuario',
            'email' => 'novo@projectlens.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(201);

        $this->assertNotSame('password123', User::where('email', 'novo@projectlens.com')->value('password'));
    }

    public function test_login_returns_token_for_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@projectlens.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/login', [
            'email' => 'admin@projectlens.com',
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
                'user' => ['id', 'name', 'email'],
            ])
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', 'admin@projectlens.com')
            ->assertJsonPath('user.id', $user->id);
    }

    public function test_login_returns_422_when_fields_missing(): void
    {
        $this->postJson('/api/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_returns_422_when_email_invalid(): void
    {
        $this->postJson('/api/login', [
            'email' => 'nao-e-email',
            'password' => 'password',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_login_returns_422_for_wrong_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@projectlens.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/login', [
            'email' => 'admin@projectlens.com',
            'password' => 'senha-errada',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_login_returns_422_for_unknown_email(): void
    {
        $this->postJson('/api/login', [
            'email' => 'naoexiste@projectlens.com',
            'password' => 'password',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_token_obtained_from_login_grants_access_to_protected_routes(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@projectlens.com',
            'password' => 'password',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'admin@projectlens.com',
            'password' => 'password',
        ]);

        $token = $login->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/projects')
            ->assertOk();
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('api-token')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logout realizado com sucesso.');

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_token_is_invalid_after_logout(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('api-token')->plainTextToken;

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout')
            ->assertOk();

        $this->app->make('auth')->forgetGuards();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/projects')
            ->assertStatus(401);
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/logout')
            ->assertStatus(401);
    }
}
