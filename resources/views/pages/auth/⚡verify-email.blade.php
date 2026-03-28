<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::guest')] #[Title('Verificar E-mail')] class extends Component
{
    public function resend(): void
    {
        if (auth()->user()->hasVerifiedEmail()) {
            $this->redirect(route('arena'));

            return;
        }

        auth()->user()->sendEmailVerificationNotification();

        session()->flash('success', 'Link de verificação reenviado!');
    }
};
?>

<div>
    <x-slot:subtitle>Verificação de E-mail</x-slot:subtitle>

    @session('success')
        <div class="mb-4">
            <x-alert variant="success">{{ $value }}</x-alert>
        </div>
    @endsession

    <div class="text-center space-y-4">
        <div class="flex justify-center mb-4">
            <svg class="size-16 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
            </svg>
        </div>

        <p class="text-sm text-on-surface-variant">
            Enviamos um link de verificação para o seu e-mail. Clique no link para ativar sua conta.
        </p>

        <p class="text-xs text-on-surface-variant">
            Não recebeu o e-mail?
        </p>

        <x-button wire:click="resend" class="w-full justify-center" wire:loading.attr="disabled">
            <span wire:loading.remove>Reenviar E-mail de Verificação</span>
            <span wire:loading>Enviando...</span>
        </x-button>
    </div>
</div>
