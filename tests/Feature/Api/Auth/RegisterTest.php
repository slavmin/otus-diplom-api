<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

beforeEach(function (): void {
    $this->artisan('migrate:fresh');
});

it('can register a new user', function (): void {
    $response = postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response
        ->assertStatus(201)
        ->assertJson([
            'data' => [
                'message' => 'Registered successfully',
            ],
        ]);

    assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);
});

it('requires name, email, password and password confirmation', function (): void {
    $response = postJson('/api/auth/register', []);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('requires password confirmation', function (): void {
    $response = postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

it('requires unique email', function (): void {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = postJson('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});
