@props([
    'username' => 'Jogador',
    'rank' => null,
])

<aside class="fixed inset-y-0 left-0 z-40 flex w-56 flex-col bg-surface-container-low">
    {{-- Logo --}}
    <div class="px-5 pt-6 pb-4">
        <a href="/" class="font-display text-xl font-bold tracking-wider text-primary" wire:navigate>TROCA</a>
    </div>

    {{-- Player info --}}
    <div class="px-5 pb-4">
        <div class="flex items-center gap-3">
            <div class="flex size-9 items-center justify-center rounded-full bg-surface-container-high text-sm font-bold text-on-surface">
                {{ strtoupper(mb_substr($username, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-on-surface">{{ $username }}</p>
                @if($rank)
                    <p class="text-xs text-on-surface-variant">Rank: {{ $rank }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-1 px-3 mt-2">
        <x-sidebar-link href="/arena" icon="arena" label="Arena" />
        <x-sidebar-link href="/leaderboard" icon="leaderboard" label="Leaderboard" />
        <x-sidebar-link href="/settings" icon="settings" label="Settings" />
    </nav>

    {{-- Bottom actions --}}
    <div class="px-3 pb-4 space-y-2">
        <x-button variant="success" class="w-full justify-center">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Lançar Dado
        </x-button>

        <x-sidebar-link href="/support" icon="support" label="Support" />

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors duration-200 cursor-pointer">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>
