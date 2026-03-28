<?php

use App\Livewire\Forms\LoginForm;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::guest')] #[Title('Entrar')] class extends Component
{
    public LoginForm $form;

    public function authenticate(): void
    {
        $this->form->validate();

        if (! auth()->attempt(
            $this->form->only(['email', 'password']),
            $this->form->remember,
        )) {
            $this->addError('form.email', 'As credenciais informadas não correspondem aos nossos registros.');

            return;
        }

        session()->regenerate();

        $this->redirect(route('arena'));
    }
};
?>

<div>
    <x-slot:subtitle>Enter the Kinetic Void</x-slot:subtitle>

    @session('status')
        <div class="mb-4">
            <x-alert variant="success">{{ $value }}</x-alert>
        </div>
    @endsession

    <form wire:submit="authenticate" class="space-y-5">
        <x-input
            label="Identidade Digital"
            name="form.email"
            type="email"
            placeholder="nome@exemplo.com"
            wire:model="form.email"
            :error="$errors->first('form.email')"
        />

        <div>
            <div class="flex justify-between items-center mb-2">
                <label for="form.password" class="block text-xs font-semibold uppercase tracking-widest text-on-surface-variant">
                    Código de Acesso
                </label>
                <a href="{{ route('password.request') }}" class="text-xs text-primary/70 hover:text-primary uppercase tracking-widest transition-colors" wire:navigate>
                    Esqueceu?
                </a>
            </div>
            <x-input
                name="form.password"
                type="password"
                placeholder="••••••••"
                wire:model="form.password"
                :error="$errors->first('form.password')"
            />
        </div>

        <x-checkbox
            label="Lembrar de mim"
            name="form.remember"
            wire:model="form.remember"
        />

        <x-button type="submit" class="w-full justify-center" wire:loading.attr="disabled">
            <span wire:loading.remove>Entrar</span>
            <span wire:loading>Entrando...</span>
        </x-button>
    </form>

    <div class="mt-6 pt-6 border-t border-outline-variant/15 text-center">
        <p class="text-on-surface-variant text-sm">
            Novo na arena?
            <a href="{{ route('register') }}" class="text-primary font-bold hover:underline ml-1" wire:navigate>Criar Nova Conta</a>
        </p>
    </div>
</div>
