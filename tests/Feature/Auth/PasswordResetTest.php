<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the forgot password page', function () {
    $this->get('/forgot-password')->assertStatus(200);
});

it('sends reset link for valid email', function () {
    Notification::fake();
    $user = User::factory()->create();

    Livewire::test('pages::auth.forgot-password')
        ->set('email', $user->email)
        ->call('sendResetLink')
        ->assertHasNoErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

it('does not reveal if email exists for security', function () {
    Notification::fake();

    Livewire::test('pages::auth.forgot-password')
        ->set('email', 'nonexistent@test.com')
        ->call('sendResetLink')
        ->assertHasNoErrors();

    Notification::assertNothingSent();
});

it('allows password reset with valid token', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    Livewire::test('pages::auth.reset-password', ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));
});

it('fails password reset with invalid token', function () {
    $user = User::factory()->create();

    Livewire::test('pages::auth.reset-password', ['token' => 'invalid-token'])
        ->set('email', $user->email)
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertHasErrors(['email']);
});

it('fails password reset with expired token', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    DB::table('password_reset_tokens')->where('email', $user->email)->delete();

    Livewire::test('pages::auth.reset-password', ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertHasErrors(['email']);
});

it('redirects to login after successful reset', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    Livewire::test('pages::auth.reset-password', ['token' => $token])
        ->set('email', $user->email)
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('resetPassword')
        ->assertRedirect(route('login'));
});
