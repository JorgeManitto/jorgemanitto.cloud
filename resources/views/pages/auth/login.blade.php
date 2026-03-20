{{-- resources/views/pages/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — M.O.N.K.Y.</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
     <link rel="stylesheet" href="{{ asset('css/monky.css') }}">
</head>
<body class="bg-background text-foreground min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md space-y-8">

        {{-- Brand --}}
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-lg bg-primary text-primary-foreground mb-4">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                    <line x1="9" y1="9" x2="9.01" y2="9"/>
                    <line x1="15" y1="9" x2="15.01" y2="9"/>
                </svg>
            </div>
            <h1 class="text-4xl font-display">M.O.N.K.Y.</h1>
            <p class="text-xs uppercase text-muted-foreground mt-1 tracking-widest">The OS for Rebels</p>
        </div>

        {{-- Login card --}}
        <div class="border-2 border-border bg-background">

            {{-- Header --}}
            <div class="flex items-center gap-2.5 p-4" style="border-bottom: 2px solid var(--border);">
                <span class="bullet"></span>
                <span class="font-medium text-sm uppercase">Iniciar Sesión</span>
                <div class="ml-auto flex items-center gap-1.5">
                    <span class="inline-block w-2.5 h-2.5 rounded-full" style="background-color: var(--destructive);"></span>
                    <span class="inline-block w-2.5 h-2.5 rounded-full" style="background-color: var(--warning);"></span>
                    <span class="inline-block w-2.5 h-2.5 rounded-full" style="background-color: var(--success);"></span>
                </div>
            </div>

            {{-- Form --}}
            <div class="p-6">

                {{-- Error global --}}
                @if($errors->any())
                    <div class="p-3 rounded border mb-6" style="border-color: var(--destructive); background-color: color-mix(in srgb, var(--destructive) 8%, transparent);">
                        <div class="flex items-center gap-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--destructive)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="15" y1="9" x2="9" y2="15"/>
                                <line x1="9" y1="9" x2="15" y2="15"/>
                            </svg>
                            <p class="text-sm text-destructive">{{ $errors->first() }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="flex items-center gap-2 text-xs font-medium text-muted-foreground uppercase mb-2">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            class="w-full px-4 py-3 bg-accent border-2 rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary transition-colors @error('email') border-destructive @else border-border @enderror"
                            placeholder="tu@email.com"
                        >
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="flex items-center gap-2 text-xs font-medium text-muted-foreground uppercase mb-2">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            Contraseña
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                autocomplete="current-password"
                                class="w-full px-4 py-3 pr-12 bg-accent border-2 border-border rounded text-sm placeholder:text-muted-foreground focus:outline-none focus:border-primary transition-colors"
                                placeholder="••••••••"
                            >
                            <button
                                type="button"
                                id="toggle-password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                                aria-label="Mostrar contraseña"
                            >
                                <svg id="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                    <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                                </svg>
                                <svg id="eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember me --}}
                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="w-4 h-4 rounded border-2 border-border bg-accent text-primary focus:ring-primary focus:ring-offset-0"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label for="remember" class="text-sm text-muted-foreground cursor-pointer select-none">
                            Recordarme
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full px-6 py-3 bg-primary text-primary-foreground rounded font-medium text-sm uppercase tracking-wider hover:opacity-90 transition-opacity flex items-center justify-center gap-2"
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        Ingresar
                    </button>

                </form>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3" style="border-top: 1px solid var(--border);">
                <p class="text-xs text-muted-foreground text-center opacity-60">
                    Sistema restringido — Solo usuarios autorizados
                </p>
            </div>

        </div>

        {{-- Terminal-style footer --}}
        <div class="text-center">
            <p class="text-xs text-muted-foreground font-mono opacity-40">
                monky@system:~$ <span class="animate-pulse">▊</span>
            </p>
        </div>

    </div>

    {{-- Toggle password visibility --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn  = document.getElementById('toggle-password');
            var passInput  = document.getElementById('password');
            var eyeOff     = document.getElementById('eye-off');
            var eyeOn      = document.getElementById('eye-on');

            if (toggleBtn && passInput) {
                toggleBtn.addEventListener('click', function () {
                    var isPassword = passInput.type === 'password';
                    passInput.type = isPassword ? 'text' : 'password';
                    eyeOff.style.display = isPassword ? 'none' : 'block';
                    eyeOn.style.display  = isPassword ? 'block' : 'none';
                });
            }
        });
    </script>

</body>
</html>
