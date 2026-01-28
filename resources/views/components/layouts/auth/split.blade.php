<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div
            class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
            <div class="absolute inset-0 bg-neutral-900"></div>

            @php
                [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
            @endphp

            <div class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                    <footer>
                        <flux:heading>{{ trim($author) }}</flux:heading>
                    </footer>
                </blockquote>
            </div>
        </div>
        <div class="w-full min-h-dvh flex items-center justify-center lg:p-8">
            <div class="mx-auto flex w-full flex-col items-center justify-center space-y-10 sm:w-[350px]">
                <a href="{{ route('home') }}" class="z-20 flex items-center justify-center font-medium group"
                    wire:navigate>
                    <span
                        class="flex items-center justify-center pb-5 bg-white dark:bg-neutral-900 transition-transform duration-200 group-hover:scale-105 group-hover:shadow-xl">
                        <x-app-logo-icon class="size-16 md:size-16" />
                    </span>
                </a>
                {{ $slot }}
            </div>
        </div>
    </div>
    @fluxScripts
</body>

</html>
