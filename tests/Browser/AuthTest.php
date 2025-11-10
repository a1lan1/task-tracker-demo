<?php

use App\Models\User;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;

it('allows a user to register', function (): void {
    $page = visit('/register');

    $page->fill('name', 'Test User')
        ->fill('email', 'test@example.com')
        ->fill('password', 'password')
        ->fill('password_confirmation', 'password')
        ->press('Create account')
        ->assertPathIs('/dashboard');

    assertAuthenticated();
});

it('allows a user to log in', function (): void {
    $user = User::factory()
        ->withoutTwoFactor()
        ->create(['email' => 'test@example.com']);

    $page = visit('/login');

    $page->fill('email', $user->email)
        ->fill('password', 'password')
        ->press('Log in')
        ->assertPathIs('/dashboard');

    assertAuthenticated();
});

it('shows an error on failed login', function (): void {
    User::factory()
        ->withoutTwoFactor()
        ->create(['email' => 'test@example.com']);

    visit('/login')
        ->fill('email', 'test@example.com')
        ->fill('password', 'wrong-password')
        ->press('Log in')
        ->assertPathIs('/login')
        ->assertSee('These credentials do not match our records.');

    assertGuest();
});
