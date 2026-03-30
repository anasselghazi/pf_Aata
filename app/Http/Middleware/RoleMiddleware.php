<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Vérifier si l'utilisateur a le bon rôle
        if (!auth()->check() || auth()->user()->role !== $role) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier si le compte est suspendu
        if (auth()->user()->est_suspendu) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été suspendu']);
        }

        return $next($request);
    }
}