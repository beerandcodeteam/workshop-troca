<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-surface antialiased">
        <x-sidebar
            :username="auth()->user()?->username ?? auth()->user()?->name ?? 'Jogador'"
            :rank="auth()->user()?->rank ?? null"
        />

        <div class="pl-56">
            <x-topbar
                :username="auth()->user()?->username ?? auth()->user()?->name ?? 'Jogador'"
                :rank="auth()->user()?->rank ?? null"
            />

            {{-- Flash messages --}}
            <div class="px-6 pt-4">
                @session('success')
                    <div class="mb-4">
                        <x-alert variant="success">{{ $value }}</x-alert>
                    </div>
                @endsession

                @session('error')
                    <div class="mb-4">
                        <x-alert variant="error">{{ $value }}</x-alert>
                    </div>
                @endsession

                @session('warning')
                    <div class="mb-4">
                        <x-alert variant="warning">{{ $value }}</x-alert>
                    </div>
                @endsession
            </div>

            {{-- Main content --}}
            <main class="px-6 pb-8">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
