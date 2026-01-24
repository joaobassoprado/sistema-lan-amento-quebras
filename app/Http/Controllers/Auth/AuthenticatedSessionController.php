<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // OPCÃO 1: Se você quer que TODOS vejam a tela de boas-vindas com animação primeiro:
        return redirect()->route('dashboard');

        /* // OPÇÃO 2: Se você quiser manter a divisão por perfil, mas o "Padrão" ser a Boas-Vindas:
        $user = $request->user();
        $profileId = (int) $user->profile_id;

        $rota = match ($profileId) {
            3       => 'minhas-atribuicoes', 
            4       => 'controle.abono',     
            default => 'dashboard', // Alterado de 'vales.dashboard' para 'dashboard'
        };

        return redirect()->route($rota);
        */
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}