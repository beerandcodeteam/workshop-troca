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
    <body class="min-h-screen bg-surface flex items-center justify-center p-4 antialiased">
        {{-- Decorative background glows --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 -left-32 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 -right-32 w-96 h-96 bg-secondary/5 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">
            {{-- Logo --}}
            <div class="text-center mb-8">
                <h1 class="font-display text-4xl font-bold tracking-wider text-on-surface">TROCA</h1>
                @if(isset($subtitle))
                    <p class="mt-2 text-sm text-on-surface-variant">{{ $subtitle }}</p>
                @endif
            </div>

            {{-- Flash messages --}}
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

            {{-- Content card --}}
            <div class="bg-surface-container rounded-xl p-6 sm:p-8">
                {{ $slot }}
            </div>
        </div>

        @livewireScripts
    </body>
</html>
