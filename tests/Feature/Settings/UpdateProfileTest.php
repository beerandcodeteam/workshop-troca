<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders settings page for authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings')
        ->assertStatus(200);
});

it('allows user to update username', function () {
    $user = User::factory()->create(['username' => 'oldname']);

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('profileForm.username', 'newname')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($user->fresh()->username)->toBe('newname');
});

it('allows user to update email and triggers re-verification', function () {
    Notification::fake();

    $user = User::factory()->create([
        'username' => 'jogador',
        'email' => 'old@test.com',
        'email_verified_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('profileForm.email', 'new@test.com')
        ->call('updateProfile')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->email)->toBe('new@test.com');
    expect($user->email_verified_at)->toBeNull();

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('fails username update with duplicate username', function () {
    User::factory()->create(['username' => 'taken']);
    $user = User::factory()->create(['username' => 'myname']);

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('profileForm.username', 'taken')
        ->call('updateProfile')
        ->assertHasErrors(['profileForm.username']);
});

it('fails email update with duplicate email', function () {
    User::factory()->create(['email' => 'taken@test.com']);
    $user = User::factory()->create(['email' => 'mine@test.com']);

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('profileForm.email', 'taken@test.com')
        ->call('updateProfile')
        ->assertHasErrors(['profileForm.email']);
});

it('fails username validation with too short username', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('profileForm.username', 'ab')
        ->call('updateProfile')
        ->assertHasErrors(['profileForm.username']);
});

it('fails username validation with too long username', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('profileForm.username', str_repeat('a', 21))
        ->call('updateProfile')
        ->assertHasErrors(['profileForm.username']);
});
