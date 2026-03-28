<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('allows user to update password with correct current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword'),
    ]);

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('passwordForm.current_password', 'oldpassword')
        ->set('passwordForm.password', 'newpassword123')
        ->set('passwordForm.password_confirmation', 'newpassword123')
        ->call('updatePassword')
        ->assertHasNoErrors();
});

it('fails password update with wrong current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('correctpassword'),
    ]);

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('passwordForm.current_password', 'wrongpassword')
        ->set('passwordForm.password', 'newpassword123')
        ->set('passwordForm.password_confirmation', 'newpassword123')
        ->call('updatePassword')
        ->assertHasErrors(['passwordForm.current_password']);
});

it('fails password update with short new password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword'),
    ]);

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('passwordForm.current_password', 'oldpassword')
        ->set('passwordForm.password', 'short')
        ->set('passwordForm.password_confirmation', 'short')
        ->call('updatePassword')
        ->assertHasErrors(['passwordForm.password']);
});

it('fails password update with mismatched confirmation', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword'),
    ]);

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('passwordForm.current_password', 'oldpassword')
        ->set('passwordForm.password', 'newpassword123')
        ->set('passwordForm.password_confirmation', 'different123')
        ->call('updatePassword')
        ->assertHasErrors(['passwordForm.password']);
});

it('password is actually changed and user can log in with new password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('oldpassword'),
    ]);

    Livewire::actingAs($user)
        ->test('pages::settings.index')
        ->set('passwordForm.current_password', 'oldpassword')
        ->set('passwordForm.password', 'newpassword123')
        ->set('passwordForm.password_confirmation', 'newpassword123')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
});
