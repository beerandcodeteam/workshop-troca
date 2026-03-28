<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows authenticated user to log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect(route('login'));

    expect(auth()->check())->toBeFalse();
});

it('destroys session after logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);
    expect(auth()->check())->toBeTrue();

    $this->post('/logout');

    expect(auth()->check())->toBeFalse();
});

it('redirects to login page after logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect(route('login'));
});

it('prevents guest from accessing logout route', function () {
    $this->post('/logout')
        ->assertRedirect(route('login'));
});
