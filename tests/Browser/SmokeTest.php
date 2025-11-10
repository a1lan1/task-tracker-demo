<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

test('smoke', function (): void {
    $routes = [
        '/',
        '/login',
        '/register',
        '/dashboard',
        '/projects/{id}',
    ];

    $owner = User::factory()->create();

    actingAs($owner);

    visit($routes)->assertNoSmoke();
});
