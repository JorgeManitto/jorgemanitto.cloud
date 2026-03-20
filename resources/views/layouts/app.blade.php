{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M.O.N.K.Y OS - @yield('title', 'Dashboard')</title>

    {{-- Tailwind (en producción usar @vite) --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Design-system tokens + utilidades --}}
    <link rel="stylesheet" href="{{ asset('css/monky.css') }}">

    @stack('styles')
</head>
<body>

    {{-- Mobile Header --}}
    {{-- <x-monky.mobile-header /> --}}

    {{-- Main Grid --}}
    <div class="w-full grid grid-cols-1 my-2 lg:grid-cols-12 gap-8 lg:px-4">

        {{-- Sidebar (desktop) --}}
        <div class=" col-span-2 top-0 relative">
            <x-monky.sidebar />
        </div>

        {{-- Main Content --}}
        <div class="{{  request()->routeIs('overview') ? 'col-span-7' : 'col-span-10' }}">
            @yield('content')
        </div>

        {{-- Right Sidebar (desktop) --}}
        @if (request()->routeIs('overview'))
            <div class="desktop-only col-span-3">
                <x-monky.right-sidebar />
            </div>
        @endif

    </div>

    @stack('scripts')
</body>
</html>
