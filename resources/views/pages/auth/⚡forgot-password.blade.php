<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts::guest')] #[Title('Esqueci a Senha')] class extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public function sendResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __($status));
            $this->reset('email');
        }

        // For security, don't reveal if email exists or not
        // Always show success-like message
        session()->flash('status', 'Se o e-mail existir em nosso sistema, enviaremos um link de recuperação.');
        $this->reset('email');
    }
};
?>

<div>
    <x-slot:subtitle>Recuperar Acesso</x-slot:subtitle>

    @session('status')
        <div class="mb-4">
            <x-alert variant="success">{{ $value }}</x-alert>
        </div>
    @endsession

    <p class="text-sm text-on-surface-variant mb-6">
        Informe seu e-mail e enviaremos um link para redefinir sua senha.
    </p>

    <form wire:submit="sendResetLink" class="space-y-5">
        <x-input
            label="E-mail"
            name="email"
            type="email"
            placeholder="seu@email.com"
            wire:model="email"
            :error="$errors->first('email')"
        />

        <x-button type="submit" class="w-full justify-center" wire:loading.attr="disabled">
            <span wire:loading.remove>Enviar Link de Recuperação</span>
            <span wire:loading>Enviando...</span>
        </x-button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-primary hover:underline" wire:navigate>Voltar ao login</a>
    </div>
</div>
