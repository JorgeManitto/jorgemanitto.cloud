<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login.
     */
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al escritorio
        if (Auth::check()) {
            return redirect()->route('overview');
        }

        return view('pages.auth.login');
    }

    /**
     * Autenticar al usuario.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'El email es obligatorio.',
            'email.email'       => 'Ingresá un email válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $remember = $request->boolean('remember');

        // Rate limiting: max 5 intentos por minuto
        $throttleKey = strtolower($request->email) . '|' . $request->ip();

        if (app('Illuminate\Cache\RateLimiter')->tooManyAttempts($throttleKey, 5)) {
            $seconds = app('Illuminate\Cache\RateLimiter')->availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => ["Demasiados intentos. Intentá de nuevo en {$seconds} segundos."],
            ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            app('Illuminate\Cache\RateLimiter')->clear($throttleKey);

            return redirect()->intended(route('overview'));
        }

        // Incrementar intentos fallidos
        app('Illuminate\Cache\RateLimiter')->hit($throttleKey, 60);

        throw ValidationException::withMessages([
            'email' => ['Las credenciales no coinciden con nuestros registros.'],
        ]);
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
