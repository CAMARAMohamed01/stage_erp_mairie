<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Si l'utilisateur n'est pas connecté ou que son rôle n'est pas dans la liste autorisée
        if (!Auth::check() || !in_array(Auth::user()->role_appli, $roles)) {
            return redirect()->route('technique.dashboard')->with('error', "Vous n'avez pas les droits pour accéder à cette page.");
        }

        return $next($request);
    }
}