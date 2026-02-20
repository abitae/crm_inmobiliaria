@php
    $title = $title ?? config('app.name');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    @include('partials.head')
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="theme-color" content="#ffffff" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <title>{{ $title }}</title>
    <style>
        /* Safe area para muescas y barra de estado en móviles */
        .safe-top { padding-top: env(safe-area-inset-top, 0); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom, 0); }
        .safe-left { padding-left: env(safe-area-inset-left, 0); }
        .safe-right { padding-right: env(safe-area-inset-right, 0); }
        /* Evitar zoom en inputs en iOS */
        @media screen and (max-width: 768px) {
            input, select, textarea { font-size: 16px !important; }
        }
    </style>
</head>
<body class="min-h-screen min-h-[100dvh] bg-gray-50 text-gray-900 antialiased flex flex-col">
    {{-- Contenido principal: scrollable, con safe areas --}}
    <main class="flex-1 w-full overflow-y-auto overflow-x-hidden">
        <div class="mx-auto w-full max-w-lg px-4 py-6 safe-left safe-right safe-bottom">
            {{ $slot }}
        </div>
    </main>

    <x-mary-toast position="toast-top toast-center" />
    @fluxScripts
</body>
</html>
