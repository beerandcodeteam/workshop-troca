@props([
    'username' => 'Jogador',
    'rank' => null,
])

<header class="sticky top-0 z-30 flex h-14 items-center justify-end gap-4 bg-surface/80 backdrop-blur-md px-6">
    {{-- Notifications --}}
    <button class="relative text-on-surface-variant hover:text-on-surface transition-colors cursor-pointer">
        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
    </button>

    {{-- Player --}}
    <div class="flex items-center gap-3">
        <div class="text-right">
            <p class="text-sm font-semibold text-on-surface">{{ $username }}</p>
            @if($rank)
                <p class="text-xs text-on-surface-variant">Rank: {{ $rank }}</p>
            @endif
        </div>
        <div class="flex size-8 items-center justify-center rounded-full bg-surface-container-high text-xs font-bold text-on-surface">
            {{ strtoupper(mb_substr($username, 0, 1)) }}
        </div>
    </div>
</header>
