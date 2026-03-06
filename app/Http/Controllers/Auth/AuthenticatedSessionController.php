<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, RateLimiter};
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Informe seu email.',
            'email.email'       => 'Digite um email válido.',
            'password.required' => 'Informe sua senha.',
        ]);

        $throttleKey = Str::transliterate(
            Str::lower($request->string('email')) . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = (int) ceil($seconds / 60);

            return back()
                ->withInput($request->only('email'))
                ->with('toast_error', "Calma! Você tentou muitas vezes. Aguarde {$minutes} " . ($minutes === 1 ? 'minuto' : 'minutos') . ' e tente novamente.');
        }

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 300);

            $remaining = 5 - RateLimiter::attempts($throttleKey);

            $msg = 'Email ou senha incorretos.';
            if ($remaining <= 2 && $remaining > 0) {
                $msg .= " Você ainda tem {$remaining} " . ($remaining === 1 ? 'tentativa' : 'tentativas') . '.';
            }

            return back()
                ->withInput($request->only('email'))
                ->with('toast_error', $msg);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
