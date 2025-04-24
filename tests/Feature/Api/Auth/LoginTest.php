<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\postJson;

beforeEach(function (): void {
    $this->artisan('migrate:fresh');
});

it('can login with valid credentials', function (): void {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $response = postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
});

it('fails with invalid credentials', function (): void {
    $user = User::factory()->create();

    $response = postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422);
});

it('requires email and password', function (): void {
    $response = postJson('/api/auth/login', []);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});
