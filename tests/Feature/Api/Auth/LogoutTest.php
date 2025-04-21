<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\postJson;

beforeEach(function (): void {
    $this->artisan('migrate:fresh');
});

it('can logout authenticated user and delete token', function (): void {
    // Создаем пользователя
    $user = User::factory()->create();

    // Создаем токен и аутентифицируем пользователя через Sanctum
    $token = $user->createToken('test-token');

    // Отправляем запрос на выход с токеном
    $response = postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer '.$token->plainTextToken,
    ]);

    // Проверяем ответ
    $response
        ->assertOk()
        ->assertJson([
            'message' => 'Successfully logged out',
        ]);

    // Проверяем, что токен был удален
    assertDatabaseCount('personal_access_tokens', 0);
});

it('fails when not authenticated', function (): void {
    $response = postJson('/api/auth/logout');
    $response->assertUnauthorized();
});
