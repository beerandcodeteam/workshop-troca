<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the login page', function () {
    $this->get('/login')->assertStatus(200);
});

it('allows user to log in with valid credentials', function () {
    $user = User::factory()->create(['password' => 'password123']);

    Livewire::test('pages::auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password123')
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(route('arena'));

    expect(auth()->check())->toBeTrue();
    expect(auth()->id())->toBe($user->id);
});

it('fails login with wrong password', function () {
    $user = User::factory()->create(['password' => 'password123']);

    Livewire::test('pages::auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'wrongpassword')
        ->call('authenticate')
        ->assertHasErrors(['form.email']);

    expect(auth()->check())->toBeFalse();
});

it('fails login with non-existent email', function () {
    Livewire::test('pages::auth.login')
        ->set('form.email', 'nonexistent@test.com')
        ->set('form.password', 'password123')
        ->call('authenticate')
        ->assertHasErrors(['form.email']);

    expect(auth()->check())->toBeFalse();
});

it('sets persistent session with remember me', function () {
    $user = User::factory()->create(['password' => 'password123']);

    Livewire::test('pages::auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password123')
        ->set('form.remember', true)
        ->call('authenticate')
        ->assertHasNoErrors()
        ->assertRedirect(route('arena'));

    expect(auth()->check())->toBeTrue();
    expect($user->fresh()->remember_token)->not->toBeNull();
});

it('redirects authenticated user to arena', function () {
    $user = User::factory()->create(['password' => 'password123']);

    Livewire::test('pages::auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('arena'));
});

it('redirects already authenticated user from login to arena', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect(route('arena'));
});
