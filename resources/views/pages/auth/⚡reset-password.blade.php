<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::guest')] #[Title('Redefinir Senha')] class extends Component
{
    #[Locked]
    public string $token = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', 'Senha redefinida com sucesso!');
            $this->redirect(route('login'));

            return;
        }

        $this->addError('email', __($status));
    }
};
?>

<div>
    <x-slot:subtitle>Nova Senha de Acesso</x-slot:subtitle>

    <form wire:submit="resetPassword" class="space-y-5">
        <x-input
            label="E-mail"
            name="email"
            type="email"
            placeholder="seu@email.com"
            wire:model="email"
            :error="$errors->first('email')"
        />

        <x-input
            label="Nova Senha"
            name="password"
            type="password"
            placeholder="••••••••"
            wire:model="password"
            :error="$errors->first('password')"
        />

        <x-input
            label="Confirmar Nova Senha"
            name="password_confirmation"
            type="password"
            placeholder="••••••••"
            wire:model="password_confirmation"
        />

        <x-button type="submit" class="w-full justify-center" wire:loading.attr="disabled">
            <span wire:loading.remove>Redefinir Senha</span>
            <span wire:loading>Redefinindo...</span>
        </x-button>
    </form>
</div>
